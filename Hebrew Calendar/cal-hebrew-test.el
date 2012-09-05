;(require 'cal-hebrew)

(load-file "./cal-hebrew.el")
(load-file "./cal-hebrew-test-data.el")

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

(defun hebrew-calendar-conversion-test-results ()
  (mapcar 'hebrew-calendar-conversion-test
          (hebrew-calendar-conversion-test-data)))

(print (hebrew-calendar-conversion-test-results))
