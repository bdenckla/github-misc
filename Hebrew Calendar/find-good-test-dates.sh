#!/bin/bash

set -e
set -x

which clisp || \
    sudo apt-get -y install clisp

clisp find-good-test-dates.lisp
