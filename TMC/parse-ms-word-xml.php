#!/usr/bin/php -q
<?php


require_once 'generate-html.php';

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

function my_html_body( $input_filename, $xml )
{
  // vese( '1 ' . $xml->getName() );

  foreach ($xml->children('pkg',TRUE) as $second_gen)
    {
      // vese( '  2 ' . $second_gen->getName() );

      foreach ($second_gen->children('pkg',TRUE) as $third_gen)
        {
          // vese( '    3 ' . $third_gen->getName() ) ;

          foreach ($third_gen->children('w',TRUE) as $fourth_gen)
            {
              $name = $fourth_gen->getName();

              // vese( '      4 ' . $name );

              if ( $name === 'document' )
                {
                  process_document( $fourth_gen, 4 );
                }
            }
        }
    }

  return html_p_na('here we are');
}

function process_document( $xml, $depth )
{
  return process_w( $xml, $depth );
}

function process_w( $xml, $depth )
{
  $depthp1 = $depth + 1;

  foreach ($xml->children('w',TRUE) as $c)
    {
      $name = $c->getName();
      $indent = str_repeat( '  ', $depthp1 );
      vese( $indent . $depthp1 . ' ' . $name );
      if ( $name === 't' )
        {
          vese( (string) $c );

        }
      process_w( $c, $depthp1 );
    }
}

function main( $argv )
{
  $input_filename = $argv[ 1 ];

  $xml = simplexml_load_file( $input_filename );

  if ( $xml === FALSE )
    {
      tneve( [ 'error opening' => $input_filename ] );
    }

  $title = 'Parsed ' . $input_filename;

  $css = 'BODY { background-color: #333; color: #ddd }';

  $head = html_head_contents( $title, $css );

  $body = my_html_body( $input_filename, $xml );

  $html = html_document( $head, $body );

  return $html->s;
}

echo main( $argv );

?>
