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

// ****************************************

function work_our_way_up()
{
    var a1 = "We will work our way up to the Jewish calendar in 3 stages.";

    var a2 = "In the first stage,"
        +" we will discuss the general goals of the Jewish calendar.";

    var a3 = "In the second stage,"
        +" we will present a simple lunisolar calendar"
        +" that pursues the same general goals as the Jewish calendar.";

    var a4 = "We will call this calendar the SLC."

    var a6 = "In the third and final stage,"
        +" we will present the Jewish calendar"
        +" as a set of complications added to the SLC.";

    return se( a1, a2, a3, a4, a6 );
}

// TODO: Acknowledge Dershowitz & Reingold.

// TODO: explain why we don't bother with months

function reconcile_cycles()
{

    var b1 = "The general goal of the Jewish calendar is to try to reconcile"
        +" three disparate cycles: the solar day, the synodic month, and the"
        +" tropical year.";

    var b2 = "These cycles correspond, roughly,"
    +" to the following underlying behaviors:"
    +" the earth spins,"
    +" the moon orbits the earth,"
    +" and the earth is tilted and orbits the sun.";

    // TODO: Find out whether, to include all relevant behaviors, we need
    // to say something like "orbit (including precession!) of the earth"
    // instead of just "orbit of the earth"?

    var b3 = "There are other cycles"
        +" reflecting the same underlying behaviors,"
        +" e.g. the sidereal month and sidereal year.";

    var b4 = "But, without getting into details, suffice it to say that"
        +" the solar day,"
        +" the synodic month, and"
        +" the tropical year"
        +" are defined in a way"
        +" that makes them the cycles most relevant to life on earth.";

    // TODO: sidereal year is used by Hindu calendar

    var b5 = "So, it is these cycles that the Jewish calendar tries"
        +" reconcile.";

    return se( b1, b2, b3, b4, b5 );
}

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

function lunisolar_goals()
{
    var c1 = 'But what do we mean when we say that lunisolar calendars'
        +' "try to reconcile" these cycles?';

    var c2 = "We mean that they choose New Year's Days in a way that"
        +" comes close to achieving the following two goals.";

    var c2a = c2a1( "" );

    var c2b = c2b1( "" );

    var c3a = "For the Jewish calendar,"
        +" the desired phase of the tropical year"
        +" is roughly the autumnal equinox"
        +" and"
        +" the desired phase of the synodic month"
        +" is the new moon.";

    // TODO: Find out whether we need to constrain our claim about the
    // autumnal equinox to something like "nowadays" or "in modern times".
    // I.e. find out whether mean RhSh was significantly distant from the
    // autumnal equinox 1000 or so years ago when the calendar
    // stabilized/was standardized.

    var c4b = "Now, what do we mean when we say that"
        +" a day \"falls on\" a point in time?";

    var c4c = "We mean that some point of time identified with that day,"
        +" e.g. its noon, falls there.";

    var c4d = "(Later we shall see that for the purposes of choosing"
        +" Jewish New Year's Days, the point of time identified with"
        +" a day is the noon of the day before!)";

    // var c5 = "It does this by varying the length of the calendar year.";

    return se( c1, c2, c2a, c2b, c3a, c4b, c4c, c4d );
}

function slc_is_arithmetic()
{

    var b6 = "Really what the SLC tries to reconcile are approximations of the"
        +" mean lengths of these cycles, not the cycles themselves."

    var b7 = "I.e. the SLC is an arithmetic calendar.";

    var b8 = "The values of its constants are motivated by a desire"
        +" to match observable cycles.";

    var b9 = "But the SLC is an algorithm divorced from observation.";

    return se( b6, b7, b8, b9 );
}

function slc_goals()
{
    var b10 = "Let m and y be the SLC's approximations for"
        +" the synodic month and tropical year.";

    var b11 = "Using only an integral number of days per calendar year,"
        +" the SLC's goals are to make calendar year n start on ny"
        +" and on km for some integer k.";

    var b12 = "Let's see how these specific goals of the SLC"
        +" match up to the generic goals that all lunisolar"
        +" calendars try to meet."

    var b13 = c2a1( " generic" );

    var b14 = "This matches up to the SLC's specific goal"
        +" that the calendar year start on ny."

    var b15 = c2b1( " generic" );

    var b16 = "This matches up to the SLC's specific goal"
        +" that the calendar year start on km for some integer k."

    return se( b10, b11, b12, b13, b14, b15, b16 );
}

function constant_values()
{
    var e = "Before showing how the SLC pursues its goals,"
        +" i.e. before showing our implementation,"
        +" we will present the constants used by the SLC.";

    var e2 = "Their exact values are not important to understanding the SLC,"
        +" in the sense that even if they were slightly different,"
        +" the calendar would behave similarly, and our implementation"
        +" would remain the same.";

    var e3 = "But it may help understand the SLC to have concrete numbers in mind.";

    var f = "The first constant";

    var constant_m = math("m");

    var h = "is";

    var i = "29 13753/29520 and has units \"days per synodic month\"."
        +" In decimal, it is about 29.531.";

    var hi = sp( h, i );

    var fghi = co( f, constant_m, hi );

    var k = "The second constant";

    var constant_y0 = math("a");

    var m = "is";

    var n =
        "235/19 and has units \"synodic months per tropical year\"."
        + " In decimal, it is about 12.37.";

    var klmn = sp( co( k, constant_y0, m ), n );

    var k1 = "With "+ math("a") +", we come closest to a case where"
        +" the exact value of a constant gives rise to specific behavior"
        +" of the calendar."

    var k1a = "The denominator's value of 19 gives rise to a 19-year"
        +" cycle of leap and non-leap years."

    var k2 = "For convenience, the SLC uses a derived constant";

    var constant_y = math("y");

    var k3 = "which is equal to " + constant_m+"a" + " and as a result"
        +" has units \"days per tropical year\". In decimal, it is"
        +" about 365.2468.";

    var klmn2 = sp( co( k2, constant_y, k3 ) );

    return se( e, e2, e3, fghi, klmn, k1, k1a, klmn2 );
}

function what_implement_means()
{
    var a = "What will it mean to \"implement\" the SLC?";

    var b = "In general, to implement a calendar is to give a day number for"
        +" each day name.";

    var c = "By \"day number\" we mean \"integer day from some absolute origin,\""
        +" and by \"day name\" we mean \"represention in that calendar.\"";

    var d = "For our purposes, we are only concerned about New Year's Days, though.";

    var e = "So, for our purposes, to implement a calendar is to give a day number"
        +" for each New Year's Day name.";

    var f = "And, for our purposes, we will name (represent) New Year's Days by"
        +" their year only.";

    var g = "E.g. instead of saying for \"New Year's Day 5773\","
        +" \"Rosh ha-Shanah 5773,\", or"
        +" \"Tishri 1, 5773,\""
        +" we simply say \"5773.\"";

    return se( a, b, c, d, e, f, g);
}

function slc_details()
{
    var d1 =
        "To implement the SLC,"
        +" first let's choose, for any year n, what \"New Year's Month\""
        +" should be, in terms of whole months elapsed since the SLC"
        +" origin."

    var d2 = "Here we use \"month\" to mean m.";

    var d3 = "We will abbreviate \"SLC New Year's Month for year n\" to σ(n).";

    var d4 = "In other words σ(n) yields a k such that km is near ny.";

    var d5 = "In particular we will choose the k such that km is closest to ny"
        +" without going over.";

    var d6 = "Mathematically this means, σ(n) = floor(ny/m), which reduces to"
        +" floor(na).";

    var d7 = "Now let's choose, for any year n, what New Year's Day should be, in"
        +" terms of whole days elapsed since the SLC origin.";

    var d8 = "We will abbreviate \"SLC New Year's Day for year n\" to s(n).";

    var d9 = "Let's make s(n) be the day that is closest to mσ(n) without going over.";

    var da = "Mathematically, we can express this as s(n) = floor(mσ(n)).";

    var db = "Or, \"inlining\" σ(n), s(n) = floor(m*floor(na)).";

    return se( d1, d2, d3, d4, d5, d6, d7, d8, d9, da, db );
}

function slc_bounds()
{
    var dc = "So, how close is s(n) to ny, and how close is it to km for some integer k?";

    var dd = "It is within (-1,0] of km for some k.";

    var de = "I.e. it is less than a day before and not after.";

    var df = "It is within (-m-1,0] of ny.";

    var dg = "I.e. it is less than m+1 before and not after.";

    return se( dc, dd, de, df, dg );
}

var outstr =
    pa( work_our_way_up(),
        reconcile_cycles(),
        lunisolar_goals(),
        slc_is_arithmetic(),
        slc_goals(),
        constant_values(),
        what_implement_means(),
        slc_details(),
        slc_bounds() );

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
