;(require 'cal-hebrew)

(load-file "./cal-hebrew.el")
(load-file "./cal-hebrew-test-data.el")

(defun hebrew-calendar-conversion-test-3 (aymd eymd ard erd)
  (let
      ((eq-rd (equal ard erd))
       (eq-ymd (equal aymd eymd)))
    (if (and eq-rd eq-ymd)
          nil
          (list (list aymd eymd ard erd)))))

(defun header ()
  (list "actual Hebrew year-month-day"
        "expected Hebrew year-month-day"
        "actual Rata Die"
        "expected Rata Die" ))

(defun hebrew-calendar-conversion-test-2 (rd ymd)
  (let
      ((aymd (calendar-hebrew-from-absolute rd))
       (ard (calendar-hebrew-to-absolute ymd)))
    (hebrew-calendar-conversion-test-3 aymd ymd ard rd)))

(defun hebrew-calendar-conversion-test (rd-and-ymd)
  (apply 'hebrew-calendar-conversion-test-2 rd-and-ymd))

(defun hebrew-calendar-conversion-test-results-3 ()
  (mapcar 'hebrew-calendar-conversion-test
          (hebrew-calendar-conversion-test-data)))

(defun hebrew-calendar-conversion-test-results-2 ()
  (apply 'append (hebrew-calendar-conversion-test-results-3)))

(defun hebrew-calendar-conversion-test-results ()
  (list
   (list 'header (header))
   (list 'data (hebrew-calendar-conversion-test-results-2))))

(print (hebrew-calendar-conversion-test-results))