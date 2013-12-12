<?php

function hu( $hex ) // hu: hex to utf-8
{
  return html_entity_decode('&#x'.$hex.';', ENT_COMPAT, 'UTF-8');
}

function cm_lookup( $char_map, $char )
{
  $r = lubn( $char, $char_map['char map itself'] );

  if ( is_null( $r ) )
    {
      $rep = printed_representation_of_char( $char );

      $name = 'XXX-u-' . $rep;

      return [ 'XXX', $name, '('.$name.')' ];
    }

  return $r;
}

function printed_representation_of_char( $charseq )
{
  return is_printable( $charseq )
    ? 'c-' . $charseq
    : 'h-' . hex_for_string( $charseq );
}

function hex_for_string( $s )
{
  return implode( array_map( 'hex_for_char', str_split( $s ) ) );
}

function hex_for_char( $char )
{
  return sprintf( '%X', ord( $char ) );
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

  $mapped = cm_lookup( $char_map, $char );

  return $mapped[2];
}

function printed_val_and_name( $char_map, $char )
{
  $mapped = cm_lookup( $char_map, $char );

  return printed_representation_of_char( $char )
    .'-'.
    $mapped[0];
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
