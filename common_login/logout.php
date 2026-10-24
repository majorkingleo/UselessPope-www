<?php

require_once( "../lib/global.php" );

session_destroy();

// Remove the persistent session cookie as well, otherwise it would linger
// for years after an explicit logout.
if( ini_get( "session.use_cookies" ) )
{
    $params = session_get_cookie_params();

    setcookie( session_name(), "", [
        "expires"  => time() - 42000,
        "path"     => $params["path"],
        "domain"   => $params["domain"],
        "secure"   => $params["secure"],
        "httponly" => $params["httponly"],
        "samesite" => $params["samesite"] ?? "Lax",
    ]);
}

header( "Location: ../index.php");

?>
