main : \
 calendrica-3.0-test \
 calendrica-tw-test \
 core-test \

clisp : /usr/bin/clisp
nodejs : /usr/bin/clisp

/usr/bin/clisp  : ; sudo apt-get -y install $@
/usr/bin/nodejs : ; sudo apt-get -y install $@

calendrica-test-data.lisp : \
 nodejs \
 sample-dates.json \
 calendrica-generate-test-data.js \
 ; \
 cat sample-dates.json \
 | nodejs calendrica-generate-test-data.js \
 > $@.tmp \
 ; \
 mv $@.tmp $@

calendrica-3.0-test.novc.actual-out : \
 clisp \
 calendrica-3.0-test.lisp \
 calendrica-test-data.lisp \
 ; \
 clisp calendrica-3.0-test.lisp > $@.tmp \
 ; \
 mv $@.tmp $@

calendrica-3.0-test : \
 calendrica-3.0-test.novc.actual-out \
 calendrica-test-data.expected-out \
 ; \
 diff calendrica-3.0-test.novc.actual-out \
      calendrica-test-data.expected-out

calendrica-tw-test.novc.actual-out : \
 clisp \
 calendrica-tw-test.lisp \
 calendrica-test-data.lisp \
 ; \
 clisp calendrica-tw-test.lisp > $@.tmp \
 ; \
 mv $@.tmp $@

calendrica-tw-test : \
 calendrica-tw-test.novc.actual-out \
 calendrica-test-data.expected-out \
 ; \
 diff calendrica-tw-test.novc.actual-out \
      calendrica-test-data.expected-out

core-test.novc.actual-out : \
 clisp \
 core-test.lisp \
 calendrica-test-data.lisp \
 ; \
 clisp core-test.lisp > $@.tmp \
 ; \
 mv $@.tmp $@

core-test : \
 core-test.novc.actual-out \
 calendrica-test-data.expected-out \
 ; \
 diff core-test.novc.actual-out \
      calendrica-test-data.expected-out
