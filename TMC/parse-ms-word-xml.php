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

// rsx: really simple xml

function get_rsx_from_file( $input_filename )
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

  return get_rsx_from_sde( $sde );
}

function get_sde_from_tsxml( $sxml )
{
  $r = NULL;

  foreach ($sxml->children('pkg',TRUE) as $second_gen)
    {
      foreach ($second_gen->children('pkg',TRUE) as $third_gen)
        {
          foreach ($third_gen->children('w',TRUE) as $fourth_gen)
            {
              if ( $fourth_gen->getName() === 'document' )
                {
                  if ( ! is_null( $r ) )
                    {
                      tneve( 'more than one document found' );
                    }
                  $r = $fourth_gen;
                }
            }
        }
    }

  return $r;
}

function my_html_body( $input_filename )
{
  $rsx = get_rsx_from_file( $input_filename );

  $srsx = simplify_rsx( $rsx );

  $table = get_table_for_rsx( $srsx );

  return $table;
}

function get_rsx_from_sde( $sde )
{
  return get_rsx_from_sxml( $sde );
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

function simplify_rsx( $r )
{
  if ( $r['name'] === 'rPr' )
    {
      tneve_ake( 'attributes', $r );

      // aap: attributes as pairs
      $aap = array_map( 'get_aap_from_rpr_chi',
                        $r['children'] );

      $r['attributes'] = kvs_from_pairs( $aap );
      unset( $r['children'] );

      return $r;
    }

  if ( $r['name'] === 'r' )
    {
      $chi = $r['children'];

      if ( $chi[0]['name'] === 'rPr' )
        {
          $schi0 = simplify_rsx( $chi[0] );
          $schi0a = $schi0['attributes'];
          if ( lubn( 'font', $schi0a ) === 'Hebraica' )
            {
              $chi1 = $chi[1];

              if ( $chi1['name'] === 't' )
                {
                  $r['children'][0] = $schi0;
                  $c = $r['children'][1]['character data'];
                  $r['children'][1]['character data'] = apply_hebraica_char_map( $c );
                  return $r;
                }
            }
        }
    }

  if ( array_key_exists( 'children', $r ) )
    {
      $r['children'] = array_map( 'simplify_rsx', $r['children'] );
    }

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
  $saa = str_split( $s );

  $r = implode( ',', array_map( 'printed_representation_of_char', $saa ) );

  $m = implode( array_map_pa( 'acm', $char_map, $saa ) );

  return $m.' '.$r;
}

function dagesh( $basics, $c )
{
  $key = is_string( $c ) ? ord( $c ) : $c;

  list ( $shortname, $longname, $char ) = $basics[ $key ];

  return [ $shortname . 'd',
           $longname . '-dag',
           $char .'ּ' ];
}

function hebraica_char_map()
{
  $basics =
    [
     ord('a') => [ 'al'  , 'aleph', 'א' ],
     ord('b') => [ 'b'   , 'bet', 'ב' ],
     ord('g') => [ 'g'   , 'gimel', 'ג' ],
     ord('d') => [ 'd', 'dalet', 'ד' ],
     ord('h') => [ 'h', 'hei', 'ה' ],
     ord('w') => [ 'v', 'vav', 'ו' ],
     ord('z') => [ 'z'  , 'zayin', 'ז' ],
     ord('j') => [ 'ch' , 'chet', 'ח' ],
     ord('f') => [ 'tt' , 'tet', 'ט' ],
     ord('y') => [ 'y', 'yod', 'י' ],
     ord('k') => [ 'k', 'kaf', 'כ' ],
     ord('l') => [ 'l'  , 'lamed', 'ל' ],
     ord('m') => [ 'm'  , 'mem', 'מ' ],
     ord('n') => [ 'n'  , 'nun', 'נ' ],
     ord('[') => [ 'ay', 'ayin', 'ע' ],
     ord('s') => [ 'sa', 'samech', 'ס' ],
     ord('p') => [ 'p', 'pe', 'פ' ],
     ord('q') => [ 'q'   , 'qof', 'ק' ],
     ord('x') => [ 'ts', 'tsadi', 'צ' ],
     ord('r') => [ 'r'   , 'resh', 'ר' ],
     ord('v') => [ 'shsh'   , 'shin-shd', 'ש'.hu('5C1') ],
     ord('c') => [ 'shsi'   , 'shin-sid', 'ש'.hu('5C2') ],
     0xA7 => [ 'shsi'   , 'shin-sid', 'ש'.hu('5C2') ],
     /* 0x8D */ 141 => [ 'sh'   , 'shin', 'ש' ],
     ord('t') => [ 'tv'   , 'tav', 'ת' ],
     251 => [ 'ks' , 'kaf-sofit', 'ך' ],
     ];

  $dageshes =
    [
     ord('A') => dagesh( $basics, 'a' ),
     ord('B') => dagesh( $basics, 'b' ),
     ord('G') => dagesh( $basics, 'g' ),
     ord('D') => dagesh( $basics, 'd' ),
     ord('H') => dagesh( $basics, 'h' ),
     // w
     ord('Z') => dagesh( $basics, 'z' ),
     // j
     ord('F') => dagesh( $basics, 'f' ),
     ord('Y') => dagesh( $basics, 'y' ),
     ord('K') => dagesh( $basics, 'k' ),
     ord('L') => dagesh( $basics, 'l' ),
     ord('M') => dagesh( $basics, 'm' ),
     ord('N') => dagesh( $basics, 'n' ),
     // [
     ord('S') => dagesh( $basics, 's' ),
     ord('P') => dagesh( $basics, 'p' ),
     ord('Q') => dagesh( $basics, 'q' ),
     ord('X') => dagesh( $basics, 'x' ),
     ord('R') => dagesh( $basics, 'r' ),
     ord('V') => dagesh( $basics, 'v' ),
     ord('C') => dagesh( $basics, 'c' ),
     ord('T') => dagesh( $basics, 't' ),
     240      => dagesh( $basics, 251 ),
    ];

  $others =
    [
     197 => [ 'tss' , 'tsadi-sofit', 'ץ' ],
     185 => [ 'ps' , 'pei-sofit', 'ף' ],
     0xB5 => [ 'ms' , 'mem-sofit', 'ם' ],
     134 => [ 'ns' , 'nun-sofit', 'ן' ],


     ord('W') => [ 'vs'  , 'vav-shuruq', 'וּ' ],
     ord('i') => [ 'hr'  , 'hiriq', 'ִ' ],
     ord('I') => [ 'hr'  , 'hiriq', 'ִ' ],
     ord(':') => [ 'qm'  , 'qamats', 'ָ' ],
     ord(';') => [ 'qm'  , 'qamats', 'ָ' ],
     226 => [ 'qm'  , 'qamats', 'ָ' ],
     ord('"') => [ 'pt'  , 'patach', 'ַ' ],
     ord("'") => [ 'pt'  , 'patach', 'ַ' ],
     0xC3 => [ 'hh'  , 'holam-haser', 'ֹ' ], // XXX see ish
     ord('O') => [ 'hh'  , 'holam-haser', 'ֹ' ],
     ord('o') => [ 'hh'  , 'holam-haser', 'ֹ' ],
     ord('/') => [ 'vhm' , 'vav-holam-male', 'וֹ' ],
     ord('E') => [ 'zr'  , 'zeire', 'ֵ' ],
     ord('e') => [ 'zr'  , 'zeire', 'ֵ' ],
     180 => [ 'zr'  , 'zeire', 'ֵ' ],
     ord(',') => [ 'sg'  , 'segol', 'ֶ' ],
     ord('<') => [ 'sg'  , 'segol', 'ֶ' ],
     ord(']') => [ 'sv'  , 'sheva', 'ְ' ],
     ord('}') => [ 'hpt' , 'hataf-patach', hu('5B2') ],
     0xE2 => [ 'hsg' , 'hataf segol', hu('5B1') ],
     213 => [ 'hsg' , 'hataf qamats', hu('5B3') ],

     194 => [ 'dr' , 'drop', '' ],
     184 => [ 'dr' , 'drop', '' ],
     128 => [ 'dr' , 'drop', '' ],
     152 => [ 'dr' , 'drop', '' ],
     203 => [ 'dr' , 'drop', '' ],
     157 => [ 'dr' , 'drop', '' ],
     166 => [ 'dr' , 'drop', '' ],

     ord(' ') => [ 'sp'   , 'space', ' ' ],
     //ord('') => [ '', '', '' ],
     //ord('') => [ '', '', '' ],
     //ord('') => [ '', '', '' ],
     ];

  $raw = $basics + $dageshes + $others;

  return [ 'char map name' => 'Hebrew',
           'apply to printables' => TRUE,
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
