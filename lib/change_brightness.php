<?php

include_once('global.php');

setlocale(LC_ALL,'en_US.UTF-8');

if( !logged_in() ) {
    exit(1);
}

connect_db();

$inc = 1;

if( !isset($_GET["incbrightness"]) ) {
    error_log( "change_brightness.php: 1 ");
    echo -1;
    exit;
}

$inc = $_GET["incbrightness"];

$res = $mysqli->query( "select * from CONFIG where `key`='brightness'" );

if( !$res ) {
    error_log( "change_brightness.php: 2 " . $mysqli->error);
    echo -2;
    exit;
}

$row = $res->fetch_assoc();
$brightness = floatval($row["value"]);


$brightness += $inc;

if( $brightness > 0.8 ) {
   $brightness = 0.8;
}

if( $brightness <= 0.0 ) {
   $brightness = 0.02;
}

$sql = sprintf( "update CONFIG set `value` = '%f' where `key` = 'brightness'", $brightness );
error_log( $sql );
$res = $mysqli->query( $sql );

if( !$res ) {
    error_log( "change_brightness.php: 2 " . $mysqli->error);
}

?>