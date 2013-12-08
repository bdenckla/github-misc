for i in `cat $1`;do echo "$i"; echo "$i" | xxd -g 1; done
