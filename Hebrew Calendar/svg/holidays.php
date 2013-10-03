#!/usr/bin/php -q
<?php

require_once 'svg.php';

function holidays()
{
  // month, day, name, [add year]
  return array
    (
     new Holiday( mn_ad(), 14, 'Purim' ),
     new Holiday( mn_ni(), 15, 'Pesach' ),
     new Holiday( mn_si(),  6, 'Shavuot' ),
     new Holiday( mn_ti(), 15, 'Sukkot' ),
     new Holiday( mn_ki(), 25, 'Chanukkah' ),
     new Holiday( mn_sh(), 15, 'Tu B\'Shevat' ),
     new Holiday( mn_ad(), 14, 'Purim2', true ),
     );
}

function all_rosh_chodesh()
{
  // month, day, name, [add year]
  return array
    (
     new Holiday( mn_ad(), 1, 'RChAd' ),
     new Holiday( mn_ni(), 1, 'RChNi' ),
     new Holiday( mn_iy(), 1, 'RChIy' ),
     new Holiday( mn_si(), 1, 'RChSi' ),
     new Holiday( mn_ta(), 1, 'RChTa' ),
     new Holiday( mn_av(), 1, 'RChAv' ),
     new Holiday( mn_el(), 1, 'RChEl' ),
     new Holiday( mn_ti(), 1, 'RChTi' ),
     new Holiday( mn_ch(), 1, 'RChCh' ),
     new Holiday( mn_ki(), 1, 'RChKi' ),
     new Holiday( mn_te(), 1, 'RChTe' ),
     new Holiday( mn_sh(), 1, 'RChSh' ),
     new Holiday( mn_ar(), 1, 'RChAr' ),
     new Holiday( mn_ad(), 1, 'RChAd2', true ),
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

function all_yearlen()
{
  return array
    (
     new YearLen( -1, 0 ),
     new YearLen(  0, 0 ),
     new YearLen(  1, 0 ),
     new YearLen( -1, 1 ),
     new YearLen(  0, 1 ),
     new YearLen(  1, 1 ),
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

function days_per_month( $month_number, YearLen $yl )
{
  list( $day_adj, $month_adj ) = array( $yl->day_adj, $yl->month_adj );

  tiu( 'month_number', $month_number, array_keys( month_name_array() ) );

  $da = 0;

  if ( $month_number == mn_ch() ) // Cheshvan
    {
      $da = $day_adj == 1 ? 1 : 0;
    }

  if ( $month_number == mn_ki() ) // Kislev
    {
      $da = $day_adj == -1 ? -1 : 0;
    }

  if ( $month_number == mn_ar() ) // Adar Rishon
    {
      $da = $month_adj ? 1 : -29;
    }

  return 29 + $month_number % 2 + $da;
}

function accumulate_days( YearLen $yl, $acc, $month_number )
{
  return $acc + days_per_month( $month_number, $yl );
}

function previous_month( $month_number )
{
  return $month_number == mn_min()
    ? mn_max()
    : $month_number - 1;
}

function day_of_year_of_month( YearLen $yl, $month_number )
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

  $pa = pa( 'accumulate_days', $yl );

  return array_reduce( $mns, $pa, 0 );
}

function days_per_year( YearLen $yl )
{
  $mns = range( mn_min(), mn_max() );

  $pa = pa( 'accumulate_days', $yl );

  return array_reduce( $mns, $pa, 0 );
}

function dwy_for_yl_hol( YearLen $yl, Holiday $holiday )
{
  $doyom = day_of_year_of_month( $yl, $holiday->month_number );

  // mdpy: maybe_days_per_year
  //
  $mdpy = $holiday->add_year ? days_per_year( $yl ) : 0;

  return $mdpy + $doyom + $holiday->day_within_month;
}

function dwy_for_hol_yl( Holiday $holiday, YearLen $yl )
{
  return dwy_for_yl_hol( $yl, $holiday );
}

function dwpy_for_hols_yl( array $hols, YearLen $yl )
{
  $dwy_given_yl_for_hol = pa( 'dwy_for_yl_hol', $yl );

  return array
    (
     'dwy_given_yl_for_hols' => array_map( $dwy_given_yl_for_hol, $hols ),
     'dpy_given_yl'          => days_per_year( $yl ),
     );
}

function dwpy_for_yls_hol( array $yls, Holiday $holiday )
{
  $dwy_given_hol_for_yl = pa( 'dwy_for_hol_yl', $holiday );

  return array
    (
     'dwy_given_hol_for_yls' => array_map( $dwy_given_hol_for_yl, $yls ),
     'dpy_for_yls'           => array_map( 'days_per_year', $yls ),
     );
}

function dwpy_for_hols_yls( array $hols, array $yls )
{
  $dwpy_given_hols_for_yl = pa( 'dwpy_for_hols_yl', $hols );

  return array_map( $dwpy_given_hols_for_yl, $yls );
}

function dwpy_for_yls_hols( array $yls, array $hols )
{
  $dwpy_given_yls_for_hol = pa( 'dwpy_for_yls_hol', $yls );

  return array_map( $dwpy_given_yls_for_hol, $hols );
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

class YearLen
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
  function __construct( $month_number,
                        $day_within_month,
                        $name,
                        $add_year = false )
  {
    $this->month_number     = $month_number;
    $this->day_within_month = $day_within_month;
    $this->name             = $name;
    $this->add_year         = $add_year;
  }
  public $month_number;
  public $day_within_month;
  public $name;
  public $add_year;
}

function ad_to_points( $dpc, $ad, $holidays )
{
  $pa = pa( 'holiday_to_point', $dpc );

  return array_map( $pa, $ad['hols_dwy'], $holidays );
}

function ad_to_arcs( $radius, $dpc, $ad )
{
  $dpy = $ad['dpy'];

  $pa = pa( 'two_hol_to_arc', $radius, $dpc, $dpy );

  $ahd = $ad['hols_dwy'];

  $adh_rotl = rotate_to_the_left( $ahd );

  return array_map_wn( $pa, $ahd );
}

// shr: shift right (retain all but the last)
//
function shr( array $a )
{
  array_pop( $a ); return $a;
}

// shl: shift_left (retain all but the first)
//
function shl( array $a )
{
  array_shift( $a ); return $a;
}

// rtl: rotate [to the] left
//
function rtl( array $a )
{
  array_push( $a, array_shift( $a ) ); return $a;
}

// wn: with next, i.e. f( a[i], a[i+1] ) where i in (0..n-2)
//
function array_map_wn( $f, array $a )
{
  // f( a[  0], a[  1] )
  // f( a[  1], a[  2] )
  // ...
  // f( a[n-2], a[n-1] )
  //
  return array_map( $f, shr( $a ), shl( $a ) );
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

  list( $x, $y ) = dwy_to_xy( $dpc, $hol_dwy, $holiday );

  return svg_gtt( $x, $y, $clabel );
}

function svg_for_edge( $dpc, $rf, $edge )
{
  list ( $node1_dwy, $node2_dwy ) = $edge;

  list( $x1, $y1 ) = dwy_to_xy( $dpc, $node1_dwy, $rf );
  list( $x2, $y2 ) = dwy_to_xy( $dpc, $node2_dwy, $rf );

  $rx = 1;

  $ry = 1;

  $x_axis_rotation = 0;

  $large_arc_flag = 0;

  $sweep_flag = 1;

  $path_attr = array
    (
     'd' => ss( 'M',
                $x1, $y1,
                'A',
                $rx, $ry,
                $x_axis_rotation,
                $large_arc_flag,
                $sweep_flag,
                $x2, $y2 ),
     'fill' => 'none',
     'stroke' => 'black',
     'stroke-width' => 0.01,
     );

  $path = xml_sc_tag( 'path', $path_attr );

  return $path;
}

function falloff()
{
  return 0.05;
}

function svg_for_node( $dpc, $rf, $dwy )
{
  $radius = $rf( $dwy );

  $line_attr = array
    (
     'y1' => -$radius,
     'y2' => -$radius + falloff(),
     'stroke' => 'black',
     'stroke-width' => 0.01,
     'transform' => svg_tr1( 360 * $dwy / $dpc ),
     );

  return xml_sc_tag( 'line', $line_attr );
}

function ss()
{
  return implode( ' ', func_get_args() );
}

function radii()
{
  return array
    (
     1 - 0 * falloff(),
     1 - 1 * falloff(),
     1 - 2 * falloff(),
     1 + 3 * falloff(),
     1 + 2 * falloff(),
     1 + 1 * falloff(),
     );
}

/* Below, r is the angle, using mathematical conventions, i.e. zero at
 * "3 o'clock" and proceeding counter-clockwise. But we'd like to use
 * clock conventions, i.e zero at "12 o'clock" and proceeding
 * clockwise. So we make s by adding M_PI_2 and negating r.
 *
 * Below, we negate the sin() since SVG puts greater y values further
 * down. (This is the convention in most computer graphics systems,
 * but is not the mathematical convention.)
 */
function dwy_to_xy( $dpc, $dwy, $rf )
{
  $r = 2 * M_PI * $dwy / $dpc;

  $s = M_PI_2 - $r;

  $radius = $rf( $dwy );

  $x = $radius * cos( $s );

  $y = $radius * -sin( $s );

  return array( $x, $y );
}

function two_hol_to_arc( $radius, $dpc, $dpy, $hol1_dwy, $hol2_dwy )
{
  $day_diff = $hol2_dwy - $hol1_dwy;

  $pos_day_diff = $day_diff < 0 ? $dpy + $day_diff : $day_diff;

  list( $x1, $y1 ) = dwy_to_xy( $radius, $dpc, $hol1_dwy );

  list( $x2, $y2 ) = dwy_to_xy( $radius, $dpc, $hol2_dwy );

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

function append( array $a, $i )
{
  return array_merge( $a, array( $i ) );
}

function main2_inner( $dpc, $holidays, $ad )
{
  $points = ad_to_points( $dpc, $ad, $holidays );

  $arcs   = array();//ad_to_arcs( $radius, $dpc, $a );

  return array_merge( $points, $arcs );
}

function radius2( YearLen $yl )
{
  list( $day_adj, $month_adj ) = array( $yl->day_adj, $yl->month_adj );

  // yls: year len scalar (0..5)
  //
  $yls = 2 + 2*$day_adj + $month_adj;

  return 1 - 0.1 * $yls;
}

function nodes_for_all_da( $all_da )
{
  $aa = array_map( 'nodes_for_one_da', $all_da );

  $f = flatten( $aa );

  return $f;
}

function nodes_for_one_da( array $da )
{
  $dwys = $da['dwy_given_hol_for_yls'];

  $udwys = array_unique( $dwys, SORT_REGULAR );

  $radii = radii();

  $rlim = array_slice( $radii, 0, count( $udwys ) );

  $udwys_to_radii = array_map( 'make_pair', $udwys, $rlim );

  return $udwys_to_radii;
}

function edges_for_all_hd( $all_hd )
{
  $aa = array_map( 'edges_for_one_hd', $all_hd );

  $a = flatten( $aa );

  return array_unique( $a, SORT_REGULAR );
}

function edges_for_one_hd( array $hd )
{
  return array_map_wn( 'make_pair', $hd['dwy_given_yl_for_hols'] );
}

function make_pair( $a, $b )
{
  return array( $a, $b );
}

function deref( array $a, $i )
{
  return $a[ $i ];
}

function pairs_to_kvs( array $a )
{
  return array_combine( firsts( $a ), seconds( $a ) );
}

function firsts( array $pairs )
{
  return array_map( 'first', $pairs );
}

function seconds( array $pairs )
{
  return array_map( 'second', $pairs );
}

function first( array $a ) { return $a[0]; }
function second( array $a ) { return $a[1]; }

function the_drawing( $dpc, $edges, $nodes )
{
  $nodes2 = pairs_to_kvs( $nodes );

  $rf = pa( 'deref', $nodes2 );

  $svg_for_node_dpc_rf = pa( 'svg_for_node', $dpc, $rf );

  $svg_for_nodes = array_map( $svg_for_node_dpc_rf, firsts( $nodes ) );

  $svg_for_edge_dpc_rf = pa( 'svg_for_edge', $dpc, $rf );

  $svg_for_edges = array_map( $svg_for_edge_dpc_rf, $edges );

  $ne = array_merge( $svg_for_edges, $svg_for_nodes );

  return xml_seq( $ne );
}

function main2( $dummy_arg )
{
  $holidays = holidays();
  //$holidays = all_rosh_chodesh();

  $all_da = dwpy_for_yls_hols( all_yearlen(), $holidays );

  $all_ad = dwpy_for_hols_yls( $holidays, all_yearlen() );

  $dpc = 365.2421897; // mean tropical year (as of Jan 1 2000)

  $nodes = nodes_for_all_da( $all_da );

  $edges = edges_for_all_hd( $all_ad );

  $drawing = the_drawing( $dpc, $edges, $nodes );

  $width = 700;

  $height = 700;

  $bounding_box = svg_rect( array(
                                  'width' => $width,
                                  'height' => $height,
                                  'stroke' => 'black',
                                  'stroke-width' => 3,
                                  'fill' => 'none',
                                  ) );

  $scale = 0.425 * min( $width, $height );

  $transforms = array
    (
     svg_tt1( $width / 2, $height / 2 ),
     svg_ts1( $scale ),
     );

  $g_attr = svg_transf2( implode( ' ', $transforms ) );

  $g = svg_g( $g_attr, $drawing );

  $bg = xml_seqa( $bounding_box, $g );

  $svg = svg_wrap( $width, $height, $bg );

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

  $all_ad = array_map( 'hols_dwy_and_dpy', all_yearlen() );

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
