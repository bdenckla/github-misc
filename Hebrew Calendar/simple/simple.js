"use strict";

// ****************************************
// Integer constants
// ****************************************

var parts_per_min       = 18;    // parts per minute

var mins_per_hour       = 60;    // minutes per hour

var hours_per_day       = 24     // hours per day

var wd_per_m            = 29;    // whole days per month

var pbwd_per_m          = 13753; // parts beyond whole days per month

var wm_per_y            = 12;    // whole months per year

var bbwm_per_y          = 7;     // blahs beyond whole months per year

var blahs_per_month     = 19;


// ****************************************
// Derived integer constants
// ****************************************

var parts_per_day = parts_per_min * mins_per_hour * hours_per_day;

// ****************************************

/*
 * mode_sym means rhs as long as it is symbolic, and lhs
 * otherwise. By "symbolic" we mean "contains no integer literals."
 */

var mode_lhs   = 'mode:lhs';
var mode_rhs   = 'mode:rhs';
var mode_sym   = 'mode:sym';
var mode_value = 'mode:value';

var constant_m_symbol = math("μ");
var constant_a_symbol = math("α");
var n_symbol = math("ν");
var k_symbol = math("κ");

var constant_m_value = constant_m( mode_value );
var constant_a_value = constant_a( mode_value );

var ma_product_value      = ma_product( mode_value );
var ma_product_expression = ma_product( mode_lhs );

var na_product = multiply_expression( n_symbol, constant_a_symbol );

var nma_product = nma_product_given_n_expression( n_symbol );

var sigma_of_n  = sigma ( mode_lhs, n_symbol );
var msigma_of_n = msigma( mode_lhs, n_symbol );
var tau_of_n    = tau   ( mode_lhs, n_symbol );

var sigma_of_n_rhs  = sigma ( mode_rhs, n_symbol );
var tau_of_n_rhs    = tau   ( mode_rhs, n_symbol );
var tau_of_n_sym    = tau   ( mode_sym, n_symbol );
var msigma_of_n_rhs = msigma( mode_rhs, n_symbol );

var km_product = km_product_given_k_expression( k_symbol );

var km_for_some_k = km_product +" for some integer " + k_symbol;

// ****************************************

// The function below is from
//
//    http://benalman.com/news/2012/09/partial-application-in-javascript/

function partial(fn /*, args...*/) {
  // A reference to the Array#slice method.
  var slice = Array.prototype.slice;
  // Convert arguments object to an array, removing the first argument.
  var args = slice.call(arguments, 1);

  return function() {
    // Invoke the originally-specified function, passing in all originally-
    // specified arguments, followed by any just-specified arguments.
    return fn.apply(this, args.concat(slice.call(arguments, 0)));
  };
}

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

function multiply_expression( a, b )
{
    return a + "" + b;
}

function call_expression( a, b )
{
    return a+"("+b+")";
}

function floor_expression( a )
{
    return call_expression( 'floor', a );
}

function add( mode, a, b )
{
    return mode == mode_value
        ? a + b
        : a + "+" + b;
}

function subtract( mode, a, b )
{
    return mode == mode_value
        ? a - b
        : a + "-" + b;
}

function multiply( mode, a, b )
{
    return mode == mode_value
        ? a * b
        : multiply_expression( a, b );
}

function divide( mode, a, b )
{
    return mode == mode_value
        ? a / b
        : a + "/" + b;
}

function floor( mode, a )
{
    return mode == mode_value
        ? Math.floor( a )
        : floor_expression( a );
}

function sigma_value( n )
{
    return sigma( mode_value, n );
}

function sigma_ceil_value( n )
{
    return Math.ceil( n * constant_a_value );
}

function msigma( mode, n )
{
    return multiply( mode, constant_m( mode ), sigma( mode, n ) );
}

function float_to_decimal( x, digits_past_decimal )
{
    return x.toFixed( digits_past_decimal );
}

function four_digits_past_decimal( x )
{
    return float_to_decimal( x, 4 );
}

function one_digit_past_decimal( x )
{
    return float_to_decimal( x, 1 );
}

function constant_m( mode )
{
    return ( mode == mode_lhs
             ||
             mode == mode_sym )
        ? constant_m_symbol
        : add( mode,
               wd_per_m,
               divide( mode, pbwd_per_m, parts_per_day ) );
}

function constant_a( mode )
{
    return ( mode == mode_lhs
             ||
             mode == mode_sym )
        ? constant_a_symbol
        : add( mode,
               wm_per_y,
               divide( mode, bbwm_per_y, blahs_per_month ) );
}

function sigma( mode, n )
{
    return mode == mode_lhs
        ? call_expression( 'σ', n )
        : sigma_rhs( if_rhs_change_to_lhs( mode ), n );
}

function sigma_rhs( mode, n )
{
    return floor( mode, multiply( mode, n, constant_a( mode ) ) );
}

function tau( mode, n )
{
    return mode == mode_lhs
        ? call_expression( 'τ', n )
        : tau_rhs( if_rhs_change_to_lhs( mode ), n );
}

function tau_rhs( mode, n )
{
    return floor( mode, msigma( mode, n ) );
}

function gamma( mode, n )
{
    return mode == mode_lhs
        ? call_expression( 'γ', n )
        : gamma_rhs( if_rhs_change_to_lhs( mode ), n );
}

function gamma_rhs( mode, n )
{
    var na = multiply( mode, n, constant_a( mode ) );

    var nap = subtract( mode, na, constant_phi( mode ) );

    var fnap = floor( mode, nap );

    var  mfnap = multiply( mode, constant_m( mode ), fnap );

    var fmfnap = floor( mode, mfnap );

    return fmfnap;
}

function sfd( mode, n ) // sigma forward delta
{
    return mode == mode_lhs
        ? call_expression( 'φ', n )
        : sfd_rhs( if_rhs_change_to_lhs( mode ), n );
}

function sfd_rhs( mode, n )
{
    var n_plus_1 = add( mode, n, 1 );

    return subtract( mode,
                     sigma( mode, n_plus_1 ),
                     sigma( mode, n ) );
}

function if_rhs_change_to_lhs( mode )
{
    return mode == mode_rhs
        ? mode_lhs
        : mode;
}

function m_plus_1( mode )
{
    return add( mode, constant_m( mode ), 1 );
}

function ma_product( mode )
{
    return multiply( mode, constant_m( mode ), constant_a( mode ) );
}

function nma_product_given_n( mode, n )
{
    return multiply( mode, n, ma_product( mode ) );
}

function nma_product_given_n_value( n )
{
    return nma_product_given_n( mode_value, n );
}

function nma_product_given_n_expression( n )
{
    return nma_product_given_n( mode_lhs, n );
}

function km_product_given_k( mode, k )
{
    return multiply( mode, k, constant_m( mode ) );
}

function km_product_given_k_value( k )
{
    return km_product_given_k( mode_value, k );
}

function km_product_given_k_expression( k )
{
    return km_product_given_k( mode_lhs, k );
}

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
        +" as a set of complications to this simple calendar.";

    return se( a, b, c, d );
}

// ****************************************

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
        +" that correspond to the same underlying facts.";

    var c2 =
        "For example, like the tropical year,"
        +" the sidereal year corresponds, rougly,"
        +" to the underlying fact"
        +" that they earth orbits the sun.";

    var d =
        "But, without getting into details, suffice it to say that"
        +" the solar day,"
        +" the synodic month, and"
        +" the tropical year"
        +" are usually the cycles most relevant to life on earth.";

    return se( a, b, c2, d );
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

    var k =
        "For the Jewish calendar, the point in time identified with"
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

    var e =
        "So, for example, it does not directly pursue the goal of having"
        +" New Year's Day fall near a new moon.";

    var f =
        "The goal it directly pursues is to have New Year's Day fall near"
        +" one of its estimates of a new moon.";

    var g =
        "These estimates are formed from an estimate of the time of some"
        +" \"original\" new moon and an estimate of the length of the"
        +" synodic month."

    var h =
        "These estimates were fixed more than a thousand years ago,"
        +" i.e. are not updated"
        +" by ongoing observation."

    return se( a, b, c, d, e, f, g, h );
}

function lhs_eq_value( f, n )
{
    return lhs_eq_mode( f, mode_value, n );
}

function lhs_eq_rhs( f, n )
{
    return lhs_eq_mode( f, mode_rhs, n );
}

function lhs_eq_sym( f, n )
{
    return lhs_eq_mode( f, mode_sym, n );
}

function lhs_eq_mode( f, mode, n )
{
    var a = f( mode_lhs, n );
    var b = f( mode,     n );

    return a + " = " + b;
}

function eiav( f, digits_past_decimal )
{
    var expression = f( mode_lhs );
    var value      = f( mode_value );

    return expression_is_about_value( expression,
                                      value,
                                      digits_past_decimal );
}

function expression_is_about_value( expression,
                                    value,
                                    digits_past_decimal )
{
    var value_as_decimal = float_to_decimal( value, digits_past_decimal );

    return expression + " is about " + value_as_decimal;
}

function slc_goals()
{
    var a =
        "Let "+constant_m_symbol
        +" and "+constant_a_symbol
        +" be the Jewish calendar's estimates for"
        +" days per synodic month and synodic months per tropical year.";

    var b =
        "Using only a whole number of days per calendar year,"
        +" the simple calendar's goals are to make New Year's Day"
        +" "+n_symbol+" fall near"
        +" "+nma_product+""
        +" and "+km_for_some_k+".";

    var example_year = 2;
    var nmae = nma_product_given_n_expression( example_year );

    var b1 =
        "So, for example, for year "+example_year+
        ", one of the simple calendar's goals is"
        +" to make New Year's Day"
        +" fall near"
        + " " + nmae
        + ".";

    var nmav = nma_product_given_n_value( example_year );
    var nmaev = expression_is_about_value( nmae, nmav, 1 );

    var b2 = nmaev + " days.";

    var b3 =
        "So, New Year's Day "+example_year
        + " should fall near"
        + " a point in time about"
        + " " + one_digit_past_decimal( nmav )
        + " days after the (as yet undefined) origin."

    var b4 =
        "The simple calendar's other goal is"
        +" to make all New Year's Days fall near "+km_for_some_k+".";

    var k1 = sigma_value( example_year );
    var k2 = sigma_ceil_value( example_year );
    var k1me = km_product_given_k_expression( k1 );
    var k2me = km_product_given_k_expression( k2 );
    var k1mv = km_product_given_k_value( k1 );
    var k2mv = km_product_given_k_value( k2 );

    var b5 =
        "For year "+example_year+", this could be satisfied by falling near either"
        +" " + k1me + " or"
        +" " + k2me + ","
        +" since these"
        +" are the multiples of "+constant_m_symbol
        +" (i.e. the values of "+km_product+")"
        +" closest to"
        +" "+nmae+".";

    var b6 =
        "("
        + expression_is_about_value( k1me, k1mv, 1 ) + " days"
        + " and"
        + " "
        + expression_is_about_value( k2me, k2mv, 1 ) + " days"
        + ".)";

    var c =
        "Let's see how these specific goals of the simple calendar"
        +" match up to the general goals of the Jewish"
        +" calendar."

    var d =
        c2a1( " general" );

    var e =
        "This matches up to the simple calendar's specific goal"
        +" to make New Year's Day "+n_symbol
        +" fall near "+nma_product+"."

    var f =
        c2b1( " general" );

    var g =
        "This matches up to the simple calendar's specific goal"
        +" to make New Year's Day fall near "+km_for_some_k+"."

    return se( a, b, b1, b2, b3, b4, b5, b6, c, d, e, f, g );
}

function introduce_constant( f, first_or_second, units )
{
    var e =
        "The " + first_or_second + " constant,"
        + " " + f( mode_lhs ) + ", is"
        + " " + f( mode_rhs )
        + " and has units \""+units+"\"."
        + " In decimal, it is about"
        + " " + four_digits_past_decimal( f( mode_value ) )
        + ".";

    return e;
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

    var d = introduce_constant( constant_m,
                                "first",
                                "days per synodic month" );

    var e = introduce_constant( constant_a,
                                "second",
                                "synodic months per tropical year" );

    var maev = eiav( ma_product, 4 );

    var f =
        "For reference, " + maev
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
        +" " + n_symbol + ", what its \"New Year's Month\""
        +" should be, in terms of whole synodic months elapsed since the"
        +" simple calendar's"
        +" origin."

    var c =
        "We'll call this " + sigma_of_n + ".";

    var d =
        "Our estimate of the length of a tropical year, in synodic months, is"
        + " " + constant_a_symbol + ".";

    var e =
        "So, we would estimate tropical year " + n_symbol
        +" to start at time " + na_product
        +" measured in synodic months.";

    var e2 =
        "But, we are constrained to whole synodic months.";

    var e3 =
        "So, we choose the whole synodic month closest to"
        +" " + na_product
        +" without going over.";

    var f =
        "I.e., " + lhs_eq_rhs( sigma, n_symbol ) + ".";

    // TODO: make the below more systematic
    var g =
        "So, for example,"
        + " " + lhs_eq_value( sigma, 0 ) +","
        + " " + lhs_eq_value( sigma, 1 ) +","
        + " " + lhs_eq_value( sigma, 2 ) +","
        + " " + lhs_eq_value( sigma, 3 ) +","
        +" etc.";

    var g1 =
        "Some presentations of the Jewish calendar focus a lot on the pattern"
        +" of leap (13-month) and non-leap (12-month) years that"
        +" "+sigma_of_n+" creates."

    // TODO: make the below more systematic
    var g2 =
        "E.g. if "+lhs_eq_rhs( sfd, n_symbol )+","
        + " " + lhs_eq_value( sfd, 0 ) +","
        + " " + lhs_eq_value( sfd, 1 ) +","
        + " " + lhs_eq_value( sfd, 2 ) +","
        + " etc.";

    var g3 =
        "But our more algorithmic orientation views this pattern as a"
        +" coincidental artifact of the particular value of the constant"
        +" "+constant_a_symbol
        +" rather than a defining feature of the calendar."

    return se( a, c, d, e, e2, e3, f, g, g1, g2, g3 );
}

function slc_details_2()
{
    var a =
        "Now that we've chosen New Year's Month, let finish our job"
        +" by choosing,"
        +" for any year "+n_symbol+", what New Year's Day should be, in"
        +" terms of whole days elapsed since the simple calendar's origin.";

    var b =
        "We'll call this "+tau_of_n+".";

    var c =
        "Let's make New Year's Day fall near the"
        +" start of New Year's Month.";

    var d =
        "Our estimate of the length of a synodic month, in days, is"
        +" "+constant_m_symbol+".";

    var e =
        "So, we would estimate synodic month "+sigma_of_n
        +" to start at time "+msigma_of_n+","
        +" measured in days.";

    var f =
        "But, we are constrained to whole days.";

    var g =
        "So, let's make "+tau_of_n
        +" be the day that is closest to "+msigma_of_n
        +" without going over.";

    var h =
        "I.e., "+lhs_eq_rhs( tau, n_symbol )+".";

    var i =
        "Or, \"inlining\" "+sigma_of_n+","
        +" "+lhs_eq_sym( tau, n_symbol )+".";

    return se( a, b, c, d, e, f, g, h, i );
}

function slc_bounds()
{
    var m_plus_1_value      = m_plus_1( mode_value );
    var m_plus_1_expression = m_plus_1( mode_lhs );

    var a =
        "So, how close is "+tau_of_n
        +" to "+nma_product
        +", and how close is it to "+km_for_some_k+"?";

    var b =
        "It is within (-("+m_plus_1_expression+"),0]"
        +" of "+nma_product+".";

    var c =
        "I.e. it is less than"
        +" "+m_plus_1_expression
        +" days before "+nma_product+" and not after it.";

    var d =
        "It is within (-1,0] of "+km_for_some_k+".";

    var e =
        "I.e. it is less than a day before some"
        +" "+km_product+" and not after.";

    var f =
        "So if the origin"
        +" is an autumnal equinox and a full moon,"
        +" and the estimates "+constant_m_symbol
        +" and "+constant_a_symbol
        +" are perfect,"
        +" all New Year's Days"
        +" will fall"
        +" within about"
        +" "+four_digits_past_decimal( m_plus_1_value )
        +" days of the autumnal equinox"
        +" and"
        +" within a day of a full moon."

    return se( a, b, c, d, e, f );
}

function phases()
{
    var a =
        "Now, what if the desired phases"
        +" of the tropical year and moon"
        +" are not zero?";

    var b =
        "For example, what if the origin (time zero) is not a new moon, "
        +" or it is a new moon but a new moon is not the desired phase?";

    var c =
        "To handle such cases we need to sprinkle a couple of phase "
        +" constants into our previous functions."

    var d =
        lhs_eq_sym( gamma, n_symbol )+".";

    return se( a, b, c, d );
}

var outstr =
    pa(
        work_our_way_up(),
        reconcile_cycles(),
        lunisolar_goals(),
        slc_is_arithmetic(),
        constant_values(),
        slc_goals(),
        what_implement_means(),
        slc_details_1(),
        slc_details_2(),
        slc_bounds(),
        // phases(),
        ""
    );

process.stdout.write( outstr );

//  LocalWords:  TODO synodic nma na inlining
