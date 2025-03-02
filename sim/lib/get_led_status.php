<?php

include_once( 'global.php' );

$result = mysqli_query($mysqli, "SELECT * FROM STATUS_LEDS");

$first = true;

printf( "{\n" );

while($obj = $result->fetch_object()){
        if( $first !== true ) {
            echo ",\n";
        }
        $first = false;
        
        printf( "\"%s\": \"#%06X\"", $obj->name, $obj->value );
}

printf( "}\n" );

#echo '{"l1":"#ff0000","l2":"#00ff00"}';

?>
