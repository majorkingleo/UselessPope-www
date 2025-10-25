<?php

require_once('../../lib/global.php');
require_once('userdata.php');
require_once('user_rights.php');

if( !isset($_POST['username']) || !isset($_POST['password']) ) {
    echo "0";
    exit;
}

if( !trim($_POST['username']) || !trim($_POST['password']) ) {
    echo "0";
    exit;
}

connect_db();
    
//error_log("username: " . $_POST["username"] . " password: " . $_POST["password"]);

$res = $mysqli->query( "select * from USERS where "
        . "username='" . addslashes($_POST["username"]) . "' and "
        . "password=PASSWORD('" . addslashes($_POST["password"]) . "') and "
        . "locked=0" );

if( !$res )
{
    error_log("failed 0:  "  . $mysqli->error);
    die( $mysqli->error );
}

while( $row = $res->fetch_assoc() )
{   
    $_SESSION["USER"] = $row;
    save_update_or_read_user_properties();
    $_SESSION["USERRIGHTS"] = get_user_rights( $_SESSION["USER"]["idx"] );
    
    $res = $mysqli->query( "update USERS set last_login = NOW(), failed_count = 0 where "
                     . " username='" . addslashes($_POST["username"]) . "' ");
            
    if( !$res )
    {
       error_log("failed 1:  "  . $mysqli->error);
       die( $mysqli->error );
    }
       
    
    echo 1;
    exit;
}

error_log( "couldn't logged in " . addslashes($_POST["username"]) . " pw: '" . addslashes($_POST["password"]) . "'" );

/* increase failed logins */

$res = $mysqli->query( "select *, UNIX_TIMESTAMP(last_failed_login) from USERS where "
        . "username='" . addslashes($_POST["username"]) . "' and "        
        . "locked=0" );

if( !$res )
{
    error_log("failed 2:  "  . $mysqli->error);
    die( $mysqli->error );
}

while( $row = $res->fetch_assoc() )
{
    error_log( "found username in database" );
    
    if( $row["failed_count"] > 5 ) {
        /* already failed too much */
        error_log( "failed count = " .  $row["failed_count"] );
        break;
    }
    
    if( $row["UNIX_TIMESTAMP(last_failed_login)"] >= time(0) - 60*60 ) {
        $res = $mysqli->query( "update USERS set last_failed_login = NOW(), failed_count = failed_count + 1 where "
                . " username='" . addslashes($_POST["username"]) . "' ");
        
        if( !$res )
        {
            error_log("failed 3:  "  . $mysqli->error);
            die( $mysqli->error );
        }
        
        error_log( "last_failed is now" );
        
        if( $row["failed_count"] + 1 > 5 ) {
            $res = $mysqli->query( "update USERS set last_failed_login = NOW(), locked = 1 where "
                     . " username='" . addslashes($_POST["username"]) . "' ");
            
            if( !$res )
            {
                error_log("failed 4:  "  . $mysqli->error);
                die( $mysqli->error );
            }        
            break;
        }
    } else {
        error_log( "last_failed is now" );
        
        $res = $mysqli->query("update USERS set last_failed_login = NOW() where "
                . " username='" . addslashes($_POST["username"]) . "' ");

        if (!$res) {
            error_log("failed 5:  " . $mysqli->error);
            die($mysqli->error);
        }
        break;
    }
}
    
echo 0;

?>
