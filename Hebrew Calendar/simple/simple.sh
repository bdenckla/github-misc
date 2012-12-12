#!/bin/bash

set -e
set -x

which nodejs || \
    sudo apt-get -y install nodejs

nodejs 'simple.js'
