#!/usr/bin/php -q
<?php

require_once 'svg.php';

function holidays()
{
  // month, day, name
  return array
    (
     new Holiday( mn_ad(), 14, 'Purim' ),
     new Holiday( mn_ni(), 15, 'Pesach' ),
     new Holiday( mn_si(),  6, 'Shavuot' ),
     new Holiday( mn_ti(), 15, 'Sukkot' ),
     new Holiday( mn_ki(), 25, 'Chanukkah' ),
     new Holiday( mn_sh(), 15, 'Tu B\'Shevat' ),
     );
}

function month_name_array()
{
  return array
    (
     mn_ad() => array( 'Adar/Adar Sheni' ),
     mn_ni() => array( 'Nisan' ),
     mn_iy() => array( 'Iyyar' ),
     mn_si() => array( 'Sivan' ),
     mn_ta() => array( 'Tammuz' ),
     mn_av() => array( 'Av' ),
     mn_el() => array( 'Elul' ),
     mn_ti() => array( 'Tishrei' ),
     mn_ch() => array( 'Cheshvan' ),
     mn_ki() => array( 'Kislev' ),
     mn_te() => array( 'Tevet' ),
     mn_sh() => array( 'Shevat' ),
     mn_ar() => array( 'Adar Rishon' ),
     );
}

function mn_ad() { return  0; }
function mn_ni() { return  1; }
function mn_iy() { return  2; }
function mn_si() { return  3; }
function mn_ta() { return  4; }
function mn_av() { return  5; }
function mn_el() { return  6; }
function mn_ti() { return  7; }
function mn_ch() { return  8; }
function mn_ki() { return  9; }
function mn_te() { return 10; }
function mn_sh() { return 11; }
function mn_ar() { return 12; }

function mn_min() { return  0; }
function mn_max() { return 12; }

function mn_start() { return mn_ad(); }

function year_species()
{
  return array
    (
     new Species( -1, 0 ),
     new Species(  0, 0 ),
     new Species(  1, 0 ),
     new Species( -1, 1 ),
     new Species(  0, 1 ),
     new Species(  1, 1 ),
     );
}

function pa() // Partially Apply
{
  $origArgs = func_get_args();
  return function() use ( $origArgs )
    {
      $allArgs = array_merge( $origArgs, func_get_args() );
      return call_user_func_array( 'call_user_func', $allArgs );
    };
}

function month_name( $month_number )
{
  $a = month_name_array();

  return $a[ $month_number ];
}

function days_per_month( $month_number, Species $species )
{
  list( $day_adj, $month_adj ) = array( $species->day_adj, $species->month_adj );

  tiu( 'month_number', $month_number, array_keys( month_name_array() ) );

  if ( $month_number == mn_ch() ) // Cheshvan
    {
      return $day_adj == 1 ? 30 : 29;
    }

  if ( $month_number == mn_ki() ) // Kislev
    {
      return $day_adj == -1 ? 29 : 30;
    }

  if ( $month_number == mn_ar() ) // Adar Rishon
    {
      return $month_adj ? 30 : 0;
    }

  return 29 + $month_number % 2;
}

function accumulate_days( Species $species, $acc, $month_number )
{
  return $acc + days_per_month( $month_number, $species );
}

function previous_month( $month_number )
{
  return $month_number == mn_min()
    ? mn_max()
    : $month_number - 1;
}

function day_of_year_of_month( Species $species, $month_number )
{
  if ( $month_number == mn_start() )
    {
      return 0;
    }

  $p = previous_month( $month_number );

  if ( $p < mn_start() )
    {
      $range1 = range( mn_start(), mn_max() );
      $range2 = range( mn_min(), $p );
      $mns = array_merge( $range1, $range2 );
    }
  else
  {
    $mns = range( mn_start(), $p );
  }

  $pa = pa( 'accumulate_days', $species );

  return array_reduce( $mns, $pa, 0 );
}

function days_per_year( Species $species )
{
  $mns = range( mn_min(), mn_max() );

  $pa = pa( 'accumulate_days', $species );

  return array_reduce( $mns, $pa, 0 );
}

function hol_dwy( Species $species, Holiday $holiday )
{
  $doyom = day_of_year_of_month( $species, $holiday->month_number );

  return $doyom + $holiday->day_within_month;
}

function all_hol_dwy( Species $species )
{
  $pa = pa( 'hol_dwy', $species );

  return array_map( $pa, holidays() );
}

function all_hol_dwy_and_dpy( Species $species )
{
  return array
    (
     'all_hol_dwy' => all_hol_dwy( $species ),
     'dpy' => days_per_year( $species ),
     );
}

/* day_adj: -1, 0, or 1 (short Kislev, normal, long Cheshvan)
 * month_adj: 0 or 1 (non-leap or leap)
 *
 * I.e. the 6 possible year dpys have the following correspondences
 * to day_adj/month_adj pairs:
 *
 * 353, 354, 355 correspond to (-1,0), (0,0), (1,0)
 * 383, 384, 385 correspond to (-1,1), (0,1), (1,1)
*/

// tiu: throw if unexpected
//
function tiu( $what, $val, $possible_vals )
{
    if ( ! in_array( $val, $possible_vals ) )
      {
        $e = array
          (
           'Unexpected',
           $what,
           $val,
           '.',
           'Expected one of the following:',
           $possible_vals
           );
        throw new ErrorException( var_export( $e, 1 ) );
      }
}

class Species
{
  function __construct( $day_adj, $month_adj )
  {
    tiu( 'day_adj', $day_adj, array( -1, 0, 1 ) );

    tiu( 'month_adj', $month_adj, array( 0, 1 ) );

    $this->day_adj = $day_adj;
    $this->month_adj = $month_adj;
  }
  public $day_adj;
  public $month_adj;
}

class Holiday
{
  function __construct( $month_number, $day_within_month, $name )
  {
    $this->month_number = $month_number;
    $this->day_within_month = $day_within_month;
    $this->name = $name;
  }
  public $month_number;
  public $day_within_month;
  public $name;
}

function ad_to_points( $dpc, $ad, $holidays )
{
  $pa = pa( 'holiday_to_point', $dpc );

  return array_map( $pa, $ad['all_hol_dwy'], $holidays );
}

function ad_to_arcs( $radius, $dpc, $ad )
{
  $dpy = $ad['dpy'];

  $pa = pa( 'two_hol_to_arc', $radius, $dpc, $dpy );

  $ahd = $ad['all_hol_dwy'];

  $adh_rotl = rotate_to_the_left( $ahd );

  return array_map( $pa, abl( $ahd ), abl( $adh_rotl ) );
}

// abl: all_but_last
//
function abl( array $a )
{
  array_pop( $a );
  return $a;
}

function rotate_to_the_left( array $a )
{
  array_push( $a, array_shift( $a ) );
  return $a;
}

// dwy: day within year
// dpc: days per circumference
//
function holiday_to_point( $dpc, $hol_dwy, Holiday $holiday )
{
  $c_attr = array
    (
     'r' => 0.04,
     'stroke' => 'black',
     'stroke-width' => 0.01,
     'fill' => 'none',
     );

  $text_attr = array( 'x' => 0.05, 'y' => -0.05, 'font-size' => 0.05 );

  $label = xml_wrap( 'text', $text_attr, $holiday->name );

  $c = xml_sc_tag( 'circle', $c_attr );

  $clabel = xml_seqa( $c, $label );

  list( $x, $y ) = holiday_to_xy( $dpc, $hol_dwy, $holiday );

  return svg_gtt( $x, $y, $clabel );
}

function holiday_to_xy( $dpc, $hol_dwy )
{
  /* Below, r is the angle, using mathematical conventions, i.e. zero
     at "3 o'clock" and proceeding counter-clockwise. But we'd like to
     use clock conventions, i.e zero at "12 o'clock" and proceeding
     clockwise. So we make s by adding M_PI_2 and negating r.
  */
  /*
    Below, we negate the sine since SVG puts greater y values further
    down. (This is the convention in most computer graphics systems,
    but is not the mathematical convention.) Instead of negating the
    sine, I suppose we could adapt to the SVG convention with a
    scaling transform of -1 on the y axis.
   */

  $radius = 1;

  if ( $hol_dwy == 291 ) { $radius = 0.9; }

  if ( $hol_dwy == 339 ) { $radius = 0.9; }
  if ( $hol_dwy == 340 ) { $radius = 0.8; }

  if ( $hol_dwy == 367 ) { $radius = 0.9; }
  if ( $hol_dwy == 368 ) { $radius = 0.7; }
  if ( $hol_dwy == 369 ) { $radius = 0.5; }
  if ( $hol_dwy == 397 ) { $radius = 0.8; }
  if ( $hol_dwy == 398 ) { $radius = 0.6; }
  if ( $hol_dwy == 399 ) { $radius = 0.4; }

  $r = 2 * M_PI * $hol_dwy / $dpc;

  $s = M_PI_2 - $r;

  $x = $radius * cos( $s );

  $y = $radius * -sin( $s );

  return array( $x, $y );
}

function two_hol_to_arc( $radius, $dpc, $dpy, $hol1_dwy, $hol2_dwy )
{
  $day_diff = $hol2_dwy - $hol1_dwy;

  $pos_day_diff = $day_diff < 0 ? $dpy + $day_diff : $day_diff;

  list( $x1, $y1 ) = holiday_to_xy( $radius, $dpc, $hol1_dwy );

  list( $x2, $y2 ) = holiday_to_xy( $radius, $dpc, $hol2_dwy );

  $line_attr = array
    (
     'x1' => $x1,
     'y1' => $y1,
     'x2' => $x2,
     'y2' => $y2,
     'stroke' => 'black',
     'stroke-width' => 0.01,
     );

  $cx = $x1 + ($x2 - $x1) / 2;
  $cy = $y1 + ($y2 - $y1) / 2;

  $text_attr = array( 'x' => $cx, 'y' => $cy, 'font-size' => 0.05 );

  $label = xml_wrap( 'text', $text_attr, $pos_day_diff );

  $line = xml_sc_tag( 'line', $line_attr );

  return xml_seqa( $label, $line );
}

function maybe_array_reverse( array $a, $reverse )
{
  return $reverse ? array_reverse( $a ) : $a;
}

function plus_final( $ad, $holidays )
{
  $ahd = $ad['all_hol_dwy'];
  $dpy = $ad['dpy'];

  $new_ad = array
    (
     'all_hol_dwy' => append( $ahd, $ahd[0] + $dpy ),
     'dpy' => $dpy
     );

  $end_holiday = new Holiday( $holidays[0]->month_number,
                              $holidays[0]->day_within_month,
                              $holidays[0]->name . '2' );

  $new_holidays = append( $holidays, $end_holiday );

  return array( $new_ad, $new_holidays );
}

function append( array $a, $i )
{
  return array_merge( $a, array( $i ) );
}

function main2_inner( $dpc, $ad )
{
  list ( $a, $h ) = plus_final( $ad, holidays() );

  $points = ad_to_points( $dpc, $a, $h );

  $arcs   = array();//ad_to_arcs( $radius, $dpc, $a );

  return array_merge( $points, $arcs );
}

function radius( Species $species )
{
  list( $day_adj, $month_adj ) = array( $species->day_adj, $species->month_adj );

  // ss: species scalar (0..5)
  //
  $ss = 2 + 2*$day_adj + $month_adj;

  return 1 - 0.1 * $ss;
}

function main2( $dummy_arg )
{
  $all_ad = array_map( 'all_hol_dwy_and_dpy', year_species() );

  $dpc = 365.2421897; // mean tropical year (as of Jan 1 2000)

  $radius = 1;

  $c_attr = array
    (
     'r' => $radius,
     'stroke' => 'black',
     'stroke-width' => 0.01,
     'fill' => 'none',
     );

  $c = svg_circle( $c_attr );

  // $radii = array_map( 'radius', year_species() );

  $pa = pa( 'main2_inner', $dpc );

  $points_and_arcs_aa = array_map( $pa, $all_ad );

  $points_and_arcs = flatten( $points_and_arcs_aa );

  $spa = xml_seq( $points_and_arcs );

  $cp = xml_seqa( $c, $spa );

  $width = 1000;

  $height = 700;

  $scale = 0.475 * min( $width, $height );

  $gts = svg_gts( $scale, $cp );

  $gttc = svg_gtt( $width / 2, $height / 2, $gts );

  $svg = svg_wrap( $width, $height, $gttc );

  return $svg->s;
}

function flatten( array $a ) // $a should be an array of arrays
{
  return array_reduce( $a, 'array_merge', array() );
}

function main( $leapness )
{
  tiu( 'leapness', $leapness, array( 'nonleap', 'yesleap' ) );

  $leapness_bool = $leapness == 'yesleap';

  $all_ad = array_map( 'all_hol_dwy_and_dpy', year_species() );

  $ad_nonleap = $all_ad[1];
  $ad_yesleap = $all_ad[4];

  // ny: nonleap and yesleap
  //
  $ny_ad = array( $ad_nonleap, $ad_yesleap );

  // ps: primary and secondary
  //
  $ps_ad = maybe_array_reverse( $ny_ad, $leapness_bool );

  list( $ad1, $ad2 ) = $ps_ad;

  $dpy1 = $ad1['dpy'];
  $dpy2 = $ad2['dpy'];

  $dpc = $dpy1;

  $dpy_yesleap = $ad_yesleap['dpy'];

  $radius1 = $dpy1 / $dpy_yesleap;
  $radius2 = $dpy2 / $dpy_yesleap;

  $points = ad_to_points( $radius1, $dpc, $ad1, holidays() );
  $arcs   = ad_to_arcs( $radius1, $dpc, $ad1, holidays() );

  $c_attr1 = array
    (
     'r' => $radius1,
     'stroke' => 'black',
     'stroke-width' => 0.01,
     'fill' => 'none',
     );

  $c_attr2 = $c_attr1;
  $c_attr2['r'] = $radius2;

  $c1 = svg_circle( $c_attr1 );

  $c2 = svg_circle( $c_attr2 );

  $sp = xml_seq( $points );

  $sa = xml_seq( $arcs );

  $cp = xml_seqa( $c1, $c2, $sp, $sa );

  $width = 1000;

  $height = 700;

  $scale = 0.475 * min( $width, $height );

  $gts = svg_gts( $scale, $cp );

  $gttc = svg_gtt( $width / 2, $height / 2, $gts );

  $svg = svg_wrap( $width, $height, $gttc );

  return $svg->s;
}

echo main2( $argv[1] );

?>
