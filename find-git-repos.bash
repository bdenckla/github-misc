#!/bin/bash

# Example use:
#
#    find $(github-misc/find-git-repos.bash) -name "*.php"
#
#    grep -r intersperse $(github-misc/find-git-repos.bash) --include "*.php"

for f in $(find . -maxdepth 2 -name ".gitignore");
do
    echo $(dirname $f)
done
