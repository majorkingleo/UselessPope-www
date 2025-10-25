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

connect_db();

$res = mysql_query( "select * from USERS "
        . " where idx=" . $_SESSION["USER"]["idx"] 
        . " and password=PASSWORD('" . addslashes($_POST["password"]) ."')" );

if( !$res ) {
    error_log(mysql_error());
    echo "-3";
    exit;
}

while( $row = mysql_fetch_assoc($res) ) {
    echo "1";
    exit;
}

echo "0";

?>
