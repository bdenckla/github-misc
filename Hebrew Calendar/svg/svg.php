<?php

function xml_wrap( $tag_name, $tag_attr, $i )
{
  $r = array
    (
     xml_open_tag( $tag_name, $tag_attr ),
     $i,
     xml_close_tag( $tag_name ),
     );

  return xml_seq( $r );
}

function xml_open_tag( $name, $attr )
{
  return xml_tag( $name, $attr, '', '' );
}

function xml_close_tag( $name )
{
  return xml_tag( $name, array(), '/', '' );
}

// sc: self-close
//
function xml_sc_tag( $name, $attr )
{
  return xml_tag( $name, $attr, '', '/' );
}

function xml_tag( $name, array $attr, $bs, $es )
{
  // aap: attr as plain pairs (duples) (as opposed to key/value pairs)
  //
  $aap = array_map( 'xml_duple', array_keys( $attr ), $attr );

  // nap: name as pair
  //
  $nap = array( $name, NULL ); // treat as a key with NULL value

  $aapwn = array_merge( array( $nap ), $aap );

  return xml_gtag( $aapwn, $bs, $es );
}

// bs: begin slash, e.g. </t>, i.e. used for a closing tag
// es: end   slash, e.g. <t/>, i.e. used for a self-closing tag
// use neither bs nor es for an opening tag
//
function xml_gtag( array $attr, $bs, $es )
{
  $keqv = array_map( 'xml_keqv', $attr );

  $attr_string = implode( ' ', $keqv );

  $t = ''
    . '<'
    . $bs
    . $attr_string
    . $es
    . '>'
    . "\n"
    ;

  return new xml_raw( $t );
}

function xml_duple( $key, $value )
{
  return array( $key, $value );
}

// keqv: key equals quoted value
//
function xml_keqv( array $kv )
{
  list ( $key, $value ) = $kv;

  if ( is_null( $value ) ) { return $key; }

  $qvalue = xml_quote( $value );

  if ( is_null( $key ) ) { return $qvalue; }

  return $key . '=' . $qvalue;
}

function xml_quote( $i )
{
  return '"' . htmlspecialchars( $i ) . '"';
}

function xml_seqa()
{
  return xml_seq( func_get_args() );
}

function xml_seq( array $a )
{
  return new xml_raw( implode( '', array_map( 'xml_me', $a ) ) );
}

// me: maybe escape
//
function xml_me( $i )
{
  $is_raw = is_object( $i ) && get_class( $i ) == 'xml_raw';

  if ( $is_raw ) { return $i->s; }

  return htmlspecialchars( $i );
}

class xml_raw
{
  public $s;
  function __construct( $s ) { $this->s = $s; }
}

function svg_wrap( $width, $height, $i )
{
  $xml_decl_attr = array( 'version' => '1.0', 'standalone' => 'no' );

  $xml_decl = xml_tag( 'xml', $xml_decl_attr, '?', '?' );

  $doctype_decl_attr = array
    (
     array( 'DOCTYPE', NULL ),
     array( 'svg', NULL ),
     array( 'PUBLIC', NULL ),
     array( NULL, '-//W3C//DTD SVG 1.1//EN' ),
     array( NULL, 'http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd' ),
     );

  $doctype_decl = xml_gtag( $doctype_decl_attr, '!', '' );

  $svg_attr = array
    (
     'width' => $width,
     'height' => $height,
     'version' => 1.1,
     'xmlns' => 'http://www.w3.org/2000/svg',
     'xmlns:xlink' => 'http://www.w3.org/1999/xlink'
     );

  $svg = xml_wrap( 'svg', $svg_attr, $i );

  return xml_seqa( $xml_decl, $doctype_decl, $svg );
}

// gt: group transform
//
function svg_gt( $fn, $args, $i )
{
  $cs_args = implode( ',', $args );

  $fncall = $fn . '(' . $cs_args . ')';

  return xml_wrap( 'g', array( 'transform' => $fncall ), $i );
}

// gtt: group transform translate
//
function svg_gtt( $x, $y, $i )
{
  return svg_gt( 'translate', array( $x, $y ), $i );
}

// gtt: group transform scale
//
function svg_gts( $s, $i )
{
  return svg_gt( 'scale', array( $s ), $i );
}

function svg_circle( $attr )
{
  return xml_sc_tag( 'circle', $attr );
}

?>