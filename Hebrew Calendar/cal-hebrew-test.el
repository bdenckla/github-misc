(require 'cal-hebrew)

(defun hebrew-calendar-conversion-test-2 (rd hd)
  (let
      ((fa (calendar-hebrew-from-absolute rd))
       (ta (calendar-hebrew-to-absolute hd)))
    (if (and
         (equal ta rd)
         (equal fa hd))
          'PASS
          'FAIL)))

(defun hebrew-calendar-conversion-test (rdhd)
  (apply 'hebrew-calendar-conversion-test-2 rdhd))

(defun hebrew-calendar-conversion-test-data ()
  (list (list -214193 (list 5 11 3174))
        (list -61387 (list 9 25 3593))
        (list 25469 (list 7 3 3831))))

(defun hebrew-calendar-conversion-test-results ()
  (mapcar 'hebrew-calendar-conversion-test
          (hebrew-calendar-conversion-test-data)))

(print (hebrew-calendar-conversion-test-results))
