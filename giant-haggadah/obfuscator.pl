#!/usr/bin/perl

use warnings;
use strict;

while (<>)
{
    tr/a-z/x/;
    tr/A-Z/X/;
    tr/0-9/n/;
    print;
}
