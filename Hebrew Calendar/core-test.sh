#!/bin/bash

set -e
set -x

./generic-clisp-test.sh

clisp core-test.lisp
