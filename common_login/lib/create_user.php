<?php

require_once('../../lib/global.php');
require_once('user_rights.php');
require_once('userdata.php');

function login_already_exists( $login ) {
    $res = mysql_query( "select count(*) from USERS where username='" . addslashes($login) . "'");
    
    if( !$res ) {
        error_log( mysql_error() );
        echo -3;
        return false;
    }
    
    $row = mysql_fetch_array($res);
    
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

$login = $_POST["login"];
$password = $_POST["password"];

if( !trim($login) ) {
    echo -1;
    exit;
}

if( !trim($password) ) {
    echo -2;
    exit;
}

if( login_already_exists( $login ) ) {
    echo 2;
    exit;
}

 if( !$_SESSION["USERRIGHTS"][USERRIGHT_CREATE_OTHER_USERS] ) {     
     error_log("not allowed to create other users");
     echo -8;
     exit;
 }
 
  if( !$_SESSION["USERRIGHTS"][USERRIGHT_EDIT_OTHER_USERS] && 
      $_POST[USERRIGHT_EDIT_OTHER_USERS]) {     
     error_log("not allowed to create other users that can edit other users");
     echo -9;
     exit;
 }

  if( !$_SESSION["USERRIGHTS"][USERRIGHT_DEL_OTHER_USERS] && 
      $_POST[USERRIGHT_DEL_OTHER_USERS]) {     
     error_log("not allowed to del other users");
     echo -10;
     exit;
 }

$res = mysql_query( "insert into USERS ( username, password, creation_time, modification_time ) " .
               " VALUES( '" . addslashes($login) . "'," 
               . "PASSWORD('" . addslashes($password) . "'),"
               . "CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() )"  );

if( !$res ) {
    error_log( "create_user.php: 1 " . mysql_error());
    echo -3;
    exit;
}

$user = mysql_insert_id();

foreach( $ALL_USERRIGHTS as $name => $value ) {        
    if( !insert_or_update_userrights( $user, $name, $_POST[$name] ) ) {
        echo -6;
        exit;
    }
}

foreach( $ALL_USERPROPS as $name => $value ) {
    if( !insert_or_update_userprops( $user, $name, $_POST[$name] ) ) {
        echo -7;
        exit;
    }
}

echo 1;              
?>
