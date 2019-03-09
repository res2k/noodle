.PHONY: dev prod prod-clean collect-deploy deploy-gitftp

all_templates = noodle-css.tpl noodle-js.tpl help-css.tpl help-js.tpl head-common.tpl foot-common.tpl

dev prod: %: $(addprefix %.tmp/,$(all_templates))
	for t in $(all_templates) ; do cp -p $*.tmp/$$t . ; done

dev: templates_c

collect-deploy: $(addprefix prod.tmp/,$(all_templates))
	mkdir -p prod.tmp/deploy
	rm -rf prod.tmp/deploy/*
	$(foreach templ,$(all_templates),python tools/collect-deploy.py prod.tmp/$(templ) --extradeploy $(templ) > prod.tmp/deploy/$(templ).json; )

deploy-gitftp: collect-deploy
	cp deploy-ignore.txt .git-ftp-ignore
	rm -f .git-ftp-include
	python tools/deploy-gitftp.py $(foreach templ,$(all_templates), prod.tmp/deploy/$(templ).json )>> .git-ftp-include

push: prod deploy-gitftp
	git-ftp push -s master

# Template cache for testing in-place
templates_c:
	mkdir templates_c
	chmod a+w templates_c/

SRC = staticsrc
TMP = tmp

$(TMP)/%.css: $(SRC)/%.scss
	mkdir -p $(dir $@)
	sass $< $@

tmp/style.css: $(SRC)/style.scss $(SRC)/style-common.scss $(SRC)/style-defs.scss
tmp/style-admin.css: $(SRC)/style-admin.scss $(SRC)/style-defs.scss
tmp/helpstyle.css: $(SRC)/helpstyle.scss $(SRC)/style-common.scss $(SRC)/style-defs.scss

JQUERY_JS = $(SRC)/jquery/jquery-3.3.1.js
JQUERY_MIN_JS = $(SRC)/jquery/jquery-3.3.1.min.js
JQUERY_AUTOCOMPLETE_JS = $(SRC)/jquery_autocomplete/dist/jquery.autocomplete.js
JQUERY_AUTOCOMPLETE_MIN_JS = $(SRC)/jquery_autocomplete/dist/jquery.autocomplete.min.js

NOODLE_JS = noodle noodle-admin jscookie/js.cookie dialog/dialog-polyfill

# dev: prep jquery-2.1.1.js, jquery.autocomplete.js
dev.tmp/noodle-css.tpl: $(SRC)/noodle-css.tpl.in $(TMP)/style.css $(TMP)/style-admin.css $(SRC)/dialog/dialog-polyfill.css
	python tools/staticprep.py --prep-dir s/g/ --output-file $@ --template $^ 

dev.tmp/help-css.tpl: $(SRC)/help-css.tpl.in $(TMP)/helpstyle.css
	python tools/staticprep.py --prep-dir s/g/ --output-file $@ --template $^ 

dev.tmp/noodle-js.tpl: $(SRC)/noodle-js.tpl.in $(foreach js,$(NOODLE_JS), $(SRC)/$(js).js) $(JQUERY_JS) $(JQUERY_AUTOCOMPLETE_JS)
	python tools/staticprep.py --prep-dir s/g/ --output-file $@ --template $^ 

dev.tmp/help-js.tpl: $(SRC)/help-js.tpl.in $(JQUERY_JS)
	python tools/staticprep.py --prep-dir s/g/ --output-file $@ --template $^ 

%.min.css: %.css
	cleancss -o $@ $^

$(TMP)/%.min.css: $(SRC)/%.css
	mkdir -p $(dir $@)
	cleancss -o $@ $^

$(TMP)/%.min.js: $(SRC)/%.js
	mkdir -p $(dir $@)
	uglifyjs $^ > $@

# prod: minify js; prep jquery-2.1.1.min.js, jquery.autocomplete.min.js
prod.tmp/noodle-css.tpl: $(SRC)/noodle-css.tpl.in $(TMP)/style.min.css $(TMP)/style-admin.min.css $(TMP)/dialog/dialog-polyfill.min.css
	python tools/staticprep.py --prep-dir s/g/ --output-file $@ --template $^

prod.tmp/help-css.tpl: $(SRC)/help-css.tpl.in $(TMP)/helpstyle.min.css
	python tools/staticprep.py --prep-dir s/g/ --output-file $@ --template $^ 

prod.tmp/noodle-js.tpl: $(SRC)/noodle-js.tpl.in $(foreach js,$(NOODLE_JS), $(TMP)/$(js).min.js) $(JQUERY_MIN_JS) $(JQUERY_AUTOCOMPLETE_MIN_JS)
	python tools/staticprep.py --prep-dir s/g/ --output-file $@ --template $^ 

prod.tmp/help-js.tpl: $(SRC)/help-js.tpl.in $(JQUERY_MIN_JS)
	python tools/staticprep.py --prep-dir s/g/ --output-file $@ --template $^ 

dev.tmp/head-common.tpl \
prod.tmp/head-common.tpl: $(SRC)/head-common.tpl.in $(SRC)/mdl/material.brown-indigo.min.css $(SRC)/web-app-manifest.json
	python tools/staticprep.py --prep-dir s/g/ --output-file $@ --template $^ 

dev.tmp/foot-common.tpl \
prod.tmp/foot-common.tpl: $(SRC)/foot-common.tpl.in $(SRC)/mdl/material.min.js
	python tools/staticprep.py --prep-dir s/g/ --output-file $@ --template $^ 
