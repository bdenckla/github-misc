#!/usr/bin/php -q
<?php

   /* TODO: to get diff label placement right, we need to treat edges
    * more like we do nodes, i.e. consider the cluster to which they
    * belong. I.e. a cluster label is placed relative to the min
    * element of that cluster, and diff labels need to be placed
    * relative to the min element of their cluster (of edges). Maybe
    * coalesce them into a single label, i.e. instead of 3 separate
    * 30s, "30 30 30" or "30 x3"?
    *
    * TODO: how to show path divergence in Rosh Chodesh case. And
    * related problem, suppressing showing zero-length month.
    *
    * TODO: use smaller radius for leap paths to make leap paths loop
    * way out of the way of other things.
    *
    * TODO: instead of leap/nonleap, produce major holidays/rosh
    * chodesh as separate files.
    *
    * Rescuscitate leap/nonleap in "figure 8" view.
    *
    * Show seasonal spread.
    *
    * Show units (360 degrees = 1 tropical year (365.25...))
    *
    * Show accompanying data table
    *
    * Show absolute dwy, not just diffs. Show on graph or in
    * accompanying table?
    *
    * Show near-symmetry surrounding Purim in non-leap years (almost
    * 50,30,30,50 diffs).
    *
    * Show near-opposition of Pesach and Sukkot and perhaps
    * near-opposition of Shavuot and Channukah.
    *
    */

require_once 'svg.php';

function holidays()
{
  // month number, day offset, name in Latin characters, name in Hebrew characters
  // usually day offset is day within month, but it can be anything
  //
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

function month_names()
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
  $r = array_map_wk( 'rosh_chodesh', month_names() );

  return add_final_holiday( $r );
}

function rosh_chodesh( $month_number, $month_names )
{
  list ( $lc_name, $hc_name ) = $month_names;

  $day_offset = 1;

  return new Holiday( $month_number, $day_offset, $lc_name, $hc_name );
}

function add_final_holiday( array $r )
{
  $first = $r[0];

  $add_year = true;

  $final = new Holiday( $first->month_number,
                        $first->day_offset,
                        '',
                        '',
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

function dummy_yearlen()
{
  return array
    (
     new YearLen( -1, 0 ),
     );
}

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

function append( array $a, $i )
{
  // Below, we could have also used array_push. It would only be
  // locally destructive due to call-by-value.
  //
  return array_merge( $a, array( $i ) );
}

function prepend( array $a, $i )
{
  // Below, we could have also used array_unshift. It would only be
  // locally destructive due to call-by-value.
  //
  return array_merge( array( $i ), $a );
}

function make_pair( $a, $b )
{
  return array( $a, $b );
}

function flipped_make_pair( $a, $b )
{
  return array( $b, $a );
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

function flatten( array $a ) // $a should be an array of arrays
{
  return array_reduce( $a, 'array_merge', array() );
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

function array_map_pa( $f, $a, array $b )
{
  return array_map( pa( $f, $a ), $b );
}

function days_per_month( YearLen $yl, $month_number )
{
  list( $day_adj, $month_adj ) = array( $yl->day_adj, $yl->month_adj );

  tiu( 'month_number', $month_number, array_keys( month_names() ) );

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

function previous_month( $month_number )
{
  // Below, we could use modulo operator, but why bother?
  //
  return $month_number == mn_min() ? mn_max() : $month_number - 1;
}

// dwy of rc: day within year of Rosh Chodesh (first of the month)
//
function dwy_of_rc( YearLen $yl, $month_number )
{
  if ( $month_number == mn_start() )
    {
      return 0;
    }

  $p = previous_month( $month_number );

  // Below, we could use modulo operator, but why bother?
  //
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

  return sum_of_days_per_month( $yl, $mns );
}

function all_mns()
{
  return range( mn_min(), mn_max() );
}

function days_per_year( YearLen $yl )
{
  return sum_of_days_per_month( $yl, all_mns() );
}

function sum_of_days_per_month( YearLen $yl, array $mns )
{
  return array_sum( array_map_pa( 'days_per_month', $yl, $mns ) );
}

function min_shabbat() { return 0; }

function shabbats()
{
  return array_map( 'shabbat', range( min_shabbat(), 60 ) );
}

function shabbat( $n )
{
  $month_number = mn_start();

  // dfms: distance_from_min_shabbat
  //
  $dfms = $n - min_shabbat();

  // usually the day offset is the day within the month but here we
  // use it as day within year
  //
  $day_offset = 7 * $dfms + 1; // i.e. 1, 8, 15, 22, 29, 36

  $name = $n;

  list ( $lc_name, $hc_name ) = array( $name, $name );

  return new Holiday( $month_number, $day_offset, $lc_name, $hc_name );
}

function dwy_for_yl_hol( YearLen $yl, Holiday $hol )
{
  $dwyom = dwy_of_rc( $yl, $hol->month_number );

  // mdpy: maybe_days_per_year
  //
  $mdpy = $hol->add_year ? days_per_year( $yl ) : 0;

  return $mdpy + $dwyom + $hol->day_offset;
}

function dwy_for_hol_yl( Holiday $hol, YearLen $yl )
{
  return dwy_for_yl_hol( $yl, $hol );
}

function dwpy_for_hols_yl( array $hols, YearLen $yl )
{
  return array
    (
     'dwy_given_yl_for_hols' => array_map_pa( 'dwy_for_yl_hol', $yl, $hols ),
     );
}

function dwy_for_yls_hol( array $yls, Holiday $hol )
{
  return array_map_pa( 'dwy_for_hol_yl', $hol, $yls );
}

function dwpy_for_hols_yls( array $hols, array $yls )
{
  return array_map_pa( 'dwpy_for_hols_yl', $hols, $yls );
}

function dwpy_for_yls_hols( array $yls, array $hols )
{
  return array
    (
     'dwy_given_yls_for_hols' => array_map_pa( 'dwy_for_yls_hol', $yls, $hols ),
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

class YearLen
{
  function __construct( $day_adj, $month_adj )
  {
    tiu( 'day_adj',   $day_adj,   array( -1, 0, 1 ) );
    tiu( 'month_adj', $month_adj, array( 0, 1 ) );

    $this->day_adj   = $day_adj;
    $this->month_adj = $month_adj;
  }
  public $day_adj;
  public $month_adj;
}

class Holiday
{
  function __construct( $month_number,
                        $day_offset,
                        $name_using_latin_chars,
                        $name_using_hebrew_chars,
                        $add_year = false )
  {
    $this->month_number            = $month_number;
    $this->day_offset              = $day_offset;
    $this->name_using_latin_chars  = $name_using_latin_chars;
    $this->name_using_hebrew_chars = $name_using_hebrew_chars;
    $this->add_year                = $add_year;
  }
  public $month_number;
  public $day_offset;
  public $name_using_latin_chars;
  public $name_using_hebrew_chars;
  public $add_year;
}

class DrPair
{
  function __construct( array $dwys, $dwy )
  {
    $this->dwys = $dwys;
    $this->dwy  = $dwy;
  }
  public $dwys;
  public $dwy;
}

function make_dr_pair( $a, $b ) { return new DrPair( $a, $b ); }

function dr_pair_dwy( DrPair $d ) { return $d->dwy; }

function dr_pair_dwys( DrPair $d ) { return $d->dwys; }

function dr_pairs_to_kvs( array $a )
{
  $keys   = array_map( 'dr_pair_dwy',  $a );
  $values = array_map( 'dr_pair_dwys', $a );

  return array_combine( $keys, $values );
}

// dwy: day within year
// dpc: days per circumference

function svg_for_ec_label( $dpc, $dr_kvs, $dwy1, $dwy2, $label )
{
  $average_dwy = ($dwy1 + $dwy2) / 2;

  $r = dradius2( $dr_kvs, $dwy2 );

  $r2 = $r - 2 * falloff();

  $d = 360 * $average_dwy / $dpc;

  $a = $d < 180 ? 'end' : 'start';
  $a = 'middle';

  $transforms = array
    (
     svg_tr1( $d ),
     svg_tt1( 0, -$r2 ),
     svg_tr1( -$d ),
     );

  $text_attr = array( 'transform' => implode( ' ', $transforms ),
                      'text-anchor' => $a,
                      'font-size' => 0.05 );

  return svg_text( $text_attr, $label );
}

function svg_for_edge( $dpc, $dr_kvs, $edge )
{
  list ( $dwy1, $dwy2 ) = $edge;

  list( $x1, $y1 ) = dwy_to_xy( $dpc, $dwy1, $dr_kvs );
  list( $x2, $y2 ) = dwy_to_xy( $dpc, $dwy2, $dr_kvs );

  $r = nradius2( $dr_kvs, $dwy1 );

  $rx = $r;

  $ry = $r;

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

function cv_as_string( $value, $count )
{
  return $count > 1 ? $value .'×'. $count : $value;
}

function svg_for_edge_cluster( $dpc, $dr_kvs, $edge_cluster )
{
  $edge_lens = array_map( 'edge_len', $edge_cluster );

  $cvs_of_els = array_count_values( $edge_lens );

  $strs_of_cvs_of_els = array_map_wk( 'cv_as_string', $cvs_of_els );

  $edge_lens_as_string = implode( ', ', $strs_of_cvs_of_els );

  list ( $dwy1, $dwy2 ) = $edge_cluster[count($edge_cluster)-1];

  $sfd = svg_for_ec_label( $dpc, $dr_kvs, $dwy1, $dwy2, $edge_lens_as_string );

  return $sfd;
}

function edge_len( array $edge )
{
  return $edge[1] - $edge[0];
}

function falloff()
{
  return 0.05;
}

function svg_for_node( $dpc, DrPair $dr_pair )
{
  $dwys = $dr_pair->dwys;
  $dwy  = $dr_pair->dwy;

  $r = nradius( $dwys, $dwy );

  $line_attr = array
    (
     'y1' => -$r,
     'y2' => -$r + falloff(),
     'stroke' => 'black',
     'stroke-width' => 0.01,
     'transform' => svg_tr1( 360 * $dwy / $dpc ),
     );

  return xml_sc_tag( 'line', $line_attr );
}

function mod( $a, $b )
{
  return ($a % $b) + ($a < 0 ? $b : 0);
}

function div( $a, $b )
{
  return (integer) ( $a / $b );
}

function svg_for_node_label( $dpc, $dr_kvs, $dl_pair )
{
  list ( $dwy, $hol ) = $dl_pair;

  $r = nradius2( $dr_kvs, $dwy );

  $r2 = $r - 1.2 * falloff();

  $d = 360 * $dwy / $dpc;

  $font_size = 0.07;

  $string = $hol->name_using_hebrew_chars;

  $bbox = bbox( $string, $font_size );

  $width = $bbox[0];
  $height = $bbox[1];

  $t = 2 * M_PI * $dwy / $dpc;

  $s = M_PI_2 - $t;

  $rectx = 0.5 * $width * -( 1 + cos( $s ) );
  $recty = 0.5 * $height * -( 1 + sin( -$s ) );

  $transforms = array
    (
     svg_tr1( $d ),
     svg_tt1( 0, -$r2 ),
     svg_tr1( -$d ),
     svg_tt1( $rectx, $recty ),
     );

  $xpad = $font_size / 12;
  $ypad = $font_size / 5;

  $text_attr = array( 'font-size' => $font_size,
                      'x' => $xpad,
                      'y' => $height - $ypad );

  $text = svg_text( $text_attr, $string );

  $rect = svg_rect( array(
                          'width' => $width,
                          'height' => $height,
                          'stroke' => 'black',
                          'stroke-width' => $font_size / 20,
                          'fill' => 'none',
                          ) );

  $g_attr = array( 'transform' => implode( ' ', $transforms ) );

  //$show_rect = false;
  $show_rect = true;

  // mrect: maybe rect
  //
  $mrect = $show_rect ? array( $rect ) : array();

  $elements = append( $mrect, $text );

  $g = svg_g( $g_attr, xml_seq( $elements ) );

  return $g;
}

function bbox( $string, $font_size )
{
  if ( is_int( $string ) )
    {
      $chars = $string === 0 ? 1 : 1 + floor( log10( $string ) );
      $width = $font_size * 0.7 * $chars;
      $height = $font_size * 1.1;
      return array( $width, $height );
    }

  $chars = mb_strlen( $string, 'UTF-8' );
  $width = $font_size * 0.7 * $chars;
  $height = $font_size * 1.1;
  return array( $width, $height );
}

function ss()
{
  return implode( ' ', func_get_args() );
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
function dwy_to_xy( $dpc, $dwy, $dr_kvs, $dwy_for_rf = NULL )
{
  list ( $r, $x, $y ) = dwy_to_rxy( $dpc, $dwy, $dr_kvs, $dwy_for_rf );

  return array( $r * $x, $r * $y );
}

function dwy_to_rxy( $dpc, $dwy, $dr_kvs, $dwy_for_rf = NULL )
{
  $actual_dwy_for_rf = is_null( $dwy_for_rf ) ? $dwy : $dwy_for_rf;

  $r = nradius2( $dr_kvs, $actual_dwy_for_rf );

  return prepend( dwy_to_uxy( $dpc, $dwy ), $r );
}

// uxy: unit x and y, i.e. x and y on the unit circle
//
function dwy_to_uxy( $dpc, $dwy )
{
  $t = 2 * M_PI * $dwy / $dpc;

  $s = M_PI_2 - $t;

  $x = cos( $s );

  $y = -sin( $s );

  return array( $x, $y );
}

// sr: sort regular [and] renumber
//
function array_unique_srr( array $a )
{
  return array_values( array_unique( $a, SORT_REGULAR ) );
}

function nodes_for_all_da( $dpc, $all_da, $hols )
{
  $in = $all_da['dwy_given_yls_for_hols'];

  $udwy_given_yls_for_hols = array_map( 'array_unique_srr', $in );

  $aa = array_map( 'nodes_for_one_hol', $udwy_given_yls_for_hols );

  $faa = flatten( $aa );

  $laa = array_map( 'dl_pairs_for_one_hol', $udwy_given_yls_for_hols, $hols );

  $flaa = flatten( $laa );

  return array( 'dr_pairs' => $faa, 'dl_pairs' => $flaa );
}

function nodes_for_one_hol( array $dwys )
{
  return array_map_pa( 'make_dr_pair', $dwys, $dwys );
}

function nradius2( $dr_kvs, $dwy )
{
  return radius2( $dr_kvs, $dwy, node_radii() );
}

function dradius2( $dr_kvs, $dwy )
{
  return radius2( $dr_kvs, $dwy, ec_label_radii() );
}

function nradius( $dwys, $dwy )
{
  return radius( $dwys, $dwy, node_radii() );
}

function radius2( $dr_kvs, $dwy, $radii )
{
  return radius( $dr_kvs[ $dwy ], $dwy, $radii );
}

function radius( $dwys, $dwy, $radii )
{
  tiu( 'dwy', $dwy, $dwys );

  $index_within = array_search( $dwy, $dwys );

  $spiral = 1 + 0.1 * ( $dwy / 365.25 );

  return $spiral * $radii[ $index_within ];
}

function node_radii()
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

function ec_label_radii()
{
  return array
    (
     1 - 1 * falloff(),
     1 - 1 * falloff(),
     1 - 1 * falloff(),
     1 - 1 * falloff(),
     1 - 1 * falloff(),
     1 - 1 * falloff(),
     );
}

function cluster( array $dwys )
{
  if ( empty( $dwys ) ) { return $dwys; }

  $p = partition_w2m( $dwys );

  return prepend( cluster( $p[1] ), $p[0] );
}

function cluster_edges( array $edges )
{
  if ( empty( $edges ) ) { return $edges; }

  $p = partition_edges_w2m( $edges );

  return prepend( cluster_edges( $p[1] ), $p[0] );
}

// w2m: within 2 of min
//
function partition_w2m( array $dwys )
{
  $within_2_of_min = pa( 'within_2', min( $dwys ) );

  return partition( $within_2_of_min, $dwys );
}

function partition_edges_w2m( array $edges )
{
  $within_2_of_min = pa( 'edges_within_2', min( $edges ) );

  return partition( $within_2_of_min, $edges );
}

function within_2( $x, $y ) { return abs( $x - $y ) <= 2; }

function edges_within_2( $x, $y )
{
  return
    within_2( $x[0], $y[0] )
    &&
    within_2( $x[1], $y[1] );
}

function partition( $f, array $a )
{
  $n = pa( 'not', $f );
  return array( array_values( array_filter( $a, $f ) ),
                array_values( array_filter( $a, $n ) ) );
}

function not( $f, $a ) { return ! $f( $a ); }

function dl_pairs_for_one_hol( array $dwys, Holiday $hol )
{
  $cdwys = cluster( $dwys );

  $mc = array_map( 'max', $cdwys );

  return array_map_pa( 'flipped_make_pair', $hol, $mc );
}

function edges_for_all_ad( $all_ad )
{
  $aa = array_map( 'edges_for_one_hd', $all_ad );

  $faa = flatten( $aa );

  $ufaa = array_unique_srr( $faa );

  $cufaa = cluster_edges( $ufaa );

  return array( 'flat' => $ufaa, 'clustered' => $cufaa );
}

function edges_for_one_hd( array $hd )
{
  return array_map_wn( 'make_pair', $hd['dwy_given_yl_for_hols'] );
}

function the_drawing( $dpc, $edges, $nodes )
{
  $dr_pairs = $nodes['dr_pairs'];
  $dl_pairs = $nodes['dl_pairs'];

  $svg_for_node_dpc = pa( 'svg_for_node', $dpc );

  $svg_for_nodes = array_map( $svg_for_node_dpc, $dr_pairs );

  $dr_kvs = dr_pairs_to_kvs( $dr_pairs );

  $svg_for_node_label_dpc = pa( 'svg_for_node_label', $dpc, $dr_kvs );

  $svg_for_node_labels = array_map( $svg_for_node_label_dpc, $dl_pairs );

  $svg_for_edge_dpc = pa( 'svg_for_edge', $dpc, $dr_kvs );

  $svg_for_edges = array_map( $svg_for_edge_dpc, $edges['flat'] );

  $svg_for_edge_cluster_dpc = pa( 'svg_for_edge_cluster', $dpc, $dr_kvs );

  $svg_for_edge_clusters = array_map( $svg_for_edge_cluster_dpc, $edges['clustered'] );

  $ne = array_merge( $svg_for_edges,
                     $svg_for_edge_clusters,
                     $svg_for_nodes,
                     $svg_for_node_labels );

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

function calendar_types()
{
  return array
    (
     'major'    => array( holidays(), all_yearlen() ),
     'roshchod' => array( all_rosh_chodesh(), all_yearlen() ),
     'shabbat'  => array( shabbats(), dummy_yearlen() ),
     );
}

function main( $calendar_type )
{
  $calendar_types = calendar_types();

  tiu( 'calendar type', $calendar_type, array_keys( $calendar_types ) );

  list( $hols, $yearlens ) = $calendar_types[ $calendar_type ];

  $all_da = dwpy_for_yls_hols( $yearlens, $hols );

  $all_ad = dwpy_for_hols_yls( $hols, $yearlens );

  $dpc = 365.2421897; // mean tropical year (as of Jan 1 2000)

  $nodes = nodes_for_all_da( $dpc, $all_da, $hols );

  $edges = edges_for_all_ad( $all_ad );

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

  $g_attr = array( 'transform' => implode( ' ', $transforms ) );

  $g = svg_g( $g_attr, $drawing );

  $bg = xml_seqa( $bounding_box, $g );

  $svg = svg_wrap( $width, $height, $bg );

  return $svg->s;
}

echo main( $argv[1] );

?>
