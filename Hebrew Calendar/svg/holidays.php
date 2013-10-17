#!/usr/bin/php -q
<?php

   /* Rescuscitate leap/nonleap. Use "figure 8" or "wheel within
    * wheel" view.
    *
    * Show seasonal spread.
    *
    * Show units (360 degrees = 1 tropical year (365.25...)).
    *
    * Show accompanying data table.
    *
    * Show absolute dwy, not just diffs. Show on graph or just in
    * accompanying table?
    *
    * Show near-symmetry surrounding Purim in non-leap years (almost
    * 50,30,30,50 diffs).
    *
    * Show near-opposition of Pesach and Sukkot and perhaps
    * near-opposition of Shavuot and Channukah.
    *
    * Fade shabbats out after 1 tropical year.
    *
    * Address the following warning given by Firefox: "Use of
    * getPreventDefault() is deprecated.  Use defaultPrevented
    * instead."
    *
    */

require_once 'svg.php';

class Bogus {}

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
    tiu( 'day_adj',   $day_adj,   [ -1, 0, 1 ] );
    tiu( 'month_adj', $month_adj, [ 0, 1 ] );

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

class Context
{
  // lelo: last edge label [should be] outside
  // snlo: string [for] node label [that should be] outside
  //
  function __construct( $dpc, $lelo, $snlo, $ndwyi )
  {
    $this->dpc = $dpc;
    $this->lelo = $lelo;
    $this->snlo = $snlo;
    $this->neutral_dwyi = $ndwyi;
  }
  public $dpc;
  public $lelo;
  public $snlo;
  public $neutral_dwyi;
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

function times() { return '×'; }

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

function month_names()
{
  return array
    (
     mn_ad() => [ 'Adar'     , shm_ad() ], // and Adar Sheni
     mn_ni() => [ 'Nisan'    , shm_ni() ],
     mn_iy() => [ 'Iyyar'    , shm_iy() ],
     mn_si() => [ 'Sivan'    , shm_si() ],
     mn_ta() => [ 'Tammuz'   , shm_ta() ],
     mn_av() => [ 'Av'       , shm_av() ],
     mn_el() => [ 'Elul'     , shm_el() ],
     mn_ti() => [ 'Tishrei'  , shm_ti() ],
     mn_ch() => [ 'Cheshvan' , shm_ch() ],
     mn_ki() => [ 'Kislev'   , shm_ki() ],
     mn_te() => [ 'Tevet'    , shm_te() ],
     mn_sh() => [ 'Shevat'   , shm_sh() ],
     mn_ar() => [ 'Adar R'   , shm_ar() ], // R for Rishon
     );
}

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

function falloff() { return 0.05; }
function node_label_font_size() { return 0.07; }
function ec_label_font_size() { return 0.05; }
function node_stroke_width() { return 0.01; }
function edge_stroke_width() { return 0.01; }
function label_rect_stroke_width() { return 0.05; }
function label_pads() { return [ 0.084, 0.2 ]; }
function spiral_factor() { return 0.05; }

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

function dummy_yearlen() { return [ new YearLen( -1, 0 ) ]; }

// tiu: throw if unexpected
//
function tiu( $what, $val, $possible_vals )
{
  $strict = true;
  if ( ! in_array( $val, $possible_vals, $strict ) )
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
      tneve( $e );
    }
}

// tneve: throw new ErrorException of var_export
function tneve( $e )
{
  throw new ErrorException( var_export( $e, 1 ) );
}

function append( array $a, $i )
{
  // Below, we could have also used array_push. It would only be
  // locally destructive due to call-by-value.
  //
  return array_merge( $a, [ $i ] );
}

function prepend( array $a, $i )
{
  // Below, we could have also used array_unshift. It would only be
  // locally destructive due to call-by-value.
  //
  return array_merge( [ $i ], $a );
}

function flipped_make_pair( $a, $b )
{
  return [ $b, $a ];
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
  return array_reduce( $a, 'array_merge', [] );
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

function days_per_month( YearLen $oyl, $month_number )
{
  list( $day_adj, $month_adj ) = [ $oyl->day_adj, $oyl->month_adj ];

  tiu( 'month_number', $month_number, array_keys( month_names() ) );

  $da = 0;

  if ( $month_number === mn_ch() ) // Cheshvan
    {
      $da = $day_adj === 1 ? 1 : 0;
    }

  if ( $month_number === mn_ki() ) // Kislev
    {
      $da = $day_adj === -1 ? -1 : 0;
    }

  if ( $month_number === mn_ar() ) // Adar Rishon
    {
      $da = $month_adj ? 1 : -29;
    }

  return 29 + $month_number % 2 + $da;
}

function previous_month( $month_number )
{
  // Below, we could use modulo operator, but why bother?
  //
  return $month_number === mn_min() ? mn_max() : $month_number - 1;
}

// dwy of rc: day within year of Rosh Chodesh (first of the month)
//
function dwy_of_rc( YearLen $oyl, $month_number )
{
  if ( $month_number === mn_start() )
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

  return sum_of_days_per_month( $oyl, $mns );
}

function all_mns()
{
  return range( mn_min(), mn_max() );
}

function days_per_year( YearLen $oyl )
{
  return sum_of_days_per_month( $oyl, all_mns() );
}

function sum_of_days_per_month( YearLen $oyl, array $mns )
{
  return array_sum( array_map_pa( 'days_per_month', $oyl, $mns ) );
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

  $name = $n % 4 === 0 ? $n : '';

  list ( $lc_name, $hc_name ) = [ $name, $name ];

  return new Holiday( $month_number, $day_offset, $lc_name, $hc_name );
}

function dwy_for_ohol_oyl( Holiday $ohol, YearLen $oyl )
{
  if ( days_per_month( $oyl, $ohol->month_number ) === 0 )
    {
      return new Bogus;
    }

  $dwyom = dwy_of_rc( $oyl, $ohol->month_number );

  // mdpy: maybe_days_per_year
  //
  $mdpy = $ohol->add_year ? days_per_year( $oyl ) : 0;

  return $mdpy + $dwyom + $ohol->day_offset;
}

function dwy_for_myl_ohol( array $myl, Holiday $ohol )
{
  return array_map_pa( 'dwy_for_ohol_oyl', $ohol, $myl );
}

function dwy_for_myl_mhol( array $myl, array $mhol )
{
  return array_map_pa( 'dwy_for_myl_ohol', $myl, $mhol );
}

function make_node( $dwys, $dwyi )
{
  return new Node( $dwys, $dwyi );
}

function node_dwyi_lt( Node $node1, Node $node2 )
{
  return $node1->dwyi < $node2->dwyi;
}

function node_dwyi_gt( Node $node1, Node $node2 )
{
  return $node1->dwyi > $node2->dwyi;
}

function node_dwy_lt( Node $node1, Node $node2 )
{
  return $node1->dwy() < $node2->dwy();
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

function svg_for_ec_label( Context $ct, $outside, $edge_cluster, $string )
{
  $c = count( $edge_cluster );
  // rei: representative edge index (index within cluster)
  //
  $rei = $outside ? $c-1 : 0;

  // redge: representative edge (representative of cluster)
  //
  $redge = $edge_cluster[$rei];

  $dwy1 = $redge->node1->dwy();
  $dwy2 = $redge->node2->dwy();

  $r_ofs = $outside ? 1 : -1;

  $where = array
    (
     'r' => radius( $ct, $redge->node2 ),
     'r ofs' => $r_ofs,
     'dwy' => ($dwy1 + $dwy2) * 0.5,
     );

  $what = array
    (
     'string' => $string,
     'font size' => ec_label_font_size(),
     'show rect' => false,
     );

  return svg_for_label( $ct->dpc, $where, $what );
}

function svg_for_edge( Context $ct, Edge $edge )
{
  list( $x1, $y1 ) = node_to_xy( $ct, $edge->node1 );
  list( $x2, $y2 ) = node_to_xy( $ct, $edge->node2 );

  $r1 = radius( $ct, $edge->node1 );
  $r2 = radius( $ct, $edge->node2 );

  $r = 0.5 * ( $r1 + $r2 );

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
     'stroke-width' => edge_stroke_width(),
     );

  $path = xml_sc_tag( 'path', $path_attr );

  return $path;
}

function edge_lens_string( array $edge_lens )
{
  $c = count( $edge_lens );

  if ( $c === 1 ) { return $edge_lens[0]; }

  $cu = count( array_unique( $edge_lens ) );

  if ( $cu === 1 )
    {
      return $edge_lens[0] . times() . count( $edge_lens );
    }

  $h = halves_equal( $edge_lens );

  if ( $h !== FALSE )
    {
      return '('.edge_lens_string( $h ).')'.times().'2';
    }

  return implode( ', ', $edge_lens );
}

function halves_equal( array $a )
{
  $c = count( $a );

  if ( $c % 2 === 1 ) { return FALSE; }

  list( $b, $c ) = array_chunk( $a, $c / 2 );

  return $b === $c ? $b : FALSE;
}

function svg_for_edge_cluster( Context $ct, $last_key, $key, $edge_cluster )
{
  $edge_lens = array_map( 'edge_len', $edge_cluster );

  $edge_lens_string = edge_lens_string( $edge_lens );

  $outside = $ct->lelo && $last_key === $key;

  $sfd = svg_for_ec_label( $ct, $outside, $edge_cluster, $edge_lens_string );

  return $sfd;
}

function edge_len( Edge $edge )
{
  return $edge->node2->dwy() - $edge->node1->dwy();
}

function svg_for_node( Context $ct, Node $node )
{
  $r = radius( $ct, $node );

  $line_attr = array
    (
     'y1' => -$r,
     'y2' => -$r + falloff(),
     'stroke' => 'black',
     'stroke-width' => node_stroke_width(),
     'transform' => svg_tr1( 360 * $node->dwy() / $ct->dpc ),
     );

  return xml_sc_tag( 'line', $line_attr );
}

function svg_for_node_label( Context $ct, $dl_pair )
{
  list ( $node_cluster, $ohol ) = $dl_pair;

  $string = $ohol->name_using_hebrew_chars;

  $outside = $string === $ct->snlo;

  // rnode: representative node (representative of cluster)
  //
  $rnode = $outside
    ? max_of_nodes_dwyi( $node_cluster )
    : min_of_nodes_dwyi( $node_cluster );

  $r_ofs = $outside ? 1 : -1;

  $where = array
    (
     'r' => radius( $ct, $rnode ),
     'r ofs' => $r_ofs,
     'dwy' => $rnode->dwy(),
     );

  $what = array
    (
     'string' => $string,
     'font size' => node_label_font_size(),
     'show rect' => false,
     );

  return svg_for_label( $ct->dpc, $where, $what );
}

function svg_for_label( $dpc, $where, $what )
{
  $r         = $where['r'];
  $dwy       = $where['dwy'];
  $r_ofs     = $where['r ofs'];
  $string    = $what['string'];
  $font_size = $what['font size'];
  $show_rect = $what['show rect'];

  $r2 = $r + $r_ofs * 1.2 * falloff();

  $d = 360 * $dwy / $dpc;

  $bbox = bbox( $string, $font_size );

  $width = $bbox[0];
  $height = $bbox[1];

  $t = 2 * M_PI * $dwy / $dpc;

  $s = M_PI_2 - $t;

  if ( $r_ofs === 1 ) { $s+=M_PI; }

  $rectx = 0.5 * $width  * -( 1 + cos(  $s ) );
  $recty = 0.5 * $height * -( 1 + sin( -$s ) );

  $transforms = array
    (
     svg_tr1( $d ),
     svg_tt1( 0, -$r2 ),
     svg_tr1( -$d ),
     svg_tt1( $rectx, $recty ),
     );

  list ( $xpad, $ypad ) = scale( $font_size, label_pads() );

  $text_attr = array( 'font-size' => $font_size,
                      'x' => $xpad,
                      'y' => $height - $ypad );

  $text = svg_text( $text_attr, $string );

  $rect = svg_rect( array(
                          'width' => $width,
                          'height' => $height,
                          'stroke' => 'black',
                          'stroke-width' => $font_size * label_rect_stroke_width(),
                          'fill' => 'none',
                          ) );

  $g_attr = [ 'transform' => implode( ' ', $transforms ) ];

  // mrect: maybe rect
  //
  $mrect = $show_rect ? [ $rect ] : [];

  $elements = append( $mrect, $text );

  $g = svg_g( $g_attr, xml_seq( $elements ) );

  return $g;
}

function scale( $k, array $a )
{
  return array_map_pa( 'multiply', $k, $a );
}

function multiply( $a, $b ) { return $a * $b; }

function bbox( $string, $font_size )
{
  return scale( $font_size, ubbox_lookup( $string ) );
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
     );

  if ( array_key_exists( $string, $a ) )
    {
      return $a[ $string ];
    }

  return ubbox_char_lookup( $string );
}

function ubbox_char_lookup( $string )
{
  $s = preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY );

  $lookups = array_map( 'ubbox_char_lookup2', $s );

  $trues = array_filter( $lookups );

  if ( count( $trues ) !== count( $lookups ) )
    {
      tneve( [ 'could not find a character in', $string, $lookups ] );
    }

  $width = array_sum( $lookups );

  return [ $width, 1.1 ];
}

function ubbox_char_lookup2( $char )
{
  $xchar = 'x' . $char; // to allow array keys below to be all strings

  $a = array
    (
     'x0' => 0.7,
     'x1' => 0.7,
     'x2' => 0.7,
     'x3' => 0.7,
     'x4' => 0.7,
     'x5' => 0.7,
     'x6' => 0.7,
     'x7' => 0.7,
     'x8' => 0.7,
     'x9' => 0.7,
     'x,' => 0.2,
     'x ' => 0.3,
     'x'.times() => 0.7,
     'x'.times() => 0.7,
     'x(' => 0.4,
     'x)' => 0.4,
     );

  return array_key_exists( $xchar, $a )
    ? $a[ $xchar ]
    : NULL;
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
function node_to_xy( Context $ct, Node $node )
{
  $t = 2 * M_PI * $node->dwy() / $ct->dpc;

  $s = M_PI_2 - $t;

  $x = cos( $s );

  $y = -sin( $s );

  return scale( radius( $ct, $node ), [ $x, $y ] );
}

// sr: sort regular [and] renumber
//
function array_unique_srr( array $a )
{
  return array_values( array_unique( $a, SORT_REGULAR ) );
}

function nodes_for_all_da( $mhol, $myl )
{
  $dwy_for_myl_mhol = dwy_for_myl_mhol( $myl, $mhol );

  // bh: by holiday, i.e. indexed by integer holiday index
  // pb: possibly-bogus
  //
  $pb_nodes_bh = array_map( 'nodes_for_ohol', $dwy_for_myl_mhol );

  $nb_nodes_bh = array_map( 'nb_nodes', $pb_nodes_bh );

  $dl_pairs_bh = array_map( 'dl_pairs_for_ohol', $nb_nodes_bh, $mhol );

  $pb_nodes_by = transpose( $pb_nodes_bh );

  $edges = flatten( array_map( 'edges_for_oyl', $pb_nodes_by ) );

  $cedges = cluster_edges( $edges );

  return array
    (
     'nodes'    => flatten( $nb_nodes_bh ),
     'dl_pairs' => flatten( $dl_pairs_bh ),
     'edges'    => $edges,
     'cedges'   => $cedges,
     );
}

function transpose( array $a )
{
  // You can't do array_map( 'array', $a, $b, $c, ... ).
  // But you can achieve that effect with array_map( NULL, $a, $b, $c, ... ).
  // That's what we're doing below.

  array_unshift( $a, NULL );
  return call_user_func_array( 'array_map', $a );
}

function edges_for_oyl( array $nodes_for_oyl )
{
  $nb_nodes = nb_nodes( $nodes_for_oyl );

  return array_map_wn( 'make_edge', $nb_nodes );
}

function nodes_for_ohol( array $dwys )
{
  return array_map_pa( 'make_node', $dwys, array_keys( $dwys ) );
}

function radius( Context $ct, Node $node )
{
  $sf = ($node->dwyi - $ct->neutral_dwyi) * spiral_factor();

  $spiral = 1 + $sf * $node->dwy() / $ct->dpc;

  return $spiral;
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

function max_of_nodes_dwyi( array $nodes )
{
  return amina( $nodes, 'node_dwyi_gt' );
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

function not_bogus( $x )
{
  return ! is_object( $x ) || get_class( $x ) !== 'Bogus';
}

function node_not_bogus( Node $node )
{
  return not_bogus( $node->dwy() );
}

// nb: non-bogus
//
function nb_nodes( array $nodes )
{
  return array_filter( $nodes, 'node_not_bogus' );
}

function dl_pairs_for_ohol( array $nodes, Holiday $ohol )
{
  $node_clusters = cluster_nodes( $nodes );

  return array_map_pa( 'flipped_make_pair', $ohol, $node_clusters );
}

function the_drawing( Context $ct, $nodes_broadly )
{
  $nodes = $nodes_broadly['nodes'];
  $dl_pairs = $nodes_broadly['dl_pairs'];

  $pa_svg_for_node = pa( 'svg_for_node', $ct );

  $svg_for_nodes = array_map( $pa_svg_for_node, $nodes );

  $pa_svg_for_node_label = pa( 'svg_for_node_label', $ct );

  $svg_for_node_labels = array_map( $pa_svg_for_node_label, $dl_pairs );

  $pa_svg_for_edge = pa( 'svg_for_edge', $ct );

  $svg_for_edges = array_map( $pa_svg_for_edge, $nodes_broadly['edges'] );

  $nbc = $nodes_broadly['cedges'];

  $lnbc = count( $nbc ) - 1; // last [index of] nbc

  $pa_svg_for_edge_cluster = pa( 'svg_for_edge_cluster', $ct, $lnbc );

  $svg_for_cedges = array_map_wk( $pa_svg_for_edge_cluster, $nbc );

  $ne = array_merge( $svg_for_edges,
                     $svg_for_cedges,
                     $svg_for_nodes,
                     $svg_for_node_labels );

  return xml_seq( $ne );
}

function lelo() { return true; }
function leli() { return false; }

function calendar_types()
{
  return array
    (
     'major'    => [ holidays()         , all_yearlen(),   lelo(), NULL,     2 ],
     'roshchod' => [ all_rosh_chodesh() , all_yearlen(),   lelo(), shm_ar(), 2 ],
     'shabbat'  => [ shabbats()         , dummy_yearlen(), leli(), NULL,     -3 ],
     );
}

function main( $calendar_type )
{
  $calendar_types = calendar_types();

  tiu( 'calendar type', $calendar_type, array_keys( $calendar_types ) );

  list( $mhol, $myl, $lelo, $snlo, $ndwyi ) = $calendar_types[ $calendar_type ];

  $dpc = 365.2421897; // mean tropical year (as of Jan 1 2000)

  $nodes_broadly = nodes_for_all_da( $mhol, $myl );

  $ct = new Context( $dpc, $lelo, $snlo, $ndwyi );

  $drawing = the_drawing( $ct, $nodes_broadly );

  $width = 700;

  $height = 800;

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

  $g_attr = [ 'transform' => implode( ' ', $transforms ) ];

  $g = svg_g( $g_attr, $drawing );

  $bg = xml_seqa( $bounding_box, $g );

  $svg = svg_wrap( $width, $height, $bg );

  return $svg->s;
}

echo main( $argv[1] );

?>
