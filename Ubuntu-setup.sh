#!/bin/bash

# This script assumes that the id_rsa file is already in ~/Downloads.

# Debian:
#     add self to sudo group, then graphically log out & back in
#     I added myself to sudo group with the graphical admin tool.
#     I don't know how to do that from the command line.
# Debian as VirtualBox guest:
#     mount VirtualBox Additions
#     sudo apt-get remove virtualbox-ose-guest*
#     sudo sh /media/cdrom0/VBoxLinuxAdditions.run
# All:
#    Download id_rsa from Google Docs into Downloads.
#    Download this file by downloading github-misc from github as a tarball.

# Notes on Brother MFC-8660DN scanner install:
# After install on 64-bit, do
#
#     cd /usr/lib64
#     sudo cp libbrcolm2.so* libbrscandec2.so* /usr/lib
#     sudo cp sane/libsane-brother2.so* /usr/lib/sane
#
# also do the thing where you edit some udev file to allow non-superuser use of the printer

set -x

sudo apt-get -y install \
    git \
    git-gui \
    gitk \
    emacs \
    php-elisp \
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
