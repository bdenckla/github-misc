#!/bin/bash

set -e
set -x

bn=gift-exchange

for f in kids grown-ups; do
    for g in svg png; do
        circo -T$g $bn-$f.gv -o $bn-$f.$g
    done
done
