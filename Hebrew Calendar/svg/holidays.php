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

// shh: string, Hebrew, holiday, i.e. string [in] Hebrew [characters for a ] holiday [name]
//
function shh_pu() { return 'פורים'; }
function shh_pe() { return 'פסח'; }
function shh_sh() { return 'שבועות'; }
function shh_su() { return 'סוכות'; }
function shh_ch() { return 'חנוכה'; }
function shh_tu() { return 'טו בשבט'; }

// shh: string, Hebrew, month, i.e. string [in] Hebrew [characters for a ] month [name]
//
function shm_ad() { return 'אדר'; } // and Adar Sheni
function shm_ni() { return 'ניסן'; }
function shm_iy() { return 'אייר'; }
function shm_si() { return 'סיון'; }
function shm_ta() { return 'תמוז'; }
function shm_av() { return 'אב'; }
function shm_el() { return 'אלול'; }
function shm_ti() { return 'תשרי'; }
function shm_ch() { return 'חשון'; }
function shm_ki() { return 'כסלו'; }
function shm_te() { return 'טבת'; }
function shm_sh() { return 'שבט'; }
function shm_ar() { return 'אדר א׳'; } // R for Rishon


function holidays()
{
  // month number, day offset, name in Latin characters, name in Hebrew characters
  // usually day offset is day within month, but it can be anything
  //
  $r = array
    (
     new Holiday( mn_ad(), 14, 'Purim',        shh_pu() ),
     new Holiday( mn_ni(), 15, 'Pesach',       shh_pe() ),
     new Holiday( mn_si(),  6, 'Shavuot',      shh_sh() ),
     new Holiday( mn_ti(), 15, 'Sukkot',       shh_su() ),
     new Holiday( mn_ki(), 25, 'Chanukkah',    shh_ch() ),
     new Holiday( mn_sh(), 15, 'Tu B\'Shevat', shh_tu() ),
     );

  return add_final_holiday( $r );
}

function month_names()
{
  return array
    (
     mn_ad() => array( 'Adar'     , shm_ad() ), // and Adar Sheni
     mn_ni() => array( 'Nisan'    , shm_ni() ),
     mn_iy() => array( 'Iyyar'    , shm_iy() ),
     mn_si() => array( 'Sivan'    , shm_si() ),
     mn_ta() => array( 'Tammuz'   , shm_ta() ),
     mn_av() => array( 'Av'       , shm_av() ),
     mn_el() => array( 'Elul'     , shm_el() ),
     mn_ti() => array( 'Tishrei'  , shm_ti() ),
     mn_ch() => array( 'Cheshvan' , shm_ch() ),
     mn_ki() => array( 'Kislev'   , shm_ki() ),
     mn_te() => array( 'Tevet'    , shm_te() ),
     mn_sh() => array( 'Shevat'   , shm_sh() ),
     mn_ar() => array( 'Adar R'   , shm_ar() ), // R for Rishon
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

  $name = $n % 4 == 0 ? $n : '';

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

function dwy_for_yls_hol( array $yls, Holiday $hol )
{
  return array_map_pa( 'dwy_for_hol_yl', $hol, $yls );
}

function dwy_for_yls_hols( array $yls, array $hols )
{
  return array_map_pa( 'dwy_for_yls_hol', $yls, $hols );
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

class Node
{
  function __construct( array $dwys, $dwyi )
  {
    $this->dwys = $dwys;
    $this->dwyi = $dwyi;
  }
  function dwy() { return $this->dwys[ $this->dwyi ]; }
  public $dwys;
  public $dwyi;
}

function make_node( $dwys, $dwyi )
{
  return new Node( $dwys, $dwyi );
}

function node_dwyi_lt( Node $node1, Node $node2 )
{
  return $node1->dwyi < $node2->dwyi;
}

function node_dwy_lt( Node $node1, Node $node2 )
{
  return $node1->dwy() < $node2->dwy();
}

class Edge
{
  function __construct( Node $node1, Node $node2 )
  {
    $this->node1 = $node1;
    $this->node2 = $node2;
  }
  public $node1;
  public $node2;
}

function make_edge( Node $node1, Node $node2 )
{
  return new Edge( $node1, $node2 );
}

function edge_lt( Edge $edge1, Edge $edge2 )
{
  if ( node_dwy_lt( $edge1->node1, $edge2->node1 ) )
    {
      return true;
    }

  if ( node_dwy_lt( $edge2->node1, $edge1->node1 ) )
    {
      return false;
    }

  return node_dwy_lt( $edge1->node2, $edge2->node2 );
}



// dwy: day within year
// dpc: days per circumference
// redge: representative edge

function svg_for_ec_label( $dpc, $redge, $string )
{
  $dwy1 = $redge->node1->dwy();
  $dwy2 = $redge->node2->dwy();

  $where = array
    (
     'r' => dradius( $redge->node2 ),
     'dwy' => ($dwy1 + $dwy2) * 0.5,
     );

  $what = array
    (
     'string' => $string,
     'font size' => 0.05,
     'show rect' => false,
     );

  return svg_for_label( $dpc, $where, $what );
}

function svg_for_edge( $dpc, Edge $edge )
{
  list( $x1, $y1 ) = node_to_xy( $dpc, $edge->node1 );
  list( $x2, $y2 ) = node_to_xy( $dpc, $edge->node2 );

  $r = nradius( $edge->node1 );

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

function svg_for_edge_cluster( $dpc, $edge_cluster )
{
  $edge_lens = array_map( 'edge_len', $edge_cluster );

  $cvs_of_els = array_count_values( $edge_lens );

  $strs_of_cvs_of_els = array_map_wk( 'cv_as_string', $cvs_of_els );

  $edge_lens_as_string = implode( ', ', $strs_of_cvs_of_els );

  // redge: representative edge (representative of cluster)
  //
  $redge = $edge_cluster[0];

  $sfd = svg_for_ec_label( $dpc, $redge, $edge_lens_as_string );

  return $sfd;
}

function edge_len( Edge $edge )
{
  return $edge->node2->dwy() - $edge->node1->dwy();
}

function falloff()
{
  return 0.05;
}

function svg_for_node( $dpc, Node $node )
{
  $r = nradius( $node );

  $line_attr = array
    (
     'y1' => -$r,
     'y2' => -$r + falloff(),
     'stroke' => 'black',
     'stroke-width' => 0.01,
     'transform' => svg_tr1( 360 * $node->dwy() / $dpc ),
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

function svg_for_node_label( $dpc, $dl_pair )
{
  list ( $node, $hol ) = $dl_pair;

  $where = array
    (
     'r' => nradius( $node ),
     'dwy' => $node->dwy(),
     );

  $what = array
    (
     'string' => $hol->name_using_hebrew_chars,
     'font size' => 0.07,
     'show rect' => false,
     );

  return svg_for_label( $dpc, $where, $what );
}

function svg_for_label( $dpc, $where, $what )
{
 $r         = $where['r'];
 $dwy       = $where['dwy'];
 $string    = $what['string'];
 $font_size = $what['font size'];
 $show_rect = $what['show rect'];

  $r2 = $r - 1.2 * falloff();

  $d = 360 * $dwy / $dpc;

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

  $xpad = $font_size * 0.084;
  $ypad = $font_size * 0.2;

  $text_attr = array( 'font-size' => $font_size,
                      'x' => $xpad,
                      'y' => $height - $ypad );

  $text = svg_text( $text_attr, $string );

  $rect = svg_rect( array(
                          'width' => $width,
                          'height' => $height,
                          'stroke' => 'black',
                          'stroke-width' => $font_size * 0.05,
                          'fill' => 'none',
                          ) );

  $g_attr = array( 'transform' => implode( ' ', $transforms ) );

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

  $ubbox = ubbox_lookup( $string );

  $width = $font_size * $ubbox[0];
  $height = $font_size * $ubbox[1];

  return array( $width, $height );
}

function ubbox_lookup( $string )
{
  $a = array
    (
     '' => [ 0, 0 ],
     shh_pu() => [ 2.4, 1 ],
     shh_pe() => [ 1.8, 1 ],
     shh_sh() => [ 3.1, 1 ],
     shh_su() => [ 2.5, 1 ],
     shh_ch() => [ 2.6, 1 ],
     shh_tu() => [ 3.5, 1 ],
     shm_ad() => [ 1.8, 1 ],
     shm_ni() => [ 1.8, 1 ],
     shm_iy() => [ 2.0, 1 ],
     shm_si() => [ 1.8, 1 ],
     shm_ta() => [ 2.0, 1 ],
     shm_av() => [ 1.2, 1 ],
     shm_el() => [ 2.1, 1 ],
     shm_ti() => [ 2.1, 1 ],
     shm_ch() => [ 2.0, 1 ],
     shm_ki() => [ 2.1, 1 ],
     shm_te() => [ 1.8, 1 ],
     shm_sh() => [ 1.9, 1 ],
     shm_ar() => [ 2.8, 1 ],
     '30×6' => [ 2.8, 1 ],
     '50×6' => [ 2.8, 1 ],
     '127×6' => [ 2.8, 1 ],
     '69×4, 70×2' => [ 2.8, 1 ],
     '48×2, 49×4' => [ 2.8, 1 ],
     '29×3' => [ 2.8, 1 ],
     '59×3' => [ 2.8, 1 ],
     '29×6' => [ 2.8, 1 ],
     '29×4, 30×2' => [ 2.8, 1 ],
     '29×2, 30×4' => [ 2.8, 1 ],
     '0×3' => [ 2.8, 1 ],
     '30×3' => [ 2.8, 1 ],
     '7' => [ 0.85, 1 ],
     );

  tiu( 'string', $string, array_keys( $a ) );

  return $a[ $string ];
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
function node_to_xy( $dpc, Node $node, $node_for_rf = NULL )
{
  list ( $r, $x, $y ) = node_to_rxy( $dpc, $node, $node_for_rf );

  return array( $r * $x, $r * $y );
}

function node_to_rxy( $dpc, Node $node, $node_for_rf = NULL )
{
  $actual_node_for_rf = is_null( $node_for_rf ) ? $node : $node_for_rf;

  $r = nradius( $actual_node_for_rf );

  return prepend( node_to_uxy( $dpc, $node ), $r );
}

// uxy: unit x and y, i.e. x and y on the unit circle
//
function node_to_uxy( $dpc, $node )
{
  $t = 2 * M_PI * $node->dwy() / $dpc;

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

function nodes_for_all_da( $dpc, $dwy_for_yls_hols, $hols )
{
  // bh: by holiday, i.e. indexed by integer holiday index
  //
  $nodes_bh = array_map( 'nodes_for_one_hol', $dwy_for_yls_hols );

  $dl_pairs_bh = array_map( 'dl_pairs_for_one_hol', $nodes_bh, $hols );

  $edges_bh = array_map_wn( 'edges_for_2_hols', $nodes_bh );

  $cedges_bh = array_map( 'cluster_edges', $edges_bh );

  return array
    (
     'nodes'    => flatten( $nodes_bh ),
     'dl_pairs' => flatten( $dl_pairs_bh ),
     'edges'    => flatten( $edges_bh ),
     'cedges'   => flatten( $cedges_bh ),
     );
}

function edges_for_2_hols( array $nodes_for_hol1, array $nodes_for_hol2 )
{
  return array_map( 'make_edge', $nodes_for_hol1, $nodes_for_hol2 );
}

function nodes_for_one_hol( array $dwys )
{
  return array_map_pa( 'make_node', $dwys, array_keys( $dwys ) );
}

function dradius( Node $node )
{
  return radius( $node, ec_label_radii() );
}

function nradius( Node $node )
{
  return radius( $node, node_radii() );
}

function radius( Node $node, $radii )
{
  $sf = ($node->dwyi - 2) * 0.05;

  $spiral = 1 + $sf * ( $node->dwy() / 365.25 );

  //return $spiral * $radii[ $index_within ];

  return $spiral;
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

function cluster_nodes( array $nodes )
{
  return cluster( $nodes, 'partition_nodes' );
}

function cluster_edges( array $edges )
{
  return cluster( $edges, 'partition_edges' );
}

// w2m: within 2 of min
//
function partition_nodes( array $nodes )
{
  $min = min_of_nodes_dwy( $nodes );

  $within_2_of_min = pa( 'nodes_are_within_2', $min );

  return partition( $within_2_of_min, $nodes );
}

function partition_edges( array $edges )
{
  $min = min_of_edges( $edges );

  $within_2_of_min = pa( 'edges_are_within_2', $min );

  return partition( $within_2_of_min, $edges );
}

function nodes_are_within_2( $x, $y )
{
  return abs( $x->dwy() - $y->dwy() ) <= 2;
}

function edges_are_within_2( Edge $edge1, Edge $edge2 )
{
  return
    nodes_are_within_2( $edge1->node1, $edge2->node1 )
    &&
    nodes_are_within_2( $edge1->node2, $edge2->node2 );
}

function min_of_nodes_dwyi( array $nodes )
{
  return amina( $nodes, 'node_dwyi_lt' );
}

function min_of_nodes_dwy( array $nodes )
{
  return amina( $nodes, 'node_dwy_lt' );
}

function min_of_edges( array $edges )
{
  return amina( $edges, 'edge_lt' );
}

// pf: partition function
//
function cluster( array $a, $pf )
{
  if ( empty( $a ) ) { return $a; }

  $p = $pf( $a );

  return prepend( cluster( $p[1], $pf ), $p[0] );
}

function partition( $f, array $a )
{
  $n = pa( 'not', $f );
  return array( array_values( array_filter( $a, $f ) ),
                array_values( array_filter( $a, $n ) ) );
}

function not( $f, $a ) { return ! $f( $a ); }

// amina: abstract minimum of array
// lt: "less than" [function]
//
function amina( array $a, $lt )
{
  if ( empty( $a ) ) { return NULL; }

  $p = array_pop( $a );

  if ( empty( $a ) ) { return $p; }

  return array_reduce( $a, pa( 'min_of_2_using_lt', $lt ), $p );
}

function min_of_2_using_lt( $lt, $a, $b )
{
  return $lt( $a, $b ) ? $a : $b;
}

function dl_pairs_for_one_hol( array $nodes, Holiday $hol )
{
  $node_clusters = cluster_nodes( $nodes );

  // modcs: representatives of node clusters with max indexes
  //
  $modcs = array_map( 'min_of_nodes_dwyi', $node_clusters );

  return array_map_pa( 'flipped_make_pair', $hol, $modcs );
}

function the_drawing( $dpc, $nodes_broadly )
{
  $nodes = $nodes_broadly['nodes'];
  $dl_pairs = $nodes_broadly['dl_pairs'];

  $svg_for_node_dpc = pa( 'svg_for_node', $dpc );

  $svg_for_nodes = array_map( $svg_for_node_dpc, $nodes );

  $svg_for_node_label_dpc = pa( 'svg_for_node_label', $dpc );

  $svg_for_node_labels = array_map( $svg_for_node_label_dpc, $dl_pairs );

  $svg_for_edge_dpc = pa( 'svg_for_edge', $dpc );

  $svg_for_edges = array_map( $svg_for_edge_dpc, $nodes_broadly['edges'] );

  $svg_for_edge_cluster_dpc = pa( 'svg_for_edge_cluster', $dpc );

  $svg_for_cedges = array_map( $svg_for_edge_cluster_dpc, $nodes_broadly['cedges'] );

  $ne = array_merge( $svg_for_edges,
                     $svg_for_cedges,
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

  $dwy_for_yls_hols = dwy_for_yls_hols( $yearlens, $hols );

  $dpc = 365.2421897; // mean tropical year (as of Jan 1 2000)

  $nodes_broadly = nodes_for_all_da( $dpc, $dwy_for_yls_hols, $hols );

  $drawing = the_drawing( $dpc, $nodes_broadly );

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
     svg_tt1( $width * 0.5, $height * 0.5 ),
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
