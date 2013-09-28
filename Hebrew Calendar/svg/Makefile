svgs-of-holidays: holidays.nonleap.novc.svg holidays.yesleap.novc.svg

holidays.nonleap.novc.svg: *.php
	./holidays.php nonleap > $@.tmp
	mv $@.tmp $@

holidays.yesleap.novc.svg: *.php
	./holidays.php yesleap > $@.tmp
	mv $@.tmp $@
