<?php

include_once('global.php');

if( !logged_in() ) {
    exit(1);
}

connect_db();

$res = $mysql->query( "select * from USERS_ACTIONS where username='%s'", addslashes($_SESSION["USER"]["username"]));

if( !$res ) {
    error_log( "press_button.php: 1 " . $mysqli->error);
    echo -1;
    exit;
}

$row = $res->fetch_assoc();
$mac = $row["mac_address"];


$sql = sprintf( "insert into BUTTON_QUEUE ( time_stamp, seq, mac_address, ip_address, action, file ) " +
                                " VALUES( '%s', '%s', '%s', '%s', '%s', '%s' ) ",
                 time(), 
                 0, 
                 addslashes($mac), 
                 addslashes("website"),
                 "PLAYSOUND",
                addslashes($file) );


if( $mysqli->query( $sql ) !== TRUE ) {
    error_log( "press_button.php: 2 " . $mysqli->error);
    echo -1;
    exit;
}


?>