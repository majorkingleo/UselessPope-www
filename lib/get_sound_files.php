<?php

include_once( "global.php" );

if( !logged_in() ) {
    error_log( "no logged in yet" );    
    exit;
}

connect_db();

$sql = sprintf( "select * from USERS_ACTIONS where username='%s'", 
                addslashes($_SESSION["USER"]["username"]));

$res = $mysqli->query( $sql );

if( !$res ) {
    error_log( "get_sound_files.php: 1 " . $mysqli->error);
    echo -1;
    exit;
}

$USERS_ACTIONS = $res->fetch_assoc();

if( !$USERS_ACTIONS || !isset($USERS_ACTIONS["home_directory"]) ) {
    error_log( "get_sound_files.php: 2 " . $mysqli->error);
    echo -2;
    exit;
}

$audio_random_dir = $USERS_ACTIONS["home_directory"] . "/" . AUDIO_RANDOM_DIRECTORY;

$audio_random_files = scandir($audio_random_dir);

print( "{ ");

$idx = 0;

foreach(  $audio_random_files as $file ) {

    if( $file == "." || $file == ".." ) {
        continue;
    }

    if( $idx > 0 ) {
        print( ",\n" );
    }

    printf( "\"sound_button_%d\": \"%s\"", $idx, $file );

    $idx++;
}

print( "}" );

?>
