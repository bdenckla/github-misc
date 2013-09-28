#!/usr/bin/php -q
<?php

require_once 'svg.php';

function holidays()
{
  // month, day, name
  return array
    (
     array( mn_ad(), 14, 'Purim' ),
     array( mn_ni(), 15, 'Pesach' ),
     array( mn_si(),  6, 'Shavuot' ),
     array( mn_ti(), 15, 'Sukkot' ),
     array( mn_ki(), 25, 'Chanukkah' ),
     array( mn_sh(), 15, 'Tu B\'Shevat' ),
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

function hol_dwy( Species $species, array $holiday )
{
  list ( $month, $day_of_month, $name ) = $holiday;

  $doyom = day_of_year_of_month( $species, $month );

  return $doyom + $day_of_month;
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

function ad_to_points( $radius, $ad )
{
  $pa = pa( 'holiday_to_point', $radius, $ad['dpy'] );

  return array_map( $pa, $ad['all_hol_dwy'], holidays() );
}

function ad_to_arcs( $radius, $ad )
{
  $dpy = $ad['dpy'];

  $pa = pa( 'two_hol_to_arc', $radius, $dpy );

  $ahd = $ad['all_hol_dwy'];

  $adhr = rotate( $dpy, $ahd );

  return array_map( $pa, $ahd, $adhr, holidays() );
}

function rotate( $dpy, array $a )
{
  $old_first = array_shift( $a );
  $new_last = $old_first + $dpy;
  array_push( $a, $new_last );
  return $a;
}

// dwy: day within year
//
function holiday_to_point( $radius, $dpy, $hol_dwy, $holiday )
{
  $r = 2 * M_PI * $hol_dwy / $dpy;

  $s = M_PI_2 - $r;

  $c_attr = array
    (
     'r' => 0.04,
     'stroke' => 'black',
     'stroke-width' => 0.01,
     'fill' => 'none',
     );

  $text_attr = array( 'x' => 0.05, 'y' => -0.05, 'font-size' => 0.05 );

  list ( $month, $day_of_month, $name ) = $holiday;

  $label = xml_wrap( 'text', $text_attr, $name );

  $c = xml_sc_tag( 'circle', $c_attr );

  $clabel = xml_seqa( $c, $label );

  $x = $radius * cos( $s );

  $y = $radius * -sin( $s );

  $path_attr = array
    (
     'd' => 'M 0 0 a 1 1 30 0 1 ' . $x . ' ' . $y,
     'stroke' => 'black',
     'stroke-width' => 0.01,
     'fill' => 'none',
     );

  return xml_sc_tag( 'path', $path_attr );

  return svg_gtt( $x, $y, $clabel );
}

function two_hol_to_arc( $radius, $dpy, $hol1_dwy, $hol2_dwy, $holiday )
{
  $hol_dwy = $hol2_dwy - $hol1_dwy;

  $r = 2 * M_PI * $hol_dwy / $dpy;

  $s = M_PI_2 - $r;

  $c_attr = array
    (
     'r' => 0.04,
     'stroke' => 'black',
     'stroke-width' => 0.01,
     'fill' => 'none',
     );

  $text_attr = array( 'x' => 0.05, 'y' => -0.05, 'font-size' => 0.05 );

  list ( $month, $day_of_month, $name ) = $holiday;

  $label = xml_wrap( 'text', $text_attr, $name );

  $c = xml_sc_tag( 'circle', $c_attr );

  $clabel = xml_seqa( $c, $label );

  $x = $radius * cos( $s );

  $y = $radius * -sin( $s );

  $path_attr = array
    (
     'd' => 'M 0 0 a 1 1 30 0 1 ' . $x . ' ' . $y,
     'stroke' => 'black',
     'stroke-width' => 0.01,
     'fill' => 'none',
     );

  //return xml_sc_tag( 'path', $path_attr );

  return svg_gtt( $x, $y, $clabel );
}

function maybe_array_reverse( array $a, $reverse )
{
  return $reverse ? array_reverse( $a ) : $a;
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

  $dpy_yesleap = $ad_yesleap['dpy'];

  $radius1 = $dpy1 / $dpy_yesleap;
  $radius2 = $dpy2 / $dpy_yesleap;

  $points = ad_to_arcs( $radius1, $ad1 );

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

  $cp = xml_seqa( $c1, $c2, $sp );

  $width = 1000;

  $height = 700;

  $scale = 0.475 * min( $width, $height );

  $gts = svg_gts( $scale, $cp );

  $gttc = svg_gtt( $width / 2, $height / 2, $gts );

  $svg = svg_wrap( $width, $height, $gttc );

  return $svg->s;
}

echo main( $argv[1] );

?>
