~/Downloads/TMC-sample.html: ~/Downloads/TMC-sample.TXT *.php
	./parse-tbt.php $< > $@.tmp
	mv $@.tmp $@
	head -100 $@

~/Downloads/TMC-sample.1000.html: ~/Downloads/TMC-sample.1000.TXT *.php
	./parse-tbt.php $< > $@.tmp
	mv $@.tmp $@
	head -100 $@

~/Downloads/TMC-sample.1000.TXT: ~/Downloads/TMC-sample.TXT
	head -1000 $< > $@.tmp
	mv $@.tmp $@
