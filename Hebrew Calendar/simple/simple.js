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

var a1 = "We will work our way up to the Jewish calendar in 3 stages.";

var a2 = "In the first stage, we will discuss lunisolar calendars in general.";

var a3 = "In the second stage, we will present the specifics of a simple lunisolar calendar."

var a4 = "We will call this calendar the SLC."

var a6 = "In the third and final stage, we will present the Jewish calendar as a set of complications added to the SLC.";

var work_our_way_up = se( a1, a2, a3, a4, a6 );

// TODO: verify that my generalizations about lunisolar calendars are
// correct.

var b1 = "Lunisolar calendars try to reconcile"
    +" three disparate cycles: the solar day, the synodic month, and the"
    +" tropical year.";


var b2 = "These cycles reflect the following underlying behaviors: the"
    +" rotation of the earth about its axis, the orbit of the moon around"
    +" the earth, and the orbit of the earth around the sun.";

// TODO: Find out whether, to include all relevant behaviors, we need
// to say something like "orbit (including precession!) of the earth"
// instead of just "orbit of the earth"?

var b3 = "There are other cycles reflecting the same underlying behaviors,"
    +" e.g. the sidereal month and sidereal year.";

var b4 = "But, without getting into details, suffice it to say that the solar"
    +" day, the synodic month, and the tropical year are defined in a way"
    +" that makes them the cycles most relevant to life on earth.";

var b5 = "So, it is these cycles that lunisolar calendars try to"
    +" reconcile.";

var reconcile_cycles = se( b1, b2, b3, b4, b5 );

var c1 = 'But what do we mean when we say that lunisolar calendars'
    +' "try to reconcile" these cycles?';

var c2 = "We mean that they choose New Year's Days in a way that"
    +" comes close to achieving the following two goals.";

function c2a1s( a, c )
{
    return "The " + c + a
        + " goal is to have New Year's Days fall";
}

function c2a1( a )
{
    return c2a1s( a, "first" ) + " on (and only on!)"
        +" each of the times that"
        +" a certain phase of the"
        +" tropical year"
        +" is reached.";
}

function c2b1( a )
{
    return c2a1s( a, "second") +" only on"
        +" a certain phase of the"
        +" synodic month.";
}

var c2a = c2a1( "" );

var c2b = c2b1( "" );

var c3a = "For the Jewish calendar,"
    +" the desired phase of the tropical year"
    +" is roughly the autumnal equinox"
    +" and"
    +" the desired phase of the synodic month"
    +" is the new moon.";

// TODO: Find out whether we need to constrain our claim about the
// aut. eq. to something like "nowadays" or "in modern times".
// I.e. find out whether mean RhSh was significantly distant from the
// aut. eq. 1000 or so years ago when the calendar stabilized/was
// standardized.

var c4b = "Now, what do we mean when we say that a day \"falls on\" a point in time?";

var c4c = "We mean that some point of time identified with that day,"
    +" e.g. its noon, falls there.";

var c4d = "(Later we shall see that for the purposes of choosing"
    +" Jewish New Year's Days, the point of time identified with"
    +" a day is the noon of the day before!)";

// var c5 = "It does this by varying the length of the calendar year.";

var lunisolar_goals = se( c1, c2, c2a, c2b, c3a, c4b, c4c, c4d );

var b6 = "Really what the SLC tries to reconcile are approximations of the"
    +" mean lengths of these cycles, not the cycles themselves."

var b7 = "I.e. the SLC is an arithmetic calendar.";

var b8 = "The values of its constants are motivated by a desire"
    +" to match observable cycles.";

var b9 = "But the SLC is an algorithm divorced from observation.";

var slc_is_arithmetic = se( b6, b7, b8, b9 );

var b10 = "Let m and y be the SLC's approximations for the synodic month and tropical year.";

var b11 = "Using only an integral number of days per calendar year, the SLC's goals are to make calendar year n start on ny and on km for some integer k.";

var b12 = "Let's see how these specific goals of the SLC match up to the generic goals that all lunisolar calendars try to meet."

var b13 = c2a1( " generic" );

var b14 = "This matches up to the SLC's specific goal that the calendar year start on ny."

var b15 = c2b1( " generic" );

var b16 = "This matches up to the SLC's specific goal that the calendar year start on km for some integer k."

var slc_goals = se( b10, b11, b12, b13, b14, b15, b16 );


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

var k2 = "For convenience, the SLC uses a derived constant";

var constant_y = math("y");

var k3 = "which is equal to " + constant_m+"a" + " and as a result has units \"days per tropical year\". In decimal, it is about 365.2468.";

var klmn2 = sp( co( k2, constant_y, k3 ) );

var constant_values = se( e, fghi, klmn, klmn2 );


/*
  But first let's choose, for any year n, what "New Year's Month"
  should be, in terms of whole months elapsed since the SLC
  origin.

  Here we use "month" to mean m.

  We will abbreviate "SLC New Year's Month for year n" to σ(n).

  In other words σ(n) yields a k such that km is near ny.

  In particular we will choose the k such that km is closest to ny
  without going over.

  Mathematically this means, σ(n) = floor(ny/m), which reduces to
  floor(na).

  Now let's choose, for any year n, what New Year's Day should be, in
  terms of whole days elapsed since the SLC origin.

  We will abbreviate "SLC New Year's Day for year n" to s(n).

  Let's make s(n) be the day that is closest to mσ(n) without going over.

  Mathematically, we can express this as s(n) = floor(mσ(n)).

  Or, "inlining" σ(n), s(n) = floor(m*floor(na)).

  So, how close is s(n) to ny, and how close is it to km for some integer k?

  It is within (-1,0] of km for some k.

  I.e. it is less than a day before and not after.

  It is within (-m-1,0] of ny.

  I.e. it is less than m+1 before and not after.

*/


var outstr =
    pa( work_our_way_up,
        reconcile_cycles,
        lunisolar_goals,
        slc_is_arithmetic,
        slc_goals,
        constant_values );

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


//  LocalWords:  TODO synodic SLC's ny na inlining
