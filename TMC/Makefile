~/Downloads/TMC-current-output.html: ~/Downloads/TMC-sample.html Makefile
	cp $< $@

~/Downloads/TMC-sample.smaller.html: ~/Downloads/TMC-sample.smaller.TXT Makefile *.php
	./parse-tbt.php $< > $@.tmp
	mv $@.tmp $@
	head -100 $@

~/Downloads/TMC-sample.html: ~/Downloads/TMC-sample.TXT Makefile *.php
	./parse-tbt.php $< > $@.tmp
	mv $@.tmp $@
	head -100 $@

~/Downloads/TMC-sample.smaller.TXT: ~/Downloads/TMC-sample.TXT Makefile
	head -1237 $< > $@.tmp
	mv $@.tmp $@

# 1237
