(if (not (find-package "CC3"))
    (defpackage "CC3"))
(in-package "CC3")

(export '(true false bogus rd sunday monday tuesday wednesday
          thursday friday saturday january february march april may
          june july august september october november december kalends
          nones ides ayyam-i-ha arya-solar-year arya-solar-month
          arya-lunar-month arya-lunar-day arya-jovian-period mecca
          spring summer autumn winter new first-quarter full
          last-quarter haifa islamic-locale jerusalem tehran paris
          ujjain hindu-locale quotient day-of-week-from-fixed
          standard-month standard-day standard-year time-of-day
          hour minute seconds 
          fixed-from-moment time-from-moment clock-from-moment
          time-from-clock angle-from-degrees moment-from-jd jd-from-moment
          fixed-from-jd jd-from-fixed fixed-from-mjd mjd-from-fixed
          range start end in-range? list-range
          egyptian-date fixed-from-egyptian egyptian-from-fixed armenian-date
          fixed-from-armenian armenian-from-fixed gregorian-date
          gregorian-leap-year? fixed-from-gregorian
          gregorian-year-from-fixed gregorian-from-fixed
          gregorian-date-difference day-number days-remaining
          alt-fixed-from-gregorian alt-gregorian-from-fixed
          alt-gregorian-year-from-fixed 
          gregorian-new-year gregorian-year-end
          independence-day kday-on-or-before kday-on-or-after
          kday-nearest kday-after kday-before nth-kday first-kday last-kday
          labor-day memorial-day election-day daylight-saving-start
          daylight-saving-end christmas advent epiphany
          unlucky-fridays-in-range
          iso-date iso-week iso-day iso-year fixed-from-iso iso-from-fixed
          iso-long-year? julian-date bce ce julian-leap-year?
          fixed-from-julian julian-from-fixed roman-date roman-year roman-month
          roman-event roman-count roman-leap ides-of-month
          nones-of-month fixed-from-roman roman-from-fixed year-rome-founded
          julian-year-from-auc-year auc-year-from-julian-year
          julian-in-gregorian eastern-orthodox-christmas coptic-date
          coptic-leap-year? fixed-from-coptic coptic-from-fixed
          ethiopic-date fixed-from-ethiopic ethiopic-from-fixed
          coptic-in-gregorian coptic-christmas orthodox-easter
          alt-orthodox-easter easter pentecost islamic-date
          islamic-leap-year? fixed-from-islamic islamic-from-fixed
          islamic-in-gregorian mawlid-an-nabi bahai-date bahai-major
          bahai-cycle bahai-year bahai-month bahai-day
          fixed-from-bahai bahai-from-fixed bahai-new-year
          feast-of-ridvan hebrew-date hebrew-leap-year?
          last-month-of-hebrew-year hebrew-sabbatical-year?
          last-day-of-hebrew-month hebrew-new-year molad fixed-from-hebrew
          hebrew-from-fixed yom-kippur passover omer purim ta-anit-esther
          tishah-be-av birkath-ha-hama sh-ela yom-ha-zikaron
          hebrew-birthday-in-gregorian yahrzeit-in-gregorian
          possible-hebrew-days
          mayan-long-count-date mayan-haab-date mayan-tzolkin-date
          mayan-baktun mayan-katun mayan-tun mayan-uinal mayan-kin
          mayan-haab-month mayan-haab-day mayan-tzolkin-number
          mayan-tzolkin-name fixed-from-mayan-long-count
          mayan-long-count-from-fixed mayan-haab-from-fixed
          mayan-haab-on-or-before mayan-tzolkin-from-fixed
          mayan-tzolkin-on-or-before mayan-year-bearer-from-fixed
          mayan-calendar-round-on-or-before aztec-xihuitl-date
          aztec-xihuitl-month aztec-xihuitl-day aztec-tonalpohualli-date
          aztec-tonalpohualli-number aztec-tonalpohualli-name
          aztec-xiuhmolpilli-designation aztec-xiuhmolpilli-number
          aztec-xiuhmolpilli-name aztec-correlation aztec-xihuitl-ordinal
          aztec-xihuitl-correlation aztec-xihuitl-from-fixed
          aztec-xihuitl-on-or-before aztec-tonalpohualli-ordinal
          aztec-tonalpohualli-correlation aztec-tonalpohualli-from-fixed
          aztec-tonalpohualli-on-or-before
          aztec-xihuitl-tonalpohualli-on-or-before
          aztec-xiuhmolpilli-from-fixed old-hindu-lunar-date
          old-hindu-lunar-month old-hindu-lunar-leap old-hindu-lunar-day
          old-hindu-lunar-year hindu-solar-date hindu-day-count
          old-hindu-solar-from-fixed fixed-from-old-hindu-solar
          old-hindu-lunar-leap-year? old-hindu-lunar-from-fixed
          fixed-from-old-hindu-lunar jovian-year balinese-date
          bali-luang bali-dwiwara bali-triwara bali-caturwara
          bali-pancawara bali-sadwara bali-saptawara bali-asatawara
          bali-sangawara bali-dasawara bali-day-from-fixed
          bali-luang-from-fixed bali-dwiwara-from-fixedok.tex
          bali-triwara-from-fixed bali-caturwara-from-fixed
          bali-pancawara-from-fixed bali-sadwara-from-fixed
          bali-saptawara-from-fixed bali-asatawara-from-fixed
          bali-sangawara-from-fixed bali-dasawara-from-fixed
          bali-pawukon-from-fixed bali-week-from-fixed
          bali-on-or-before positions-in-range
          kajeng-keliwon tumpek hr sec deg mt
          angle location latitude longitude elevation zone direction
          standard-from-universal
          universal-from-standard local-from-universal
          universal-from-local standard-from-local local-from-standard
          midday midnight local-from-apparent apparent-from-local
          dawn dusk sunrise sunset islamic-sunrise
          islamic-sunset jewish-dusk jewish-sabbath-ends
          daytime-temporal-hour nighttime-temporal-hour
          standard-from-sundial jewish-morning-end asr
          universal-from-dynamical dynamical-from-universal
          sidereal-from-moment equation-of-time solar-longitude
          solar-longitude-after sidereal-solar-longitude
          lunar-longitude nth-new-moon new-moon-before new-moon-at-or-after 
          lunar-phase lunar-phase-at-or-before lunar-phase-at-or-after
          topocentric-lunar-altitude lunar-diameter
          lunar-latitude lunar-altitude lunar-distance 
          fixed-from-observational-islamic
          persian-date persian-new-year-on-or-before
          fixed-from-persian persian-from-fixed
          arithmetic-persian-leap-year? fixed-from-arithmetic-persian
          arithmetic-persian-year-from-fixed
          arithmetic-persian-from-fixed naw-ruz french-date
          french-new-year-on-or-before fixed-from-french
          french-from-fixed arithmetic-french-leap-year?
          fixed-from-arithmetic-french arithmetic-french-from-fixed
          chinese-date chinese-cycle chinese-year chinese-month
          chinese-leap chinese-day chinese-location
          chinese-solar-longitude-on-or-after current-major-solar-term
          major-solar-term-on-or-after current-minor-solar-term
          minor-solar-term-on-or-after chinese-new-year-on-or-before
          chinese-new-year chinese-from-fixed fixed-from-chinese
          chinese-name chinese-stem chinese-branch
          chinese-sexagesimal-name chinese-name-difference
          chinese-name-of-year chinese-name-of-month
          chinese-name-of-day chinese-day-name-on-or-before dragon-festival
          qing-ming chinese-age chinese-year-marriage-augury
          japanese-location korean-location korean-year
          vietnamese-location hindu-lunar-date hindu-lunar-month
          hindu-lunar-leap-month hindu-lunar-day hindu-lunar-leap-day
          hindu-lunar-year hindu-lunar-day-at-or-after hindu-solar-from-fixed
          fixed-from-hindu-solar hindu-lunar-from-fixed
          fixed-from-hindu-lunar hindu-sunrise alt-hindu-sunrise
          ayanamsha astro-hindu-sunset hindu-sunset hindu-sundial-time
          astro-hindu-solar-from-fixed fixed-from-astro-hindu-solar
          astro-hindu-lunar-from-fixed fixed-from-astro-hindu-lunar
          hindu-fullmoon-from-fixed fixed-from-hindu-fullmoon
          hindu-lunar-station hindu-solar-longitude-at-or-after
          mesha-samkranti hindu-lunar-holiday diwali shiva rama
          hindu-lunar-new-year karana yoga sacred-wednesdays
          tibetan-from-fixed fixed-from-tibetan losar tibetan-new-year
          future-bahai-new-year-on-or-before fixed-from-future-bahai
          future-bahai-from-fixed phasis-on-or-before phasis-on-or-after
          observational-islamic-from-fixed astronomical-easter
          observational-hebrew-from-fixed fixed-from-observational-hebrew
          observational-hebrew-new-year classical-passover-eve
          ))


;;;; Section: Basic Code

(defconstant true
  ;; TYPE boolean
  ;; Constant representing true.
  t)

(defconstant false
  ;; TYPE boolean
  ;; Constant representing false.
  nil)

(defconstant bogus
  ;; TYPE string
  ;; Used to denote nonexistent dates.
  "bogus")

(defun quotient (m n)
  ;; TYPE (real nonzero-real) -> integer
  ;; Whole part of $m$/$n$.
  (floor m n))

(defun amod (x y)
  ;; TYPE (real real) -> real
  ;; The value of ($x$ mod $y$) with $y$ instead of 0.
  (+ y (mod x (- y))))

(defmacro next (index initial condition)
  ;; TYPE (* integer (integer->boolean)) -> integer
  ;; First integer greater or equal to $initial$ such that
  ;; $condition$ holds.
  `(do ((,index ,initial (1+ ,index)))
       (,condition ,index)))

(defmacro final (index initial condition)
  ;; TYPE (* integer (integer->boolean)) -> integer
  ;; Last integer greater or equal to $initial$ such that
  ;; $condition$ holds.
  `(do ((,index ,initial (1+ ,index)))
       ((not ,condition) (1- ,index))))

(defmacro sum (expression index initial condition)
  ;; TYPE ((integer->real) * integer (integer->boolean))
  ;; TYPE -> real
  ;; Sum $expression$ for $index$ = $initial$ and successive
  ;; integers, as long as $condition$ holds.
  (let* ((temp (gensym)))
    `(do ((,temp 0 (+ ,temp ,expression))
          (,index ,initial (1+ ,index)))
         ((not ,condition) ,temp))))

(defmacro binary-search (l lo h hi x test end)
  ;; TYPE (* real * real * (real->boolean)
  ;; TYPE  ((real real)->boolean)) -> real
  ;; Bisection search for $x$ in [$lo$,$hi$] such that
  ;; $end$ holds.  $test$ determines when to go left.
  (let* ((left (gensym)))
    `(do* ((,x false (/ (+ ,h ,l) 2))
           (,left false ,test)
           (,l ,lo (if ,left ,l ,x))
           (,h ,hi (if ,left ,x ,h)))
          (,end (/ (+ ,h ,l) 2)))))

(defmacro invert-angular (f y a b)
  ;; TYPE (real->angle real real real) -> real 
  ;; Use bisection to find inverse of angular function
  ;; $f$ at $y$ within interval [$a$,$b$].
  (let* ((varepsilon 1/100000)); Desired accuracy
    `(binary-search l ,a u ,b x
                    (< (mod (- (,f x) ,y) 360) (deg 180))
                    (< (- u l) ,varepsilon))))

(defmacro sigma (list body)
  ;; TYPE (list-of-pairs (list-of-reals->real))
  ;; TYPE -> real
  ;; $list$ is of the form ((i1 l1)..(in ln)).
  ;; Sum of $body$ for indices i1..in
  ;; running simultaneously thru lists l1..ln.
  `(apply '+ (mapcar (function (lambda
                                 ,(mapcar 'car list)
                                 ,body))
                     ,@(mapcar 'cadr list))))

(defun poly (x a)
  ;; TYPE (real list-of-reals) -> real
  ;; Sum powers of $x$ with coefficients (from order 0 up)
  ;; in list $a$.
  (if (equal a nil)
      0
    (+ (first a) (* x (poly x (rest a))))))

(defun rd (tee)
  ;; TYPE moment -> moment
  ;; Identity function for fixed dates/moments.  If internal
  ;; timekeeping is shifted, change $epoch$ to be RD date of
  ;; origin of internal count.  $epoch$ should be an integer.
  (let* ((epoch 0))
    (- tee epoch)))

(defconstant sunday
  ;; TYPE day-of-week
  ;; Residue class for Sunday.
  0)

(defconstant monday
  ;; TYPE day-of-week
  ;; Residue class for Monday.
  (+ sunday 1))

(defconstant tuesday
  ;; TYPE day-of-week
  ;; Residue class for Tuesday.
  (+ sunday 2))

(defconstant wednesday
  ;; TYPE day-of-week
  ;; Residue class for Wednesday.
  (+ sunday 3))

(defconstant thursday
  ;; TYPE day-of-week
  ;; Residue class for Thursday.
  (+ sunday 4))

(defconstant friday
  ;; TYPE day-of-week
  ;; Residue class for Friday.
  (+ sunday 5))

(defconstant saturday
  ;; TYPE day-of-week
  ;; Residue class for Saturday.
  (+ sunday 6))

(defun day-of-week-from-fixed (date)
  ;; TYPE fixed-date -> day-of-week
  ;; The residue class of the day of the week of $date$.
  (mod (- date (rd 0) sunday) 7))

(defun standard-month (date)
  ;; TYPE standard-date -> standard-month
  ;; Month field of $date$ = (year month day).
  (second date))

(defun standard-day (date)
  ;; TYPE standard-date -> standard-day
  ;; Day field of $date$ = (year month day).
  (third date))

(defun standard-year (date)
  ;; TYPE standard-date -> standard-year
  ;; Year field of $date$ = (year month day).
  (first date))

(defun time-of-day (hour minute second)
  ;; TYPE (hour minute second) -> clock-time
  (list hour minute second))

(defun hour (clock)
  ;; TYPE clock-time -> hour
  (first clock))

(defun minute (clock)
  ;; TYPE clock-time -> minute
  (second clock))

(defun seconds (clock)
  ;; TYPE clock-time -> second
  (third clock))

(defun fixed-from-moment (tee)
  ;; TYPE moment -> fixed-date
  ;; Fixed-date from moment $tee$.
  (floor tee))

(defun time-from-moment (tee)
  ;; TYPE moment -> time
  ;; Time from moment $tee$.
  (mod tee 1))

(defun clock-from-moment (tee)
  ;; TYPE moment -> clock-time
  ;; Clock time hour:minute:second from moment $tee$.
  (let* ((time (time-from-moment tee))
         (hour (floor (* time 24)))
         (minute (floor (mod (* time 24 60) 60)))
         (second (mod (* time 24 60 60) 60)))
    (time-of-day hour minute second)))

(defun time-from-clock (hms)
  ;; TYPE clock-time -> time
  ;; Time of day from $hms$ = (hour minute second).
  (let* ((h (hour hms))
         (m (minute hms))
         (s (seconds hms)))
    (* 1/24 (+ h (/ (+ m (/ s 60)) 60)))))

(defun degrees-minutes-seconds (d m s)
  ;; TYPE (degree minute real) -> angle
  (list d m s))

(defun angle-from-degrees (alpha)
  ;; TYPE angle -> list-of-reals
  ;; List of degrees-arcminutes-arcseconds from angle
  ;; $alpha$ in degrees.
   (let* ((d (floor alpha))
          (m (floor (* 60 (mod alpha 1))))
          (s (mod (* alpha 60 60) 60)))
     (degrees-minutes-seconds d m s)))

(defconstant jd-epoch
  ;; TYPE moment
  ;; Fixed time of start of the julian day number.
  (rd -1721424.5L0))

(defun moment-from-jd (jd)
  ;; TYPE julian-day-number -> moment
  ;; Moment of julian day number $jd$.
  (+ jd jd-epoch))

(defun jd-from-moment (tee)
  ;; TYPE moment -> julian-day-number
  ;; Julian day number of moment $tee$.
  (- tee jd-epoch))

(defun fixed-from-jd (jd)
  ;; TYPE julian-day-number -> fixed-date
  ;; Fixed date of julian day number $jd$.
  (floor (moment-from-jd jd)))

(defun jd-from-fixed (date)
  ;; TYPE fixed-date -> julian-day-number
  ;; Julian day number of fixed $date$.
  (jd-from-moment date))

(defconstant mjd-epoch
  ;; TYPE fixed-date
  ;; Fixed time of start of the modified julian day number.
  (rd 678576))

(defun fixed-from-mjd (mjd)
  ;; TYPE julian-day-number -> fixed-date
  ;; Fixed date of modified julian day number $mjd$.
  (+ mjd mjd-epoch))

(defun mjd-from-fixed (date)
  ;; TYPE fixed-date -> julian-day-number
  ;; Modified julian day number of fixed $date$.
  (- date mjd-epoch))

(defun interval (t0 t1)
  ;; TYPE (moment moment) -> range
  ;; Closed interval [$t0$,$t1$].
  (list t0 t1))

(defun start (range)
  ;; TYPE range -> moment
  ;; Start $t0$ of $range$=[$t0$,$t1$].
  (first range))

(defun end (range)
  ;; TYPE range -> moment
  ;; End $t1$ of $range$=[$t0$,$t1$].
  (second range))

(defun in-range? (tee range)
  ;; TYPE (moment range) -> boolean
  ;; True if $tee$ is in $range$. 
  (<= (start range) tee (end range)))

(defun list-range (ell range)
  ;; TYPE (list-of-moments range) -> range
  ;; Those moments in list $ell$ that occur in $range$.
  (if (equal ell nil)
      nil
    (let* ((r (list-range (rest ell) range)))
      (if (in-range? (first ell) range)
          (append (list (first ell)) r)
        r))))

;;;; Section: Hebrew Calendar

(defun hebrew-date (year month day)
  ;; TYPE (hebrew-year hebrew-month hebrew-day) -> hebrew-date
  (list year month day))

(defconstant nisan
  ;; TYPE hebrew-month
  ;; Nisan is month number 1.
  1)

(defconstant iyyar
  ;; TYPE hebrew-month
  ;; Iyyar is month number 2.
  2)

(defconstant sivan
  ;; TYPE hebrew-month
  ;; Sivan is month number 3.
  3)

(defconstant tammuz
  ;; TYPE hebrew-month
  ;; Tammuz is month number 4.
  4)

(defconstant av
  ;; TYPE hebrew-month
  ;; Av is month number 5.
  5)

(defconstant elul
  ;; TYPE hebrew-month
  ;; Elul is month number 6.
  6)

(defconstant tishri
  ;; TYPE hebrew-month
  ;; Tishri is month number 7.
  7)

(defconstant marheshvan
  ;; TYPE hebrew-month
  ;; Marheshvan is month number 8.
  8)

(defconstant kislev
  ;; TYPE hebrew-month
  ;; Kislev is month number 9.
  9)

(defconstant tevet
  ;; TYPE hebrew-month
  ;; Tevet is month number 10.
  10)

(defconstant shevat
  ;; TYPE hebrew-month
  ;; Shevat is month number 11.
  11)

(defconstant adar
  ;; TYPE hebrew-month
  ;; Adar is month number 12.
  12)

(defconstant adarii
  ;; TYPE hebrew-month
  ;; Adar II is month number 13.
  13)

(defconstant hebrew-epoch
  ;; TYPE fixed-date
  ;; Fixed date of start of the Hebrew calendar, that is,
  ;; Tishri 1, 1 AM.
  -1373427) ;; (fixed-from-julian (julian-date (bce 3761) october 7)))

(defun hebrew-leap-year? (h-year)
  ;; TYPE hebrew-year -> boolean
  ;; True if $h-year$ is a leap year on Hebrew calendar.
  (< (mod (1+ (* 7 h-year)) 19) 7))

(defun last-month-of-hebrew-year (h-year)
  ;; TYPE hebrew-year -> hebrew-month
  ;; Last month of Hebrew year.
  (if (hebrew-leap-year? h-year)
      adarii
    adar))

(defun hebrew-sabbatical-year? (h-year)
  ;; TYPE hebrew-year -> boolean
  ;; True if $h-year$ is a sabbatical year on the Hebrew
  ;; calendar.
  (= (mod h-year 7) 0))

(defun last-day-of-hebrew-month (h-month h-year)
  ;; TYPE (hebrew-month hebrew-year) -> hebrew-day
  ;; Last day of month$h-month$  in Hebrew year $h-year$.
  (if (or (member h-month
                  (list iyyar tammuz elul tevet adarii))
          (and (= h-month adar)
               (not (hebrew-leap-year? h-year)))
          (and (= h-month marheshvan)
               (not (long-marheshvan? h-year)))
          (and (= h-month kislev)
               (short-kislev? h-year)))
      29
    30))

(defconstant parts-per-day 25920)

(defconstant parts-per-lunation
  (+ (* 29 parts-per-day)
     13753))

(defconstant years-to-months-phase 234)

(defconstant epoch-phase -12084)

(defun avoid-certain-days-of-week (days)
  ;; The argument "days" is days since the Hebrew epoch, which was a
  ;; Monday.  I.e. day 0 was a Monday.  So, for example, day 2 was a
  ;; Wednesday, as is any day whose 7-modulus is 2.
  (+ days
     (if (member (mod days 7) '(2 4 6)); W, F, or Su
         1 ; Delay one day.
       0)))

(defun months-elapsed (h-year)
  (quotient (- (* 235 h-year) years-to-months-phase) 19))

(defun parts-elapsed (h-year)
  (- (* (months-elapsed h-year) parts-per-lunation)
     epoch-phase))

(defun days-elapsed (h-year)
  (quotient (parts-elapsed h-year) parts-per-day))

(defun hebrew-calendar-elapsed-days (h-year)
  ;; TYPE hebrew-year -> integer
  ;; Number of days elapsed from the (Sunday) noon prior
  ;; to the epoch of the Hebrew calendar to the mean
  ;; conjunction (molad) of Tishri of Hebrew year $h-year$,
  ;; or one day later.
  (avoid-certain-days-of-week (days-elapsed h-year)))

(defun hebrew-new-year (h-year)
  ;; TYPE hebrew-year -> fixed-date
  ;; Fixed date of Hebrew new year $h-year$.
  (+ hebrew-epoch
     (hebrew-calendar-elapsed-days h-year)
     (hebrew-year-length-correction h-year)))

(defun hebrew-year-length-correction (h-year)
  ;; TYPE hebrew-year -> {0,1,2}
  ;; Delays to start of Hebrew year $h-year$ to keep ordinary
  ;; year in range 353-356 and leap year in range 383-386.
  (let* ((ny0 (hebrew-calendar-elapsed-days (1- h-year)))
         (ny1 (hebrew-calendar-elapsed-days h-year))
         (ny2 (hebrew-calendar-elapsed-days (1+ h-year))))
    (cond
     ((= (- ny2 ny1) 356) ; Next year would be too long.
      2)
     ((= (- ny1 ny0) 382) ; Previous year too short.
      1)
     (t 0))))

(defun days-in-hebrew-year (h-year)
  ;; TYPE hebrew-year -> {353,354,355,383,384,385}
  ;; Number of days in Hebrew year $h-year$.
  (- (hebrew-new-year (1+ h-year))
     (hebrew-new-year h-year)))

(defun long-marheshvan? (h-year)
  ;; TYPE hebrew-year -> boolean
  ;; True if Marheshvan is long in Hebrew year $h-year$.
  (member (days-in-hebrew-year h-year) (list 355 385)))

(defun short-kislev? (h-year)
  ;; TYPE hebrew-year -> boolean
  ;; True if Kislev is short in Hebrew year $h-year$.
  (member (days-in-hebrew-year h-year) (list 353 383)))

(defun fixed-from-hebrew (h-date)
  ;; TYPE hebrew-date -> fixed-date
  ;; Fixed date of Hebrew date $h-date$.
  (let* ((month (standard-month h-date))
         (day (standard-day h-date))
         (year (standard-year h-date)))
    (+ (hebrew-new-year year)
       day -1               ; Days so far this month.
       (if ;; before Tishri
           (< month tishri)
           ;; Then add days in prior months this year before
           ;; and after Nisan.
           (+ (sum (last-day-of-hebrew-month m year)
                   m tishri
                   (<= m (last-month-of-hebrew-year year)))
              (sum (last-day-of-hebrew-month m year)
                   m nisan (< m month)))
         ;; Else add days in prior months this year
         (sum (last-day-of-hebrew-month m year)
              m tishri (< m month))))))

(defun hebrew-from-fixed (date)
  ;; TYPE fixed-date -> hebrew-date
  ;; Hebrew (year month day) corresponding to fixed $date$.
  ;; The fraction can be approximated by 365.25.
  (let* ((approx    ; Approximate year
          (1+
           (quotient (- date hebrew-epoch) 35975351/98496)))
         ;; The value 35975351/98496, the average length of
         ;; a Hebrew year, can be approximated by 365.25
         (year      ; Search forward.
          (final y (1- approx)
                 (<= (hebrew-new-year y) date)))
         (start     ; Starting month for search for month.
          (if (< date (fixed-from-hebrew
                       (hebrew-date year nisan 1)))
              tishri
            nisan))
         (month ; Search forward from either Tishri or Nisan.
          (next m start
                (<= date
                    (fixed-from-hebrew
                     (hebrew-date
                      year
                      m
                      (last-day-of-hebrew-month m year))))))
         (day   ; Calculate the day by subtraction.
          (1+ (- date (fixed-from-hebrew
                       (hebrew-date year month 1))))))
    (hebrew-date year month day)))

(defun yom-kippur (g-year)
  ;; TYPE gregorian-year -> fixed-date
  ;; Fixed date of Yom Kippur occurring in Gregorian year
  ;; $g-year$.
  (let* ((hebrew-year
          (1+ (- g-year
                 (gregorian-year-from-fixed
                  hebrew-epoch)))))
    (fixed-from-hebrew (hebrew-date hebrew-year tishri 10))))

(defun passover (g-year)
  ;; TYPE gregorian-year -> fixed-date
  ;; Fixed date of Passover occurring in Gregorian year
  ;; $g-year$.
  (let* ((hebrew-year
          (- g-year
             (gregorian-year-from-fixed hebrew-epoch))))
    (fixed-from-hebrew (hebrew-date hebrew-year nisan 15))))

(defun omer (date)
  ;; TYPE fixed-date -> omer-count
  ;; Number of elapsed weeks and days in the omer at $date$.
  ;; Returns bogus if that date does not fall during the
  ;; omer.
  (let* ((c (- date
               (passover
                (gregorian-year-from-fixed date)))))
    (if (<= 1 c 49)
        (list (quotient c 7) (mod c 7))
      bogus)))

(defun purim (g-year)
  ;; TYPE gregorian-year -> fixed-date
  ;; Fixed date of Purim occurring in Gregorian year $g-year$.
  (let* ((hebrew-year
          (- g-year
             (gregorian-year-from-fixed hebrew-epoch)))
         (last-month  ; Adar or Adar II
          (last-month-of-hebrew-year hebrew-year)))
    (fixed-from-hebrew
     (hebrew-date hebrew-year last-month 14))))

(defun ta-anit-esther (g-year)
  ;; TYPE gregorian-year -> fixed-date
  ;; Fixed date of Ta'anit Esther occurring in
  ;; Gregorian year $g-year$.
  (let* ((purim-date (purim g-year)))
    (if ; Purim is on Sunday
        (= (day-of-week-from-fixed purim-date) sunday)
        ;; Then prior Thursday
        (- purim-date 3)
      ;; Else previous day
      (1- purim-date))))

(defun tishah-be-av (g-year)
  ;; TYPE gregorian-year -> fixed-date
  ;; Fixed date of Tishah be-Av occurring in
  ;; Gregorian year $g-year$.
  (let* ((hebrew-year
          (- g-year
             (gregorian-year-from-fixed hebrew-epoch)))
         (av9
          (fixed-from-hebrew
           (hebrew-date hebrew-year av 9))))
    (if ; Ninth of Av is Saturday
        (= (day-of-week-from-fixed av9) saturday)
        ;; Then the next day
        (1+ av9)
      av9)))

(defun birkath-ha-hama (g-year)
  ;; TYPE gregorian-year -> list-of-fixed-dates
  ;; List of fixed date of Birkath ha-Hama occurring in
  ;; Gregorian year $g-year$, if it occurs.
  (let* ((dates (coptic-in-gregorian 7 30 g-year)))
    (if (and (not (equal dates nil))
             (= (mod (standard-year
                      (coptic-from-fixed (first dates)))
                     28)
                17))
        dates
      nil)))

(defun sh-ela (g-year)
  ;; TYPE gregorian-year -> list-of-fixed-dates
  ;; List of fixed dates of Sh'ela occurring in
  ;; Gregorian year $g-year$.
  (coptic-in-gregorian 3 26 g-year))

(defun yom-ha-zikkaron (g-year)
  ;; TYPE gregorian-year -> fixed-date
  ;; Fixed date of Yom ha-Zikkaron occurring in Gregorian
  ;; year $g-year$.
  (let* ((hebrew-year
          (- g-year
             (gregorian-year-from-fixed hebrew-epoch)))
         (iyyar4; Ordinarily Iyyar 4
          (fixed-from-hebrew
           (hebrew-date hebrew-year iyyar 4))))
    (cond ((member (day-of-week-from-fixed iyyar4)
                   (list thursday friday))
          ;; If Iyyar 4 is Friday or Saturday, then Wednesday
           (kday-before wednesday iyyar4))
          ;; If it's on Sunday, then Monday
          ((= sunday (day-of-week-from-fixed iyyar4))
           (1+ iyyar4))
          (t iyyar4))))

(defun hebrew-birthday (birthdate h-year)
  ;; TYPE (hebrew-date hebrew-year) -> fixed-date
  ;; Fixed date of the anniversary of Hebrew $birthdate$
  ;; occurring in Hebrew $h-year$.
  (let* ((birth-day (standard-day birthdate))
         (birth-month (standard-month birthdate))
         (birth-year (standard-year birthdate)))
    (if ; It's Adar in a normal Hebrew year or Adar II
        ; in a Hebrew leap year,
        (= birth-month (last-month-of-hebrew-year birth-year))
        ;; Then use the same day in last month of Hebrew year.
      (fixed-from-hebrew
       (hebrew-date h-year (last-month-of-hebrew-year h-year)
                    birth-day))
      ;; Else use the normal anniversary of the birth date,
      ;; or the corresponding day in years without that date
      (+ (fixed-from-hebrew
          (hebrew-date h-year birth-month 1))
         birth-day -1))))

(defun hebrew-birthday-in-gregorian (birthdate g-year)
  ;; TYPE (hebrew-date gregorian-year)
  ;; TYPE -> list-of-fixed-dates
  ;; List of the fixed dates of Hebrew $birthday$
  ;; that occur in Gregorian $g-year$.
  (let* ((jan1 (gregorian-new-year g-year))
         (y (standard-year (hebrew-from-fixed jan1)))
         ;; The possible occurrences in one year are
         (date1 (hebrew-birthday birthdate y))
         (date2 (hebrew-birthday birthdate (1+ y))))
    ;; Combine in one list those that occur in current year.
    (list-range (list date1 date2) 
                (gregorian-year-range g-year))))

(defun yahrzeit (death-date h-year)
  ;; TYPE (hebrew-date hebrew-year) -> fixed-date
  ;; Fixed date of the anniversary of Hebrew $death-date$
  ;; occurring in Hebrew $h-year$.
  (let* ((death-day (standard-day death-date))
         (death-month (standard-month death-date))
         (death-year (standard-year death-date)))
    (cond
     ;; If it's Marheshvan 30 it depends on the first
     ;; anniversary; if that was not Marheshvan 30, use
     ;; the day before Kislev 1.
     ((and (= death-month marheshvan)
           (= death-day 30)
           (not (long-marheshvan? (1+ death-year))))
      (1- (fixed-from-hebrew
           (hebrew-date h-year kislev 1))))
     ;; If it's Kislev 30 it depends on the first
     ;; anniversary; if that was not Kislev 30, use
     ;; the day before Tevet 1.
     ((and (= death-month kislev)
           (= death-day 30)
           (short-kislev? (1+ death-year)))
      (1- (fixed-from-hebrew
           (hebrew-date h-year tevet 1))))
     ;; If it's Adar II, use the same day in last
     ;; month of Hebrew year (Adar or Adar II).
     ((= death-month adarii)
      (fixed-from-hebrew
       (hebrew-date
             h-year (last-month-of-hebrew-year h-year)
             death-day)))
     ;; If it's the 30th in Adar I and Hebrew year is not a
     ;; Hebrew leap year (so Adar has only 29 days), use the
     ;; last day in Shevat.
     ((and (= death-day 30)
           (= death-month adar)
           (not (hebrew-leap-year? h-year)))
      (fixed-from-hebrew (hebrew-date h-year shevat 30)))
     ;; In all other cases, use the normal anniversary of
     ;; the date of death.
     (t (+ (fixed-from-hebrew
            (hebrew-date h-year death-month 1))
           death-day -1)))))

(defun yahrzeit-in-gregorian (death-date g-year)
  ;; TYPE (hebrew-date gregorian-year)
  ;; TYPE -> list-of-fixed-dates
  ;; List of the fixed dates of $death-date$ (yahrzeit)
  ;; that occur in Gregorian year $g-year$.
  (let* ((jan1 (gregorian-new-year g-year))
         (y (standard-year (hebrew-from-fixed jan1)))
         ;; The possible occurrences in one year are
         (date1 (yahrzeit death-date y))
         (date2 (yahrzeit death-date (1+ y))))
    ;; Combine in one list those that occur in current year
    (list-range (list date1 date2) 
                (gregorian-year-range g-year))))

(defun shift-days (l cap-Delta)
  ;; TYPE (list-of-weekdays integer) -> list-of-weekdays
  ;; Shift each weekday on list $l$ by $cap-Delta$ days
  (if (equal l nil)
      nil
    (append (list (day-of-week-from-fixed
                   (+ (first l) cap-Delta)))
            (shift-days (rest l) cap-Delta))))

(defun possible-hebrew-days (h-month h-day)
;; TYPE (hebrew-month hebrew-day) -> list-of-weekdays
;; Possible days of week
  (let* ((h-date0 (hebrew-date 5 nisan 1))
         ;; leap year with full pattern
         (h-year (if (> h-month elul) 6 5))
         (h-date (hebrew-date h-year h-month h-day))
         (n (- (fixed-from-hebrew h-date)
               (fixed-from-hebrew h-date0)))
         (tue-thu-sat (list tuesday thursday saturday))
         (sun-wed-fri
          (cond
           ((and (= h-day 30)
                 (member h-month (list marheshvan kislev)))
            nil)
           ((= h-month kislev)
            (list sunday wednesday friday))
           (t (list sunday))))
         (mon (if (member h-month
                          (list kislev tevet shevat adar))
                  (list monday) 
                nil)))
    (shift-days (append tue-thu-sat sun-wed-fri mon) n)))
