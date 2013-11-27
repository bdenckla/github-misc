~/Downloads/TMC-sample.html: ~/Downloads/TMC-sample.TXT Makefile php5-pspell.novc.marker *.php
	./parse-tbt.php $< > $@.tmp
	mv $@.tmp $@

php5-pspell.novc.marker:
	sudo apt-get -y install php5-pspell
	touch $@
