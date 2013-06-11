;(require 'cal-hebrew)

(load "./calendrica-3.0.lisp")
(load "./calendrica-test-data.lisp")

(defun header ()
  (format t
          "~S ~S ~S ~S~%"
          "actual Hebrew year-month-day"
          "expected Hebrew year-month-day"
          "actual Rata Die"
          "expected Rata Die" ))

(defun hebrew-calendar-conversion-test-2 (rd ymd)
  (let
      ((aymd (CC3::hebrew-from-fixed rd))
       (ard (CC3::fixed-from-hebrew ymd)))
    (format t "~S ~S ~S ~S~%" aymd ymd ard rd)))

(defun hebrew-calendar-conversion-test (rd-and-ymd)
  (apply 'hebrew-calendar-conversion-test-2 rd-and-ymd))

(defun hebrew-calendar-conversion-test-results-3 ()
  (mapcar 'hebrew-calendar-conversion-test
          (hebrew-calendar-conversion-test-data)))

(header)
(hebrew-calendar-conversion-test-results-3)
