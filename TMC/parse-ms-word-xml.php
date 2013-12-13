#!/usr/bin/php -q
<?php

require_once 'generate-html.php';
require_once 'parse-common.php';

// tneve: throw new ErrorException of var_export
function tneve( $e )
{
  throw new ErrorException( var_export( $e, 1 ) );
}

// vese: var_export to standard error
function vese( $x )
{
  fprintf( STDERR, var_export( $x, 1 ) . "\n" );
}

// lubn: lookup, [with] behavior "null [on failure]"
//
function lubn( $k, array $a )
{
  return array_key_exists( $k, $a ) ? $a[ $k ] : NULL;
}

function kvs_from_pairs( array $pairs )
{
  $c0 = array_column( $pairs, 0 );
  $c1 = array_column( $pairs, 1 );
  return array_combine( $c0, $c1 );
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

function array_map_pa( $f, $a, array $b )
{
  return array_map( pa( $f, $a ), $b );
}

// wk: with keys, i.e.
//
// array_map_wk( $f, [ $k => $v ] )
//
// is
//
// [ $f( $k, $v ) ]
//
function array_map_wk( $f, array $a )
{
  return array_map( $f, array_keys( $a ), $a );
}

// rn: renumber
//
function array_filter_rn( array $a, $f )
{
  return array_values( array_filter( $a, $f ) );
}

function tr_of_tds( $td_bodies )
{
  $tds = array_map( 'td_na', $td_bodies );

  return xml_wrap( 'tr', [], xml_seq( $tds ) );
}

// na: no attributes
//
function td_na( $body )
{
  return xml_wrap( 'td', [], $body );
}

// b1: no attributes except "border"=1
//
function table_b1( $trs )
{
  $table_attr = [ 'border' => 1 ];

  return xml_wrap( 'table', $table_attr, xml_seq( $trs ) );
}

function spanspan( array $node )
{
  return process_pairwise_2( 'pairwise_spanspan', $node );
}

function htmhtm( array $node )
{
  return process_pairwise_2( 'pairwise_htmhtm', $node );
}

function pairwise_spanspan( $n0, $n1 )
{
  return pairwise_should_spanspan( $n0, $n1 )
    ? pairwise_do_the_spanspan( $n0, $n1 )
    : NULL;
}

function pairwise_htmhtm( $n0, $n1 )
{
  return pairwise_should_htmhtm( $n0, $n1 )
    ? pairwise_do_the_htmhtm( $n0, $n1 )
    : NULL;
}

function pairwise_tt( $n0, $n1 )
{
  return pairwise_should_tt( $n0, $n1 )
    ? pairwise_do_the_tt( $n0, $n1 )
    : NULL;
}

function pairwise_should_spanspan( $n0, $n1 )
{
  if ( is_t( $n0 ) && is_t( $n1 ) ) { return TRUE; }

  if ( ! is_span( $n0 ) || ! is_span( $n1 ) ) { return FALSE; }

  $a0 = lubn( 'attributes', $n0 );
  $a1 = lubn( 'attributes', $n1 );

  return $a0 === $a1;
}

function pairwise_should_htmhtm( $n0, $n1 )
{
  return ( is_htm( $n0 ) && is_htm( $n1 ) );
}

function pairwise_should_tt( $n0, $n1 )
{
  return is_t( $n0 ) && is_t( $n1 );
}

function is_span( $r )
{
  return $r['name'] === 'span';
}

function is_t( $r )
{
  return $r['name'] === 't';
}

function is_htm( $r )
{
  return $r['name'] === 'htm';
}

function pairwise_do_the_spanspan( $e0, $e1 )
{
  $e0['character data'] .= $e1['character data'];

  return $e0;
}

function pairwise_do_the_htmhtm( $e0, $e1 )
{
  $e0['htm'] = xml_seqa( $e0['htm'], $e1['htm'] );

  return $e0;
}

function pairwise_do_the_tt( $e0, $e1 )
{
  $e0['character data'] .= $e1['character data'];

  return $e0;
}

function process_pairwise_2( $f, array $node )
{
  $f2 = pa( 'process_pairwise', $f );

  return traverse_yyy( $f2, $node );
}

function process_pairwise( $f, array $a )
{
  $pa = pa( 'pairwise_helper', $f );

  return array_reduce( $a, $pa, [] );
}

function pairwise_helper( $f, $acc, $item )
{
  $c = count( $acc );

  if ( $c !== 0 )
    {
      $cm1 = $c - 1;

      $acc_cm1 = $acc[ $cm1 ];

      $r = $f( $acc_cm1, $item );

      if ( ! is_null( $r ) )
        {
          $acc[ $cm1 ] = $r;

          return $acc;
        }
    }

  $acc[] = $item;

  return $acc;
}

// rsx: really simple xml

function get_rsxes_from_file( $input_filename )
{
  // tsxml: top-level SimpleXML element
  //
  $tsxml = simplexml_load_file( $input_filename );

  if ( $tsxml === FALSE )
    {
      tneve( [ 'error opening' => $input_filename ] );
    }

  // sde: sxml document element
  // i.e. <document> element in sxml (SimpleXML form)
  //
  $sde = get_sde_from_tsxml( $tsxml );

  return get_rsxes_from_sde( $sde );
}

function get_sde_from_tsxml( $sxml )
{
  $r = NULL;

  foreach ($sxml->children('pkg',TRUE) as $gen2)
    {
      foreach ($gen2->children('pkg',TRUE) as $gen3)
        {
          foreach ($gen3->children('w',TRUE) as $gen4)
            {
              if ( $gen4->getName() === 'document' )
                {
                  if ( ! is_null( $r ) )
                    {
                      tneve( 'more than one document found' );
                    }
                  $r = $gen4;
                }
            }
        }
    }

  return $r;
}

function my_html_body( $input_filename )
{
  $rsxes = get_rsxes_from_file( $input_filename );

  $r1 = array_map( 'simplify_rsx', $rsxes );

  $r2 = array_map( 'spanspan', $r1 );

  $r3 = array_map( 'htmhtm', $r2 );

  $rfinal = $r3;

  $tables = get_tables_for_rsxes( $rfinal );

  return xml_seq( $tables );
}

function get_rsxes_from_sde( $sde )
{
  foreach ($sde->body->children('w',TRUE) as $body_child)
    {
      $r[] = get_rsx_from_sxml( $body_child );
    }
  return $r;
}

function get_rsx_from_sxml( $sxml )
{
  $name = $sxml->getName();

  $r['name'] = $name;

  foreach ( $sxml->attributes('w',TRUE) as $k => $v )
    {
      $r['attributes'][$k] = (string) $v;
    }

  $is_text = $name === 't';

  if ( $is_text )
    {
      $r['character data'] =  (string) $sxml;
    }

  foreach ($sxml->children('w',TRUE) as $c)
    {
      $r['children'][] = get_rsx_from_sxml( $c );
    }

  return $r;
}

// ake: array_key_exists
//
function tneve_ake( $k, array $a )
{
  if ( array_key_exists( $k, $a ) )
    {
      tneve([ 'msg' => 'unexpected key exists',
              'key' => $k,
              'array' => $a ]);
    }
}

function get_rpr_children_as_attr( $r )
{
  // aap: attributes as pairs
  //
  $aap = array_map( 'get_aap_from_rpr_chi', $r['children'] );

  tneve_ake( 'attributes', $r );

  return kvs_from_pairs( $aap );
}

/* Hoist rpr into its enclosing r. I.e. hoist the attributes of an rpr
   into being attributes of its enclosing r and get rid of the (now
   empty) rpr.
 */
function hoist_rpr( $r )
{
  if ( $r['name'] === 'tab' )  { return [ 'name' => 't',
                                          'character data' => '' ]; }

  if ( $r['name'] !== 'r' )  { return $r; }

  $r['children'] = process_pairwise( 'pairwise_tt', $r['children'] );

  $c = count( $r['children'] );

  if ( $c === 1 ) { return hoist_r_1( $r ); }

  if ( $c === 2 ) { return hoist_r_2( $r ); }

  return $r;
}

function hoist_r_1( $r )
{
  $chi0 = $r['children'][0];

  if ( $chi0['name'] !== 't' ) { return $r; }

  $r = hoist_common( $r, $chi0 );

  /* Italics are not possible here, since this is a plain text
     element, but we use the general routine here since it handles
     plain case.
   */
  return maybe_italicize( $r );
}

function hoist_r_2( $r )
{
  $chi = $r['children'];

  $chi0 = $chi[0];

  if ( $chi0['name'] !== 'rPr' ) { return $r; }

  $chi1 = $chi[1];

  if ( $chi1['name'] !== 't' ) { return $r; }

  tneve_ake( 'attributes', $r );

  $r['attributes'] = get_rpr_children_as_attr( $chi0 );

  $r = hoist_common( $r, $chi1 );

  $y = apply_hebraica_map_to_span( $r );

  if ( $y !== FALSE ) { $r = $y; }

  $r = maybe_italicize( $r );

  return $r;
}

function maybe_italicize( $r )
{
  if ( ! array_key_exists( 'attributes', $r ) )
    {
      return [ 'name' => 'htm',
               'htm' => $r['character data'] ];
    }

  if ( $r['attributes'] === [ 'italic' => 1 ] )
    {
      return [ 'name' => 'htm',
               'htm' => html_i_na( $r['character data'] ) ];
    }

  if ( $r['attributes'] === [ 'underline' => 'single' ] )
    {
      return [ 'name' => 'htm',
               'htm' => xml_wrap( 'u', [], $r['character data'] ) ];
    }

  if ( $r['attributes'] === [ 'font' => 'Hebraica',
                              'size' => '22' ] )
    {
      return [ 'name' => 'htm',
               'htm' => xml_wrap( 'span',
                                  [ 'lang' => 'hbo' ],
                                  $r['character data'] ) ];
    }

  return $r;
}

// chin: child n (n=0 or n=1)
//
function hoist_common( $r, $chin )
{
  $r['character data'] = $chin['character data'];

  unset( $r['children'] );

  $r['name'] = 'span';

  return $r;
}

function apply_hebraica_map_to_span( $r )
{
  $attr = $r['attributes'];

  if ( lubn( 'font', $attr ) !== 'Hebraica' ) { return $r; }

  $c = $r['character data'];

  $r['character data'] = apply_hebraica_char_map( $c );

  return $r;
}

function simplify_rsx( $r )
{
  return traverse_xxx( 'hoist_rpr', $r );
}

function traverse_xxx( $f, $r )
{
  $c = lubn( 'children', $r );

  if ( ! is_null( $c ) )
    {
      $r['children'] = array_map_pa( __FUNCTION__, $f, $c );
    }

  return $f( $r );
}

function traverse_yyy( $f, $r )
{
  $c = lubn( 'children', $r );

  if ( is_null( $c ) ) { return $r; }

  $cdeep = array_map_pa( __FUNCTION__, $f, $c );

  $r['children'] = $f( $cdeep );

  return $r;
}

function apply_hebraica_char_map( $s )
{
  return apply_char_map( hebraica_char_map(), $s );
}

function apply_char_map( $char_map, $s )
{
  // saa: s as array of single-char strings
  //
  $saa = preg_split('//u', $s, -1, PREG_SPLIT_NO_EMPTY);

  $debug = FALSE;

  $maybe_debug_suffix = $debug
    ? ' ' . char_map_debug_string( $char_map, $saa )
    : '';

  $m = implode( array_map_pa( 'acm', $char_map, $saa ) );

  // $mrm: maybe reversed [version of] $m
  //
  $mrm = lubn( 'reverse', $char_map )
    ? unicode_aware_reverse_string( $m )
    : $m;

  return $mrm . $maybe_debug_suffix;
}

function unicode_aware_reverse_string( $string )
{
    $length = grapheme_strlen( $string );

    $initial = '';
    $ret = '';
    $final = '';

    for ($i = $length-1; $i >= 0; $i--)
      {
        $g = grapheme_substr($string, $i, 1);

        if ( $ret === '' && $g === ' ' )
          {
            $final = ' ';
          }
        elseif ( $i === 0 && $g === ' ' )
          {
            $initial = ' ';
          }
        else
          {
            $ret .= $g;
          }
      }

    $irf = $initial . $ret . $final;

    vese([$string,$ret, $irf]);
    return $irf;
}

function char_map_debug_string( $char_map, $saa )
{
  $y = array_map_pa( 'printed_val_and_name', $char_map, $saa );

  return implode( ',', $y );
}

function dagesh( $basics, $key )
{
  list ( $shortname, $longname, $char ) = $basics[ $key ];

  return [ $shortname . 'd',
           $longname . '-dag',
           $char .'ּ' ];
}

function hebraica_char_map()
{
  $basics =
    [
     'a' => [ 'al'  , 'aleph', 'א' ],
     'b' => [ 'b'   , 'bet', 'ב' ],
     'g' => [ 'g'   , 'gimel', 'ג' ],
     'd' => [ 'd', 'dalet', 'ד' ],
     'h' => [ 'h', 'hei', 'ה' ],
     'w' => [ 'v', 'vav', 'ו' ],
     'z' => [ 'z'  , 'zayin', 'ז' ],
     'j' => [ 'ch' , 'chet', 'ח' ],
     'f' => [ 'tt' , 'tet', 'ט' ],
     'y' => [ 'y', 'yod', 'י' ],
     'k' => [ 'k', 'kaf', 'כ' ],
     'l' => [ 'l'  , 'lamed', 'ל' ],
     'm' => [ 'm'  , 'mem', 'מ' ],
     'n' => [ 'n'  , 'nun', 'נ' ],
     '[' => [ 'ay', 'ayin', 'ע' ],
     's' => [ 'sa', 'samech', 'ס' ],
     'p' => [ 'p', 'pe', 'פ' ],
     'q' => [ 'q'   , 'qof', 'ק' ],
     'x' => [ 'ts', 'tsadi', 'צ' ],
     'r' => [ 'r'   , 'resh', 'ר' ],
     'v' => [ 'shsh'   , 'shin-shd', 'ש'.hu('5C1') ],
     'c' => [ 'shsi'   , 'shin-sid', 'ש'.hu('5C2') ],
     hu('E7') /* LATIN SMALL LETTER C WITH CEDILLA */ => [ 'sh'   , 'shin', 'ש' ],
     't' => [ 'tv'   , 'tav', 'ת' ],
     hu('2DA') /* RING ABOVE */ => [ 'ks' , 'kaf-sofit', 'ך' ],
     hu('2248') /* ALMOST EQUAL TO */ => [ 'tss' , 'tsadi-sofit', 'ץ' ],
     hu('3C0') /* GREEK SMALL LETTER PI */ => [ 'ps' , 'pei-sofit', 'ף' ],
     hu('00B5') /* MICRO SIGN */ => [ 'ms' , 'mem-sofit', 'ם' ],
     hu('2C6') /* MODIFIER LETTER CIRCUMFLEX ACCENT */ => [ 'ns' , 'nun-sofit', 'ן' ],

     ];

  $dageshes =
    [
     'A' => dagesh( $basics, 'a' ),
     'B' => dagesh( $basics, 'b' ),
     'G' => dagesh( $basics, 'g' ),
     'D' => dagesh( $basics, 'd' ),
     'H' => dagesh( $basics, 'h' ),
     // w
     'Z' => dagesh( $basics, 'z' ),
     // j
     'F' => dagesh( $basics, 'f' ),
     'Y' => dagesh( $basics, 'y' ),
     'K' => dagesh( $basics, 'k' ),
     'L' => dagesh( $basics, 'l' ),
     'M' => dagesh( $basics, 'm' ),
     'N' => dagesh( $basics, 'n' ),
     // [
     'S' => dagesh( $basics, 's' ),
     'P' => dagesh( $basics, 'p' ),
     'Q' => dagesh( $basics, 'q' ),
     'X' => dagesh( $basics, 'x' ),
     'R' => dagesh( $basics, 'r' ),
     'V' => dagesh( $basics, 'v' ),
     'C' => dagesh( $basics, 'c' ),
     'T' => dagesh( $basics, 't' ),
     //  => dagesh( $basics,  ),
    ];

  $others =
    [
     hu('DA') /* LATIN CAPITAL LETTER U WITH ACUTE */ => [ 'ksqm', 'kaf-sofit-qamats', hu('5DA').hu('5B8') ],

     hu('00F2') /* LATIN SMALL LETTER O WITH GRAVE */ =>
     [ 'gr', 'geresh', hu('5F3') ],

     // U+00EA LATIN SMALL LETTER E WITH CIRCUMFLEX: ???

     hu('2DD') /* DOUBLE ACUTE ACCENT */ => [ 'mq', 'maqaf', hu('5BE') ],

     'u' /* DOUBLE ACUTE ACCENT */ => [ 'qb', 'qubuts', hu('5BB') ],
     hu('A8') /* DIAERESIS */ => [ 'qb', 'qubuts', hu('5BB') ],

     'W' => [ 'vs'  , 'vav-shuruq', 'וּ' ],
     'i' => [ 'hr'  , 'hiriq', 'ִ' ],
     'I' => [ 'hr'  , 'hiriq', 'ִ' ],
     ':' => [ 'qm'  , 'qamats', 'ָ' ],
     ';' => [ 'qm'  , 'qamats', 'ָ' ],
     hu('2026') /* HORIZONTAL ELLIPSIS */ => [ 'qm'  , 'qamats', 'ָ' ],
     '"' => [ 'pt'  , 'patach', 'ַ' ],
     "'" => [ 'pt'  , 'patach', 'ַ' ],
     hu('E6') /* LATIN SMALL LETTER AE */ => [ 'pt'  , 'patach', 'ַ' ],
     hu('F8') /* LATIN SMALL LETTER O WITH STROKE */ => [ 'hh'  , 'holam-haser', 'ֹ' ],
     'O' => [ 'hh'  , 'holam-haser', 'ֹ' ],
     'o' => [ 'hh'  , 'holam-haser', 'ֹ' ],
     '/' => [ 'vhm' , 'vav-holam-male', 'וֹ' ],
     'E' => [ 'zr'  , 'zeire', 'ֵ' ],
     'e' => [ 'zr'  , 'zeire', 'ֵ' ],
     hu('B4') /* ACUTE ACCENT */ => [ 'zr'  , 'zeire', 'ֵ' ],
     // => [ 'zr'  , 'zeire', 'ֵ' ],
     ',' => [ 'sg'  , 'segol', 'ֶ' ],
     '<' => [ 'sg'  , 'segol', 'ֶ' ],
     hu('2264') /* LESS-THAN OR EQUAL TO */ => [ 'sg'  , 'segol', 'ֶ' ],
     ']' => [ 'sv'  , 'sheva', 'ְ' ],
     hu('201C') /* LEFT DOUBLE QUOTATION MARK */ => [ 'sv'  , 'sheva', 'ְ' ],
     '}' => [ 'hpt' , 'hataf-patach', hu('5B2') ],
     hu('C4') /* LATIN CAPITAL LETTER A WITH DIAERESIS */ => [ 'hpt' , 'hataf-patach', hu('5B2') ],
     hu('2018') /* LEFT SINGLE QUOTATION MARK */ => [ 'hsg' , 'hataf segol', hu('5B1') ],
     // => [ 'hsg' , 'hataf qamats', hu('5B3') ],

     hu('E2') /* LATIN SMALL LETTER A WITH CIRCUMFLEX */ => [ 'mt' , 'meteg', hu('5BD') ],

     hu('2265') /* GREATER-THAN OR EQUAL TO */ => [ 'pr', 'period', '.' ],
     ' ' => [ 'sp'   , 'space', ' ' ],
     ];

  $raw = $basics + $dageshes + $others;

  return [ 'char map name' => 'Hebrew',
           'apply to printables' => TRUE,
           'reverse' => TRUE,
           'char map itself' => $raw ];
}

function get_aap_from_rpr_chi( $rpr_child )
{
  tneve_ake( 'children', $rpr_child );

  if ( $rpr_child['name'] === 'rFonts' )
    {
      $v = $rpr_child['attributes']['ascii'];
      return [ 'font', $v ];
    }

  if ( $rpr_child['name'] === 'color' )
    {
      $v = $rpr_child['attributes']['val'];
      return [ 'color', $v ];
    }

  if ( $rpr_child['name'] === 'sz' )
    {
      $v = $rpr_child['attributes']['val'];
      return [ 'size', $v ];
    }

  if ( $rpr_child['name'] === 'kern' )
    {
      $v = $rpr_child['attributes']['val'];
      return [ 'kern', $v ];
    }

  if ( $rpr_child['name'] === 'lang' )
    {
      $attr = $rpr_child['attributes'];
      $val = lubn( 'val', $attr );
      if ( ! is_null( $val ) )
        {
          return [ 'lang-val', $val ];
        }
      $eastAsia = lubn( 'eastAsia', $attr );
      if ( ! is_null( $eastAsia ) )
        {
          return [ 'lang-eastAsia', $eastAsia ];
        }
      tneve([ 'lang with neither of the recognized attrs', $rpr_child ]);
    }

  if ( $rpr_child['name'] === 'u' )
    {
      $v = $rpr_child['attributes']['val'];
      return [ 'underline', $v ];
    }

  if ( $rpr_child['name'] === 'vertAlign' )
    {
      $v = $rpr_child['attributes']['val'];
      return [ 'vertAlign', $v ];
    }

  tneve_ake( 'attributes', $rpr_child );

  if ( $rpr_child['name'] === 'b' )
    {
      return [ 'bold', 1 ];
    }

  if ( $rpr_child['name'] === 'i' )
    {
      return [ 'italic', 1 ];
    }

  if ( $rpr_child['name'] === 'smallCaps' )
    {
      return [ 'smallCaps', 1 ];
    }

  tneve( [ 'unrecognized rpr child', $rpr_child ] );
}

function get_tables_for_rsxes( $rsxes )
{
  return array_map( 'get_table_for_rsx', $rsxes );
}

function get_table_for_rsx( $rsx )
{
  // vese( $rsx );

  $n_a_cha_rows[] = tr_of_tds( [ 'n', $rsx['name'] ] );

  $a = lubn( 'attributes', $rsx );

  if ( $a )
    {
      $n_a_cha_rows[] = tr_of_tds( [ 'a', $a ] );
    }

  $cha = lubn( 'character data', $rsx );

  if ( $cha )
    {
      $n_a_cha_rows[] = tr_of_tds( [ 'cha', $cha ] );
    }

  $htm = lubn( 'htm', $rsx );

  if ( $htm )
    {
      $n_a_cha_rows[] = tr_of_tds( [ 'htm', $htm ] );
    }

  $chi = lubn( 'children', $rsx );

  $chi_rows = $chi
    ? array_map( 'get_row_for_child', $chi )
    : [];

  $rows = array_merge( $n_a_cha_rows, $chi_rows );

  return table_b1( $rows );
}

function get_row_for_child( $child )
{
  return tr_of_tds( [ 'chi', get_table_for_rsx( $child ) ] );
}

function main( $argv )
{
  $input_filename = $argv[ 1 ];

  $title = 'Parsed ' . $input_filename;

  $css = 'BODY { background-color: #333; color: #ddd }';

  $head = html_head_contents( $title, $css );

  $body = my_html_body( $input_filename );

  $html = html_document( $head, $body );

  return $html->s;
}

echo main( $argv );

?>
