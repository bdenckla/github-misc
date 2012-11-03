(defun years-to-months (years)
  (floor (- (* 235 years) 234) 19))

(defun months-to-parts (months)
  (mod (+ 12084 (* 13753 months)) 25920))

(defun ymp (years)
  (let*
      ((months (years-to-months years))
       (parts (months-to-parts months)))
    (list years months parts)))

(defun iota (count)
  (loop repeat count for i from 0 collect i))

(defun candidates ()
  (mapcar 'ymp (iota 7000)))

(defun return-the-better (a b)
  (if (< (third a) (third b)) a b))

(defun find-best-test-date ()
  (reduce 'return-the-better (candidates)))

(print (find-best-test-date))
