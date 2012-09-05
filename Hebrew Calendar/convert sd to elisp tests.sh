#!/bin/bash

set -e
set -x

cat 'sample dates.json' \
    | node 'convert sd to elisp tests.js' \
    > cal-hebrew-test-data.el
