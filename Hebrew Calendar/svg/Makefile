default_target: holidays.novc.html svgs-of-holidays cat

svgs-of-holidays: holidays.major.novc.svg holidays.roshchod.novc.svg holidays.shabbat.novc.svg

holidays.novc.html: *.php
	./holidays.php html > $@.tmp
	mv $@.tmp $@

holidays.major.novc.svg: *.php
	./holidays.php svg major > $@.tmp
	mv $@.tmp $@

holidays.roshchod.novc.svg: *.php
	./holidays.php svg roshchod > $@.tmp
	mv $@.tmp $@

holidays.shabbat.novc.svg: *.php
	./holidays.php svg shabbat > $@.tmp
	mv $@.tmp $@

cat: holidays.novc.html
	cat holidays.novc.html
