#!/bin/bash

for f in $(find . -maxdepth 2 -name ".gitignore");
do
    echo $(dirname $f)
done
