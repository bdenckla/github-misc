#!/usr/bin/php -q
<?php

require_once 'generate-html.php';

// tneve: throw new ErrorException of var_export
function tneve( $e )
{
  throw new ErrorException( var_export( $e, 1 ) );
}

function parse_line( $line )
{
  $eol = substr( $line, -2 );

  if ( $eol !== "\r\n" )
    {
      tneve( [ 'unexpected end of line', $eol ] );
    }

  $rline = substr( $line, 0, -2 );

  if ( $rline === '' )
    {
      return [ 'empty', '' ];
    }

  $dpat = '/(?<dollar>\$)(?<after_dollar>.+)/';

  $r = preg_match( $dpat, $rline, $matches);

  if ( $r === FALSE )
    {
      tneve( 'preg_match error on', $rline );
    }

  if ( $r )
    {
      return [ $matches['dollar'], $matches['after_dollar'] ];
    }

  $cpat = '/(?<command>[a-z]+)(?<after_command>.+)/';

  $r = preg_match( $cpat, $rline, $matches);

  if ( $r === FALSE )
    {
      tneve( 'preg_match error on', $rline );
    }

  if ( $r )
    {
      return [ $matches['command'], $matches['after_command'] ];
    }

  return [ 'misc', $rline ];
}

function pline_to_html( $pline )
{
  return html_p_na( var_export( $pline, 1 ) );
}

function get_pline_type( $pline )
{
  return $pline[0];
}

function pline_is_misc( $pline )
{
  return get_pline_type( $pline ) === 'misc';
}

function html_body( $input_filename, $input )
{
  $plines = array_map( 'parse_line', $input );

  $types_of_plines = array_map( 'get_pline_type', $plines );

  $pline_type_counts = array_count_values( $types_of_plines );

  $misc_plines = array_filter( $plines, 'pline_is_misc' );

  return xml_seqa( html_p_na( var_export( $pline_type_counts, 1 ) ),
                   html_p_na( var_export( $misc_plines, 1 ) )
                   );
}

function main( $argv )
{
  $input_filename = $argv[1];

  $input = file( $input_filename );

  if ( $input === FALSE )
    {
      tneve( [ 'error opening', $input_filename ] );
    }

  $head = html_head_contents( 'Parsed 3B2' );

  $body = html_body( $input_filename, $input );

  $html = html_document( $head, $body );

  return $html->s;
}

echo main( $argv );

?>
