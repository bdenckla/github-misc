#!/bin/bash

set -e
set -x

./generic-clisp-test.sh

clisp calendrica-3.0-test.lisp
