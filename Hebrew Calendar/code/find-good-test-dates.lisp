(defun years-to-months (years)
  (floor (- (* 235 years) 234) 19))

(defun months-to-parts (months)
  (mod (+ 12084 (* 13753 months)) 25920))

(defun years-to-parts (years)
  (months-to-parts (years-to-months years)))

(defun years-to-yp (years)
  (list "years:" years
        "parts:" (years-to-parts years)))

; extracts the "parts" member of a yp (year/parts pair)
(defun yp-p (yp)
  (fourth yp))

(defun iota (count)
  (loop repeat count for i from 0 collect i))

(defun candidate-years ()
  (iota 7000))

(defun candidate-yps ()
  (mapcar 'years-to-yp (candidate-years)))

(defun return-the-better-yp (cmp)
  (lambda (a b) ; a and b have type yp
    (if (funcall cmp (yp-p a) (yp-p b)) a b)))

(defun best-yp (cmp)
  (reduce (return-the-better-yp cmp) (candidate-yps)))

(defun best-two-yps ()
  (list (best-yp '<)
        (best-yp '>)))

; psd: parts short of a day
(defun add-psd (yp)
  (append yp (list "psd:" (- 25920 (yp-p yp)))))

(defun best-two-yps-with-psd ()
  (mapcar 'add-psd (best-two-yps)))

(print (best-two-yps-with-psd))
