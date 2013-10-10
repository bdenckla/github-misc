svgs-of-holidays: holidays.major.novc.svg holidays.roshchod.novc.svg holidays.shabbat.novc.svg echo

holidays.major.novc.svg: *.php
	./holidays.php major > $@.tmp
	mv $@.tmp $@

holidays.roshchod.novc.svg: *.php
	./holidays.php roshchod > $@.tmp
	mv $@.tmp $@

holidays.shabbat.novc.svg: *.php
	./holidays.php shabbat > $@.tmp
	mv $@.tmp $@

echo: *.php
	./holidays.php major
