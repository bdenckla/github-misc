#!/bin/bash

set -e
set -x

./generic-clisp-test.sh

clisp calendrica-tw-test.lisp
