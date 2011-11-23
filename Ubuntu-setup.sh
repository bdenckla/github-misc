sudo apt-get -y install \
    git \
    emacs \
    php-elisp \
    #

cd ~ && git clone git@github.com:bdenckla/github-misc.git

for f in gitconfig emacs; do
    ln -s ~/github-misc/dot-$f ~/.$f;
done
