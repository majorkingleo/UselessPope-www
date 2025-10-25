<?php

require_once( '../../lib/global.php' );
require_once( 'userdata.php' );

if( !logged_in() ) {    
    echo "0";
    exit;
}

connect_db();

$_SESSION["USERPROPERTIES"]["forename"] = $_POST["forename"];
$_SESSION["USERPROPERTIES"]["surename"] = $_POST["surename"];
$_SESSION["USERPROPERTIES"]["title"] = $_POST["title"];

// error_log( "forname: " . $_SESSION["USERPROPERTIES"]["forname"]);

foreach( $ALL_USERPROPS as $name => $value ) {
    error_log( " $name => " . $_POST[$name] . " ");
    $res = insert_or_update_userprops($_SESSION["USER"]["idx"], $name, $_POST[$name]);
    if( !$res ) {
        error_log($mysqli->error);
        echo "-3";
        exit;
    }    
}

echo "1";

?>
