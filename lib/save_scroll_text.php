<?php

include_once('global.php');

if( !logged_in() ) {
    exit(1);
}

if( !isset($_POST["scroll_text"]) ) {
    error_log( "save_scroll_text.php: 1 missing scroll_text" );
    echo -1;
    exit;
}

$scroll_text = trim($_POST["scroll_text"]);

connect_db();

/**
 * Upsert a single CONFIG key/value pair.
 * - INSERT (with hist_an_* + hist_ae_* stamped) when the key is missing.
 * - UPDATE value + hist_ae_* when the key already exists.
 */
function save_config_value( $mysqli, $key, $value )
{
    $username = addslashes($_SESSION["USER"]["username"]);
    $key      = addslashes($key);
    $value    = addslashes($value);

    $res = $mysqli->query( "select * from CONFIG where `key`='{$key}'" );
    if( !$res ) {
        error_log( "save_scroll_text.php: select {$key} " . $mysqli->error );
        return false;
    }

    $row = $res->fetch_assoc();

    if( $row === null || $row === false ) {
        $sql = sprintf( "insert into CONFIG ( `key`, `value`, hist_an_zeit, hist_an_user, hist_ae_zeit, hist_ae_user ) " .
                        "values ( '%s', '%s', CURRENT_TIMESTAMP(), '%s', CURRENT_TIMESTAMP(), '%s' )",
                        $key, $value, $username, $username );
    } else {
        $sql = sprintf( "update CONFIG set `value` = '%s', " .
                        "`hist_ae_zeit` = CURRENT_TIMESTAMP(), " .
                        "`hist_ae_user` = '%s' where `key` = '%s'",
                        $value, $username, $key );
    }

    $res = $mysqli->query( $sql );
    if( !$res ) {
        error_log( "save_scroll_text.php: upsert {$key} " . $mysqli->error );
        return false;
    }

    return true;
}

/**
 * Read a single CONFIG value. Returns null when the key is missing.
 */
function get_config_value( $mysqli, $key )
{
    $key = addslashes($key);

    $res = $mysqli->query( "select * from CONFIG where `key`='{$key}'" );
    if( !$res ) {
        error_log( "save_scroll_text.php: select {$key} " . $mysqli->error );
        return null;
    }

    $row = $res->fetch_assoc();
    if( $row === null || $row === false ) {
        return null;
    }

    return $row["value"];
}

// The Magnificat text is displayed by text_scroll.py via CONFIG `scroll_text`.
// Keep the text-scroll animation's title (`animation5_title`) in sync so the
// animation listing / web UI reflect the new message too.
$ok  = save_config_value( $mysqli, "scroll_text",      $scroll_text );
$ok  = save_config_value( $mysqli, "animation5_title", $scroll_text ) && $ok;

// Make sure the text-scroll animation (index 5) is the active one, so the new
// message actually shows up. Only switch when it isn't already 5.
if( intval( get_config_value( $mysqli, "current_animation" ) ) !== 5 ) {
    $ok = change_animation_to( 5 ) && $ok;
}

if( !$ok ) {
    echo -2;
    exit;
}

echo 1;

?>
