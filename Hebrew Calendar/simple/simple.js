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

var a = "We will begin with a simple lunisolar calendar.";

var ba = "We will then complicate this calendar to make it Jewish.";

var bb = "We will call our simple lunisolar calendar the SLC.";

var b1 = "Like any lunisolar calendar, the SLC will try to reconcile"
    +" three disparate cycles: the solar day, the synodic month, and the"
    +" tropical year.";


var b2 = "These cycles reflect the following underlying behaviors: the"
    +" rotation of the earth about its axis, the orbit of the moon around"
    +" the earth, and the orbit of the earth around the sun.";

var b3 = "There are other cycles reflecting the same underlying behaviors,"
    +" e.g. the sidereal month and sidereal year.";

var b4 = "But, without getting into details, suffice it to say that the solar"
    +" day, the synodic month, and the tropical year are defined in a way"
    +" that makes them the cycles most relevant to life on earth.";

var b5 = "So, it is these cycles that a lunisolar calendar tries to"
    +" reconcile.";

var c1 = 'But what do we mean by "tries to reconcile"?';

var c2 = "We mean that it tries to have each New Year's Day fall near the same seasonal moment and near the same phase of the moon.";

var c3a = "For the Jewish calendar, the seasonal moment is roughly the"
    +" autumnal equinox and the phase of the moon is the new moon.";

var c4 = "In other words, using only an integral number of days per calendar year, a lunisolar calendar tries to make each calendar year start in the same phase of the tropical year and the same phase of the synodic month.";

var c5 = "It does this, of course, by varying the length of the calendar year.";

var b6 = "Really what the SLC attempts to reconcile are approximations of the"
    +" mean lengths of these cycles.";

var b7 = "I.e. the SLC is an arithmetic calendar.";

var b8 = "The values of its constants are motivated, of course, by a desire"
    +" to match observable cycles.";

var b9 = "But the SLC is an algorithm divorced from observation.";

var b10 = "The SLC only needs to approximate 2 of the 3 cycles since it is"
    +" only concerned with the ratios between the cycles.";

var b11 = "The SLC approximates the synodic month and tropical year, leaving"
    +" the solar day as the unit of time.";

var b12 = "In other words, the day is undefined and the approximations of the"
    +" synodic month and tropical year are defined with respect to the day.";

var b13 = "This is a sensible division, since the day is the shortest cycle,"
    +" but everything would work equally well with either of the other"
    +" choices of unit.";

var b14 = "Though the day is the unit of time, we shall see that it is not the"
    +" smallest amount of time used; very small fractions of a day are"
    +" used.";

var bs1 = se( b1, b2, b3, b4, b5 );

var cs = se( c1, c2, c3a, c4 );

var bs2 = se( b6, b7, b8, b9 );

var bs3 = se( b10, b11, b12, b13, b14 );


var e = "The approximations of the synodic month and tropical year are as follows.";

var f = "The first constant";

var constant_m = math("m");

var h = "is";

var i = "29 13753/29520 and has units \"days per synodic month\". In decimal, it is about 29.531.";

var hi = sp( h, i );

var fghi = co( f, constant_m, hi );

var k = "The second constant";

var constant_y0 = math("a");

var m = "is";

var n =
    "235/19 and has units \"synodic months per tropical year\"."
    + " In decimal, it is about 12.37.";

var klmn = sp( co( k, constant_y0, m ), n );

var k2 = "For convenience the SLC uses a derived constant";

var constant_y = math("y");

var k3 = "which is equal to "+"a" + constant_m + " and as a result has units \"days per tropical year\". In decimal, it is about 365.2468.";

var klmn2 = sp( co( k2, constant_y, k3 ) );


/*
  The SLC will tell us, for any year n, where New Year's Day falls, in
  terms of whole days elapsed since the SLC origin. We will abbreviate
  "SLC New Year's Day for year n" to s(n).

  We want s(n) to be near ny and be near a multiple of m. These two
  criteria make the SLC a lunisolar calendar.

  But, how near is near? The answer is as follows.  We want s(n) to
  fall on or less than m after ny. (s(n) - ny is in [0,m).)
  Additionally, we want s(n) to fall on or less than a day after a
  multiple of m. (s(n) - km is in [0,1) for some integer k.)

  How can we define s(n) such that it does all this?

  First let's figure out, for any year n, where "New Year's Month"
  falls, in terms of whole months elapsed since the SLC origin. We
  will abbreviate "SLC New Year's Month for year n" to σ(n). Though
  the word "month" has many different meanings, here we use it as a
  synonym for m.

  The value of σ(n) is the multiple of m that falls on or less
  than m after ny. I.e. σ(n) = k where km - ny is in [0,m). In closed
  form, σ(n) = floor(ny/m), which reduces to floor(na).

  The value of s(n) is then floor(mσ(n)).

  Or, "inlining" σ(n), s(n) = floor(m*floor( an )).

*/


var outstr =
    pa( se( a, ba, bb ), bs1, cs, bs2, bs3, se( e, fghi, klmn, klmn2 ) );

function math( s )
{
    return s;
}

function pa() { return paragraph_join( Array.prototype.slice.call( arguments ) ); }

function paragraph_join( a )
{
    return a.join( "\n\n---\n\n" );
}

function se() { return sentence_join( Array.prototype.slice.call( arguments ) ); }

function sentence_join( a )
{
    return a.join( "\n\n" );
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

