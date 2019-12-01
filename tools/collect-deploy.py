# Tool to collect generated files for deployment

import argparse
import collections
import json
import os
import re
import subprocess
import sys

parser = argparse.ArgumentParser(description='Collect files for deployment')
parser.add_argument('staticprep_files', nargs='+', metavar='STATICPREP_FILES', help='files produced by staticprep')
parser.add_argument('--extradeploy', nargs='*', metavar='EXTRADEPLOY', help='additional files to deploy')
args = parser.parse_args()

# Deployment info. Key is a source file, values are additional generated files
deploy_files = {}

make_dependency_re = re.compile('update target \'(.*)\' due to: (.*)')

# Filter out untracked files
def filter_untracked(files):
    tracked = []
    for f in files:
        git_process = subprocess.Popen(' '.join (['git', 'ls-files', '--error-unmatch', f]), shell=True,
                                       stdout=open(os.devnull, 'w'), stderr=subprocess.STDOUT)
        git_process.wait()
        if git_process.returncode == 0:
            tracked.append(f)
    return tracked

# Recursively determine dependencies
def resolve_dependencies(dep_tree, target):
    pending_deps = collections.deque()
    seen_deps = set()

    pending_deps.extend(dep_tree.get(target, []))
    while len(pending_deps) > 0:
        next_dep = pending_deps.popleft()
        if not next_dep in seen_deps:
            seen_deps.add(next_dep)
            pending_deps.extend(dep_tree.get(next_dep, []))
    return seen_deps

# Collect all files the staticprep_files depend on using make
for prep_file_name in args.staticprep_files:
    dep_tree = {}
    make_process = subprocess.Popen(' '.join (['make', '-s', '-n', '-B', '--trace', prep_file_name]),
                                    shell=True, stdout=subprocess.PIPE)
    for line in make_process.stdout:
        line = line.decode('utf-8').strip()
        if line.startswith("Makefile:"):
            # Extract part after first ' '
            message = line.split(' ', 1)[1]
            dep_match = make_dependency_re.match(message)
            if dep_match:
                deps = set(dep_match.group(2).split())
                dep_tree.setdefault(dep_match.group(1), set()).update(deps)

    # Inject 'dependency' of extradeploy files on produced file
    for extradeploy in args.extradeploy:
        dep_tree.setdefault(extradeploy, set()).add(prep_file_name)

    # Read generated files, add as dependencies
    generated_files = {}
    with open(prep_file_name + '.gen') as prep_file_generated:
        generated_files = json.load(prep_file_generated)

    for generated, deps in generated_files.items():
        dep_tree.setdefault(generated, set()).update(deps)

    # Determine tracked files
    all_deps = set()
    for file, deps in dep_tree.items():
        all_deps = all_deps | deps
    tracked_deps = filter_untracked(all_deps)

    # Generate deployment info
    for to_deploy in list(generated_files.keys()) + args.extradeploy:
        deploy_dep = resolve_dependencies(dep_tree, to_deploy)
        deploy_dep = filter(lambda f: f in tracked_deps, deploy_dep)
        deploy_files.setdefault(to_deploy, []).extend(deploy_dep)

# Write out json
json.dump(deploy_files, sys.stdout, indent=2)
