#!/bin/bash

set -e
set -x

cat 'sample dates.json' \
    | nodejs 'cal-hebrew-generate-test-data.js' \
    > cal-hebrew-test-data.el

emacs --no-site-file --script cal-hebrew-test.el
