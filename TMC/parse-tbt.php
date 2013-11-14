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

function parse_line( $line_number, $line_contents )
{
  $eol = substr( $line_contents, -2 );

  if ( $eol !== "\r\n" )
    {
      tneve( [ 'unexpected end of line' => $eol ] );
    }

  $rline = substr( $line_contents, 0, -2 );

  if ( $rline === '' )
    {
      return empty_pline( $line_number );
    }

  $dpat = '/(?<dollar>\$)(?<after_dollar>.+)/';

  $r = preg_match( $dpat, $rline, $matches);

  if ( $r === FALSE )
    {
      tneve( 'preg_match error on', $rline );
    }

  if ( $r )
    {
      return dollar_pline( $matches['after_dollar'], $line_number );
    }

  $cpat = '/(?<command>[a-z]+)(?<after_command>.+)/';

  $r = preg_match( $cpat, $rline, $matches);

  if ( $r === FALSE )
    {
      tneve( 'preg_match error on', $rline );
    }

  if ( $r )
    {
      $m = [ $matches['command'], $matches['after_command'] ];

      return command_pline( $m, $line_number );
    }

  return misc_pline( $rline, $line_number );
}

function misc_pline( $body, $line_number )
{
  return [ 'type' => 'misc',
           'body' => $body,
           'line number' => $line_number ];
}

function command_pline( $body, $line_number )
{
  return [ 'type' => 'command',
           'body' => $body,
           'line number' => $line_number ];
}

function dollar_pline( $body, $line_number )
{
  return [ 'type' => '$',
           'body' => $body,//'...',
           'line number' => $line_number ];
}

function empty_pline()
{
  return [ 'type' => 'empty' ];
}

function pline_type( $pline )
{
  return $pline[ 'type' ];
}

function pline_is_misc( $pline )
{
  return pline_type( $pline ) === 'misc';
}

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

function split_on_sem_test_inputs()
{
  return [ [ 'a', ';', 'b' ],
           [      ';', 'b' ],
           [ 'a', ';'      ],
           [      ';'      ],
           [],
           [ 'a' ],
           [ 'a', 'b' ],
           [ 'a', ';', 'b', ';', 'c' ],
           [ 'a', 'b', 'c', ';', 'd', ';', 'e', 'f' ],
           [ 'a', ';', ';', 'b' ],
           ];
}

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

function html_p_na_ve( $k, $v )
{
  $s = var_export( [ $k, $v ], 1 );
  return html_p_na( $s );
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

function dollar_pline_value( $pline )
{
  tneve_if_ptiu( $pline, '$' );

  return $pline[ 'body' ];
}

function command_pline_value( $pline )
{
  tneve_if_ptiu( $pline, 'command' );

  return $pline[ 'body' ];
}

function coalesce_block( array $block )
{
  if ( $block === [] )
    {
      return [];
    }

  $vcommand = command_pline_value( $block[ 0 ] );

  if ( count( $block ) === 1 )
    {
      return [ 'command' => $vcommand ];
    }

  $dollars = array_slice( $block, 1 );

  $vdollars = array_map( 'dollar_pline_value', $dollars );

  $ivdollars = implode( $vdollars );

  $limit = 16*1024;

  if ( strlen( $ivdollars ) > $limit )
    {
      $t = substr( $ivdollars, 0, $limit - 1 );
      return [ 'command' => $vcommand,
               'truncated dollars' => $t ];
    }

  return [ 'command' => $vcommand,
           'dollars' => $ivdollars ];
}

function html_body( $input_filename, $input )
{
  $plines = array_map_wk( 'parse_line', $input );

  $types_of_plines = array_map( 'pline_type', $plines );

  $pline_type_counts = [ array_count_values( $types_of_plines ) ];

  $misc_plines = array_filter( $plines, 'pline_is_misc' );

  $sem_test_io = array_map( 'io_split_on_sem', split_on_sem_test_inputs() );

  $blocks = split_on( empty_pline(), $plines );

  $cblocks = array_map( 'coalesce_block', $blocks );

  $htmls = array_map_wk( 'html_p_na_ve', $cblocks );

  return xml_seq( $htmls );
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
