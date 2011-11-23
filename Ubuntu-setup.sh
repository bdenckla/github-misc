set -e

sudo apt-get -y install \
    git \
    emacs \
    php-elisp \
    #

# Download id_rsa

[ -d ~/.ssh ] || \
   ( mkdir ~/.ssh && chmod go-rwx ~/.ssh )

[ -f ~/.ssh/id_rsa ] || \
    ( mv ~/Downloads/id_rsa ~/.ssh && chmod go-rwx ~/.ssh/id_rsa )

[ -d ~/github-misc ] || \
    git clone git@github.com:bdenckla/github-misc.git ~/github-misc

for f in gitconfig emacs; do
    ln -s ~/github-misc/dot-$f ~/.$f;
done

ls -l \
    ~/.ssh \
    ~/github-misc \
    ~/.emacs \
    ~/.gitconfig \
    #
