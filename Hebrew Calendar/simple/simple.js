"use strict";

var parts_per_min = 18; // parts per minute

var mins_per_hour = 60; // minutes per hour

var hours_per_day = 24 // hours per day

var wd_per_m = 29; // whole days per m

var pbwd_per_m = 13753; // parts beyond whole days per m

var months_per_leap = 13;

var months_per_non_leap = 12;

var leaps_per_cycle = 7;

var non_leaps_per_cycle = 12;

var parts_per_day = parts_per_min * mins_per_hour * hours_per_day;

var parts_per_m = wd_per_m * parts_per_day + pbwd_per_m;

var months_per_cycle =
    months_per_leap     * leaps_per_cycle
    +
    months_per_non_leap * non_leaps_per_cycle;

var parts_per_cycle = months_per_cycle * parts_per_m;

var years_per_cycle = leaps_per_cycle + non_leaps_per_cycle;

// ****************************************

function work_our_way_up()
{
    var a =
        "We will work our way up to the Jewish calendar in 3 stages.";

    var b =
        "In the first stage,"
        +" we will discuss the general goal of the Jewish calendar.";

    var c =
        "In the second stage,"
        +" we will present"
        +" a simple calendar that has the same general goal as the"
        +" Jewish calendar.";

    var d =
        "In the third and final stage,"
        +" we will present the actual Jewish calendar"
        +" as a set of complications added to the simple calendar.";

    return se( a, b, c, d );
}

// TODO: Acknowledge Dershowitz & Reingold.

// TODO: explain why we don't bother with months

function reconcile_cycles()
{
    var a =
        "The general goal of the Jewish calendar is to try to reconcile"
        +" three disparate cycles: the solar day, the synodic month, and the"
        +" tropical year.";

    var b =
        "These cycles correspond, roughly,"
        +" to the following underlying facts:"
        +" the earth spins,"
        +" the moon orbits the earth, and"
        +" the earth orbits the sun.";

    var c =
        "There are other cycles"
        +" that correspond to the same underlying facts,"
        +" e.g. the sidereal year.";

    var d =
        "But, without getting into details, suffice it to say that"
        +" the solar day,"
        +" the synodic month, and"
        +" the tropical year"
        +" are usually the cycles most relevant to life on earth.";

    return se( a, b, c, d );
}

function relevancy()
{
    var a =
        "Nonetheless, I admit that relevancy is in the eye of"
        +" the beholder.";

    var b =
        "And even if we could agree on what is relevant,"
        +" is there no room for fanciful flights of irrelevancy"
        +" in a calendar?";

    var c =
        "For example, various Hindu calendars align"
        +" with the sidereal rather than tropical year.";

    var d =
        "That means their years align with the stars rather"
        +" than the seasons.";

    var e =
        "To me, the stars seem less relevant than the seasons.";

    var f =
        "But that's just my opinion.";

    var g =
        "And even though a sidereal year seems less relevant to me,"
        +" I find it delightful.";

    var h =
        "We shall see, much later, that the Jewish calendar itself"
        +" has a far less relevant constraint than"
        +" the Hindu alignment with the sidereal year."

    var i =
        "Namely, the Jewish calendar is"
        +" made much more complex by its delightful"
        +" insistence that the year can only start on 4 of the 7 days"
        +" of the week.";

    var j =
        "This constraint has no astronomical basis, and is"
        +" only relevant within the Jewish belief system.";

    return se( a, b, c, d, e, f, g, h, i, j );
}

function c2a1s( a, c )
{
    return "The " + c + a
        + " sub-goal is to have New Year's Days fall";
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
    var a =
        "Putting questions of relevancy aside, let's get back to the goal"
        +" of the Jewish calendar."

    var b =
        "What do we mean when we say that the Jewish calendar"
        +" \"tries to reconcile\" the solar day, synodic month, and"
        +" tropical year?";

    var c =
        "We mean that it chooses New Year's Days in a way that"
        +" comes close to achieving the following two sub-goals.";

    var d =
        c2a1( "" );

    var e =
        c2b1( "" );

    var f =
        "For the Jewish calendar,"
        +" the desired phase of the tropical year"
        +" is roughly the autumnal equinox"
        +" and"
        +" the desired phase of the synodic month"
        +" is the new moon.";

    var g =
        "In other words, New Year's Day should fall both near a new"
        +" moon and near the autumnal equinox.";

    // TODO: Find out whether we need to constrain our claim about the
    // autumnal equinox to something like "nowadays" or "in modern times".
    // I.e. find out whether mean RhSh was significantly distant from the
    // autumnal equinox 1000 or so years ago when the calendar
    // stabilized/was standardized.

    var h =
        "Now, what do we mean when we say that"
        +" a day \"falls on\" a point in time?";

    var i =
        "We mean that some point of time identified with that day,"
        +" e.g. its noon, falls there.";

    var j =
        "(Later we shall see that for the purposes of choosing"
        +" Jewish New Year's Days, the point of time identified with"
        +" a day is the noon of the day before!)";

    // var c5 = "It does this by varying the length of the calendar year.";

    return se( a, b, c, d, e, f, g, h, i, j );
}

function slc_is_arithmetic()
{
    var a =
        "Really what the Jewish calendar tries to reconcile are"
        +" approximations of the"
        +" mean lengths of these cycles, not the cycles themselves."

    var b =
        "I.e. the Jewish calendar is an arithmetic calendar.";

    var c =
        "The values of its constants are motivated by a desire"
        +" to match observable cycles.";

    var d =
        "But the Jewish calendar is an algorithm divorced from"
        +" any ongoing observation.";

    return se( a, b, c, d );
}

function slc_goals()
{
    var a =
        "Let m and y be the simple calendar's approximations for"
        +" the synodic month and tropical year.";

    var b =
        "Using only an integral number of days per calendar year,"
        +" the simple calendar's goal is to make calendar year n start on ny"
        +" and on km for some integer k.";

    var c =
        "Let's see how this specific goal of the simple calendar"
        +" matches up to the general goal of the Jewish"
        +" calendar."

    var d =
        c2a1( " generic" );

    var e =
        "This matches up to the simple calendar's specific sub-goal"
        +" that the calendar year start on ny."

    var f =
        c2b1( " generic" );

    var g =
        "This matches up to the simple calendar's specific sub-goal"
        +" that the calendar year start on km for some integer k."

    return se( a, b, c, d, e, f, g );
}

function constant_values()
{
    var a =
        "Before showing how the simple calendar pursues its goal,"
        +" i.e. before showing our implementation,"
        +" we will present the constants used by the simple calendar.";

    var b =
        "Their exact values are not important to understanding the simple calendar,"
        +" in the sense that even if they were slightly different,"
        +" the calendar would behave similarly, and our implementation"
        +" would remain the same.";

    var c =
        "But it may help understand the simple calendar to have concrete"
        +" numbers in mind.";

    var d =
        "The first constant,"
        + " " + math("m") + ","
        + " is"
        + " " + wd_per_m // whole days per m
        + " "
        + pbwd_per_m // parts beyond whole days per m
        + "/"
        + parts_per_day // parts per day
        + " and has units \"days per synodic month\"."
        + " In decimal, it is about 29.531.";

    var e =
        "The second constant, " + math("a") + ", is"
        + " " + months_per_cycle + "/" + years_per_cycle
        + " and has units \"synodic months per tropical year\"."
        + " In decimal, it is about 12.37.";

    var f =
        "With "+ math("a") +", we come closest to a case where"
        +" the exact value of a constant gives rise to specific behavior"
        +" of the calendar."

    var g =
        "The denominator's value of 19 gives rise to a 19-year"
        +" cycle of leap and non-leap years."

    var h =
        "For convenience, the simple calendar uses a derived constant,"
        + " " + math("y") + ","
        + " which is equal to " + math("m") + math("a")
        + " and as a result"
        + " has units \"days per tropical year\". In decimal, it is"
        + " about 365.2468.";

    return se( a, b, c, d, e, f, g, h );
}

function what_implement_means()
{
    var a =
        "What will it mean to \"implement\" the simple calendar?";

    var b =
        "In general, to implement a calendar is to give a day number for"
        +" each day name.";

    var c =
        "By \"day number\" we mean \"integer day from some absolute origin,\""
        +" and by \"day name\" we mean \"represention in that calendar.\"";

    var d =
        "For our purposes, we are only concerned about New Year's Days, though.";

    var e =
        "So, for our purposes, to implement a calendar is to give a day number"
        +" for each New Year's Day name.";

    var f =
        "And, for our purposes, we will name (represent) New Year's Days by"
        +" their year only.";

    var g =
        "E.g. instead of saying for \"New Year's Day 5773\","
        +" \"Rosh ha-Shanah 5773,\", or"
        +" \"Tishri 1, 5773,\""
        +" we simply say \"5773.\"";

    return se( a, b, c, d, e, f, g);
}

function slc_details()
{
    var a =
        "To implement the simple calendar,"
        +" first let's choose, for any year n, what \"New Year's Month\""
        +" should be, in terms of whole months elapsed since the simple calendar's"
        +" origin."

    var b =
        "Here we use \"month\" to mean m.";

    var c =
        "We will abbreviate \"simple calendar New Year's Month"
        +" for year n\" to σ(n).";

    var d =
        "In other words σ(n) yields a k such that km is near ny.";

    var e =
        "In particular we will choose the k such that km is closest to ny"
        +" without going over.";

    var f =
        "Mathematically this means, σ(n) = floor(ny/m), which reduces to"
        +" floor(na).";

    var g =
        "So,"
        + " σ(1) = " + sigma(1) +","
        + " σ(2) = " + sigma(2) +","
        + " σ(3) = " + sigma(3) +","
        +" etc.";

    var h =
        "Now let's choose, for any year n, what New Year's Day should be, in"
        +" terms of whole days elapsed since the simple calendar's origin.";

    var i =
        "We will abbreviate \"simple calendar New Year's Day for year n\" to s(n).";

    var j =
        "Let's make s(n) be the day that is closest to mσ(n) without going over.";

    var k =
        "Mathematically, we can express this as s(n) = floor(mσ(n)).";

    var l =
        "Or, \"inlining\" σ(n), s(n) = floor(m*floor(na)).";

    return se( a, b, c, d, e, f, g, h, i, j, k, l );
}

function slc_bounds()
{
    var a =
        "So, how close is s(n) to ny, and how close is it to km for"
        +" some integer k?";

    var b =
        "It is within (-1,0] of km for some k.";

    var c =
        "I.e. it is less than a day before and not after.";

    var d =
        "It is within (-m-1,0] of ny.";

    var e =
        "I.e. it is less than m+1 before and not after.";

    var f = "So if the origin (day zero)"
        +" falls on an autumnal equinox and a full moon,"
        +" and the estimates m and y are perfect,"
        +" all New Year's Days"
        +" will fall within a day of a full moon"
        +" and within about 30.531 days of the autumnal equinox."

    return se( a, b, c, d, e, f );
}

var outstr =
    pa( work_our_way_up(),
        reconcile_cycles(),
        relevancy(),
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

function constant_a()
{
    return months_per_cycle / years_per_cycle;
}

function sigma( n )
{
    return Math.floor( n * constant_a() );
}

process.stdout.write( outstr );


//  LocalWords:  TODO synodic ny na inlining
