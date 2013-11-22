#!/usr/bin/php -q
<?php

   // PI-21 special characters:
   //    xm for circumflex over x
   //    xl for acute accent over x

   // PAR-H should be recognized as Hebrew char map

   // &nk; goes to :

   // &mk; goes to -

   // branch of &scs; followed by number goes to just number
   // branch of &scd; followed by number goes to just number
   // branch of &sc;  followed by number goes to just number
   // branch of &SC;  followed by number goes to just number
   // branch of &NN;  followed by number goes to just number
   // branch of &NN;  followed by number range (A--B) to just number range

   // branch of &scs; followed by single char goes to just single char

   // don't put space after any kind of dash (incl. hypen)

   // eliminate hypen before line break when appropriate

   // branch of &VB; followed by 'c:v]' goes to chapter_and_verse
   // branch of &VB; followed by 'v]' goes to verse

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

  $eol = substr( $body, -2 );

  if ( $eol === ' \\' )
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

  $strip_verb_found = array_search( $verb, $svs ) !== FALSE;

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

  $a2_blocks = array_map_tree( 'dropper', $a1_blocks );

  $a3_blocks = array_map_tree( 'substitute', $a2_blocks );

  $a4_blocks = array_map_tree( 'remove_line_breaks', $a3_blocks );

  $a5_blocks = array_map_tree( 'apply_char_map_d', $a4_blocks );

  //return xml_wrap( 'pre', [], var_export( $a5_blocks, 1 ) );

  return table_for_lined_trees( $a5_blocks );
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
  $trs = array_map( 'tr_for_tree_node', $branch['tree nodes'] );

  return table_b1( $trs );
}

function tr_for_tree_node( $tree_node )
{
  if ( is_branch( $tree_node ) )
    {
      $node_type = 'branch';
      $node_value = table_for_branch( $tree_node );
      $tds = [ $node_type, $node_value ];
    }
  else
    {
      $node_type = 'leaf';
      $elval = elval( $tree_node );
      $coan = lubn( 'char ords and names', $tree_node );

      if ( is_null( $coan ) )
        {
          $node_value = elval( $tree_node );
        }
      else
        {
          $node_value = table_for_elval_and_coan( $elval, $coan );
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

      $found = array_search( $m, $ts ) !== FALSE;

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

// tree node: a branch or a leaf

// d: default char map
//
function apply_char_map_d( $tree_node )
{
  return apply_char_map( default_char_map(), $tree_node );
}

function is_amp( $tree_node )
{
  return eltype( $tree_node ) === 'amp';
}

function is_ang( $tree_node )
{
  return eltype( $tree_node ) === 'ang';
}

function is_car( $tree_node )
{
  return eltype( $tree_node ) === 'car';
}

function is_txt( $tree_node )
{
  return eltype( $tree_node ) === 'txt';
}

// p: particular
// i.e. is not only an amp, but has a particular elval
//
function is_p_amp( $tree_node, $elval )
{
  return is_amp( $tree_node )
    &&
    elval( $tree_node ) === amp_sem( $elval );
}

function eltype( $tree_node )
{
  return lubn( 'eltype', $tree_node );
}

function elval( $tree_node )
{
  return lubn( 'elval', $tree_node );
}

function substitute( $tree_node )
{
  if ( is_branch( $tree_node ) )
    {
      $tree_node['tree nodes'] =
        array_map( 'substitute',
                   $tree_node['tree nodes'] );

      return $tree_node;
    }

  if ( is_p_amp( $tree_node, '#146' ) )
    {
      $tree_node['eltype'] = 'txt';
      $tree_node['elval'] = ' ';

      return $tree_node;
    }

  return $tree_node;
}

function apply_char_map( $char_map, $tree_node )
{
  if ( is_branch( $tree_node ) )
    {
      $is_hebrew = is_p_amp( $tree_node['tree nodes'][0], 'H' );

      $char_map = $is_hebrew ? hebrew_char_map() : default_char_map();

      $tree_node['tree nodes'] =
        array_map_pa( 'apply_char_map',
                      $char_map,
                      $tree_node['tree nodes'] );

      return $tree_node;
    }

  if ( is_txt( $tree_node ) )
    {
      return apply_char_map_to_txt( $char_map, $tree_node );
    }

  return $tree_node;
}

function dropper( $tree_node )
{
  if ( is_branch( $tree_node ) )
    {
      $tree_nodes = $tree_node['tree nodes'];

      $filtered = array_filter( $tree_nodes, 'preserve' );

      $tree_node['tree nodes'] = array_map( 'dropper', $filtered );
    }

  return $tree_node;
}

function is_branch( $tree_node )
{
  return array_key_exists( 'tree nodes', $tree_node );
}

function has_non_printable( $x )
{
  $pat = '/[^[:print:]]/';

  list( $r, $matches ) = preg_match_toe( $pat, $x );

  return $r;
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
        ||
        $elval === '<>'
        ||
        $elval === '<PAR-T1>'
        ||
        $elval === '<PAR-BT1>'
        ||
        $elval === '<PAR-AT>'
        );
}

function is_amp_to_drop( $d )
{
  $elval = elval( $d );

  return is_amp( $d )
    && (
        $elval === amp_sem( '#132' )
        ||
        $elval === amp_sem( '#133' )
        ||
        $elval === amp_sem( '#134' )
        ||
        $elval === amp_sem( '#135' )
        ||
        $elval === amp_sem( '#136' )
        ||
        $elval === amp_sem( '#4' )
        ||
        $elval === amp_sem( '#6' ) );
}

function begins_with($str, $sub)
{
    return (strncmp($str, $sub, strlen($sub)) == 0);
}

function preserve( $tree_node )
{
  if ( is_ang_to_drop( $tree_node )
       ||
       is_amp_to_drop( $tree_node ) )
    {
      return FALSE;
    }
  return TRUE;
}

function tree_parse( $elements )
{
  $raw_pushers = [ 'in', 'sc', 'SC', 'scs', 'scd', 'hs8', 'ib1',
                   'H', 'I', 'NN', 'VB', 'SCI' ];

  $pushers = array_map( 'amp_sem', $raw_pushers );

  $n = 0;
  $a[0] = [ 'level' => 0,
            'tree nodes' => [] ];

  foreach ( $elements as $element )
  {
    $is_a_pusher =
      is_amp( $element )
      &&
      array_search( elval( $element ), $pushers ) !== FALSE;

    $is_car   = is_car( $element );
    $is_amp_d = is_p_amp( $element, 'D' );

    $is_a_popper = $n > 0 && ( $is_car || $is_amp_d );

    if ( $is_a_pusher )
      {
        $n++;

        $a[$n] = [ 'level' => $n,
                   'tree nodes' => [ $element ] ];
      }
    elseif ( $is_a_popper )
      {
        $n--;
        if ( $n < 0 )
          {
            tneve( [ 'n < 0' => $element ] );
          }
        $a[$n]['tree nodes'][] = $a[$n+1];
        $a[$n+1] = NULL;
      }
    else
      {
        $a[$n]['tree nodes'][] = $element;
      }
  }

  return $a[0];
}

function last_char( $x )
{
  return substr( $x, -1 );
}

function remove_line_breaks( $tree_node )
{
  if ( is_branch( $tree_node ) )
    {
      return remove_line_breaks_from_branch( $tree_node );
    }
  return $tree_node;
}

function remove_line_breaks_from_branch( $branch )
{
  $jammers = [ '-', '/' ]; // TODO: others?

  $a = [];
  $stack = [];

  foreach ( $branch['tree nodes'] as $tree_node )
  {
    if ( is_branch( $tree_node ) )
      {
        $tree_node = remove_line_breaks_from_branch( $tree_node );
      }

    $is_txt = is_txt( $tree_node );

    $is_lb = is_p_amp( $tree_node, '#1' ); // lb: line break

    $dumpstack = FALSE;

    $c = count( $stack );

    if ( $c === 0 )
      {
        if ( $is_txt )
          {
            $stack[] = $tree_node;
          }
        else
          {
            $dumpstack = TRUE;
          }
      }
    elseif ( $c === 1 )
      {
        if ( $is_lb )
          {
            $stack[] = $tree_node;
          }
        elseif ( $is_txt )
          {
            $stack = [ melded_text( $stack[0], $tree_node, '' ) ];
          }
        else
          {
            $dumpstack = TRUE;
          }
      }
    elseif ( $c === 2 )
      {
        if ( $is_txt )
          {
            $stack = [ melded_text( $stack[0], $tree_node, ' ' ) ];
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
        $a[] = $tree_node;
      }
  }

  foreach ( $stack as $stack_el )
    {
      $a[] = $stack_el;
    }
  $stack = [];

  $branch['tree nodes'] = $a;

  return $branch;
}

function melded_text( $e0, $e1, $glue )
{
  $txt0 = $e0['elval'];
  $txt1 = $e1['elval'];

  $element = element( 'txt', $txt0 . $glue . $txt1 );

  $element['melded'] = TRUE;

  return $element;
}

/*
    $is_txt_jammer = $is_txt
      &&
      array_search( last_char( $tree_node['elval'] ), $jammers ) !== FALSE;
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
      tneve( [ 'failed to basic_parse' => $dollars ] );
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

function hp_roman_8()
{
  return
    [
     [ 0x00, 0x0000, NULL, 'NULL' ],
     [ 0x01, 0x0001, NULL, 'START OF HEADING' ],
     [ 0x02, 0x0002, NULL, 'START OF TEXT' ],
     [ 0x03, 0x0003, NULL, 'END OF TEXT' ],
     [ 0x04, 0x0004, NULL, 'END OF TRANSMISSION' ],
     [ 0x05, 0x0005, NULL, 'ENQUIRY' ],
     [ 0x06, 0x0006, NULL, 'ACKNOWLEDGE' ],
     [ 0x07, 0x0007, NULL, 'BELL' ],
     [ 0x08, 0x0008, NULL, 'BACKSPACE' ],
     [ 0x09, 0x0009, NULL, 'CHARACTER TABULATION' ],
     [ 0x0A, 0x000A, NULL, 'LINE FEED (LF)' ],
     [ 0x0B, 0x000B, NULL, 'LINE TABULATION' ],
     [ 0x0C, 0x000C, NULL, 'FORM FEED (FF)' ],
     [ 0x0D, 0x000D, NULL, 'CARRIAGE RETURN (CR)' ],
     [ 0x0E, 0x000E, NULL, 'SHIFT OUT' ],
     [ 0x0F, 0x000F, NULL, 'SHIFT IN' ],
     [ 0x10, 0x0010, NULL, 'DATA LINK ESCAPE' ],
     [ 0x11, 0x0011, NULL, 'DEVICE CONTROL ONE' ],
     [ 0x12, 0x0012, NULL, 'DEVICE CONTROL TWO' ],
     [ 0x13, 0x0013, NULL, 'DEVICE CONTROL THREE' ],
     [ 0x14, 0x0014, NULL, 'DEVICE CONTROL FOUR' ],
     [ 0x15, 0x0015, NULL, 'NEGATIVE ACKNOWLEDGE' ],
     [ 0x16, 0x0016, NULL, 'SYNCHRONOUS IDLE' ],
     [ 0x17, 0x0017, NULL, 'END OF TRANSMISSION BLOCK' ],
     [ 0x18, 0x0018, NULL, 'CANCEL' ],
     [ 0x19, 0x0019, NULL, 'END OF MEDIUM' ],
     [ 0x1A, 0x001A, NULL, 'SUBSTITUTE' ],
     [ 0x1B, 0x001B, NULL, 'ESCAPE' ],
     [ 0x1C, 0x001C, NULL, 'INFORMATION SEPARATOR FOUR' ],
     [ 0x1D, 0x001D, NULL, 'INFORMATION SEPARATOR THREE' ],
     [ 0x1E, 0x001E, NULL, 'INFORMATION SEPARATOR TWO' ],
     [ 0x1F, 0x001F, NULL, 'INFORMATION SEPARATOR ONE' ],
     [ 0x20, 0x0020, NULL, 'SPACE' ],
     [ 0x21, 0x0021, '!', 'EXCLAMATION MARK' ],
     [ 0x22, 0x0022, '"', 'QUOTATION MARK' ],
     [ 0x23, 0x0023, '#', 'NUMBER SIGN' ],
     [ 0x24, 0x0024, '$', 'DOLLAR SIGN' ],
     [ 0x25, 0x0025, '%', 'PERCENT SIGN' ],
     [ 0x26, 0x0026, '&', 'AMPERSAND' ],
     [ 0x27, 0x0027, '\'', 'APOSTROPHE' ],
     [ 0x28, 0x0028, '(', 'LEFT PARENTHESIS' ],
     [ 0x29, 0x0029, ')', 'RIGHT PARENTHESIS' ],
     [ 0x2A, 0x002A, '*', 'ASTERISK' ],
     [ 0x2B, 0x002B, '+', 'PLUS SIGN' ],
     [ 0x2C, 0x002C, ',', 'COMMA' ],
     [ 0x2D, 0x002D, '-', 'HYPHEN-MINUS' ],
     [ 0x2E, 0x002E, '.', 'FULL STOP' ],
     [ 0x2F, 0x002F, '/', 'SOLIDUS' ],
     [ 0x30, 0x0030, '0', 'DIGIT ZERO' ],
     [ 0x31, 0x0031, '1', 'DIGIT ONE' ],
     [ 0x32, 0x0032, '2', 'DIGIT TWO' ],
     [ 0x33, 0x0033, '3', 'DIGIT THREE' ],
     [ 0x34, 0x0034, '4', 'DIGIT FOUR' ],
     [ 0x35, 0x0035, '5', 'DIGIT FIVE' ],
     [ 0x36, 0x0036, '6', 'DIGIT SIX' ],
     [ 0x37, 0x0037, '7', 'DIGIT SEVEN' ],
     [ 0x38, 0x0038, '8', 'DIGIT EIGHT' ],
     [ 0x39, 0x0039, '9', 'DIGIT NINE' ],
     [ 0x3A, 0x003A, ':', 'COLON' ],
     [ 0x3B, 0x003B, ';', 'SEMICOLON' ],
     [ 0x3C, 0x003C, '<', 'LESS-THAN SIGN' ],
     [ 0x3D, 0x003D, '=', 'EQUALS SIGN' ],
     [ 0x3E, 0x003E, '>', 'GREATER-THAN SIGN' ],
     [ 0x3F, 0x003F, '?', 'QUESTION MARK' ],
     [ 0x40, 0x0040, '@', 'COMMERCIAL AT' ],
     [ 0x41, 0x0041, 'A', 'LATIN CAPITAL LETTER A' ],
     [ 0x42, 0x0042, 'B', 'LATIN CAPITAL LETTER B' ],
     [ 0x43, 0x0043, 'C', 'LATIN CAPITAL LETTER C' ],
     [ 0x44, 0x0044, 'D', 'LATIN CAPITAL LETTER D' ],
     [ 0x45, 0x0045, 'E', 'LATIN CAPITAL LETTER E' ],
     [ 0x46, 0x0046, 'F', 'LATIN CAPITAL LETTER F' ],
     [ 0x47, 0x0047, 'G', 'LATIN CAPITAL LETTER G' ],
     [ 0x48, 0x0048, 'H', 'LATIN CAPITAL LETTER H' ],
     [ 0x49, 0x0049, 'I', 'LATIN CAPITAL LETTER I' ],
     [ 0x4A, 0x004A, 'J', 'LATIN CAPITAL LETTER J' ],
     [ 0x4B, 0x004B, 'K', 'LATIN CAPITAL LETTER K' ],
     [ 0x4C, 0x004C, 'L', 'LATIN CAPITAL LETTER L' ],
     [ 0x4D, 0x004D, 'M', 'LATIN CAPITAL LETTER M' ],
     [ 0x4E, 0x004E, 'N', 'LATIN CAPITAL LETTER N' ],
     [ 0x4F, 0x004F, 'O', 'LATIN CAPITAL LETTER O' ],
     [ 0x50, 0x0050, 'P', 'LATIN CAPITAL LETTER P' ],
     [ 0x51, 0x0051, 'Q', 'LATIN CAPITAL LETTER Q' ],
     [ 0x52, 0x0052, 'R', 'LATIN CAPITAL LETTER R' ],
     [ 0x53, 0x0053, 'S', 'LATIN CAPITAL LETTER S' ],
     [ 0x54, 0x0054, 'T', 'LATIN CAPITAL LETTER T' ],
     [ 0x55, 0x0055, 'U', 'LATIN CAPITAL LETTER U' ],
     [ 0x56, 0x0056, 'V', 'LATIN CAPITAL LETTER V' ],
     [ 0x57, 0x0057, 'W', 'LATIN CAPITAL LETTER W' ],
     [ 0x58, 0x0058, 'X', 'LATIN CAPITAL LETTER X' ],
     [ 0x59, 0x0059, 'Y', 'LATIN CAPITAL LETTER Y' ],
     [ 0x5A, 0x005A, 'Z', 'LATIN CAPITAL LETTER Z' ],
     [ 0x5B, 0x005B, '[', 'LEFT SQUARE BRACKET' ],
     [ 0x5C, 0x005C, '\\', 'REVERSE SOLIDUS' ],
     [ 0x5D, 0x005D, ']', 'RIGHT SQUARE BRACKET' ],
     [ 0x5E, 0x005E, '^', 'CIRCUMFLEX ACCENT' ],
     [ 0x5F, 0x005F, '_', 'LOW LINE' ],
     [ 0x60, 0x0060, '`', 'GRAVE ACCENT' ],
     [ 0x61, 0x0061, 'a', 'LATIN SMALL LETTER A' ],
     [ 0x62, 0x0062, 'b', 'LATIN SMALL LETTER B' ],
     [ 0x63, 0x0063, 'c', 'LATIN SMALL LETTER C' ],
     [ 0x64, 0x0064, 'd', 'LATIN SMALL LETTER D' ],
     [ 0x65, 0x0065, 'e', 'LATIN SMALL LETTER E' ],
     [ 0x66, 0x0066, 'f', 'LATIN SMALL LETTER F' ],
     [ 0x67, 0x0067, 'g', 'LATIN SMALL LETTER G' ],
     [ 0x68, 0x0068, 'h', 'LATIN SMALL LETTER H' ],
     [ 0x69, 0x0069, 'i', 'LATIN SMALL LETTER I' ],
     [ 0x6A, 0x006A, 'j', 'LATIN SMALL LETTER J' ],
     [ 0x6B, 0x006B, 'k', 'LATIN SMALL LETTER K' ],
     [ 0x6C, 0x006C, 'l', 'LATIN SMALL LETTER L' ],
     [ 0x6D, 0x006D, 'm', 'LATIN SMALL LETTER M' ],
     [ 0x6E, 0x006E, 'n', 'LATIN SMALL LETTER N' ],
     [ 0x6F, 0x006F, 'o', 'LATIN SMALL LETTER O' ],
     [ 0x70, 0x0070, 'p', 'LATIN SMALL LETTER P' ],
     [ 0x71, 0x0071, 'q', 'LATIN SMALL LETTER Q' ],
     [ 0x72, 0x0072, 'r', 'LATIN SMALL LETTER R' ],
     [ 0x73, 0x0073, 's', 'LATIN SMALL LETTER S' ],
     [ 0x74, 0x0074, 't', 'LATIN SMALL LETTER T' ],
     [ 0x75, 0x0075, 'u', 'LATIN SMALL LETTER U' ],
     [ 0x76, 0x0076, 'v', 'LATIN SMALL LETTER V' ],
     [ 0x77, 0x0077, 'w', 'LATIN SMALL LETTER W' ],
     [ 0x78, 0x0078, 'x', 'LATIN SMALL LETTER X' ],
     [ 0x79, 0x0079, 'y', 'LATIN SMALL LETTER Y' ],
     [ 0x7A, 0x007A, 'z', 'LATIN SMALL LETTER Z' ],
     [ 0x7B, 0x007B, '{', 'LEFT CURLY BRACKET' ],
     [ 0x7C, 0x007C, '|', 'VERTICAL LINE' ],
     [ 0x7D, 0x007D, '}', 'RIGHT CURLY BRACKET' ],
     [ 0x7E, 0x007E, '~', 'TILDE' ],
     [ 0x7F, 0x007F, NULL, 'DELETE' ],
     [ 0x80, 0x0080, NULL, NULL ],
     [ 0x81, 0x0081, NULL, NULL ],
     [ 0x82, 0x0082, NULL, 'BREAK PERMITTED HERE' ],
     [ 0x83, 0x0083, NULL, 'NO BREAK HERE' ],
     [ 0x84, 0x0084, NULL, NULL ],
     [ 0x85, 0x0085, NULL, 'NEXT LINE (NEL)' ],
     [ 0x86, 0x0086, NULL, 'START OF SELECTED AREA' ],
     [ 0x87, 0x0087, NULL, 'END OF SELECTED AREA' ],
     [ 0x88, 0x0088, NULL, 'CHARACTER TABULATION SET' ],
     [ 0x89, 0x0089, NULL, 'CHARACTER TABULATION WITH JUSTIFICATION' ],
     [ 0x8A, 0x008A, NULL, 'LINE TABULATION SET' ],
     [ 0x8B, 0x008B, NULL, 'PARTIAL LINE FORWARD' ],
     [ 0x8C, 0x008C, NULL, 'PARTIAL LINE BACKWARD' ],
     [ 0x8D, 0x008D, NULL, 'REVERSE LINE FEED' ],
     [ 0x8E, 0x008E, NULL, 'SINGLE SHIFT TWO' ],
     [ 0x8F, 0x008F, NULL, 'SINGLE SHIFT THREE' ],
     [ 0x90, 0x0090, NULL, 'DEVICE CONTROL STRING' ],
     [ 0x91, 0x0091, NULL, 'PRIVATE USE ONE' ],
     [ 0x92, 0x0092, NULL, 'PRIVATE USE TWO' ],
     [ 0x93, 0x0093, NULL, 'SET TRANSMIT STATE' ],
     [ 0x94, 0x0094, NULL, 'CANCEL CHARACTER' ],
     [ 0x95, 0x0095, NULL, 'MESSAGE WAITING' ],
     [ 0x96, 0x0096, NULL, 'START OF GUARDED AREA' ],
     [ 0x97, 0x0097, NULL, 'END OF GUARDED AREA' ],
     [ 0x98, 0x0098, NULL, 'START OF STRING' ],
     [ 0x99, 0x0099, NULL, NULL ],
     [ 0x9A, 0x009A, NULL, 'SINGLE CHARACTER INTRODUCER' ],
     [ 0x9B, 0x009B, NULL, 'CONTROL SEQUENCE INTRODUCER' ],
     [ 0x9C, 0x009C, NULL, 'STRING TERMINATOR' ],
     [ 0x9D, 0x009D, NULL, 'OPERATING SYSTEM COMMAND' ],
     [ 0x9E, 0x009E, NULL, 'PRIVACY MESSAGE' ],
     [ 0x9F, 0x009F, NULL, 'APPLICATION PROGRAM COMMAND' ],
     [ 0xA0, 0x00A0, NULL, 'NO-BREAK SPACE' ],
     [ 0xA1, 0x00C0, 'À', 'LATIN CAPITAL LETTER A WITH GRAVE' ],
     [ 0xA2, 0x00C2, 'Â', 'LATIN CAPITAL LETTER A WITH CIRCUMFLEX' ],
     [ 0xA3, 0x00C8, 'È', 'LATIN CAPITAL LETTER E WITH GRAVE' ],
     [ 0xA4, 0x00CA, 'Ê', 'LATIN CAPITAL LETTER E WITH CIRCUMFLEX' ],
     [ 0xA5, 0x00CB, 'Ë', 'LATIN CAPITAL LETTER E WITH DIAERESIS' ],
     [ 0xA6, 0x00CE, 'Î', 'LATIN CAPITAL LETTER I WITH CIRCUMFLEX' ],
     [ 0xA7, 0x00CF, 'Ï', 'LATIN CAPITAL LETTER I WITH DIAERESIS' ],
     [ 0xA8, 0x00B4, '´', 'ACUTE ACCENT' ],
     [ 0xA9, 0x02CB, 'ˋ', 'MODIFIER LETTER GRAVE ACCENT' ],
     [ 0xAA, 0x02C6, 'ˆ', 'MODIFIER LETTER CIRCUMFLEX ACCENT' ],
     [ 0xAB, 0x00A8, '¨', 'DIAERESIS' ],
     [ 0xAC, 0x02DC, '˜', 'SMALL TILDE' ],
     [ 0xAD, 0x00D9, 'Ù', 'LATIN CAPITAL LETTER U WITH GRAVE' ],
     [ 0xAE, 0x00DB, 'Û', 'LATIN CAPITAL LETTER U WITH CIRCUMFLEX' ],
     [ 0xAF, 0x20A4, '₤', 'LIRA SIGN' ],
     [ 0xB0, 0x00AF, '¯', 'MACRON' ],
     [ 0xB1, 0x00DD, 'Ý', 'LATIN CAPITAL LETTER Y WITH ACUTE' ],
     [ 0xB2, 0x00FD, 'ý', 'LATIN SMALL LETTER Y WITH ACUTE' ],
     [ 0xB3, 0x00B0, '°', 'DEGREE SIGN' ],
     [ 0xB4, 0x00C7, 'Ç', 'LATIN CAPITAL LETTER C WITH CEDILLA' ],
     [ 0xB5, 0x00E7, 'ç', 'LATIN SMALL LETTER C WITH CEDILLA' ],
     [ 0xB6, 0x00D1, 'Ñ', 'LATIN CAPITAL LETTER N WITH TILDE' ],
     [ 0xB7, 0x00F1, 'ñ', 'LATIN SMALL LETTER N WITH TILDE' ],
     [ 0xB8, 0x00A1, '¡', 'INVERTED EXCLAMATION MARK' ],
     [ 0xB9, 0x00BF, '¿', 'INVERTED QUESTION MARK' ],
     [ 0xBA, 0x00A4, '¤', 'CURRENCY SIGN' ],
     [ 0xBB, 0x00A3, '£', 'POUND SIGN' ],
     [ 0xBC, 0x00A5, '¥', 'YEN SIGN' ],
     [ 0xBD, 0x00A7, '§', 'SECTION SIGN' ],
     [ 0xBE, 0x0192, 'ƒ', 'LATIN SMALL LETTER F WITH HOOK' ],
     [ 0xBF, 0x00A2, '¢', 'CENT SIGN' ],
     [ 0xC0, 0x00E2, 'â', 'LATIN SMALL LETTER A WITH CIRCUMFLEX' ],
     [ 0xC1, 0x00EA, 'ê', 'LATIN SMALL LETTER E WITH CIRCUMFLEX' ],
     [ 0xC2, 0x00F4, 'ô', 'LATIN SMALL LETTER O WITH CIRCUMFLEX' ],
     [ 0xC3, 0x00FB, 'û', 'LATIN SMALL LETTER U WITH CIRCUMFLEX' ],
     [ 0xC4, 0x00E1, 'á', 'LATIN SMALL LETTER A WITH ACUTE' ],
     [ 0xC5, 0x00E9, 'é', 'LATIN SMALL LETTER E WITH ACUTE' ],
     [ 0xC6, 0x00F3, 'ó', 'LATIN SMALL LETTER O WITH ACUTE' ],
     [ 0xC7, 0x00FA, 'ú', 'LATIN SMALL LETTER U WITH ACUTE' ],
     [ 0xC8, 0x00E0, 'à', 'LATIN SMALL LETTER A WITH GRAVE' ],
     [ 0xC9, 0x00E8, 'è', 'LATIN SMALL LETTER E WITH GRAVE' ],
     [ 0xCA, 0x00F2, 'ò', 'LATIN SMALL LETTER O WITH GRAVE' ],
     [ 0xCB, 0x00F9, 'ù', 'LATIN SMALL LETTER U WITH GRAVE' ],
     [ 0xCC, 0x00E4, 'ä', 'LATIN SMALL LETTER A WITH DIAERESIS' ],
     [ 0xCD, 0x00EB, 'ë', 'LATIN SMALL LETTER E WITH DIAERESIS' ],
     [ 0xCE, 0x00F6, 'ö', 'LATIN SMALL LETTER O WITH DIAERESIS' ],
     [ 0xCF, 0x00FC, 'ü', 'LATIN SMALL LETTER U WITH DIAERESIS' ],
     [ 0xD0, 0x00C5, 'Å', 'LATIN CAPITAL LETTER A WITH RING ABOVE' ],
     [ 0xD1, 0x00EE, 'î', 'LATIN SMALL LETTER I WITH CIRCUMFLEX' ],
     [ 0xD2, 0x00D8, 'Ø', 'LATIN CAPITAL LETTER O WITH STROKE' ],
     [ 0xD3, 0x00C6, 'Æ', 'LATIN CAPITAL LETTER AE' ],
     [ 0xD4, 0x00E5, 'å', 'LATIN SMALL LETTER A WITH RING ABOVE' ],
     [ 0xD5, 0x00ED, 'í', 'LATIN SMALL LETTER I WITH ACUTE' ],
     [ 0xD6, 0x00F8, 'ø', 'LATIN SMALL LETTER O WITH STROKE' ],
     [ 0xD7, 0x00E6, 'æ', 'LATIN SMALL LETTER AE' ],
     [ 0xD8, 0x00C4, 'Ä', 'LATIN CAPITAL LETTER A WITH DIAERESIS' ],
     [ 0xD9, 0x00EC, 'ì', 'LATIN SMALL LETTER I WITH GRAVE' ],
     [ 0xDA, 0x00D6, 'Ö', 'LATIN CAPITAL LETTER O WITH DIAERESIS' ],
     [ 0xDB, 0x00DC, 'Ü', 'LATIN CAPITAL LETTER U WITH DIAERESIS' ],
     [ 0xDC, 0x00C9, 'É', 'LATIN CAPITAL LETTER E WITH ACUTE' ],
     [ 0xDD, 0x00EF, 'ï', 'LATIN SMALL LETTER I WITH DIAERESIS' ],
     [ 0xDE, 0x00DF, 'ß', 'LATIN SMALL LETTER SHARP S' ],
     [ 0xDF, 0x00D4, 'Ô', 'LATIN CAPITAL LETTER O WITH CIRCUMFLEX' ],
     [ 0xE0, 0x00C1, 'Á', 'LATIN CAPITAL LETTER A WITH ACUTE' ],
     [ 0xE1, 0x00C3, 'Ã', 'LATIN CAPITAL LETTER A WITH TILDE' ],
     [ 0xE2, 0x00E3, 'ã', 'LATIN SMALL LETTER A WITH TILDE' ],
     [ 0xE3, 0x00D0, 'Ð', 'LATIN CAPITAL LETTER ETH' ],
     [ 0xE4, 0x00F0, 'ð', 'LATIN SMALL LETTER ETH' ],
     [ 0xE5, 0x00CD, 'Í', 'LATIN CAPITAL LETTER I WITH ACUTE' ],
     [ 0xE6, 0x00CC, 'Ì', 'LATIN CAPITAL LETTER I WITH GRAVE' ],
     [ 0xE7, 0x00D3, 'Ó', 'LATIN CAPITAL LETTER O WITH ACUTE' ],
     [ 0xE8, 0x00D2, 'Ò', 'LATIN CAPITAL LETTER O WITH GRAVE' ],
     [ 0xE9, 0x00D5, 'Õ', 'LATIN CAPITAL LETTER O WITH TILDE' ],
     [ 0xEA, 0x00F5, 'õ', 'LATIN SMALL LETTER O WITH TILDE' ],
     [ 0xEB, 0x0160, 'Š', 'LATIN CAPITAL LETTER S WITH CARON' ],
     [ 0xEC, 0x0161, 'š', 'LATIN SMALL LETTER S WITH CARON' ],
     [ 0xED, 0x00DA, 'Ú', 'LATIN CAPITAL LETTER U WITH ACUTE' ],
     [ 0xEE, 0x0178, 'Ÿ', 'LATIN CAPITAL LETTER Y WITH DIAERESIS' ],
     [ 0xEF, 0x00FF, 'ÿ', 'LATIN SMALL LETTER Y WITH DIAERESIS' ],
     [ 0xF0, 0x00DE, 'Þ', 'LATIN CAPITAL LETTER THORN' ],
     [ 0xF1, 0x00FE, 'þ', 'LATIN SMALL LETTER THORN' ],
     [ 0xF2, 0x00B7, '·', 'MIDDLE DOT' ],
     [ 0xF3, 0x00B5, 'µ', 'MICRO SIGN' ],
     [ 0xF4, 0x00B6, '¶', 'PILCROW SIGN' ],
     [ 0xF5, 0x00BE, '¾', 'VULGAR FRACTION THREE QUARTERS' ],
     [ 0xF6, 0x00AD, '-', 'SOFT HYPHEN' ],
     [ 0xF7, 0x00BC, '¼', 'VULGAR FRACTION ONE QUARTER' ],
     [ 0xF8, 0x00BD, '½', 'VULGAR FRACTION ONE HALF' ],
     [ 0xF9, 0x00AA, 'ª', 'FEMININE ORDINAL INDICATOR' ],
     [ 0xFA, 0x00BA, 'º', 'MASCULINE ORDINAL INDICATOR' ],
     [ 0xFB, 0x00AB, '«', 'LEFT-POINTING DOUBLE ANGLE QUOTATION MARK' ],
     [ 0xFC, 0x25A0, '■', 'BLACK SQUARE' ],
     [ 0xFD, 0x00BB, '»', 'RIGHT-POINTING DOUBLE ANGLE QUOTATION MARK' ],
     [ 0xFE, 0x00B1, '±', 'PLUS-MINUS SIGN' ],
     ];
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

  return [ 'char map name' => 'HP Roman-8',
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
