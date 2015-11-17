#!/bin/bash

set -e
set -x

for f in kids grown-ups; do
    circo -Tsvg gift-exchange-$f.gv -o gift-exchange-$f.svg
    circo -Tpng gift-exchange-$f.gv -o gift-exchange-$f.png
done
