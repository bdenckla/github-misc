(defun years-to-months (years)
  (floor (- (* 235 years) 234) 19))

(defun months-to-parts (months)
  (mod (+ 12084 (* 13753 months)) 25920))

(defun ymp (years)
  (let*
      ((months (years-to-months years))
       (parts (months-to-parts months)))
    (list years months parts (- parts 25920))))

; extracts the "parts" member of a ymp (year-month-parts)
(defun ymp-p (ymp)
  (third ymp))

(defun iota (count)
  (loop repeat count for i from 0 collect i))

(defun candidates ()
  (mapcar 'ymp (iota 7000)))

(defun return-the-better (cmp)
  (lambda (a b)
    (if (funcall cmp (ymp-p a) (ymp-p b)) a b)))

(defun find-best-test-date (cmp)
  (reduce (return-the-better cmp) (candidates)))

(defun find-best-test-dates ()
  (list (find-best-test-date '<)
        (find-best-test-date '>)))

(print (find-best-test-dates))
