<?php

require_once('../lib/global.php');
require_once('lib/userdata.php');

if( !logged_in() ) {
    header('Location: login.php');
    exit;
}

print_header("","../");

connect_db();

function list_users()
{
    $users = fetch_users();
    
    foreach( $users as $key => $user ) 
    {      
        echo "<tr><td class=\"button-users\">";
        
        $username = $user["title"] . " " . $user["forename"] . " " . $user["surename"];
        
        if( !trim($username) ) {
            $username = $user["username"];
        }
        
        echo "<a  class=\"button ui-button ui-button-text-only  ui-state-default ui-widget ui-corner-all button-users\"" 
                . " id=\"" . $user["idx"] . "\""
                . " href=\"admin_users_edit_user.php?user=" . $user["idx"] . "\">";
        echo htmlspecialchars($username);
        echo "</a>";
        
        if( $user["locked"] ) {
            echo "</td><td  class=\"button-users-icons\">";                
            echo "<button class=\"button ui-state-default ui-corner-all button-users-icons\"><span class=\"ui-icon ui-icon-locked\"/></button>";
        }
        
        // echo "<button class=\"ui-state-default ui-corner-all\"/><span class=\"ui-icon ui-icon-trash\"></span></button>";
        
        echo "</td></tr>\n";
    }
}

?>

<h1>BenutzerInnen Administrieren</h1>

<fieldset>
    <legend>BenutzerInnen</legend>
    <table>    
     <?php list_users() ?>    
     </table>
    <br/>    
    
<?php if( $_SESSION["USERRIGHTS"][USERRIGHT_CREATE_OTHER_USERS] ) {
    echo "<a href=\"admin_users_create_user.php\" id=\"create-new-user\">\n";
    echo "    neuen BenutzerIn anlegen\n";
    echo "</a>\n";
}
?>
</fieldset>

<script>
$(document).ready( function() {    
   $("#create-new-user").button();
   $(".button").button();   
});
</script>
<?php
print_footer();
?>
