"use strict";

var ppd = 25920; // parts per day

var wd_per_m = 29; // whole days per m

var pbwd_per_m = 13753; // parts beyond whole days per m

var parts_per_m = wd_per_m * ppd + pbwd_per_m;

var parts_per_leap = 13 * parts_per_m;

var parts_per_non_leap = 12 * parts_per_m;

var leaps_per_cycle = 7;

var non_leaps_per_cycle = 12;

var years_per_cycle = leaps_per_cycle + non_leaps_per_cycle;

var parts_per_cycle =
    parts_per_leap     * leaps_per_cycle
    +
    parts_per_non_leap * non_leaps_per_cycle;

/////////////////////////////////////////

var a = "We will begin with a simple lunisolar calendar";

var b = "We will then complicate this calendar to make it Jewish";

var c = "The day is our basic unit of time";

var d = "For our purposes, we do not need to define what a day is";

var e = "We will use 3 constants";

var f = "The first";

var constant_m = math("m");

var h = "is";

var i = "29 13753/29520 days (about 29.531 days)";

var hi = sp( h, i );

var fghi = co( f, constant_m, hi );

var j = "It is an estimate of a synodic month";

var k = "The second constant";

var constant_y0 = math("a");

var m = "is";

var n =
    "235/19"
    + " (about 12.37)";

var klmn = sp( co( k, constant_y0, m ), n );

var k1 = "It is an estimate of a year, expressed in units of "+constant_m+", though left unitless."

var k2 = "The third constant";

var constant_y = math("y");

var k3 = "is closely related. It is the same estimate of a year, but expressed in days:"

var k4 = "a" + constant_m + " (about 365.2468 days)";

var klmn2 = sp( co( k2, constant_y, k3 ), k4 );

var o = "It is an estimate of a year"

var p = "Our simple lunisolar calendar uses the following system to make its years have mean length";

var pq = sp( p, constant_y );

var r = "Divide time into chunks of 19 years. For each chunk, make 7 years have mean length 13" + constant_m + " and make the remaining 12 years have mean length 12"+ constant_m;

var s = "This will make the year have mean length ((7 * 13 + 12 * 12)/19)"+constant_m+", which is (235/19)"+constant_m;

/*
  The SLC's job is to say what multiple of d begins any year. I.e. it
  says, for any year n, where New Year's Day falls, in terms of whole
  days elapsed since the SLC origin. We will abbreviate "SLC New
  Year's Day for year n" to s(n).

  We want s(n) to fall near ny <emph>and</emph> near a multiple of
  m. This additional criteria is what makes the SLC a lunisolar
  calendar rather than just a solar calendar.

  But, how near is near? The answer is as follows.  We want s(n) to
  fall on ny or less than m after it. Additionally, we want s(n) to
  fall on a multiple of m or less than a day after it.

  Let's split the SLC's job in two to see how it does all this.

  The first half of the SLC's job is to say what multiple of m begins
  any year. I.e. it says, for any year n, where "New Year's Month"
  falls, in terms of whole months elapsed since the SLC origin. We
  will abbreviate "SLC New Year's Month for year n" to σ(n). Though
  the word "month" has many different meanings, here we use it as a
  synonym for m.

  The value of σ(n) is the multiple of m that falls on ny or is less
  than m after it. In mathematical notation, this has the succint
  description floor( ny/m ), which reduces to floor( an ).

  The second half of the SLC's job is to say what multiple of d begins
  the month that begins any year. This is floor( mσ(n) ).

  Putting the two together, s(n) = floor( m*floor( an )

*/


var outstr =
    se( a, b, c, d, e, fghi, j, klmn, klmn2, o, pq, r, s );

function math( s )
{
    return s;
}

function se() { return sentence_join( Array.prototype.slice.call( arguments ) ); }

function sentence_join( a )
{
    return a.join( ".\n\n" ) + ".";
}

function co() { return comma_join( Array.prototype.slice.call( arguments ) ); }

function comma_join( a )
{
    return a.join( ", " );
}

function sp() { return space_join( Array.prototype.slice.call( arguments ) ); }

function space_join( a )
{
    return a.join( " " );
}

process.stdout.write( outstr );

