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

$current_animation = get_config( "current_animation" );

if( $current_animation === false ) {
    error_log( "change_animation.php: 2 " . $mysqli->error);
    echo -2;
    exit;
}

$animation = intval($current_animation) + 1;

// wrap back to 0 when the next animation index doesn't exist
if( get_config( sprintf( "animation%d", $animation ) ) === false ) {
    $animation = 0;

    if( get_config( sprintf( "animation%d", $animation ) ) === false ) {
        error_log( "change_animation.php: 3 " . $mysqli->error);
        echo -2;
        exit;
    }
}

change_animation_to( $animation );

?>