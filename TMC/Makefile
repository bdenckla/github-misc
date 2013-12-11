top: ~/Downloads/TMC-sample.html ~/Downloads/RP-Gen-commentary.html

O := ~/Downloads/TMC-sample.TXT
T := ~/Downloads/TMC-sample.tmp.TXT
P := ~/Downloads/TMC-sample.patched.TXT
C := ~/Downloads/TMC-sample.corrected.TXT
D := TMC-sample-corrections

TBT_PREREQS := $P php5-pspell.novc.marker Makefile generate-html.php parse-tbt.php
MSW_PREREQS := ~/Downloads/RP-Gen-commentary.pp.xml Makefile generate-html.php ./parse-ms-word-xml.php

~/Downloads/TMC-sample.html: $(TBT_PREREQS)
	./parse-tbt.php $< > $@.tmp
	mv $@.tmp $@

~/Downloads/RP-Gen-commentary.pp.xml: ~/Downloads/RP-Gen-commentary.xml libxml2-utils.novc.marker
	xmllint --format $< > $@.tmp
	mv $@.tmp $@

~/Downloads/RP-Gen-commentary.html: $(MSW_PREREQS)
	./parse-ms-word-xml.php $< > $@.tmp
	mv $@.tmp $@

$P: $O $D Makefile
	cp $O $T
	patch $T $D
	mv $T $P

new-patchfile: $O $C Makefile
	cp $O $T
	diff --context $T $C > $D.tmp || true
	mv $D.tmp $D

php5-pspell.novc.marker:
	sudo apt-get -y install php5-pspell
	touch $@

libxml2-utils.novc.marker:
	sudo apt-get install libxml2-utils
	touch $@
