<?php

function xml_wrap( $x, $tag, $tag_attributes )
{
  $r = array
    (
     xml_open_tag( $tagName, $tag_attributes ),
     $x,
     xml_close_tag( $tagName ),
     "\n",
     );

  return xml_seq( $r );
}

function xml_open_tag( $name, $attributes )
{
  return xml_tag( $name, $attributes, '', '' );
}

function xml_close_tag( $name )
{
  return xml_tag( $name, array(), '/', '' );
}

// bs: begin slash, e.g. </t>, i.e. used for a closing tag
// es: end   slash, e.g. <t/>, i.e. used for a self-closing tag
// use neither bs nor es for an opening tag

function xml_tag( $name, $attributes, $bs, $es )
{
  // double map
  $keqv = array_map( 'xml_keqv',
                     array_keys( $attributes ),
                     $attributes );

  $attributes_string = implode( ' ', $keqv );

  $t = ''
    . '<'
    . $bs
    . $name
    . ' '
    . $attributes_string
    . $es
    . '>'
    ;

  return new xml_raw( $t );
}

// keqv: key equals quoted value
//
function xml_keqv( $key, $value )
{
  return is_null( $value )
    ? $key
    : $key . '=' . '"' . htmlspecialchars( $value ) . '"';
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
function xml_me( $x )
{
  $is_raw = is_object( $x ) && get_class( $x ) == 'xml_raw';

  if ( $is_raw ) { return $x->s; }

  return htmlspecialchars( (string) $x );
}

class xml_raw
{
  public $s;
  function __construct( $s ) { $this->s = $s; }
}

?>
