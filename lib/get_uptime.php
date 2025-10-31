<?php

include_once( "global.php" );

$uptime = @file_get_contents( "/proc/uptime");

$uptime_val = intval($uptime);

$boot_time = time() - $uptime_val;

# Freitag, 21. Oktober 2025, 10:37:23
setlocale( LC_TIME, "de_AT" );
$boot_time_str = strftime( "%A, %e. %B %Y %T", $boot_time );

printf( "{ \"uptimevalue\": \"%s\",\n", $uptime_val );
printf( "  \"boottime\": \"%s\"\n", $boot_time_str );

# temp=75.2'C
$handle = popen("sudo vcgencmd measure_temp", "r" );
$cpu_temp = fread($handle, 2096);
$cpu_temp_values = explode( "=", $cpu_temp );

if( count( $cpu_temp_values ) == 2 )
{
    # 75.2'C -> 75.2
    $pos = strpos( $cpu_temp_values[1], "'" );
    if( $pos !== false ) {
        $cpu_temp_values[1] = substr( $cpu_temp_values[1], 0, $pos );
    }

    printf( ", \"cputemp\": \"%s\"\n", $cpu_temp_values[1]);
}

printf( "}\n");

?>
