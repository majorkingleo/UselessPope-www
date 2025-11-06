<?php
include_once( 'lib/global.php' );

if( !logged_in() ) {
    error_log( "no logged in yet" );    
    exit;
}

connect_db();

print_header();

?>

<body lang="de" id="log">
 <table class="log">

<?php
$sql = sprintf( "select * from SERMON order by hist_an_zeit desc");

$res = $mysqli->query( $sql );

if( !$res ) {
    error_log( "log.php: 1 " . $mysqli->error);
    echo -1;
    exit;
}

while( $SERMON = $res->fetch_assoc() ) {
      printf( "<tr>\n");
      printf( "  <td>%s</td>\n", $SERMON["hist_an_zeit"]);
      printf( "  <td><p>%s</p>\n", $SERMON["action"]);
      printf( "      <p>→ %s</p>\n", $SERMON["reaction"]);
      printf( "   </td>\n");
      printf( "</tr>\n");
}

?>

 </table>

</body>

</html>