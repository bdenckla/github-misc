#!/bin/bash

set -e
set -x

which clisp || \
    sudo apt-get -y install clisp

cat 'sample dates.json' \
    | node 'calendrica-generate-test-data.js' \
    > calendrica-test-data.cl

clisp calendrica-tw-test.lisp
