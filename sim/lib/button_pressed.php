<?php

include_once( 'global.php' );

$id = $_GET["id"];

// b10 => l10
$id = "l" . substr( $id, 1 );


$stmt = $mysqli->prepare( "SELECT * FROM STATUS_LEDS where name = ?");
$stmt->bind_param( "s", $id );
$stmt->execute();
$result = $stmt->get_result();

$obj = $result->fetch_object();

error_log(  sprintf( "\"%s\": \"#%06X\"", $obj->name, $obj->value ) );


if( $obj->value != 0x00FF00 ) {
    $color = 0x00FF00;
} else {
    $color = 0xFFFFFF;
}

$stmt = $mysqli->prepare( "update STATUS_LEDS set value = ? where name = ?" );
$stmt->bind_param( "is", $color, $id );
$stmt->execute();
$mysqli->commit();

?>
