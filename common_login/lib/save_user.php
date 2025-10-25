<?php

require_once('../../lib/global.php');
require_once('user_rights.php');
require_once('userdata.php');

function other_login_already_exists( $login, $user ) {
    global $mysqli;

    $res = $mysqli->query( "select count(*) from USERS where username='" . addslashes($login) . "'"
            . " and idx != '" . addslashes($user) . "'");
    
    if( !$res ) {
        error_log( $mysqli->error );
        echo -3;
        return false;
    }
    
    $row = $res->fetch_array();
    
    if( $row[0] > 0 ) {
        return true;
    }
    
    return false;
}

if( !logged_in() ) {    
    echo "0";
    exit;
}

connect_db();

$title = $_POST["title"];
$login = $_POST["login"];
$user = $_POST["user"];
$locked = $_POST["locked"];

$password = "";

if( isset( $_POST["password"]) )
    $password = $_POST["password"];

error_log(print_r($_POST, true));

if( !trim($user) ) {
    echo -4;
    exit;
}

if( !trim($login) ) {
    echo -1;
    exit;
}

if( other_login_already_exists( $login, $user ) ) {
    echo 2;
    exit;
}


 if( !$_SESSION["USERRIGHTS"][USERRIGHT_EDIT_OTHER_USERS] ) {     
     error_log("not allowed to edit other users");
     echo -8;
     exit;
 }
 
  if( !$_SESSION["USERRIGHTS"][USERRIGHT_CREATE_OTHER_USERS] && 
      $_POST[USERRIGHT_CREATE_OTHER_USERS]) {     
     error_log("not allowed to give other users tha the permission to edit other users");
     echo -9;
     exit;
 }


$res = $mysqli->query( "update USERS set " 
        . " username='" . addslashes($login) . "',"
        . " locked='" . addslashes($locked) . "',"
        . " modification_time=CURRENT_TIMESTAMP()"
        . " where idx = '" . addslashes($user) . "'");

if( !$res ) {
    error_log( "edit_user.php: 1 " . $mysqli->error);
    echo -3;
    exit;
}

if( $password ) {
    $res = $mysqli->query( "update USERS set " 
        . " password=PASSWORD('" . addslashes($password) . "'),"
        . " modification_time=CURRENT_TIMESTAMP()"
        . " where idx = '" . addslashes($user) . "'");

    if( !$res ) {
        error_log( "edit_user.php: 2 " . $mysqli->error);
        echo -5;
        exit;
    }
}

foreach( $ALL_USERRIGHTS as $name => $value ) {
    if( !insert_or_update_userrights( $user, $name, $_POST[$name] ) ) {
        echo -6;
        exit;
    }
}

foreach( $ALL_USERPROPS as $name => $value ) {
    if( !insert_or_update_userprops( $user, $name, $_POST[$name] ) ) {
        echo -6;
        exit;
    }
    
    if( $user == $_SESSION["USER"]["idx"] ) {
        $_SESSION["USERRIGHTS"] = get_user_rights($user);
    }
}

echo 1;              
?>
