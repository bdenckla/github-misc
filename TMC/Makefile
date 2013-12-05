O := ~/Downloads/TMC-sample.TXT
T := ~/Downloads/TMC-sample.tmp.TXT
P := ~/Downloads/TMC-sample.patched.TXT
C := ~/Downloads/TMC-sample.corrected.TXT
D := TMC-sample-corrections

~/Downloads/TMC-sample.html: $P Makefile php5-pspell.novc.marker *.php
	./parse-tbt.php $< > $@.tmp
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
