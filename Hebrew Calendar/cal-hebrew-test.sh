#!/bin/bash

set -e
set -x

'./convert sd to elisp tests.sh'

emacs --no-site-file --script cal-hebrew-test.el
