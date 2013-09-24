#!/usr/bin/php -q
<?php

function pa() // Partially Apply
{
  $origArgs = func_get_args();
  return function() use ( $origArgs )
    {
      $allArgs = array_merge( $origArgs, func_get_args() );
      return call_user_func_array( 'call_user_func', $allArgs );
    };
}

function holidays()
{
  // month, day, name
  return array
    (
     array( mn_ad(), 14, 'Purim' ),
     array( mn_ni(), 15, 'Pesach' ),
     array( mn_si(),  6, 'Shavuot' ),
     array( mn_ti(),  1, 'Rosh Hashanah' ),
     array( mn_ki(), 25, 'Chanukkah' ),
     array( mn_sh(), 15, 'Tu B\'Shevat' ),
     );
}

function month_name_array()
{
  return array
    (
     mn_ad() => array( 'Adar/Adar Sheni' ),
     mn_ni() => array( 'Nisan' ),
     mn_iy() => array( 'Iyyar' ),
     mn_si() => array( 'Sivan' ),
     mn_ta() => array( 'Tammuz' ),
     mn_av() => array( 'Av' ),
     mn_el() => array( 'Elul' ),
     mn_ti() => array( 'Tishrei' ),
     mn_ch() => array( 'Cheshvan' ),
     mn_ki() => array( 'Kislev' ),
     mn_te() => array( 'Tevet' ),
     mn_sh() => array( 'Shevat' ),
     mn_ar() => array( 'Adar Rishon' ),
     );
}

function mn_ad() { return  0; }
function mn_ni() { return  1; }
function mn_iy() { return  2; }
function mn_si() { return  3; }
function mn_ta() { return  4; }
function mn_av() { return  5; }
function mn_el() { return  6; }
function mn_ti() { return  7; }
function mn_ch() { return  8; }
function mn_ki() { return  9; }
function mn_te() { return 10; }
function mn_sh() { return 11; }
function mn_ar() { return 12; }

function mn_min() { return  0; }
function mn_max() { return 12; }

function mn_start() { return mn_ad(); }

function month_name( $month_number )
{
  $a = month_name_array();

  return $a[ $month_number ];
}

/* day_adj: -1, 0, or 1 (short Kislev, normal, long Cheshvan)
 * month_adj: 0 or 1 (non-leap or leap)
 *
 * I.e. the 6 possible year lengths have the following correspondences
 * to day_adj/month_adj pairs:
 *
 * 353, 354, 355 correspond to (-1,0), (0,0), (1,0)
 * 383, 384, 385 correspond to (-1,1), (0,1), (1,1)
*/

function days_per_month( $month_number, array $species )
{
  list( $day_adj, $month_adj ) = $species;

  $e = NULL;

  if ( ! array_key_exists( $month_number, month_name_array() ) )
    {
      $e = array( 'unexpected month number', $month_number );
    }

  if ( ! in_array( $day_adj, array( -1, 0, 1 ) ) )
    {
      $e = array( 'unexpected day_adj', $day_adj );
    }

  if ( ! in_array( $month_adj, array( 0, 1 ) ) )
    {
      $e = array( 'unexpected month_adj', $month_adj );
    }

  if ( $e )
    {
      throw new ErrorException( var_export( $e, 1 ) );
    }

  if ( $month_number == mn_ch() ) // Cheshvan
    {
      return $day_adj == 1 ? 30 : 29;
    }

  if ( $month_number == mn_ki() ) // Kislev
    {
      return $day_adj == -1 ? 29 : 30;
    }

  if ( $month_number == mn_ar() ) // Adar Rishon
    {
      return $month_adj ? 30 : 0;
    }

  return 29 + $month_number % 2;
}

function accumulate_days( $species, $acc, $month_number )
{
  return $acc + days_per_month( $month_number, $species );
}

function previous_month( $month_number )
{
  return $month_number == mn_min()
    ? mn_max()
    : $month_number - 1;
}

function day_of_year_of_month( $species, $month_number )
{
  if ( $month_number == mn_start() )
    {
      return 0;
    }

  $p = previous_month( $month_number );

  if ( $p < mn_start() )
    {
      $range1 = range( mn_start(), mn_max() );
      $range2 = range( mn_min(), $p );
      $mns = array_merge( $range1, $range2 );
    }
  else
  {
    $mns = range( mn_start(), $p );
  }

  $pa = pa( 'accumulate_days', $species );

  return array_reduce( $mns, $pa, 0 );
}

function day_of_year_of_holiday( $species, array $holiday )
{
  list ( $month, $day_of_month, $name ) = $holiday;

  $doyom = day_of_year_of_month( $species, $month );

  $doy = $doyom + $day_of_month;

  return $doy;
}

function day_of_year_of_holidays( $species )
{
  $pa = pa( 'day_of_year_of_holiday', $species );

  $a = array_map( $pa, holidays() );

  return $a;
}

function doyoh_sort( $doyoh1, $doyoh2 )
{
  list( $holiday1, $doy1 ) = $doyoh1;
  list( $holiday2, $doy2 ) = $doyoh2;

  return $doy1 > $doy2;
}

function main()
{
  $species = array
    (
     array( -1, 0 ),
     array(  0, 0 ),
     array(  1, 0 ),
     array( -1, 1 ),
     array(  0, 1 ),
     array(  1, 1 ),
     );

  return array_map( 'day_of_year_of_holidays', $species );
}

var_export( main() );

?>
