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

function my_html_body( $input_filename, $sxml )
{
  // rsx: really simple XML
  //
  $rsx = NULL;

  foreach ($sxml->children('pkg',TRUE) as $second_gen)
    {
      foreach ($second_gen->children('pkg',TRUE) as $third_gen)
        {
          foreach ($third_gen->children('w',TRUE) as $fourth_gen)
            {
              $name = $fourth_gen->getName();

              if ( $name === 'document' )
                {
                  if ( ! is_null( $rsx ) )
                    {
                      tneve( 'more than one document found' );
                    }
                  $rsx = process_document( $fourth_gen );
                }
            }
        }
    }

  $table = table_for_element( $rsx );

  return $table;
}

function process_document( $sxml )
{
  return process_w( $sxml );
}

// cp: current paragraph
//
function process_w( $sxml )
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
      $r['children'][] = process_w( $c );
    }

  return $r;
}

function table_for_element( $rsx )
{
  vese( $rsx );

  $n = tr_of_tds( 'name', $rsx['name'] );

  $a = tr_of_tds( 'attributes', $rsx['attributes'] );

  $c = tr_of_tds( 'character data', $rsx['character data'] );

  return table_b1( [ $n, $a, $c ] );
}

function main( $argv )
{
  $input_filename = $argv[ 1 ];

  $sxml = simplexml_load_file( $input_filename );

  if ( $sxml === FALSE )
    {
      tneve( [ 'error opening' => $input_filename ] );
    }

  $title = 'Parsed ' . $input_filename;

  $css = 'BODY { background-color: #333; color: #ddd }';

  $head = html_head_contents( $title, $css );

  $body = my_html_body( $input_filename, $sxml );

  $html = html_document( $head, $body );

  return $html->s;
}

echo main( $argv );

?>
