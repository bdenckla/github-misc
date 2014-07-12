#!/bin/bash

set -e
#set -x

X=input.txt
Y1=output-of-1st-pass.txt
Y2=output-of-2nd-pass.txt

uconv -V
echo -n שָּׁ  > $X
uconv -x nfc $X > $Y1
uconv -x nfc $Y1 > $Y2
cat $X $Y1 $Y2 && echo
