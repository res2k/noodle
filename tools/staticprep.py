from __future__ import print_function
import argparse
import base64
import gzip
import hashlib
import io
import json
import os
import shutil
import sys

# Compute hash code for file
def file_hash(file_data):
    hash_data = hashlib.md5(file_data).digest()
    return base64.urlsafe_b64encode(hash_data).decode('ascii').rstrip('=')

# Computes 'substitution key' by removing .min part from filename
def substitution_key(filename):
    root, ext = os.path.splitext(filename)
    if root.endswith('.min'):
        root = root[:-4]
    return os.path.basename(root + ext)

parser = argparse.ArgumentParser(description='Static preprocessor for JS and CSS resources.')
parser.add_argument('--prep-dir', metavar='DIR', help='output directory for \'prepped\' files', required=True)
parser.add_argument('--template', metavar='TEMPLATE', help='template file for prepped file substitutions', required=True)
parser.add_argument('--output-file', metavar='FILE', help='processed template output file name', required=True)
parser.add_argument('input_files', nargs='+', metavar='INPUT_FILES', help='files to prepare')
args = parser.parse_args()

if not os.path.exists(args.prep_dir):
    os.makedirs(args.prep_dir)
out_file_dir = os.path.dirname(args.output_file)
if out_file_dir and not os.path.exists(out_file_dir):
    os.makedirs(out_file_dir)

files_map = {}
generated_files = {}
for f in args.input_files:
    # Read file
    file_data = open(f, 'rb').read()
    # Generate hash
    hash_str = file_hash(file_data)
    # Compute output filename
    root, ext = os.path.splitext(os.path.basename(f))
    dest_fn = '{0}.{1}{2}'.format (root, hash_str, ext)
    dest_path = os.path.join (args.prep_dir, dest_fn)
    # Copy to destination
    shutil.copy2(f, dest_path)
    # Compress
    dest_path_gz = dest_path + '.gz'
    with gzip.GzipFile(dest_path_gz, 'wb') as gz_file:
        gz_file.write (file_data)
    # Store substitution
    files_map[substitution_key(f)] = dest_path
    generated_files.setdefault(dest_path, []).append(f)
    generated_files.setdefault(dest_path_gz, []).append(f)

# Write list of generated files
with io.open(args.output_file + '.gen', 'w', encoding='utf-8') as list_file:
    json.dump(generated_files, list_file, indent=2)

template_data = open(args.template, 'rb').read().decode('utf-8')
new_data = ''
# Replace placeholders in template
while template_data != '':
    ph_begin = template_data.find('@')
    if ph_begin == -1:
        new_data = new_data + template_data
        template_data = ''
        continue
    new_data = new_data + template_data[:ph_begin]
    ph_end = template_data.find('@', ph_begin+1)
    if ph_end == -1: ph_end = len(template_data)
    key = template_data[ph_begin+1:ph_end]
    if not key in files_map:
        print("{0}: Unknown placeholder: {1}".format(args.template, key), file=sys.stderr)
    else:
        new_data = new_data + files_map[key]
    template_data = template_data[ph_end+1:]
with io.open(args.output_file, 'w', encoding='utf-8') as out_file:
    out_file.write (new_data)
