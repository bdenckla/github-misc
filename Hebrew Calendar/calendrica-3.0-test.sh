#!/bin/bash

set -e
set -x

'./convert sd to cl tests.sh'

clisp calendrica-3.0-test.cl
