// number of days
//
n = 400;

// The mean tropical year on January 1, 2000
//
mty = 365.2421897;

// radius and diameter
r = 10;
d = r * 2;

// one day, in angular units (fraction of a circle, e.g. 360 degrees = 1)
//
od_iau = 1 / mty;

// one day, in (undefined) linear units
//
od_ilu = PI * d * od_iau;

pitch = 15 * od_ilu;

thread_spacing = od_ilu;

module cube_on_helical_path( i, thread, color_arg )
{
  // day i, in angular units (fraction of a circle, e.g. 360 degrees = 1)

  di_iau = i * od_iau;

  z = thread_spacing * thread + di_iau * pitch;

  rotate([ 0, 0, di_iau * 360 ])
    {
      translate([ r, 0, z ])
        {
          color( color_arg ) cube( od_ilu );
        }
    }
}

module example002()
{
  start = floor( -n/2 );
  stop = floor( n/2 );

  sh_start = floor( start / 7 );
  sh_stop = floor( stop / 7 );

  r_ch_start = floor( start / 29 );
  r_ch_stop = floor( stop / 29 );

  union()
  {
    for (i=[sh_start : sh_stop])
      {
        cube_on_helical_path( i*7, 0, "red" );
      }
    for (i=[r_ch_start : r_ch_stop])
      {
        cube_on_helical_path( i*29, 1, "blue" );
      }
  }
}

example002();
