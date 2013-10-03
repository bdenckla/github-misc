#!/usr/bin/php -q
<?php

require_once 'svg.php';

function holidays()
{
  // month, day, name in Latin characters, name in Hebrew characters, [whether to add year]
  $r = array
    (
     new Holiday( mn_ad(), 14, 'Purim',        'פורים' ),
     new Holiday( mn_ni(), 15, 'Pesach',       'פסח' ),
     new Holiday( mn_si(),  6, 'Shavuot',      'שבועות' ),
     new Holiday( mn_ti(), 15, 'Sukkot',       'סוכות' ),
     new Holiday( mn_ki(), 25, 'Chanukkah',    'חנוכה' ),
     new Holiday( mn_sh(), 15, 'Tu B\'Shevat', 'טו בשבט' ),
     );

  return add_final_holiday( $r );
}

function month_name_array()
{
  return array
    (
     mn_ad() => array( 'Adar'     , 'אדר' ), // and Adar Sheni
     mn_ni() => array( 'Nisan'    , 'ניסן' ),
     mn_iy() => array( 'Iyyar'    , 'אייר' ),
     mn_si() => array( 'Sivan'    , 'סיון' ),
     mn_ta() => array( 'Tammuz'   , 'תמוז' ),
     mn_av() => array( 'Av'       , 'אב' ),
     mn_el() => array( 'Elul'     , 'אלול' ),
     mn_ti() => array( 'Tishrei'  , 'תשרי' ),
     mn_ch() => array( 'Cheshvan' , 'חשון' ),
     mn_ki() => array( 'Kislev'   , 'כסלו' ),
     mn_te() => array( 'Tevet'    , 'טבת' ),
     mn_sh() => array( 'Shevat'   , 'שבט' ),
     mn_ar() => array( 'Adar R'   , 'אדר א׳' ), // R for Rishon
     );
}

function all_rosh_chodesh()
{
  $r = array_map_wk( 'rosh_chodesh', month_name_array() );

  return add_final_holiday( $r );
}

function rosh_chodesh( $month_number, $month_names )
{
  list ( $lc_name, $hc_name ) = $month_names;

  $day_within_month = 1;

  return new Holiday( $month_number, $day_within_month, $lc_name, $hc_name );
}

function add_final_holiday( array $r )
{
  $first = $r[0];

  $add_year = true;

  $final = new Holiday( $first->month_number,
                        $first->day_within_month,
                        $first->name_using_latin_chars,
                        $first->name_using_hebrew_chars,
                        $add_year );

  return append( $r, $final );
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

function dwy_for_yls_hol( array $yls, Holiday $holiday )
{
  $dwy_given_hol_for_yl = pa( 'dwy_for_hol_yl', $holiday );

  return array_map( $dwy_given_hol_for_yl, $yls );
}

function dwpy_for_hols_yls( array $hols, array $yls )
{
  $dwpy_given_hols_for_yl = pa( 'dwpy_for_hols_yl', $hols );

  return array_map( $dwpy_given_hols_for_yl, $yls );
}

function dwpy_for_yls_hols( array $yls, array $hols )
{
  $dwy_given_yls_for_hol = pa( 'dwy_for_yls_hol', $yls );

  return array
    (
     'dwy_given_yls_for_hols' => array_map( $dwy_given_yls_for_hol, $hols ),
     'dpy_for_yls'            => array_map( 'days_per_year', $yls ),
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
                        $name_using_latin_chars,
                        $name_using_hebrew_chars,
                        $add_year = false )
  {
    $this->month_number            = $month_number;
    $this->day_within_month        = $day_within_month;
    $this->name_using_latin_chars  = $name_using_latin_chars;
    $this->name_using_hebrew_chars = $name_using_hebrew_chars;
    $this->add_year                = $add_year;
  }
  public $month_number;
  public $day_within_month;
  public $name_using_latin_chars;
  public $name_using_hebrew_chars;
  public $add_year;
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

// wk: with keys, i.e. f( k, v ) for array( k => v );
//
function array_map_wk( $f, array $a )
{
  return array_map( $f, array_keys( $a ), $a );
}

// dwy: day within year
// dpc: days per circumference

function holiday_label( $x, $y, Holiday $holiday )
{
  $text_attr = array( 'x' => $x, 'y' => $y, 'font-size' => 0.05 );

  $label = xml_wrap( 'text', $text_attr, $holiday->name_using_hebrew_chars );

  return $label;
}

function svg_for_diff( $x1, $y1, $x2, $y2, $diff )
{
  $cx = $x1 + ($x2 - $x1) / 2;
  $cy = $y1 + ($y2 - $y1) / 2;

  $text_attr = array( 'x' => $cx, 'y' => $cy, 'font-size' => 0.05 );

  $label = xml_wrap( 'text', $text_attr, $diff );

  return $label;
}

function svg_for_edge( $dpc, $rf, $edge )
{
  list ( $node1_dwy, $node2_dwy ) = $edge;

  list( $x1, $y1 ) = dwy_to_xy( $dpc, $node1_dwy, $rf );
  list( $x2, $y2 ) = dwy_to_xy( $dpc, $node2_dwy, $rf );

  $sfd = svg_for_diff( $x1, $y1, $x2, $y2, $node2_dwy - $node1_dwy );

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

  return xml_seqa( $path, $sfd );
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

function one() { return 1; }

function svg_for_node_label( $dpc, $dl_pair )
{
  list ( $dwy, $holiday ) = $dl_pair;

  $rf = 'one';

  list( $x, $y ) = dwy_to_xy( $dpc, $dwy, $rf );

  return holiday_label( $x, $y, $holiday );
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
     1 - 0 * falloff(),
     1 - 1 * falloff(),
     1 - 2 * falloff(),
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

function append( array $a, $i )
{
  return array_merge( $a, array( $i ) );
}

function nodes_for_all_da( $dpc, $all_da, $holidays )
{
  $dwy_given_yls_for_hols = $all_da['dwy_given_yls_for_hols'];

  $dwy_given_yls_for_first_hol = $dwy_given_yls_for_hols[0];

  $min_dwy_of_first_hol = min( $dwy_given_yls_for_first_hol );

  $pa = pa( 'nodes_for_one_hol', $dpc, $min_dwy_of_first_hol );

  $aa = array_map( $pa, $dwy_given_yls_for_hols );

  $f = flatten( $aa );

  $laa = array_map( 'node_labels_for_one_hol', $dwy_given_yls_for_hols, $holidays );

  $fl = flatten( $laa );

  return array( 'dr_pairs' => $f, 'dl_pairs' => $fl );
}

// mdofh: min dwy of first holiday
//
function nodes_for_one_hol( $dpc, $mdofh, array $dwys )
{
  $udwys = array_values( array_unique( $dwys, SORT_REGULAR ) );

  $pa = pa( 'radii2', $dpc, $mdofh );

  // dr_pairs: (days within week, radius) pairs
  //
  $dr_pairs = array_map_wk( $pa, $udwys );

  return $dr_pairs;
}

function node_labels_for_one_hol( array $dwys, Holiday $holiday )
{
  $min = min( $dwys );
  $max = max( $dwys );

  $min_label = array( $min, $holiday );
  $max_label = array( $max, $holiday );

  $big_diff = $max - $min > 3;

  return $big_diff
    ? array( $min_label, $max_label )
    : array( $min_label );
}

function radii2( $dpc, $mdofh, $index_within_da, $dwy )
{
  $radii = radii();

  $r = $radii[ $index_within_da ];

  $ofs = 3*falloff();

  $max = $mdofh + $dpc;

  $r2 = $dwy > $max ? $r + $ofs : $r;

  return array( $dwy, $r2 );
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
  $dr_pairs = $nodes['dr_pairs'];
  $dl_pairs = $nodes['dl_pairs'];

  $dr_pairs2 = pairs_to_kvs( $dr_pairs );

  $rf = pa( 'deref', $dr_pairs2 );

  $svg_for_node_dpc_rf = pa( 'svg_for_node', $dpc, $rf );

  $svg_for_nodes = array_map( $svg_for_node_dpc_rf, firsts( $dr_pairs ) );

  $svg_for_node_label_dpc = pa( 'svg_for_node_label', $dpc );

  $svg_for_node_labels = array_map( $svg_for_node_label_dpc, $dl_pairs );

  $svg_for_edge_dpc_rf = pa( 'svg_for_edge', $dpc, $rf );

  $svg_for_edges = array_map( $svg_for_edge_dpc_rf, $edges );

  $ne = array_merge( $svg_for_edges, $svg_for_nodes, $svg_for_node_labels );

  return xml_seq( $ne );
}
/*
 * var_export( array( $all_da, $all_ad ) );
 *
 *
 * array (
 *   0 =>
 *   array (
 *     'dwy_given_yls_for_hols' =>
 *     array (
 *       0 =>
 *       array (
 *         0 => 14,
 *         1 => 14,
 *         2 => 14,
 *         3 => 14,
 *         4 => 14,
 *         5 => 14,
 *       ),
 *       1 =>
 *       array (
 *         0 => 44,
 *         1 => 44,
 *         2 => 44,
 *         3 => 44,
 *         4 => 44,
 *         5 => 44,
 *       ),
 *       2 =>
 *       array (
 *         0 => 94,
 *         1 => 94,
 *         2 => 94,
 *         3 => 94,

 *         4 => 94,
 *         5 => 94,
 *       ),
 *       3 =>
 *       array (
 *         0 => 221,
 *         1 => 221,
 *         2 => 221,
 *         3 => 221,
 *         4 => 221,
 *         5 => 221,
 *       ),
 *       4 =>
 *       array (
 *         0 => 290,
 *         1 => 290,
 *         2 => 291,
 *         3 => 290,
 *         4 => 290,
 *         5 => 291,
 *       ),
 *       5 =>
 *       array (
 *         0 => 338,
 *         1 => 339,
 *         2 => 340,
 *         3 => 338,
 *         4 => 339,
 *         5 => 340,
 *       ),
 *       6 =>
 *       array (
 *         0 => 367,
 *         1 => 368,
 *         2 => 369,
 *         3 => 397,
 *         4 => 398,
 *         5 => 399,
 *       ),
 *     ),
 *     'dpy_for_yls' =>
 *     array (
 *       0 => 353,
 *       1 => 354,
 *       2 => 355,
 *       3 => 383,
 *       4 => 384,
 *       5 => 385,
 *     ),
 *   ),
 *   1 =>
 *   array (
 *     0 =>
 *     array (
 *       'dwy_given_yl_for_hols' =>
 *       array (
 *         0 => 14,
 *         1 => 44,
 *         2 => 94,
 *         3 => 221,
 *         4 => 290,
 *         5 => 338,
 *         6 => 367,
 *       ),
 *       'dpy_given_yl' => 353,
 *     ),
 *     1 =>
 *     array (
 *       'dwy_given_yl_for_hols' =>
 *       array (
 *         0 => 14,
 *         1 => 44,
 *         2 => 94,
 *         3 => 221,
 *         4 => 290,
 *         5 => 339,
 *         6 => 368,
 *       ),
 *       'dpy_given_yl' => 354,
 *     ),
 *     2 =>
 *     array (
 *       'dwy_given_yl_for_hols' =>
 *       array (
 *         0 => 14,
 *         1 => 44,
 *         2 => 94,
 *         3 => 221,
 *         4 => 291,
 *         5 => 340,
 *         6 => 369,
 *       ),
 *       'dpy_given_yl' => 355,
 *     ),
 *     3 =>
 *     array (
 *       'dwy_given_yl_for_hols' =>
 *       array (
 *         0 => 14,
 *         1 => 44,
 *         2 => 94,
 *         3 => 221,
 *         4 => 290,
 *         5 => 338,
 *         6 => 397,
 *       ),
 *       'dpy_given_yl' => 383,
 *     ),
 *     4 =>
 *     array (
 *       'dwy_given_yl_for_hols' =>
 *       array (
 *         0 => 14,
 *         1 => 44,
 *         2 => 94,
 *         3 => 221,
 *         4 => 290,
 *         5 => 339,
 *         6 => 398,
 *       ),
 *       'dpy_given_yl' => 384,
 *     ),
 *     5 =>
 *     array (
 *       'dwy_given_yl_for_hols' =>
 *       array (
 *         0 => 14,
 *         1 => 44,
 *         2 => 94,
 *         3 => 221,
 *         4 => 291,
 *         5 => 340,
 *         6 => 399,
 *       ),
 *       'dpy_given_yl' => 385,
 *     ),
 *   ),
 * )
 */

function main2( $dummy_arg )
{
  $holidays = holidays();
  //$holidays = all_rosh_chodesh();

  $all_yearlen = all_yearlen();

  $yearlens = $all_yearlen;
  //$yearlens = array( $all_yearlen[3] );

  $all_da = dwpy_for_yls_hols( $yearlens, $holidays );

  $all_ad = dwpy_for_hols_yls( $holidays, $yearlens );

  $dpc = 365.2421897; // mean tropical year (as of Jan 1 2000)

  $nodes = nodes_for_all_da( $dpc, $all_da, $holidays );

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

echo main2( $argv[1] );

?>
