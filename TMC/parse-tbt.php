#!/usr/bin/php -q
<?php

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

// wk: with keys, i.e. f( k, v ) for array( k => v );
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
  return $pline[ 'type' ];
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

  $body = $pline[ 'body' ];

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

  return $pline[ 'body' ];
}

function command_pline_lineno( $pline )
{
  tneve_if_ptiu( $pline, 'command' );

  return $pline[ 'lineno' ];
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

  $pecblocks = array_map_klfa( 'dollars', 'elements',
                               'basic_parse', $ecblocks );

  $a1_blocks = array_map_klfa( 'elements', 'tree',
                               'tree_parse', $pecblocks );

  $a2_blocks = array_map_klfa( 'tree', 'tree',
                               'dropper', $a1_blocks );

  $a3_blocks = array_map_klfa( 'tree', 'tree',
                               'substituter', $a2_blocks );

  $a4_blocks = array_map_klfa( 'tree', 'tree',
                               'remove_line_breaks', $a3_blocks );

  return xml_wrap( 'pre', [], var_export( $a4_blocks, 1 ) );
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

/* TODO: retain line numbers further that we do, for diagnostic
 * reasons?
 */

function amp_sem( $x ) { return wrap( '&', ';', $x ); }

function dropper( $tree )
{
  $tree['branches'] = array_filter( $tree['branches'], 'preserve' );

  return $tree;
}

function substituter( $tree )
{
  $tree['branches'] = array_map( 'substitute', $tree['branches'] );

  return $tree;
}

function substitute( $element )
{
  if ( $element === element( 'amp', amp_sem( '#146' ) ) )
    {
      return element( 'txt', ' ' );
    }

  return $element;
}

function is_ang_to_drop( $d )
{
  return LuBn( 'eltype', $d ) === 'ang'
    && (
        begins_with( $d['elval'], '<?tpl=' )
        ||
        begins_with( $d['elval'], '<?tpt=' )
        ||
        begins_with( $d['elval'], '<?twb' )
        ||
        $d['elval'] === '<>'
        ||
        $d['elval'] === '<PAR-T1>'
        ||
        $d['elval'] === '<PAR-BT1>'
        ||
        $d['elval'] === '<PAR-AT>'
        );
}

function is_amp_to_drop( $d )
{
  return LuBn( 'eltype', $d ) === 'amp'
    && ( $d['elval'] === amp_sem( '#132' )
         ||
         $d['elval'] === amp_sem( '#133' )
         ||
         $d['elval'] === amp_sem( '#134' )
         ||
         $d['elval'] === amp_sem( '#135' )
         ||
         $d['elval'] === amp_sem( '#4' )
         ||
         $d['elval'] === amp_sem( '#6' ) );
}

function begins_with($str, $sub)
{
    return (strncmp($str, $sub, strlen($sub)) == 0);
}

function preserve( $dollar )
{
  if ( is_ang_to_drop( $dollar )
       ||
       is_amp_to_drop( $dollar ) )
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
            'branches' => [] ];

  foreach ( $elements as $element )
  {
    $is_a_pusher =
      $element['eltype'] == 'amp'
      &&
      array_search( $element['elval'], $pushers ) !== FALSE;

    $is_car   = $element === element( 'car', '^' );
    $is_amp_d = $element === element( 'amp', amp_sem( 'D' ) );

    $is_a_popper = $n > 0 && ( $is_car || $is_amp_d );

    if ( $is_a_pusher )
      {
        $n++;

        $a[$n] = [ 'level' => $n,
                   'branches' => [ $element ] ];
      }
    elseif ( $is_a_popper )
      {
        $n--;
        if ( $n < 0 )
          {
            tneve( [ 'n < 0' => $element ] );
          }
        $a[$n]['branches'][] = $a[$n+1];
        $a[$n+1] = NULL;
      }
    else
      {
        $a[$n]['branches'][] = $element;
      }
  }

  return $a[0];
}

function last_char( $x )
{
  return substr( $x, -1 );
}

function remove_line_breaks( $tree )
{
  // lb: line break
  //
  $lb = element( 'amp', amp_sem( '#1' ) );

  $jammers = [ '-', '/' ]; // TODO: others?

  $a = [];
  $stack = [];

  foreach ( $tree['branches'] as $branch )
  {
    $is_txt = LuBn( 'eltype', $branch ) === 'txt';

    $is_lb = $branch === $lb;

    $dumpstack = FALSE;

    $c = count( $stack );

    if ( $c === 0 )
      {
        if ( $is_txt )
          {
            $stack[] = $branch;
            $branch['c'] = $c;
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
            $stack[] = $branch;
            $branch['c'] = $c;
          }
        elseif ( $is_txt )
          {
            $txt0 = $stack[0]['elval'];
            $txt1 = $branch['elval'];
            $stack = [ element( 'txt', $txt0 . $txt1 ) ];
            $branch['c'] = $c;
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
            $txt0 = $stack[0]['elval'];
            $txt1 = $branch['elval'];
            $stack = [ element( 'txt', $txt0 . ' ' . $txt1 ) ];
            $branch['c'] = $c;
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
        $a[] = $branch;
      }
  }

  foreach ( $stack as $stack_el )
    {
      $a[] = $stack_el;
    }
  $stack = [];

  $tree['branches'] = $a;

  return $tree;
}

/*
    $is_txt_jammer = $is_txt
      &&
      array_search( last_char( $branch['elval'] ), $jammers ) !== FALSE;
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
  return [ 'eltype' => $type, 'elval' => $value ];
}

function array_map_klfa( $k, $l, $f, array $a )
{
  return array_map( pa( 'klfa', $k, $l, $f ), $a );
}

function klfa( $k, $l, $f, $a )
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
