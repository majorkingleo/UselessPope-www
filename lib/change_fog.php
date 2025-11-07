<?php

function get_config( $key )
{
    global $mysqli;

    $res = $mysqli->query( sprintf( "select * from CONFIG where `key`='%s'", $key ) );

    if( !$res ) {
        error_log( "change_animation.php: 2 " . $mysqli->error);
        echo -2;
        exit;
    }

    $row = $res->fetch_assoc();

    if( !isset($row["value"]) ) {
        return false;
    }

    if( $row === false ) {
        return false;
    }

    # print_r( $row );

    return $row["value"];
}

include_once('global.php');

setlocale(LC_ALL,'en_US.UTF-8');

if( !logged_in() ) {
    exit(1);
}

connect_db();


// current animation

$current_fog = get_config( "fog" );

if( $current_fog === false ) {
    error_log( "change_fog.php: 2 " . $mysqli->error);
    echo -2;
    exit;
}

$fog = !intval($current_fog);

$sql = sprintf( "update CONFIG set `value` = '%d' where `key` = 'fog'", $fog );
$res = $mysqli->query( $sql );

if( !$res ) {
    error_log( "change_animation.php: 4 " . $mysqli->error);
}

?>