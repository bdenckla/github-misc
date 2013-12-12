<?php

function hu( $hex ) // hu: hex to utf-8
{
  return html_entity_decode('&#x'.$hex.';', ENT_COMPAT, 'UTF-8');
}

function cm_lookup( $char_map, $ord )
{
  $r = lubn( $ord, $char_map['char map itself'] );

  if ( is_null( $r ) )
    {
      $rep = printed_representation_of_ord( $ord );

      $name = 'XXX-u-' . $rep;

      return [ 'XXX', $name, '('.$name.')' ];
    }

  return $r;
}

function printed_representation_of_ord( $ord )
{
  $char = chr( $ord );

  return is_printable( $char )
    ? 'c-' . $char
    : 'h-' . sprintf( '%X', $ord );
}

function printed_representation_of_char( $char )
{
  return printed_representation_of_ord( ord( $char ) );
}

function apply_to_printables( $char_map )
{
  $atp = lubn( 'apply to printables', $char_map );

  return $atp === TRUE;
}

function acm( $char_map, $char )
{
  $atp = apply_to_printables( $char_map );

  if ( ! $atp && is_printable( $char ) ) { return $char; }

  $ord = ord( $char );

  $mapped_ord = cm_lookup( $char_map, $ord );

  return $mapped_ord[2];
}

function ord_and_name( $char_map, $char )
{
  $ord = ord( $char );

  $mapped_ord = cm_lookup( $char_map, $ord );

  return [ $ord, $mapped_ord[0] ];
}

function printed_ord_and_name( $char_map, $char )
{
  $ord_and_name = ord_and_name( $char_map, $char );

  return printed_representation_of_ord( $ord_and_name[0] )
    .'-'.
    $ord_and_name[1];
}

function is_printable( $x )
{
  $pat = '/^[[:print:]]*$/';

  return preg_match_toe2( $pat, $x );
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

  return $r === 1 ? $output : NULL;
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

?>
