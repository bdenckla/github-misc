svgs-of-holidays: holidays.nonleap.novc.svg holidays.yesleap.novc.svg echo

holidays.nonleap.novc.svg: *.php
	./holidays.php nonleap > $@.tmp
	mv $@.tmp $@

holidays.yesleap.novc.svg: *.php
	./holidays.php yesleap > $@.tmp
	mv $@.tmp $@

echo: *.php
	./holidays.php nonleap
