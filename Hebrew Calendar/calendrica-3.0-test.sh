#!/bin/bash

set -e
set -x

which clisp || \
    sudo apt-get -y install clisp

cat 'sample dates.json' \
    | node 'calendrica-3.0-generate-test-data.js' \
    > calendrica-3.0-test-data.cl

clisp calendrica-3.0-test.cl
