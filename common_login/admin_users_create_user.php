<?php
require_once('../lib/global.php');
require_once('lib/user_rights.php');

if( !logged_in() ) {
    header('Location: login.php');
    exit;
}

print_header("","../");

connect_db();

function list_userrights($user= -1)
{
    global $ALL_USERRIGHTS_DESC;
    global $ALL_USERRIGHTS;
        
    $rights = $ALL_USERRIGHTS;
    
    if( $user >= 0 ) {
        $rights = get_user_rights($user);
    }
    
    foreach($rights as $name => $value )   {
        
        if( !$_SESSION["USERRIGHTS"][USERRIGHT_EDIT_OTHER_USERS] &&
            $name == USERRIGHT_EDIT_OTHER_USERS ) 
        {
            continue;
        }                
        
        if( !$_SESSION["USERRIGHTS"][USERRIGHT_DEL_OTHER_USERS] &&
            $name == USERRIGHT_DEL_OTHER_USERS ) 
        {
            continue;
        }           
        
        echo "<tr><td>";
        echo "<label for=\"" . $name . "\">". $ALL_USERRIGHTS_DESC[$name] . ":</td><td>";                            
        echo "<input type=\"checkbox\" id=\"" . $name . "\" class=\"ui-widget ui-corner-all\"";
        if( $value ) {
             echo "checked";
        }
        echo "/></td></tr>";
    }
}

?>

<h1>BenutzerInnen Anlegen</h1>

<form id="create_user">
<fieldset>
    <legend>BenutzerIn anlegen</legend>

    <table>
        <tr><td><label for="login">Login:</td><td><input type="text" id="login" class="ui-widget ui-corner-all"/></td></tr>
        
        <tr><td><label for="title">Titel:</td><td><input type="text" id="title" class="ui-widget ui-corner-all"/></td></tr>
        
        <tr><td><label for="forename">Vorname:</td><td><input type="text" id="forename" class="ui-widget ui-corner-all"/></td></tr>
        <tr><td><label for="surename">Nachname:</td><td><input type="text" id="surename" class="ui-widget ui-corner-all"/></td></tr>
        
        <tr><td><label for="password1">Passwort:</td><td><input type="password" id="password1" class="ui-widget ui-corner-all"/></td></tr>
        <tr><td><label for="password2">Wiederholung:</td><td><input type="password" id="password2" class="ui-widget ui-corner-all"/></td></tr>
    </table>
</fieldset>
<fieldset>
    <legend>Rechte</legend>

    <table>
<?php  list_userrights(); ?>        
    </table>    
</fieldset>
    <br/>
    <input type="submit" id="save" class="ui-button ui-button-text-only ui-widget ui-state-default ui-corner-all" value="BenutzerIn anlegen"/>    
</form>       

<script>       
function verify_login() {
    login = $("#login").val();
    
    if( !login ) {
        alert( "Bitten einen Login vergeben.")
        return false;
    }
    
   var pw1 = $("#password1").val();
   var pw2 = $("#password2").val();
        
   if( pw1 != pw2 ) {
        alert('Die Passwörter unterscheiden sich.');
        $("input[type=password]").val("");
        $("#password1").focus;
        return false;
    } // pw1 != pw2     
    
    
        $.ajax({
            type: "POST",
            url: "lib/create_user.php",
            data: { login: login,
                    title:    $("#title").val(),
                    forename: $("#forename").val(),
                    surename: $("#surename").val(),
               <?php insert_userrights_javascript(); ?>                        
                    password: pw1
                },
            success: function( reqCode ) {
                if( reqCode == 1 ) {
                    parent.history.back();
                } else if( reqCode == 2 ) {
                    alert('Dieser Login Name ist bereits vergeben.');                    
                    $("#login").focus();
                } else if( reqCode == -2) {
                    alert('Bitte vergeben Sie ein Passwort');                    
                    $("#password1").focus();
                } else {
                    alert('Fehler beim Anlegen des Benutzers, oder der Benutzerin');
                }
            },                
            error: function() {
                alert('Fehler beim Anlegen des Benutzers, oder der Benutzerin');
            }
        });      
    
    return false;
}        
    
$(document).ready( function() {    
    $("#save").click(verify_login);
});
</script>

<?php
print_footer();
?>
