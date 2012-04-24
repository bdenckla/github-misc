<?php

main();

function main()
{
  $count_of_non_matched_lines = 0;

  while ( $line = fgets( STDIN ) )
    {
      $pattern = '#<B>(.*),(.*)</B> (.*)#';
      $m = preg_match( $pattern, $line, $matches );

      if ( $m === 1 )
        {
          process_verse( $matches );
        }
      else
        {
          ++$count_of_non_matched_lines;
        }
    }
}

function process_verse( $matches )
{
  list( /* $full_match */, $chapter, $verse, $text ) = $matches;

  $pattern = '/[ ,;:\'".?!]+/';
  $limit = -1;
  $flags = PREG_SPLIT_NO_EMPTY;

  $words = preg_split( $pattern, $text, $limit, $flags );

  //print_r( $text );
  //print_r( $words );

  //array_map( 'print_word', $words );

  array_map( 'process_word', $words );

}

function process_word( $word )
{
  if ( array_key_exists( $word, $word_to_number ) )
    {
      $number = $word_to_number[ $word ];
    }
  else
    {
      ++$count;
      $number = $word_to_number[ $word ] = $count;
    }

  ++$histo[ $number ];
}

function print_word( $word )
{
  echo $word . "\n";
}

?>
