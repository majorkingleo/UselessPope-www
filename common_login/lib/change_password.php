<?php

require_once( '../../lib/global.php' );

if( !logged_in() ) {    
    echo "0";
    exit;
}

if( !isset( $_POST["password"] ) || !trim($_POST["password"]) ) {
    echo "-1";
    exit;
}

if( !isset( $_POST["old_password"] ) || !trim($_POST["old_password"]) ) {
    echo "-2";
    exit;
}

connect_db();

$res = $mysqli->query( "update USERS set "
        . " password=PASSWORD('" . addslashes($_POST["password"]) . "'), "
        . " modification_time = CURRENT_TIMESTAMP() " 
        . " where idx=" . $_SESSION["USER"]["idx"] 
        . " and password=PASSWORD('" . addslashes($_POST["old_password"]) ."')" );

if( !$res ) {
    error_log($mysqli->error);
    echo "-3";
    exit;
}

echo "1";

?>
