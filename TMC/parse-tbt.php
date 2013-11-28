#!/usr/bin/php -q
<?php

   // brbr can't join COM1/COM due to htm/txt across the boundary

   // eliminate excess space inside of parens

   // allow search for places where char maps (coans) are used

   // make footnotes (numbered and asterisk) into hyperlinks

   // footnotes mushed together: should be separated by CT

   // indentify chaper/verse references.

   // opening (``) and closing (") double quote substitution
   // double-dash substitution
   // triple-dash substitution

   // handle &mul;

   // branch of &VB; followed by 'c:v]' goes to chapter_and_verse
   // branch of &VB; followed by 'v]' goes to verse

   // Pi-3 special characters:
   //    $ for asterisk

   // PI-21 special characters:
   //    xm for circumflex over x
   //    xl for acute accent over x

   // normally we ignore &#6; but it is needed here in a title:
   //
   //    Genesis&#6;and

   // extra close angle bracket in "&#131>;"?

   // wrong closing (xS) in "hbrk b&SSN;c&xS;"?

   // seemingly-erroneous space before '---'
   // in 'moral freedom ---a gift'
   // coming from
   // $[...] moral freedom \
   // $---a gift [...]
   // not a problem on paper because a line breaks after "moral"
   // unless you consider starting a line with --- (em-dash) to
   // be a stylistic error

   // extra &D; documented in email

   // inconsistent paren/italic handling

   // transliteration inside &H; seems like mistake and is inconsistent

   // Why are some line wraps (incl. some with hypenation) "enforced"?
   // Is this the result of some manual process?

   // b'reishit bara elohim wraps weirdly in printed (p. 19)
   // same with eileh tol'dot (p. 22)

require_once 'generate-html.php';

// tneve: throw new ErrorException of var_export
function tneve( $e )
{
  throw new ErrorException( var_export( $e, 1 ) );
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

// zb_lineno: zero-based line number
//
function parse_line( $zb_lineno, $line_contents )
{
  $eol = substr( $line_contents, -2 );

  if ( $eol !== "\r\n" )
    {
      tneve( [ 'unexpected end of line' => $eol ] );
    }

  $rline = substr( $line_contents, 0, -2 );

  if ( $rline === '' )
    {
      return empty_pline( $zb_lineno );
    }

  $dpat = '/(?<dollar>\$)(?<after_dollar>.+)/';

  $matches = preg_match_toe( $dpat, $rline );

  if ( ! is_null( $matches ) )
    {
      return dollar_pline( $matches['after_dollar'], $zb_lineno );
    }

  $cpat = '/(?<command>[a-z]+)(?<after_command>.*)/';

  $matches = preg_match_toe( $cpat, $rline );

  if ( ! is_null( $matches ) )
    {
      $m = [ 'verb' => $matches['command'],
             'arguments' => $matches['after_command'] ];

      return command_pline( $m, $zb_lineno );
    }

  return misc_pline( $rline, $zb_lineno );
}

function misc_pline( $body, $zb_lineno )
{
  return [ 'type' => 'misc',
           'body' => $body,
           'lineno' => $zb_lineno + 1 ];
}

function command_pline( $body, $zb_lineno )
{
  return [ 'type' => 'command',
           'body' => $body,
           'lineno' => $zb_lineno + 1 ];
}

function dollar_pline( $body, $zb_lineno )
{
  return [ 'type' => '$',
           'body' => $body,//'...',
           'lineno' => $zb_lineno + 1 ];
}

function empty_pline()
{
  return [ 'type' => 'empty' ];
}

function pline_type( $pline )
{
  return $pline['type'];
}

/* function pline_is_misc( $pline ) */
/* { */
/*   return pline_type( $pline ) === 'misc'; */
/* } */

function my_array_slice( array $a, $start, $stop )
{
  // [ 3, 4 ] goes to [ 4, 0 ]
  return array_slice( $a, $start + 1, $stop - $start - 1 );
}

function split_on( $splitter, array $a )
{
  if ( $a === [] ) { return []; }

  $strict = TRUE;

  $slocs = array_keys( $a, $splitter, $strict );

  if ( $slocs === [] ) { return [ $a ]; }

  //  01234567
  // 'abc;d;ef'
  // [ -1, 3, 5    ]
  // [     3, 5, 8 ]
  // [ -1, 3, 5 ]
  // [  3, 5, 8 ]

  $starts = array_merge( [ -1 ], $slocs );
  $stops = array_merge( $slocs, [ count( $a ) ] );

  $pa = pa( 'my_array_slice', $a );

  $a1 = array_map( $pa, $starts, $stops );

  $a2_without_empties = array_filter( $a1 );

  $a3_renumbered = array_values( $a2_without_empties );

  return $a3_renumbered;
}

/* function split_on_sem_test_inputs() */
/* { */
/*   return [ [ 'a', ';', 'b' ], */
/*            [      ';', 'b' ], */
/*            [ 'a', ';'      ], */
/*            [      ';'      ], */
/*            [], */
/*            [ 'a' ], */
/*            [ 'a', 'b' ], */
/*            [ 'a', ';', 'b', ';', 'c' ], */
/*            [ 'a', 'b', 'c', ';', 'd', ';', 'e', 'f' ], */
/*            [ 'a', ';', ';', 'b' ], */
/*            ]; */
/* } */

function io_split_on_sem( array $a )
{
  $raw_output = split_on( ';', $a );

  $nice_output = array_map( 'implode', $raw_output );

  $nice_input = implode( $a );

  return [ 'input' => $nice_input,
           'output' => $nice_output,
           'raw input' => $a,
           'raw output' => $raw_output ];
}

// ptiu: pline type is unexpected
//
function tneve_if_ptiu( $pline, $ept )
{
  $apt = pline_type( $pline );

  if ( $apt != $ept )
    {
      tneve( [ 'unexpected pline type' => $apt,
               'expected pline type' => $ept ] );
    }
}

function dollar_pline_body( $pline )
{
  tneve_if_ptiu( $pline, '$' );

  $body = $pline['body'];

  $eol = substr( $body, -1 );

  if ( $eol === '\\' )
    {
      return substr( $body, 0, -1 );
    }

  return $body;
}

function command_pline_body( $pline )
{
  tneve_if_ptiu( $pline, 'command' );

  return $pline['body'];
}

function command_pline_lineno( $pline )
{
  tneve_if_ptiu( $pline, 'command' );

  return $pline['lineno'];
}

function coalesce_block( array $block )
{
  // svs: strip verbs (verbs whose dollars should be stripped)
  //
  $svs = [ 'fgr', 'fdi' ];

  $command_pline = $block[0];

  $command_pline_body = command_pline_body( $command_pline );

  $command_pline_lineno = command_pline_lineno( $command_pline );

  $verb = $command_pline_body['verb'];

  if ( count( $block ) === 1 )
    {
      return [ 'lineno' => $command_pline_lineno,
               'dollars-absent-reason' => 'absent in original' ];
    }

  $strip_verb_found = is_in( $verb, $svs );

  if ( $strip_verb_found )
    {
      return [ 'lineno' => $command_pline_lineno,
               'dollars-absent-reason' => 'stripped' ];
    }

  $dollars = array_slice( $block, 1 );

  $vdollars = array_map( 'dollar_pline_body', $dollars );

  $ivdollars = implode( $vdollars );

  return [ 'lineno' => $command_pline_lineno,
           'dollars' => $ivdollars ];
}

function is_in( $needle, array $haystack )
{
  $strict = TRUE;

  return in_array( $needle, $haystack, $strict );
}

function html_body( $input_filename, $input )
{
  $plines = array_map_wk( 'parse_line', $input );

  // $types_of_plines = array_map( 'pline_type', $plines );

  // $pline_type_counts = [ array_count_values( $types_of_plines ) ];

  // $misc_plines = array_filter( $plines, 'pline_is_misc' );

  /* $sem_test_io = array_map( 'io_split_on_sem', */
  /*                           split_on_sem_test_inputs() ); */

  $blocks = split_on( empty_pline(), $plines );

  $cblocks = array_map( 'coalesce_block', $blocks );

  $ecblocks = array_filter( $cblocks, 'is_english' );

  $pecblocks = array_map_fakl( 'basic_parse', $ecblocks,
                               'dollars', 'elements' );

  $a1_blocks = array_map_fakl( 'tree_parse', $pecblocks,
                               'elements', 'tree' );

  $f = [
        'footnote_sort',
        'drop',
        'substitute1',
        'substitute2',
        'txttxt',
        'brbr',
        'txttxt',
        'apply_char_maps',
        'inline_hebrew',
        'txttxt',
        'inline_italics',
        'txthtm',
        ];

  $a6_blocks = array_reduce( $f, 'fl_array_map_tree', $a1_blocks );

  //return xml_wrap( 'pre', [], var_export( $a1_blocks, 1 ) );

  return tables_for_lined_trees( $a6_blocks );
}

function tables_for_lined_trees( $lined_trees )
{
  $tables = array_map( 'table_for_lined_tree', $lined_trees );

  return xml_seq( $tables );
}

function table_for_lined_tree( $lined_tree )
{
  return table_for_branch( $lined_tree['tree'] );
}

function brbred_trs( $branch )
{
  $mpu = 'brbred pushers';
  $mpo = 'brbred poppers';

  $trs = [];

  if ( array_key_exists( $mpu, $branch ) )
    {
      $trs[] = tr_of_tds( [ $mpu, var_export( $branch[ $mpu ], 1 ) ] );
    }

  if ( array_key_exists( $mpo, $branch ) )
    {
      $trs[] = tr_of_tds( [ $mpo, var_export( $branch[ $mpo ], 1 ) ] );
    }

  return $trs;
}

function table_for_branch( $branch )
{
  $tr_for_pusher = tr_for_pp( 'pusher', 'pu', $branch );

  $tr_for_popper = tr_for_pp( 'popper', 'po', $branch );

  $level = $branch['level'];

  $nodes = $branch['nodes'];

  $trs_for_nodes = array_map_pa( 'tr_for_node', $level, $nodes );

  $trs = array_merge( [ $tr_for_pusher ],
                      [ $tr_for_popper ],
                      brbred_trs( $branch ),
                      $trs_for_nodes );

  /* TODO: have mode where pushers and poppers aren't shown so
     literally so, for example, '&' (ampersand) can be searched for to
     find leaves in need of special handling. */

  return table_b1( $trs );
}

function tr_for_pp( $pusher_or_popper_key,
                    $pusher_or_popper_display,
                    array $branch )
{
  $tds[] = $pusher_or_popper_display;

  $tds[] = elval( $branch[ $pusher_or_popper_key ] );

  return tr_of_tds( $tds );
}

function tr_for_node( $level, array $node )
{
  if ( is_branch( $node ) )
    {
      $tds[] = 'b' . $level;

      $tds[] = table_for_branch( $node );
    }
  else
    {
      $node_type = 'l' . $level;
      $elval = elval( $node );
      $coan = lubn( 'char map', $node );

      if ( ! is_null( $coan ) )
        {
          $node_value = html_for_elval_and_coan( $node, $elval, $coan );
        }
      elseif ( array_keys( $node ) !== [ 'eltype', 'elval' ] )
        {
          $node_value = var_export( $node, 1 );
        }
      else
        {
          $node_value = $elval;
        }

      $tds = [ $node_type, $node_value ];
    }

  return tr_of_tds( $tds );
}

function html_for_elval_and_coan( $node, $elval, $coan )
{
  if ( array_keys( $node ) !== [ 'eltype', 'elval', 'char map' ] )
    {
      return var_export( $node, 1 );
    }

  $show_coan = FALSE;

  if ( ! $show_coan ) { return $elval; }

  $trs = [
          tr_of_tds( [ $elval ] ),
          tr_of_tds( [ table_for_coan( $coan['ords and names'] ) ] ),
          ];

  return table_b1( $trs );
}

function firsts( array $a )
{
  return array_column( $a, 0 );
}

function seconds( array $a )
{
  return array_column( $a, 1 );
}

function table_for_coan( $coan )
{
  $trs = [
          tr_of_tds( firsts( $coan ) ),
          tr_of_tds( seconds( $coan ) ),
          ];

  return table_b1( $trs );
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

// lubn: lookup, [with] behavior "null [on failure]"
//
function lubn( $k, array $a )
{
  return array_key_exists( $k, $a ) ? $a[ $k ] : NULL;
}

// toe: throw on error
//
function preg_match_toe( $pattern, $input )
{
  $output = NULL;

  $r = preg_match( $pattern, $input, $output );

  if ( $r === FALSE )
    {
      // TODO: how to provoke (i.e. test) such an error?

      tneve( [ 'preg_match error',
               'pattern' => $pattern,
               'input' => $input ] );
    }

  return $r === 1 ? $output : NULL;
}

// toe: throw on error
//
function preg_match_toe2( $pattern, $input )
{
  $r = preg_match( $pattern, $input );

  if ( $r === FALSE )
    {
      // TODO: how to provoke (i.e. test) such an error?

      tneve( [ 'preg_match error',
               'pattern' => $pattern,
               'input' => $input ] );
    }

  return $r; // 0 or 1
}

function is_english( $cblock )
{
  $dollars = lubn( 'dollars', $cblock );

  if ( is_null( $dollars ) ) { return FALSE; }

  $tpat = '/^<(?<tag>[^>]*)>/';

  $matches = preg_match_toe( $tpat, $dollars );

  if ( ! is_null( $matches ) )
    {
      $m = $matches['tag'];

      $ts = [ 'TT', 'CT', 'PAR-E', 'COM' ];

      $found = is_in( $m, $ts );

      return $found;
    }

  return FALSE;
}

function named_capture( $pat )
{
  $name = pat_name( $pat );

  $raw_pattern = $pat[1];

  $an = wrap( '<', '>', $name );

  $qan = '?' . $an;

  $body = $qan . $raw_pattern;

  return wrap( '(', ')', $body );
}

function pat_name( $pat ) { return $pat[0]; }

function pat_names( $pats ) { return array_map( 'pat_name', $pats ); }

function wrap( $a, $c, $b )
{
  return $a . $b . $c;
}

function amp_sem( $x ) { return wrap( '&', ';', $x ); }
// oab: opening angle bracket
// cab: opening angle bracket
function oab_cab( $x ) { return wrap( '<', '>', $x ); }

// amas: array_map amp_sem
//
function amas( array $a ) { return array_map( 'amp_sem', $a ); }

// amoc: array_map oab_cab
//
function amoc( array $a ) { return array_map( 'oab_cab', $a ); }

// node: a branch or a leaf

// d: default char map
//
function apply_char_maps( $node )
{
  return apply_char_map( default_char_map(), $node );
}

function is_amp( array $node )
{
  return eltype( $node ) === 'amp';
}

function is_ang( array $node )
{
  return eltype( $node ) === 'ang';
}

function is_car( array $node )
{
  return eltype( $node ) === 'car';
}

function is_txt( array $node )
{
  return eltype( $node ) === 'txt';
}

function is_htm( array $node )
{
  return eltype( $node ) === 'htm';
}

// p: particular
// i.e. is not only an amp, but has a particular elval
//
function is_p_amp( $node, $elval )
{
  return is_amp( $node )
    &&
    elval( $node ) === amp_sem( $elval );
}

// p: particular
// i.e. is not only an ang, but has a particular elval
//
function is_p_ang( $node, $elval )
{
  return is_ang( $node )
    &&
    elval( $node ) === oab_cab( $elval );
}

function is_amp_in( $node, array $elvals )
{
  return
    is_amp( $node )
    &&
    is_in( elval( $node ), amas( $elvals ) );
}

function is_ang_in( $node, array $elvals )
{
  return
    is_ang( $node )
    &&
    is_in( elval( $node ), amoc( $elvals ) );
}

function eltype( array $node )
{
  return lubn( 'eltype', $node );
}

function elval( $node )
{
  return lubn( 'elval', $node );
}

function substitute1( array $node )
{
  if ( is_branch( $node ) )
    {
      $node['nodes'] = array_map( 'substitute1', $node['nodes'] );

      return $node;
    }

  // TODO: clean this up: all three "ifs" have similar form

  if ( is_p_amp( $node, '#146' ) )
    {
      $node['eltype'] = 'txt';
      $node['elval'] = ' ';

      return $node;
    }

  if ( is_p_amp( $node, 'nk' ) )
    {
      $node['eltype'] = 'txt';
      $node['elval'] = ':';

      return $node;
    }

  if ( is_p_amp( $node, 'mk' ) )
    {
      $node['eltype'] = 'txt';
      $node['elval'] = '-';

      return $node;
    }

  return $node;
}

function replacement_for_misc( $branch )
{
  $nodes = $branch['nodes'];

  $pusher = $branch['pusher'];

  $misc = [ 'hs8', 'ib1', 'in' ];

  if ( ! is_amp_in( $pusher, $misc ) ) { return FALSE; }

  $leading_space = is_p_amp( $pusher, 'hs8' )
    ? [ element( 'txt', ' ' ) ]
    : [];

  return array_merge( $leading_space, $nodes );
}

function replacement_for_numeric( $branch )
{
  $nodes = $branch['nodes'];

  if ( count( $nodes ) !== 1 ) { return FALSE; }

  $number_styles = [ 'SC', 'SCI', 'sc', 'scs', 'scd', 'NN' ];

  $num_pat = '/^[[:digit:] ,\/\-]+$/';

  // Examples: '1', '12', '12--13', '12, 13, 14', '1,234', '1/2'

  $pusher = $branch['pusher'];

  if ( ! is_amp_in( $pusher, $number_styles ) ) { return FALSE; }

  // evfo: elval of first (and only)
  //
  $evfo = elval( $nodes[0] );

  if ( ! preg_match_toe2( $num_pat, $evfo ) ) { return FALSE; }

  return $nodes;
}

function substitute2( array $node )
{
  $a = substitute_h( 'replacement2', $node );

  return $a[0];
}

function inline_hebrew( array $node )
{
  $a = substitute_h( 'inline_hebrew_r', $node );

  return $a[0];
}

function inline_italics( array $node )
{
  $a = substitute_h( 'inline_italics_r', $node );

  return $a[0];
}

function substitute_h( $replacement_fn, array $node )
{
  if ( is_branch( $node ) )
    {
      $r = $replacement_fn( $node );

      $deep = $r === FALSE ? $node['nodes'] : $r;

      $aa = array_map_pa( 'substitute_h', $replacement_fn, $deep );

      $faa = flatten( $aa );

      if ( $r === FALSE )
        {
          $node['nodes'] = $faa;
          return [ $node ];
        }

      return $faa;
    }

  return [ $node ];
}

function replacement2( $branch )
{
  $r = replacement_for_numeric( $branch );

  if ( $r === FALSE )
    {
      $r = replacement_for_misc( $branch );
    }

  return $r;
}

function inline_hebrew_r( $branch )
{
  $pusher = $branch['pusher'];

  if ( ! is_p_amp( $pusher, 'H' ) ) { return FALSE; }

  $nodes = $branch['nodes'];

  return $nodes;
}

function inline_italics_r( $branch )
{
  $pusher = $branch['pusher'];

  $nodes = $branch['nodes'];

  if ( count( $nodes ) !== 1 ) { return FALSE; }

  if ( ! is_p_amp( $pusher, 'I' ) ) { return FALSE; }

  // evfo: elval of first (and only)
  //
  $evfo = elval( $nodes[0] );

  $newfo = element( 'htm', xml_wrap( 'i', [], $evfo ) );

  return [ $newfo ];
}

function flatten( array $a )
{
  return array_reduce( $a, 'array_merge', [] );
}

function apply_char_map( array $char_map, array $node )
{
  if ( is_branch( $node ) )
    {
      $pusher = $node['pusher'];

      $is_hebrew =
        is_p_amp( $pusher, 'H' )
        ||
        is_p_ang( $pusher, 'PAR-H' );

      $char_map = $is_hebrew ? hebrew_char_map() : default_char_map();

      $node['nodes'] =
        array_map_pa( 'apply_char_map',
                      $char_map,
                      $node['nodes'] );

      return $node;
    }

  if ( is_txt( $node ) )
    {
      return apply_char_map_to_txt( $char_map, $node );
    }

  return $node;
}

function make_pair( $a, $b ) { return [ $a, $b ]; }

function footnote_sort( array $node )
{
  if ( is_branch( $node ) )
    {
      $kns = array_map_wk( 'make_pair', $node['nodes'] );

      $success = uasort( $kns, 'node_footnote_compare' );

      if ( ! $success )
        {
          tneve(['uasort failed']);
        }

      $node['nodes'] = seconds( $kns );
    }

  return $node;
}

function node_footnote_compare( $kn0, $kn1 )
{
  $c0 = node_footnote_class( $kn0[1] );
  $c1 = node_footnote_class( $kn1[1] );

  $ccmp = cmp( $c0, $c1 );

  return $ccmp === 0
    ? cmp( $kn0[0], $kn1[0] )
    : $ccmp;
}

function node_footnote_class( $node )
{
  $is_footnote =
    is_p_ang( $node['pusher'], 'IFN' )
    ||
    is_p_ang( $node['pusher'], 'PAR-F' )
    ;

  if ( $is_footnote  )
    {
      return 1;
    }

  return 0;
}

function cmp( $a, $b )
{
  if ( $a === $b ) { return 0; }

  return ( $a < $b ) ? -1 : 1;
}

function drop( array $node )
{
  if ( is_branch( $node ) )
    {
      $nodes = $node['nodes'];

      $c = count( $nodes );

      if ( $c && is_car( $nodes[ $c-1 ] ) )
        {
          array_pop( $nodes );
        }

      $filtered = array_filter_rn( $nodes, 'do_not_drop' );

      $node['nodes'] = array_map( 'drop', $filtered );
    }

  return $node;
}

function is_branch( array $node )
{
  return array_key_exists( 'nodes', $node );
}

function has_non_printable( $x )
{
  $pat = '/[^[:print:]]/';

  return preg_match_toe2( $pat, $x );
}

function is_printable( $x )
{
  $pat = '/^[[:print:]]*$/';

  return preg_match_toe2( $pat, $x );
}

function apply_char_map_to_txt( $char_map, $element )
{
  $elval = elval( $element );

  if ( has_non_printable( $elval ) )
    {
      // eaa: elval as array of single-char strings
      //
      $eaa = str_split( $elval );

      // nps: non-printables
      //
      $nps = array_filter( $eaa, 'has_non_printable' );

      $element['char map']['name'] = $char_map['char map name'];

      $element['char map']['ords and names'] =
        array_map_pa( 'ord_and_name', $char_map, $nps );

      $element['elval'] = implode( array_map_pa( 'acm', $char_map, $eaa ) );
    }

  return $element;
}

function is_ang_to_drop( $d )
{
  $elval = elval( $d );

  return is_ang( $d )
    && (
        begins_with( $elval, '<?tlsb=' )
        ||
        begins_with( $elval, '<?tpl=' )
        ||
        begins_with( $elval, '<?tpt=' )
        ||
        begins_with( $elval, '<?th=' )
        ||
        begins_with( $elval, '<?twb' )
        ||
        $elval === '<?down>'
        ||
        $elval === '<?up>'
        ||
        $elval === '<?tf="DAN-R">'
        );
}

function is_amp_to_drop( $node )
{
   // do something other than just drop #128?

   // do something other than just drop #131?

   // Handle ellipsis represented as &#128 . &#128 . &#128 . &#128
   // ( 4 128s with 3 .s in between)

  $droppers = [
               '#128',
               '#131',
               '#132',
               '#133',
               '#134',
               '#135',
               '#136',
               ];

  return is_amp_in( $node, $droppers );
}

function begins_with($str, $sub)
{
    return (strncmp($str, $sub, strlen($sub)) == 0);
}

function do_not_drop( $node )
{
  if ( is_ang_to_drop( $node )
       ||
       is_irrelevant_whitespace( $node )
       ||
       is_amp_to_drop( $node ) )
    {
      return FALSE;
    }
  return TRUE;
}

function is_irrelevant_whitespace( $node )
{
  /* Get rid of the following 2 types of branches:

     1. empty nodes and #4 popper
     2. non-empty nodes, all of whom are #6
  */

  if ( ! is_branch( $node ) ) { return FALSE; }

  if ( $node['nodes'] === [] )
    {
      return is_p_amp( $node['popper'], '#4' );
    }

  $non_hash_6s = array_filter( $node['nodes'], 'is_non_hash_6' );

  return $non_hash_6s === [];
}

function is_non_hash_6( $node )
{
  return ! is_p_amp( $node, '#6' );
}

function get_poppers( $element )
{
  /*
    If $element is pusher, get its popper or poppers.

    If $element is not a pusher, return the empty list.
   */

  $caret = element( 'car', '^' );

  $amp_d = amp_element( 'D' );

  // e14: empty, #1, or #4
  //
  $e14 = [ ang_element( '' ), amp_element( '#1' ), amp_element( '#4' ) ];

  $ps =
    [
     'amp' =>
     [
      amp_sem('SS') => [ amp_element('XS'), amp_element('xS') ],
      amp_sem('SSN') => [ amp_element('XSN'),
                          amp_element('xSN'),
                          amp_element('xS') ],
      amp_sem('sc') => [ $amp_d, $caret ],
      amp_sem('SC') => [ $amp_d, $caret ],
      amp_sem('H') => [ $amp_d ],
      amp_sem('I') => [ $amp_d ],
      amp_sem('NN') => [ $amp_d ],
      amp_sem('SCI') => [ $amp_d ],
      amp_sem('VB') => [ $amp_d ],
      amp_sem('hs8') => [ $amp_d ],
      amp_sem('ib1') => [ $amp_d ],
      amp_sem('in') => [ $caret ],
      amp_sem('scd') => [ $amp_d ],
      amp_sem('scs') => [ $amp_d ],
      ],
     'ang' =>
     [
      oab_cab('?tvs=-5pt') => [ ang_element( '?tvs' ) ],

      oab_cab('CT') => [ ang_element( '' ) ],
      oab_cab('IAU') => [ ang_element( '' ) ],
      oab_cab('IAH') => [ ang_element( '' ) ],
      oab_cab('PAR-A') => [ ang_element( '' ) ],
      oab_cab('PAR-B') => [ ang_element( '' ) ],
      oab_cab('PAR-E') => [ ang_element( '' ) ],
      oab_cab('PAR-H') => [ ang_element( '' ) ],
      oab_cab('PAR-S') => [ ang_element( '' ) ],
      oab_cab('l')     => [ ang_element( '' ) ],

      oab_cab('TT') => $e14,
      oab_cab('TT1') => $e14,
      oab_cab('ITF') => $e14,
      oab_cab('IFN') => $e14,
      oab_cab('ITI') => $e14,
      oab_cab('ITX') => $e14,
      oab_cab('ITX1') => $e14,
      oab_cab('COM') => $e14,
      oab_cab('COM1') => $e14,
      oab_cab('COMa') => $e14,
      oab_cab('PAR-AT') => $e14,
      oab_cab('PAR-BT') => $e14,
      oab_cab('PAR-BT1') => $e14,
      oab_cab('PAR-F') => $e14,
      oab_cab('PAR-T') => $e14,
      oab_cab('PAR-T1') => $e14,
      ],
     ];

  $ps1 = lubn( eltype( $element ), $ps );

  if ( is_null( $ps1 ) ) { return []; }

  $ps2 = lubn( elval( $element ), $ps1 );

  if ( is_null( $ps2 ) ) { return []; }

  //fprintf( STDERR, var_export( $ps2, 1 ) );

  return $ps2;
}

function tree_parse( $elements )
{
  $n = 0;
  $a[0] = [ 'level' => 0,
            'pusher' => element( 'top-level pusher',
                                 'top-level pusher' ) ,
            'popper' => element( 'top-level popper',
                                 'top-level popper' ),
            'nodes' => [] ];
  $poppers_saught_stack[0] = [];

  foreach ( $elements as $element )
  {
    $poppers_saught = get_poppers( $element );

    if ( $poppers_saught ) // i.e. $element is a pusher
      {
        $n++;

        $a[$n] = [ 'level' => $n,
                   'pusher' => $element,
                   'nodes' => [] ];

        $poppers_saught_stack[$n] = $poppers_saught;
      }
    elseif ( is_in( $element, $poppers_saught_stack[$n] ) )
      {
        if ( $n === 0 )
          {
            $element['XXX excess popper'] = TRUE;
            $a[$n]['nodes'][] = $element;
          }
        else
          {
            $a[$n]['popper'] = $element;
            $a[$n-1]['nodes'][] = $a[$n];
            $a[$n] = NULL;
            $poppers_saught_stack[$n] = NULL;
            $n--;
          }
      }
    else
      {
        $a[$n]['nodes'][] = $element;
      }
  }

  while ( $n !== 0 )
    {
      $a[$n]['popper'] = element( 'auto-supplied popper',
                                  'auto-supplied popper' );
      $a[$n-1]['nodes'][] = $a[$n];
      $a[$n] = NULL;
      $n--;
    }

  return $a[0];
}

function last_char( $x )
{
  return substr( $x, -1 );
}

function brbr( array $node )
{
  return process_pairwise_2( 'pairwise_brbr', $node );
}

function txttxt( array $node )
{
  return process_pairwise_2( 'pairwise_txttxt', $node );
}

function txthtm( array $node )
{
  return process_pairwise_2( 'pairwise_txthtm', $node );
}

function process_pairwise_2( $f, array $node )
{
  if ( is_branch( $node ) )
    {
      $a = array_map_pa( 'process_pairwise_2', $f, $node['nodes'] );

      $node['nodes'] = process_pairwise( $f, $a );

      return $node;
    }

  return $node;
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

function pairwise_brbr( $b0, $b1 )
{
  return pairwise_should_brbr( $b0, $b1 )
    ? pairwise_do_the_brbr( $b0, $b1 )
    : NULL;
}

function pairwise_txttxt( $n0, $n1 )
{
  return pairwise_should_txttxt( $n0, $n1 )
    ? pairwise_do_the_txttxt( $n0, $n1 )
    : NULL;
}

function pairwise_txthtm( $n0, $n1 )
{
  return pairwise_should_txthtm( $n0, $n1 )
    ? pairwise_do_the_txthtm( $n0, $n1 )
    : NULL;
}

function pairwise_should_brbr( $n0, $n1 )
{
  return is_branch( $n0 ) && is_branch( $n1 )
    &&
    (
     brbrer( $n0, $n1, 'PAR-AT', 'PAR-AT' )
     ||
     brbrer( $n0, $n1, 'PAR-T1', 'PAR-T1' )
     ||
     brbrer( $n0, $n1, 'PAR-T', 'PAR-T1' )
     ||
     brbrer( $n0, $n1, 'PAR-BT', 'PAR-BT1' )
     ||
     brbrer( $n0, $n1, 'ITX1', 'ITX1' )
     ||
     brbrer( $n0, $n1, 'ITX', 'ITX1' )
     ||
     brbrer( $n0, $n1, 'ITI', 'ITX1' )
     ||
     brbrer( $n0, $n1, 'COM', 'COM' )
     ||
     brbrer( $n0, $n1, 'COM1', 'COM' )
     ||
     brbrer( $n0, $n1, 'COMa', 'COM' )
     ||
     brbrer( $n0, $n1, 'TT', 'TT1' )
     );
}

function brbrer( $b0, $b1, $ang0, $ang1 )
{
  return
    is_p_ang( $b0['pusher'], $ang0 )
    &&
    ! is_p_ang( $b0['popper'], '' )
    &&
    is_p_ang( $b1['pusher'], $ang1 );
}

function brbr_instr_unclear()    { return 'BRBR-UNCLEAR'; }
function brbr_instr_space()      { return 'BRBR-SPACE'; }
function brbr_instr_normal_jam() { return 'BRBR-JAM-N'; }
function brbr_instr_super_jam()  { return 'BRBR-JAM-S'; }

function pairwise_do_the_brbr( $b0, $b1 )
{
  $b0n = $b0['nodes'];
  $b1n = $b1['nodes'];

  $brbr_instr = brbr_instr( $b0n, $b1n );

  $debug = FALSE;

  if ( $brbr_instr === brbr_instr_unclear() )
    {
      $mid_nodes = [ element( 'txt', '(' . 'XXX-' . $brbr_instr . ')' ) ];
    }
  elseif ( $brbr_instr === brbr_instr_normal_jam() )
    {
      $mid_nodes = mid_nodes( $debug, $brbr_instr, '' );
    }
  elseif ( $brbr_instr === brbr_instr_super_jam() )
    {
      $b0n = do_super_jam( $b0n );
      $mid_nodes = mid_nodes( $debug, $brbr_instr, '' );
    }
  elseif ( $brbr_instr === brbr_instr_space() )
    {
      $mid_nodes = mid_nodes( $debug, $brbr_instr, ' ' );
    }
  else
    {
      tneve(['unrecognized brbr instruction' => $brbr_instr]);
    }

  $b0['nodes'] = array_merge( $b0n,
                              $mid_nodes,
                              $b1n );

  // $b0['brbred poppers'][] = $b0['popper'];
  // $b0['brbred pushers'][] = $b1['pusher'];

  $b0['popper'] = $b1['popper'];

  // TODO what about other fields of $b1 than just 'nodes'?

  return $b0;
}

function mid_nodes( $debug, $brbr_instr, $non_debug_txt )
{
  return $debug
    ? [ element( 'txt', '(' . $brbr_instr . ')' ) ]
    : [ element( 'txt', $non_debug_txt ) ];
}

function pairwise_should_txttxt( $n0, $n1 )
{
  return is_txt( $n0 ) && is_txt( $n1 );
}

function pairwise_do_the_txttxt( $e0, $e1 )
{
  $txt0 = $e0['elval'];
  $txt1 = $e1['elval'];

  $element = element( 'txt', $txt0 . $txt1 );

  return $element;
}

function pairwise_should_txthtm( $n0, $n1 )
{
  return
    is_txt( $n0 ) && is_htm( $n1 )
    ||
    is_htm( $n0 ) && is_txt( $n1 )
    ||
    is_htm( $n0 ) && is_htm( $n1 )
    ;
}

function pairwise_do_the_txthtm( $e0, $e1 )
{
  $ev0 = $e0['elval'];
  $ev1 = $e1['elval'];

  $element = element( 'htm', xml_seqa( $ev0, $ev1 ) );

  return $element;
}

function brbr_instr( $nodes0, $nodes1 )
{
  $c0 = count( $nodes0 );

  if ( $c0 === 0 ) { return brbr_instr_unclear(); }

  $last_node0 = $nodes0[ $c0 - 1 ];

  // For instance, it might be an italic branch ending in hypen.
  // We wouldn't know, so we better just say "unclear."
  //
  if ( ! is_txt( $last_node0 ) ) { return brbr_instr_unclear(); }

  $te0 = elval( $last_node0 );

  $slash = preg_match_toe2( '|/$|', $te0 ); // TODO: test

  if ( $slash ) { return brbr_instr_normal_jam(); }

  $dash = preg_match_toe2( '|-$|', $te0 );

  if ( ! $dash ) { return brbr_instr_space(); }

  $w0m = preg_match_toe( '/(\w+)-$/', $te0 );

  if ( is_null( $w0m ) ) { return brbr_instr_unclear(); }

  $w0 = $w0m[1];

  if ( count( $nodes1 ) === 0 ) { return brbr_instr_unclear(); }

  $first_of_nodes1 = $nodes1[0];

  if ( ! is_txt( $first_of_nodes1 ) ) { return brbr_instr_unclear(); }

  $te1 = elval( $first_of_nodes1 );

  $w1m = preg_match_toe( '/^(\w+)/', $te1 );

  if ( is_null( $w1m ) ) { return brbr_instr_unclear(); }

  $w1 = $w1m[1];

  $s0 = spells_a_word( $w0 );
  $s1 = spells_a_word( $w1 );

  $parts_spell_words = $s0 && $s1;

  $whole = $w0 . $w1;

  $whole_spells_a_word = spells_a_word( $whole );

  $log_line = $w0.'-'.$w1
    . ' '
    . ( $parts_spell_words ? 'yp' : 'np' )
    . ( $whole_spells_a_word ? 'yw' : 'nw' )
    . "\n";

  // fprintf( STDERR, $log_line );

  if ( $whole_spells_a_word )
    {
      return brbr_instr_super_jam();
    }

  return brbr_instr_unclear();
}

function do_super_jam( array $nodes )
{
  $c = count( $nodes );

  if ( $c === 0 ) { tneve(['super jam fail']); }

  $last_node = $nodes[ $c - 1 ];

  if ( ! is_txt( $last_node ) ) { tneve(['super jam fail']); }

  $te = elval( $last_node );

  $tes = substr( $te, 0, -1 );

  $last_node['elval'] = $tes;

  $nodes[ $c - 1 ] = $last_node;

  return $nodes;
}

function spells_a_word( $x )
{
  $pspell_link = pspell_new('en');

  return pspell_check( $pspell_link, $x );
}


function basic_parse( $dollars )
{
  //$x = basic_parse( '<aaa><>&bbb;ccc&d;<ee>f' );

  $pats =
    [
     [ 'amp', '&[^;]*;' ],
     [ 'ang', '<[^>]*>' ],
     [ 'car', '\^' ],
     [ 'txt', '[^&<\^]+' ],
     ];

  // ppats: parenthesized pats
  //
  $ppats = array_map( 'named_capture', $pats );

  // oppats: OR'ed, parenthesized pats
  //
  $oppats = implode( '|', $ppats );

  $tpat = '/'. $oppats . '/';

  $r = preg_match_all( $tpat, $dollars, $m, PREG_SET_ORDER );

  if ( $r === FALSE )
    {
      tneve( [ 'preg_match_all error',
               'pattern' => $tpat,
               'input' => $dollars ] );
    }

  $pat_names = pat_names( $pats );

  // labelled matches, e.g. '<PAR-E>' becomes [ 'ang', 'PAR-E' ]
  //
  return array_map_pa( 'label_match', $pat_names, $m );
}

/*
  Example of $match:
      array (
        0 => '<PAR-E>',
        'amp' => '',
        1 => '',
        'ang' => '<PAR-E>',
        2 => '<PAR-E>',
      ),

  Examples of $aik: (gets rid of numeric-keyed elements)
      array (
        'amp' => '',
        'ang' => '<PAR-E>',
      ),

  Example of $af: (gets rid of empty-string-valued elements)
      array (
        'ang' => '<PAR-E>',
      ),

  Example of $kvp:
      array (
        0 => 'ang',
        1 => '<PAR-E>',
      ),

 */

function label_match( $pat_names, $match )
{
  $fpn = array_flip( $pat_names );

  $aik = array_intersect_key( $match, $fpn );

  $af = array_filter( $aik );

  $ks = array_keys( $af );

  $vs = array_values( $af );

  return element( $ks[0], $vs[0] );
}

function element( $type, $value )
{
  $element = [ 'eltype' => $type, 'elval' => $value ];

  return $element;
}

function amp_element( $value )
{
  return element ( 'amp', amp_sem( $value ) );
}

function ang_element( $value )
{
  return element ( 'ang', oab_cab( $value ) );
}

function default_char_map()
{
  $a =
    [
     0xA8 => [ 'aa', 'ACUTE ACCENT', hu('301') ],
     //0xA9 => [ 'mlga', 'MODIFIER LETTER GRAVE ACCENT', hu('300') ],
     0xAA => [ 'mlca', 'MODIFIER LETTER CIRCUMFLEX ACCENT', hu('302') ],
     0xAB => [ 'dia', 'DIAERESIS', hu('308') ],
     0xAC => [ 'til', 'SMALL TILDE', hu('303') ],
     0xF5 => [ 'mlcaron', 'modifier letter caron', hu('30C') ],
     // above (caron) differs from HP Roman-8!
     ];

  /* Below we use "ish" in the char map name because it is only kind
     of like HP Roman-8. */

  return [ 'char map name' => 'HP Roman-8-ish',
           'char map itself' => $a ];
}


function hu( $hex ) // hu: hex to utf-8
{
  return html_entity_decode('&#x'.$hex.';', ENT_COMPAT, 'UTF-8');
}

function hebrew_char_map()
{
  $raw =
    [
     163 => [ 'pt'  , 'patach', 'ַ' ],
     166 => [ 'sg'  , 'segol', 'ֶ' ],
     171 => [ 'zr'  , 'zeire', 'ֵ' ],
     188 => [ 'hsg' , 'hataf segol', 'ֱ' ],
     192 => [ 'qm'  , 'qamats', 'ָ' ],
     194 => [ 'al'  , 'aleph', 'א' ],
     195 => [ 'b'   , 'bet', 'ב' ],
     // gimmel? (g)
     197 => [ 'd', 'dalet', 'ד' ],
     198 => [ 'h', 'hei', 'ה' ],
     // vav? (v)
     200 => [ 'z'  , 'zayin', 'ז' ],
     201 => [ 'ch' , 'chet', 'ח' ],
     // tet? (tt)
     203 => [ 'y', 'yod', 'י' ],
     // kaf sofit (ks)
     // kaf (k)
     206 => [ 'l'  , 'lamed', 'ל' ],
     207 => [ 'ms' , 'mem-sofit', 'ם' ],
     208 => [ 'm'  , 'mem', 'מ' ],
     209 => [ 'ns' , 'nun-sofit', 'ן' ],
     210 => [ 'n'  , 'nun', 'נ' ],
     // samech?
     212 => [ 'ay', 'ayin', 'ע' ],
     // pei sofit? (ps)
     // pei (p)
     // tsadi sofit (tss)
     // tsadi (ts)
     217 => [ 'q'   , 'qof', 'ק' ],
     218 => [ 'r'   , 'resh', 'ר' ],
     219 => [ 'sh'   , 'shin', 'ש' ],
     220 => [ 'tv'   , 'tav', 'ת' ],
     221 => [ 'hr'  , 'hiriq', 'ִ' ],
     222 => [ 'sv'  , 'sheva', 'ְ' ],
     224 => [ 'hh'  , 'holam-haser', 'ֹ' ],
     225 => [ 'bd'  , 'bet-dagesh', 'בּ' ],
     226 => [ 'gd'  , 'gimel-dagesh', 'גּ' ],
     229 => [ 'vs'  , 'vav-shuruq', 'וּ' ],
     230 => [ 'vhm' , 'vav-holam-male', 'וֹ' ],
     235 => [ 'ld'  , 'lamed-dagesh', 'לּ' ],
     242 => [ 'shsh'   , 'shin-w-shin-dot', 'שׁ' ],
     245 => [ 'shsid' , 'shin-w-sin-dot-and-dagesh', 'שֹּ' ],
     246 => [ 'td'  , 'tav-dagesh', 'תּ' ],
     250 => [ 'hpt' , 'hataf-patach', 'ֲ' ],
     ];

  return [ 'char map name' => 'Hebrew',
           'char map itself' => $raw ];
}

function cm_lookup( $char_map, $ord )
{
  $r = lubn( $ord, $char_map['char map itself'] );

  if ( is_null( $r ) )
    {
      $name = 'XXX-unknown-' . $ord;

      return [ 'XXX', $name, '('.$name.')' ];
    }

  return $r;
}

function ord_and_name( $char_map, $char )
{
  $ord = ord( $char );

  $mapped_ord = cm_lookup( $char_map, $ord );

  return [ $ord, $mapped_ord[0] ];
}

function acm( $char_map, $char )
{
  if ( is_printable( $char ) ) { return $char; }

  $ord = ord( $char );

  $mapped_ord = cm_lookup( $char_map, $ord );

  return $mapped_ord[2];
}

function array_map_tree( $f, array $a )
{
  return array_map_fakl( $f, $a, 'tree', 'tree' );
}

// fl: flipped, i.e. arg order flipped
//
function fl_array_map_tree( array $a, $f )
{
  return array_map_tree( $f, $a );
}

function array_map_fakl( $f, array $a, $k, $l )
{
  return array_map( pa( 'fkla', $f, $k, $l ), $a );
}

function fkla( $f, $k, $l, array $a )
{
  $a[$l] = $f( $a[$k] );

  if ( $k !== $l ) { unset( $a[$k] ); }

  return $a;
}

function main( $argv )
{
  $input_filename = $argv[ 1 ];

  $input = file( $input_filename );

  if ( $input === FALSE )
    {
      tneve( [ 'error opening' => $input_filename ] );
    }

  $head = html_head_contents( 'Parsed ' . $input_filename );

  $body = html_body( $input_filename, $input );

  $html = html_document( $head, $body );

  return $html->s;
}

echo main( $argv );

?>
