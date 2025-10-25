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

$res = mysql_query( "select * from USERS where "
        . "username='" . addslashes($_POST["username"]) . "' and "
        . "password=PASSWORD('" . addslashes($_POST["password"]) . "') and "
        . "locked=0" );

if( !$res )
{
    error_log("failed 0:  "  . mysql_error());
    die( mysql_error() );
}

while( $row = mysql_fetch_assoc($res ) )
{   
    $_SESSION["USER"] = $row;
    save_update_or_read_user_properties();
    $_SESSION["USERRIGHTS"] = get_user_rights( $_SESSION["USER"]["idx"] );
    
    $res = mysql_query( "update USERS set last_login = NOW(), failed_count = 0 where "
                     . " username='" . addslashes($_POST["username"]) . "' ");
            
    if( !$res )
    {
       error_log("failed 1:  "  . mysql_error());
       die( mysql_error() );
    }
       
    
    echo 1;
    exit;
}

error_log( "couldn't logged in " . addslashes($_POST["username"]) . " pw: '" . addslashes($_POST["password"]) . "'" );

/* increase failed logins */

$res = mysql_query( "select *, UNIX_TIMESTAMP(last_failed_login) from USERS where "
        . "username='" . addslashes($_POST["username"]) . "' and "        
        . "locked=0" );

if( !$res )
{
    error_log("failed 2:  "  . mysql_error());
    die( mysql_error() );
}

while( $row = mysql_fetch_assoc($res ) )
{
    error_log( "found username in database" );
    
    if( $row["failed_count"] > 5 ) {
        /* already failed too much */
        error_log( "failed count = " .  $row["failed_count"] );
        break;
    }
    
    if( $row["UNIX_TIMESTAMP(last_failed_login)"] >= time(0) - 60*60 ) {
        $res = mysql_query( "update USERS set last_failed_login = NOW(), failed_count = failed_count + 1 where "
                . " username='" . addslashes($_POST["username"]) . "' ");
        
        if( !$res )
        {
            error_log("failed 3:  "  . mysql_error());
            die( mysql_error() );
        }
        
        error_log( "last_failed is now" );
        
        if( $row["failed_count"] + 1 > 5 ) {
            $res = mysql_query( "update USERS set last_failed_login = NOW(), locked = 1 where "
                     . " username='" . addslashes($_POST["username"]) . "' ");
            
            if( !$res )
            {
                error_log("failed 4:  "  . mysql_error());
                die( mysql_error() );
            }        
            break;
        }
    } else {
        error_log( "last_failed is now" );
        
        $res = mysql_query("update USERS set last_failed_login = NOW() where "
                . " username='" . addslashes($_POST["username"]) . "' ");

        if (!$res) {
            error_log("failed 5:  " . mysql_error());
            die(mysql_error());
        }
        break;
    }
}
    
echo 0;

?>
