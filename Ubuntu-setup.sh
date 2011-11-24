#!/bin/bash

# This script assumes that the id_rsa file has already been downloaded
# to ~/Downloads.

set -x

sudo apt-get -y install \
    git \
    emacs \
    php-elisp \
    git-el \
    || \
    exit

[ -d ~/.ssh ] || \
   ( mkdir ~/.ssh && chmod go-rwx ~/.ssh ) \
    || \
    exit

[ -f ~/.ssh/id_rsa ] || \
    ( mv ~/Downloads/id_rsa ~/.ssh && chmod go-rwx ~/.ssh/id_rsa ) \
    || \
    exit

[ -d ~/github-misc ] || \
    git clone git@github.com:bdenckla/github-misc.git ~/github-misc \
    || \
    exit

for f in gitconfig emacs; do
    [ -h ~/.$f ] \
    || \
    ln -s ~/github-misc/dot-$f ~/.$f \
    || \
    exit
done

which \
    git \
    emacs \
    || \
    exit

ls -l \
    ~/.ssh \
    ~/github-misc \
    ~/.emacs \
    ~/.gitconfig \
    || \
    exit

echo $0 ending with success.
