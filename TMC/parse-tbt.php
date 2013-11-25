#!/usr/bin/php -q
<?php

   // remove superfluous terminal carets (e.g. of headings)

   // allow search for places where char maps (coans) are used

   // investigate seemingly-erroneous space before --- in 'moral freedom ---a gift'

   // make footnotes (numbered and asterisk) into hyperlinks

   // indentify chaper/verse references.

   // opening (``) and closing (") double quote substitution
   // double-dash substitution
   // triple-dash substitution

   // handle &mul;

   // branch of &VB; followed by 'c:v]' goes to chapter_and_verse
   // branch of &VB; followed by 'v]' goes to verse

   // don't put space after any kind of dash (incl. hypen)

   // eliminate hypen before line break when appropriate

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

   // extra &D; documented in email

   // inconsistent paren/italic handling

   // Why are some line wraps (incl. some with hypenation) "enforced"?
   // Is this the result of some manual process?

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

  list( $r, $matches ) = preg_match_toe( $dpat, $rline );

  if ( $r )
    {
      return dollar_pline( $matches['after_dollar'], $zb_lineno );
    }

  $cpat = '/(?<command>[a-z]+)(?<after_command>.*)/';

  list( $r, $matches ) = preg_match_toe( $cpat, $rline );

  if ( $r )
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
  return array_search( $needle, $haystack ) !== FALSE;
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
        'dropper',
        'substitute1',
        'substitute2',
        'remove_line_breaks',
        'apply_char_map_d',
        ];

  $a6_blocks = array_reduce( $f, 'fl_array_map_tree', $a1_blocks );

  //return xml_wrap( 'pre', [], var_export( $a1_blocks, 1 ) );

  return table_for_lined_trees( $a6_blocks );
}

function table_for_lined_trees( $lined_trees )
{
  $trs = array_map( 'tr_for_lined_tree', $lined_trees );

  return table_b1( $trs );
}

function tr_for_lined_tree( $lined_tree )
{
  $lineno = $lined_tree['lineno'];

  $tree = $lined_tree['tree'];

  $td_lineno = 'lineno: ' . $lineno;

  $td_tree = table_for_branch( $tree );

  $tds = [ $td_lineno, $td_tree ];

  return tr_of_tds( $tds );
}

function table_for_branch( $branch )
{
  $tr_for_pusher = tr_for_pp( 'pusher', $branch );

  $tr_for_popper = tr_for_pp( 'popper', $branch );

  $nodes = $branch['nodes'];

  $trs_for_nodes = array_map( 'tr_for_node', $nodes );

  $trs = array_merge( [ $tr_for_pusher ],
                      [ $tr_for_popper ],
                      $trs_for_nodes );

  /* TODO: have mode where pushers and poppers aren't shown so
     literally so, for example, '&' (ampersand) can be searched for to
     find leaves in need of special handling. */

  return table_b1( $trs );
}

function tr_for_pp( $pusher_or_popper, array $branch )
{
  $tds[] = $pusher_or_popper;

  $tds[] = elval( $branch[ $pusher_or_popper ] );

  return tr_of_tds( $tds );
}

function tr_for_node( array $node )
{
  if ( is_branch( $node ) )
    {
      $level = $node['level'];

      $tds[] = $level > 1 ? 'branch' . $level : 'branch';

      $tds[] = table_for_branch( $node );
    }
  else
    {
      $node_type = 'leaf';
      $elval = elval( $node );
      $coan = lubn( 'char ords and names', $node );

      if ( ! is_null( $coan ) )
        {
          $node_value = table_for_elval_and_coan( $elval, $coan );
        }
      elseif( array_keys( $node ) !== [ 'eltype', 'elval' ] )
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

function table_for_elval_and_coan( $elval, $coan )
{
  $trs = [
          tr_of_tds( [ $elval ] ),
          tr_of_tds( [ table_for_coan( $coan ) ] ),
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

  return [ $r, $output ];
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

  list( $r, $matches ) = preg_match_toe( $tpat, $dollars );

  if ( $r )
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
function apply_char_map_d( $node )
{
  return apply_char_map( default_char_map(), $node );
}

function is_amp( $node )
{
  return eltype( $node ) === 'amp';
}

function is_ang( $node )
{
  return eltype( $node ) === 'ang';
}

function is_car( $node )
{
  return eltype( $node ) === 'car';
}

function is_txt( $node )
{
  return eltype( $node ) === 'txt';
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

function eltype( $node )
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

function branch_is_numeric( $branch )
{
  $nodes = $branch['nodes'];

  if ( count( $nodes ) !== 1 ) { return FALSE; }

  $number_styles = [ 'SC', 'SCI', 'sc', 'scs', 'scd', 'NN' ];

  $num_pat = '/^[[:digit:] ,\/\-]+$/';

  // Examples: '1', '12', '12--13', '12, 13, 14', '1,234', '1/2'

  $first = $branch['pusher'];

  if ( ! is_amp_in( $first, $number_styles ) ) { return FALSE; }

  // evs: elval of second
  //
  $evs = elval( $nodes[0] );

  if ( ! preg_match_toe2( $num_pat, $evs ) ) { return FALSE; }

  return $nodes[0];
}

function branch_is_initial_drop_cap( $branch )
{
  $nodes = $branch['nodes'];

  if ( count( $nodes ) !== 1 ) { return FALSE; }

  $first = $branch['pusher'];

  if ( ! is_p_amp( $first, 'in' ) ) { return FALSE; }

  return $nodes[0];
}

function substitute2( array $node )
{
  if ( is_branch( $node ) )
    {
      $r = branch_is_numeric( $node );

      if ( $r !== FALSE ) { return $r; }

      $r = branch_is_initial_drop_cap( $node );

      if ( $r !== FALSE ) { return $r; }

      $node['nodes'] = array_map( 'substitute2', $node['nodes'] );

      return $node;
    }

  return $node;
}

function apply_char_map( array $char_map, array $node )
{
  if ( is_branch( $node ) )
    {
      $first = $node['pusher'];

      $is_hebrew =
        is_p_amp( $first, 'H' )
        ||
        is_p_ang( $first, 'PAR-H' );

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

function dropper( array $node )
{
  if ( is_branch( $node ) )
    {
      $nodes = $node['nodes'];

      $filtered = array_filter( $nodes, 'preserve' );

      $node['nodes'] = array_map( 'dropper', $filtered );
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

      $ords = array_map( 'ord', $nps );

      $element['char map name'] = $char_map['char map name'];

      $element['char ords and names'] =
        array_map_pa( 'ord_and_name', $char_map, $ords );
    }

  return $element;
}

function is_ang_to_drop( $d )
{
  $elval = elval( $d );

  return is_ang( $d )
    && (
        begins_with( $elval, '<?tpl=' )
        ||
        begins_with( $elval, '<?tpt=' )
        ||
        begins_with( $elval, '<?twb' )
        /* || */
        /* $elval === '<>' */
        /* || */
        /* $elval === '<PAR-T1>' */
        /* || */
        /* $elval === '<PAR-BT1>' */
        /* || */
        /* $elval === '<PAR-AT>' */
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
               '#4', // TODO: determine whether this needs to be dropped
               '#6',
               ];

  return is_amp_in( $node, $droppers );
}

function begins_with($str, $sub)
{
    return (strncmp($str, $sub, strlen($sub)) == 0);
}

function preserve( $node )
{
  if ( is_ang_to_drop( $node )
       ||
       is_amp_to_drop( $node ) )
    {
      return FALSE;
    }
  return TRUE;
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
      amp_sem('SSN') => [ amp_element('XSN'), amp_element('xSN'), amp_element('xS') ],
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

function remove_line_breaks( array $node )
{
  if ( is_branch( $node ) )
    {
      return remove_line_breaks_from_branch( $node );
    }
  return $node;
}

/* $pspell_link = pspell_new("en"); */

/* if (pspell_check($pspell_link, "testt")) { */
/*     echo "This is a valid spelling"; */
/* } else { */
/*     echo "Sorry, wrong spelling"; */
/* } */

function remove_line_breaks_from_branch( $branch )
{
  $a = [];
  $stack = [];

  foreach ( $branch['nodes'] as $node )
  {
    $is_branch = is_branch( $node );

    $dumpstack = FALSE;

    $c = count( $stack );

    if ( $c === 0 )
      {
        if ( $is_branch )
          {
            $stack[] = $node;
          }
        else
          {
            $dumpstack = TRUE;
          }
      }
    elseif ( $c === 1 )
      {
        if ( $is_branch )
          {
            if
              (
               melder( $stack[0], $node, 'PAR-AT', 'PAR-AT' )
               ||
               melder( $stack[0], $node, 'PAR-T1', 'PAR-T1' )
               ||
               melder( $stack[0], $node, 'PAR-T', 'PAR-T1' )
               ||
               melder( $stack[0], $node, 'PAR-BT', 'PAR-BT1' )
               ||
               melder( $stack[0], $node, 'ITX', 'ITX1' )
               )
              {
                $stack = [ meld_branches( $stack[0], $node ) ];
              }
            else
              {
                $a[] = $stack[0];
                $stack = [ $node ];
              }
          }
        else
          {
            $dumpstack = TRUE;
          }
      }

    if ( $dumpstack )
      {
        foreach ( $stack as $stack_el )
          {
            $a[] = $stack_el;
          }
        $stack = [];
        $a[] = $node;
      }
  }

  foreach ( $stack as $stack_el )
    {
      $a[] = $stack_el;
    }
  $stack = [];

  $branch['nodes'] = $a;

  return $branch;
}

function melder( $b0, $b1, $ang0, $ang1 )
{
  return
    is_p_ang( $b0['pusher'], $ang0 )
    &&
    is_p_ang( $b1['pusher'], $ang1 );
}

function meld_branches( $b0, $b1 )
{
  $b0['nodes'] = array_merge( $b0['nodes'], $b1['nodes'] );

  // TODO what about other fields of $b1 than just 'nodes'?

  return $b0;
}

function meld_txt_txt( $e0, $e1 )
{
  $txt0 = $e0['elval'];
  $txt1 = $e1['elval'];

  $element = element( 'txt', $txt0 . $txt1 );

  return $element;
}

function meld_txt_lb_txt( $e0, $e1 )
{
  $txt0 = $e0['elval'];
  $txt1 = $e1['elval'];

  $jammers = [ '-', '/' ]; // others?

  // lc; last char
  //
  $lc0 = substr( $txt0, -1 );

  if ( is_in( $lc0, $jammers ) )
    {
      $glue = 'JAM';
    }
  else
    {
      $glue = ' ';
    }

  $element = element( 'txt', $txt0 . $glue . $txt1 );

  return $element;
}

/*
    $is_txt_jammer = $is_txt
      &&
      is_in( last_char( $node['elval'] ), $jammers );
*/

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
     0xA8 => 'ACUTE ACCENT',
     0xA9 => 'MODIFIER LETTER GRAVE ACCENT',
     0xAA => 'MODIFIER LETTER CIRCUMFLEX ACCENT',
     0xAB => 'DIAERESIS',
     0xAC => 'SMALL TILDE',
     0xF5 => 'modifier letter caron', // differs from HP Roman-8!
     ];

  /* Below we use "ish" in the char map name because it is only kind
     of like HP Roman-8. */

  return [ 'char map name' => 'HP Roman-8-ish',
           'char map itself' => $a ];
}

function hebrew_char_map()
{
  $raw =
    [
     209 => 'nun-sofit',
     226 => 'gimel-dagesh',
     235 => 'lamed-dagesh',
     245 => 'shin-w-sin-dot-and-dagesh',
     188 => 'hataf segol',
     163 => 'patach',
     166 => 'segol',
     171 => 'zeire',
     192 => 'qamats',
     194 => 'aleph',
     195 => 'bet',
     197 => 'dalet',
     198 => 'hei',
     200 => 'zayin',
     201 => 'chet',
     203 => 'yod',
     206 => 'lamed',
     207 => 'mem-sofit',
     208 => 'mem',
     210 => 'gimel',
     212 => 'ayin',
     217 => 'qof',
     218 => 'resh',
     219 => 'shin',
     220 => 'tav',
     221 => 'hiriq',
     222 => 'sheva',
     224 => 'holam-haser',
     225 => 'bet-dagesh',
     229 => 'vav-shuruq',
     230 => 'vav-holam-male',
     242 => 'shin-w-shin-dot',
     246 => 'tav-dagesh',
     250 => 'hataf-patach',
     ];

  return [ 'char map name' => 'Hebrew',
           'char map itself' => $raw ];
}

function ord_and_name( $char_map, $ord )
{
  $mapped_ord = lubn( $ord, $char_map['char map itself'] );

  return [ $ord, $mapped_ord ];
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
