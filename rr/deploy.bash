#!/bin/bash

# Example use: ./deploy.bash

set -o pipefail
set -e
set -x

deployment_subdir=rr
src=~/github-misc/rr

deployment_branch=gh-pages
deployment_repo=~/efs

deployment_dir=$deployment_repo/$deployment_subdir

cd $deployment_repo

git checkout $deployment_branch

git pull origin

mkdir -p "$deployment_dir"

mv "$deployment_dir" /tmp/deploy.bash.$RANDOM

mkdir "$deployment_dir"

cp --dereference \
   $src/*.xhtml \
   $src/*.css \
   $src/*.js \
   "$deployment_dir"/$sub

cd "$deployment_dir"

timestamp=$(date --rfc-3339=seconds)

git add \*

git status

git commit -m "deployment $src $timestamp"

git push origin $deployment_branch

git status
