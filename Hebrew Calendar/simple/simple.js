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

// ****************************************

var parts_per_day = parts_per_min * mins_per_hour * hours_per_day;

var parts_per_m = wd_per_m * parts_per_day + pbwd_per_m;

var months_per_cycle =
    months_per_leap     * leaps_per_cycle
    +
    months_per_non_leap * non_leaps_per_cycle;

var parts_per_cycle = months_per_cycle * parts_per_m;

var years_per_cycle = leaps_per_cycle + non_leaps_per_cycle;

// ****************************************

var math_m = math("$m");
var math_a = math("$a");
var math_n = math("$n");
var math_k = math("$k");

var constant_m_float = constant_m( add_float, divide_float );

var constant_m_integer_expression = constant_m( add_expression, divide_expression );

var ma_product_symbolic_expression = math_m + math_a;

var ma_product_float = constant_m_float * constant_a_float();

var na_product_symbolic_expression = math_n + math_a;

var nma_product = math_n + math_m + math_a;

var sigma_of_n = 'σ(n)';

var msigma_of_n = math_m+sigma_of_n;

var ess_of_n = 's(n)';

var floor_of_na = floor_of( na_product_symbolic_expression );

var mfloor_of_na = math_m + floor_of_na;

var km_product = product_expression( math_k, math_m );

var km_for_some_k = km_product +" for some integer " + math_k;

var m_plus_1_float = add_float( constant_m_float, 1 );

var m_plus_1_expression = add_expression( math_m, '1' );

// ****************************************

function work_our_way_up()
{
    var a =
        "We will work our way up to the Jewish calendar in 3 stages.";

    var b =
        "First,"
        +" we will discuss the general goals of the Jewish calendar.";

    var c =
        "Then,"
        +" we will present"
        +" a simple calendar that has the same general goals as the"
        +" Jewish calendar.";

    var d =
        "Finally,"
        +" we will present the Jewish calendar"
        +" as a set of complications to the simple calendar.";

    return se( a, b, c, d );
}

// TODO: Acknowledge Dershowitz & Reingold.

function reconcile_cycles()
{
    var a =
        "The Jewish calendar tries to reconcile"
        +" three disparate cycles:"
        +" the solar day,"
        +" the synodic month, and"
        +" the tropical year.";

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

function c2a1s( a, c )
{
    return "The " + c + a
        + " goal is to have New Year's Day fall";
}

function c2a1( a )
{
    return c2a1s( a, "first" ) + " near"
        +" a certain phase of the"
        +" tropical year.";
}

function c2b1( a )
{
    return c2a1s( a, "second") +" near"
        +" a certain phase of the"
        +" synodic month.";
}

function lunisolar_goals()
{
    var b =
        "What do we mean when we say that the Jewish calendar"
        +" \"tries to reconcile\" the solar day, synodic month, and"
        +" tropical year?";

    var c =
        "We mean that it chooses New Year's Days in a way that"
        +" comes close to achieving the following two goals.";

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
        "In other words, New Year's Day should fall both"
        +" near the autumnal equinox"
        +" and"
        +" near a new moon"
        +".";

    // TODO: Find out whether we need to constrain our claim about the
    // autumnal equinox to something like "nowadays" or "in modern times".
    // I.e. find out whether mean RhSh was significantly distant from the
    // autumnal equinox 1000 or so years ago when the calendar
    // stabilized/was standardized.

    var h =
        "Now, what do we mean when we say that"
        +" a day \"falls near\" a point in time like a new moon"
        +" or an equinox?";

    var i =
        "We mean that some point in time identified with that day,"
        +" e.g. its noon, falls near there.";

    var j =
        "(We often associate a whole day, i.e. a span of time,"
        +" with events like a new moon or an equinox,"
        +" but in fact they occur at a point in time.)";

    var k = "For the Jewish calendar, the point in time identified with"
        +" a day is the noon of the day before!";

    return se( b, c, d, e, f, g, h, i, j, k );
}

function slc_is_arithmetic()
{
    var a =
        "Really what the Jewish calendar tries to reconcile are"
        +" estimates of the"
        +" mean lengths of these cycles, not the cycles themselves."

    var b =
        "I.e. the Jewish calendar is an arithmetic calendar.";

    var c =
        "The values of its constants are motivated by a desire"
        +" to match observable cycles.";

    var d =
        "But the Jewish calendar is an algorithm divorced from"
        +" any ongoing observation.";

    var e = "So, for example, it does not directly pursue the goal of having"
        +" New Year's Day fall near a new moon.";

    var f = "The goal it directly pursues is to have New Year's Day fall near"
        +" one of its estimates of a new moon.";

    var g = "These estimates are formed from an estimate of the time of some"
        +" \"original\" new moon and an estimate of the length of the"
        +" synodic month."

    var h = "These estimates were fixed more than a thousand years ago,"
        +" i.e. are not updated"
        +" by ongoing observation."

    return se( a, b, c, d, e, f, g, h );
}

function slc_goals()
{
    var example_year = 2;

    var a =
        "Let "+math_m+" and "+math_a+" be the Jewish calendar's estimates for"
        +" days per synodic month and synodic months per tropical year.";

    var b =
        "Using only a whole number of days per calendar year,"
        +" the simple calendar's goals are to make New Year's Day "+math_n+" fall near"
        +" "+nma_product+""
        +" and "+km_for_some_k+".";

    var b1 =
        "So, for example, for year "+example_year+
        ", one of the simple calendar's goals is"
        +" to make New Year's Day"
        +" fall near"
        + " " + nma_product_given_n_symbolic( example_year )
        + ".";

    var b2 =
        nma_product_given_n_symbolic( example_year )
        + " is about"
        + " "+one_digit_past_decimal( nma_product_given_n_float( example_year ) )
        + " days.";

    var b3 = "So, New Year's Day "+example_year
        + " should fall near"
        + " a point in time about"
        + " " + one_digit_past_decimal( nma_product_given_n_float( example_year ) )
        + " days after the (as yet undefined) origin."

    var b4 =
        "The simple calendar's other goal is"
        +" to make all New Year's Days fall near "+km_for_some_k+".";

    var example_k1 = sigma( example_year );
    var example_k2 = sigma_ceil( example_year );
    var example_km1 = one_digit_past_decimal( msigma( example_year ) );
    var example_km2 = one_digit_past_decimal( msigma_ceil( example_year ) );

    var b5 =
        "For year "+example_year+", this could be satisfied by falling near either"
        +" " + example_k1+math_m+" or"
        +" " + example_k2+math_m+","
        +" since these"
        +" are the multiples of "+math_m+" (i.e. the values of "+km_product+")"
        +" closest to"
        +" "+nma_product_given_n_symbolic( example_year )+".";

    var b6 =
        "("
        + example_k1+math_m+" is about "+example_km1+" days"
        + " and"
        + " "
        + example_k2+math_m+" is about "+example_km2+" days"
        + ".)";

    var c =
        "Let's see how these specific goals of the simple calendar"
        +" match up to the general goals of the Jewish"
        +" calendar."

    var d =
        c2a1( " general" );

    var e =
        "This matches up to the simple calendar's specific goal"
        +" to make New Year's Day "+math_n+" fall near "+nma_product+"."

    var f =
        c2b1( " general" );

    var g =
        "This matches up to the simple calendar's specific goal"
        +" to make New Year's Day fall near "+km_for_some_k+"."

    return se( a, b, b1, b2, b3, b4, b5, b6, c, d, e, f, g );
}

function constant_values()
{
    var a =
        "Before showing how the simple calendar pursues its goals,"
        +" i.e. before showing an implementation for it,"
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
        + " " + math_m + ","
        + " is"
        + " " + constant_m_integer_expression
        + " and has units \"days per synodic month\"."
        + " In decimal, it is about"
        + " " + four_digits_past_decimal( constant_m_float )
        + ".";

    var e =
        "The second constant, " + math_a + ", is"
        + " " + constant_a_integer_expression()
        + " and has units \"synodic months per tropical year\"."
        + " In decimal, it is about"
        + " " + four_digits_past_decimal( constant_a_float() )
        + ".";

    var f =
        "For reference, " + ma_product_symbolic_expression
        + " is about"
        + " " + four_digits_past_decimal( ma_product_float )
        + " and has units \"days per tropical year\".";

    return se( a, b, c, d, e, f );
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
        "How the days within a Jewish year are named, that is, how they are"
        +" divided up into months, is not of much"
        +" mathematical or algorithmic interest.";

    var e =
        "So, for our purposes, to implement a calendar is to give a day number"
        +" for each New Year's Day's name.";

    var f =
        "So, we can name (represent) New Year's Days by"
        +" their year only.";

    var g =
        "E.g. instead of using names like \"New Year's Day 5773\","
        +" \"Rosh ha-Shanah 5773,\", or"
        +" \"Tishri 1, 5773,\""
        +" we will simply use \"5773.\"";

    return se( a, b, c, d, e, f, g);
}

function slc_details_1()
{
    var a =
        "To implement the simple calendar,"
        +" first let's choose, for any year"
        +" " + math_n + ", what its \"New Year's Month\""
        +" should be, in terms of whole synodic months elapsed since the"
        +" simple calendar's"
        +" origin."

    var c =
        "We'll call this " + sigma_of_n + ".";

    var d =
        "Our estimate of the length of a tropical year, in synodic months, is"
        + " " + math_a + ".";

    var e =
        "So, we would estimate tropical year " + math_n
        +" to start at time " + na_product_symbolic_expression
        +" measured in synodic months.";

    var e2 =
        "But, we are constrained to whole synodic months.";

    var e3 =
        "So, we choose the whole synodic month closest to"
        +" " + na_product_symbolic_expression
        +" without going over.";

    var f =
        "I.e., " + sigma_of_n + " = " + floor_of_na + ".";

    var g =
        "So, for example,"
        + " σ(0) = " + sigma(0) +","
        + " σ(1) = " + sigma(1) +","
        + " σ(2) = " + sigma(2) +","
        + " σ(3) = " + sigma(3) +","
        +" etc.";

    var g1 =
        "Some presentations of the Jewish calendar focus a lot on the pattern"
        +" of leap (13-month) and non-leap (12-month) years that"
        +" "+sigma_of_n+" creates."

    var g2 =
        "E.g. if e(n) = σ(n+1) - "+sigma_of_n+","
        + " e(0) = " + sigma_forward_delta(0) +","
        + " e(1) = " + sigma_forward_delta(1) +","
        + " e(2) = " + sigma_forward_delta(2) +","
        +" etc.";

    var g3 =
        "But our more algorithmic orientation views this pattern as a"
        +" coincidental artifact of the particular value of the constant a"
        +" rather than a defining feature of the calendar."

    return se( a, c, d, e, e2, e3, f, g, g1, g2, g3 );
}

function slc_details_2()
{
    var a =
        "Now that we've chosen New Year's Month, let finish our job"
        +" by choosing,"
        +" for any year "+math_n+", what New Year's Day should be, in"
        +" terms of whole days elapsed since the simple calendar's origin.";

    var b =
        "We'll call this "+ess_of_n+".";

    var c =
        "Let's make New Year's Day fall near the"
        +" start of New Year's Month.";

    var d =
        "Our estimate of the length of a synodic month, in days, is "+math_m+".";

    var e =
        "So, we would estimate synodic month "+sigma_of_n
        +" to start at time "+msigma_of_n+","
        +" measured in days.";

    var f =
        "But, we are constrained to whole days.";

    var g =
        "So, let's make "+ess_of_n
        +" be the day that is closest to "+msigma_of_n
        +" without going over.";

    var h =
        "I.e., "+ess_of_n+" = "+floor_of( msigma_of_n )+".";

    var i =
        "Or, \"inlining\" "+sigma_of_n+", "+ess_of_n+" ="
        + " "+floor_of( mfloor_of_na )+".";

    return se( a, b, c, d, e, f, g, h, i );
}

function slc_bounds()
{
    var a =
        "So, how close is "+ess_of_n+" to "+nma_product+", and how close is it to"
        +" "+""+km_for_some_k+"?";

    var b =
        "It is within (-("+m_plus_1_expression+"),0] of "+nma_product+".";

    var c =
        "I.e. it is less than"
        +" "+m_plus_1_expression
        +" days before "+nma_product+" and not after it.";

    var d =
        "It is within (-1,0] of "+km_for_some_k+".";

    var e =
        "I.e. it is less than a day before some "+km_product+" and not after.";

    var f = "So if the origin (day zero)"
        +" falls exactly on an autumnal equinox and a full moon,"
        +" and the estimates "+math_m+" and "+math_a+" are perfect,"
        +" all New Year's Days"
        +" will fall"
        +" within about"
        + " "+four_digits_past_decimal( m_plus_1_float )
        +" days of the autumnal equinox"
        +" and"
        +" within a day of a full moon"
        +"."

    return se( a, b, c, d, e, f );
}

var outstr =
    pa( work_our_way_up(),
        reconcile_cycles(),
        lunisolar_goals(),
        slc_is_arithmetic(),
        slc_goals(),
        constant_values(),
        what_implement_means(),
        slc_details_1(),
        slc_details_2(),
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

function constant_m( add, divide )
{
    return add( wd_per_m, divide( pbwd_per_m, parts_per_day ) );
}

function constant_a( divide )
{
    return divide( months_per_cycle, years_per_cycle );
}

function constant_a_float()
{
    return constant_a( divide_float );
}

function constant_a_integer_expression()
{
    return constant_a( divide_expression );
}

function divide_expression( a, b )
{
    return a + "/" + b;
}

function divide_float( a, b )
{
    return a / b;
}

function add_expression( a, b )
{
    return a + "+" + b;
}

function add_float( a, b )
{
    return a + b;
}

function product_expression( a, b )
{
    return a + b;
}

function sigma( n )
{
    return Math.floor( n * constant_a_float() );
}

function sigma_ceil( n )
{
    return Math.ceil( n * constant_a_float() );
}

function msigma( n )
{
    return constant_m_float * sigma( n );
}

function msigma_ceil( n )
{
    return constant_m_float * sigma_ceil( n );
}

function sigma_forward_delta( n )
{
    return sigma( n + 1 ) - sigma( n );
}

function four_digits_past_decimal( x )
{
    return x.toFixed( 4 );
}

function one_digit_past_decimal( x )
{
    return x.toFixed( 1 );
}

function floor_of( x )
{
    return "floor("+x+")";
}

function nma_product_given_n_symbolic( n )
{
    return n + ma_product_symbolic_expression;
}

function nma_product_given_n_float( n )
{
    return n * ma_product_float;
}

process.stdout.write( outstr );


//  LocalWords:  TODO synodic nma na inlining
