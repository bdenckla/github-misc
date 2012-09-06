#!/bin/bash

set -e
set -x

cat 'sample dates.json' \
    | node 'convert sd to cl tests.js' \
    > calendrica-3.0-test-data.cl
