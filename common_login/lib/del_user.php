<?php

require_once('../../lib/global.php');
require_once('user_rights.php');
require_once('userdata.php');

 if( !$_SESSION["USERRIGHTS"][USERRIGHT_DEL_OTHER_USERS] ) { 
     echo "-1";
     exit;
 }
 
 connect_db();
 
 $res = $mysqli->query( "delete from USERS where idx='" . addslashes($_POST["user_id"]) . "'");
 
 if( !$res ) {
     error_log("cannot delete user: " . $mysqli->error);
     echo "-2";
     exit;
 }
 
 echo 1;
?>
