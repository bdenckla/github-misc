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

var e = "We will use two constants";

var f = "The first";

var constant_m = math("m");

var h = "is";

var i = "29 13753/29520 days (about 29.531 days)";

var hi = sp( h, i );

var fghi = co( f, constant_m, hi );

var j = "It is an estimate of a synodic month";

var k = "The second constant";

var l = math("y");

var m = "is";

var n =
    "(235/19)" + constant_m
    + " (about 12.37" + constant_m
    + " (about 365.2468 days))";

var klmn = sp( co( k, l, m ), n );

var o = "It is an estimate of a year"

var p = "Our simple lunisolar calendar uses the following system to make its years have mean length";

var q = "y";

var pq = sp( p, q );

var r = "Divide time into chunks of 19 years. For each chunk, make 7 years have mean length 13" + constant_m + " and make the remaining 12 years have mean length 12"+ constant_m;

var s = "This will make the year have mean length ((7 * 13 + 12 * 12)/19)"+constant_m+", which is (235/19)"+constant_m;


var outstr =
    se( a, b, c, d, e, fghi, j, klmn, o, pq, r, s );

function math( s )
{
    return s;
}

function se() { return sentence_join( Array.prototype.slice.call( arguments ) ); }

function sentence_join( a )
{
    return a.join( ". " ) + ".";
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

