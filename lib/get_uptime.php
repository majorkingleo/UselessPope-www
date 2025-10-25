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
printf( "}\n");

?>
