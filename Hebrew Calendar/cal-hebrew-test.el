;(require 'cal-hebrew)

(load-file "./cal-hebrew.el")
(load-file "./cal-hebrew-test-data.el")

(defun hebrew-calendar-conversion-test-3 (amdy emdy ard erd)
  (let
      ((eq-rd (equal ard erd))
       (eq-mdy (equal amdy emdy)))
    (if (and eq-rd eq-mdy)
          nil
          (list (list amdy emdy ard erd)))))

(defun header ()
  (list "actual Hebrew month-day-year"
        "expected Hebrew month-day-year"
        "actual Rata Die"
        "expected Rata Die" ))

(defun hebrew-calendar-conversion-test-2 (rd mdy)
  (let
      ((amdy (calendar-hebrew-from-absolute rd))
       (ard (calendar-hebrew-to-absolute mdy)))
    (hebrew-calendar-conversion-test-3 amdy mdy ard rd)))

(defun hebrew-calendar-conversion-test (rd-and-mdy)
  (apply 'hebrew-calendar-conversion-test-2 rd-and-mdy))

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
