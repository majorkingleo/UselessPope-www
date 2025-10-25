<?php

include_once('global.php');

if( !logged_in() ) {
    exit(1);
}

connect_db();

$res = $mysqli->query( sprintf("select * from USERS_ACTIONS where username='%s'", addslashes($_SESSION["USER"]["username"])));

if( !$res ) {
    error_log( "press_button.php: 1 " . $mysqli->error);
    echo -1;
    exit;
}

$row = $res->fetch_assoc();
$mac = $row["button_mac_address"];


$sql = sprintf( "insert into BUTTON_QUEUE ( time_stamp, seq, mac_address, ip_address, action, file, hist_an_user, hist_ae_user, hist_lo_user ) " .
                                " VALUES( '%d', '%d', '%s', '%s', '%s', '%s' , '%s' , '%s' , '%s' ) ",
                 time(), 
                 0, 
                 addslashes($mac), 
                 addslashes("website"),
                 "PLAYSOUND",
                addslashes($_POST["file"]),
                $_SESSION["USER"]["username"],
                $_SESSION["USER"]["username"],
                $_SESSION["USER"]["username"]                
             );


if( $mysqli->query( $sql ) !== TRUE ) {
    error_log( "press_button.php: 2 " . $mysqli->error);
    echo -1;
    exit;
}


?>