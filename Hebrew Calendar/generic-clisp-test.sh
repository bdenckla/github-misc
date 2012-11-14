#!/bin/bash

set -e
set -x

which clisp || \
    sudo apt-get -y install clisp

which nodejs || \
    sudo apt-get -y install nodejs

cat 'sample dates.json' \
    | nodejs 'calendrica-generate-test-data.js' \
    > calendrica-test-data.lisp

