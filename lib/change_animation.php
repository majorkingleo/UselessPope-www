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

$animation_file = get_config( sprintf( "animation%d", $animation ) );

if( $animation_file === false ) {
    $animation = 0;
    $animation_file = get_config( sprintf( "animation%d", $animation ) );

    if( $animation_file === false ) {
        error_log( "change_animation.php: 3 " . $mysqli->error);
        echo -2;
        exit;
    }
}


$sql = sprintf( "update CONFIG set `value` = '%d' where `key` = 'current_animation'", $animation );
$res = $mysqli->query( $sql );

if( !$res ) {
    error_log( "change_animation.php: 4 " . $mysqli->error);
}

$sql = sprintf( "insert into PLAY_QUEUE_ANIMATION( hist_an_user, hist_ae_user, hist_lo_user, file ) " .
                        "values( '%s', '%s', '%s', '%s' )",
                        addslashes($_SESSION["USER"]["username"]),
                        addslashes($_SESSION["USER"]["username"]),
                        addslashes($_SESSION["USER"]["username"]),
                        addslashes( $animation_file) );
$res = $mysqli->query( $sql );

if( !$res ) {
    error_log( "change_animation.php: 5 " . $mysqli->error);
}


?>