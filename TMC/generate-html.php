<?php

function xml_wrap( $tag_name, $tag_attr, $i, $linesep = NULL )
{
  $r = array
    (
     xml_open_tag( $tag_name, $tag_attr, $linesep ),
     $i,
     xml_close_tag( $tag_name, $linesep ),
     );

  return xml_seq( $r );
}

function xml_open_tag( $name, $attr, $linesep = NULL )
{
  return xml_tag( $name, $attr, '', '', $linesep );
}

function xml_close_tag( $name, $linesep = NULL )
{
  return xml_tag( $name, [], '/', '', $linesep );
}

// sc: self-close
//
function xml_sc_tag( $name, $attr, $linesep = NULL )
{
  return xml_tag( $name, $attr, '', '/', $linesep );
}

function xml_tag( $name, array $attr, $bs, $es, $linesep )
{
  // aap: attr as plain pairs (duples) (as opposed to key/value pairs)
  //
  $aap = array_map( 'xml_duple', array_keys( $attr ), $attr );

  // nap: name as pair
  //
  $nap = [ $name, NULL ]; // treat as a key with NULL value

  $aapwn = array_merge( [ $nap ], $aap );

  return xml_gtag( $aapwn, $bs, $es, $linesep );
}

// bs: begin slash, e.g. </t>, i.e. used for a closing tag
// es: end   slash, e.g. <t/>, i.e. used for a self-closing tag
// use neither bs nor es for an opening tag
//
function xml_gtag( array $attr, $bs, $es, $linesep = NULL )
{
  $keqv = array_map( 'xml_keqv', $attr );

  $attr_string = implode( ' ', $keqv );

  // $linesep === NULL means "use default".
  // The default is "<!--\n>".
  // lss: line separator string

  $lss = $linesep === NULL ? "<!--\n-->" : $linesep;

  $t = ''
    . '<'
    . $bs
    . $attr_string
    . $es
    . '>'
    . $lss
    ;

  return new xml_raw( $t );
}

function xml_duple( $key, $value )
{
  return [ $key, $value ];
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

  $s = is_string( $i ) ? $i : var_export( $i, 1 );

  return htmlspecialchars( $s, ENT_SUBSTITUTE );
}

class xml_raw
{
  public $s;
  function __construct( $s ) { $this->s = $s; }
}

function html_head_meta()
{
  $attr =
    [
     'http-equiv' => 'Content-type',
     'content' => 'text/html;charset=UTF-8',
     ];
  return xml_sc_tag( 'meta', $attr );
}

function html_head_contents( $title, $css )
{
  $meta = html_head_meta();

  /* default linesep (comments) doesn't work inside title element */
  $title_linesep = '';

  $title = xml_wrap( 'title', [], $title, $title_linesep );

  //$integer = '.integer { text-align: right; }';
  //$hebrew = '.hebrew { text-align: right; }';

  /* default linesep (comments) doesn't work inside title element */
  $style_linesep = "\n";

  $style_attr = [ 'type' => 'text/css' ];

  $style = xml_wrap( 'style', $style_attr, $css, $style_linesep );

  return xml_seqa( $meta, $title, $style );
}

function html_document( $head, $body )
{
  $doctype_decl_attr = array
    (
     [ 'DOCTYPE', NULL ],
     [ 'html', NULL ],
     );

  $doctype_decl = xml_gtag( $doctype_decl_attr, '!', '' );

  $html_attr = array
    (
     'lang' => 'en',
     );

  $hb = xml_seqa( xml_wrap( 'head', [], $head ),
                  xml_wrap( 'body', [], $body ) );

  $html = xml_wrap( 'html', $html_attr, $hb );

  return xml_seqa( $doctype_decl, $html );
}

function html_td( $attr, $i )
{
  return xml_wrap( 'td', $attr, $i );
}

function html_tr( $attr, $i )
{
  return xml_wrap( 'tr', $attr, $i );
}

function html_table( $attr, $i )
{
  return xml_wrap( 'table', $attr, $i );
}

function html_i_na( $i )
{
  return xml_wrap( 'i', [], $i );
}

function html_p_na( $i )
{
  return xml_wrap( 'p', [], $i );
}

function html_div( $attr, $i )
{
  return xml_wrap( 'div', $attr, $i );
}

function html_object( $attr )
{
  // self-closing only allowed in XHTML
  //
  return xml_wrap( 'object', $attr, '' );
}

function html_div_object( $attr )
{
  return html_div( [], html_object( $attr ) );
}

?>
