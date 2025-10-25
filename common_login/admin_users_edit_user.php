<?php
require_once('../lib/global.php');
require_once('lib/userdata.php');
require_once('lib/user_rights.php');

if( !logged_in() ) {
    header('Location: login.php');
    exit;
}

print_header("","../");

connect_db();

$user = fetch_users($_GET["user"]);

$user = $user[0];

function list_userrights($user= -1)
{
    global $ALL_USERRIGHTS_DESC;
    global $ALL_USERRIGHTS;
        
    $rights = $ALL_USERRIGHTS;
    
    if( $user >= 0 ) {
        $rights = get_user_rights($user);
    }
           
    
    foreach($rights as $name => $value )   {
        
        if( $_SESSION["USER"]["idx"] == $user && 
            !$_SESSION["USERRIGHTS"][USERRIGHT_CREATE_OTHER_USERS] &&
            $name == USERRIGHT_CREATE_OTHER_USERS ) 
        {
            continue;
        }
        
        
        if( $_SESSION["USER"]["idx"] == $user && 
            !$_SESSION["USERRIGHTS"][USERRIGHT_DEL_OTHER_USERS] &&
            $name == USERRIGHT_DEL_OTHER_USERS ) 
        {
            continue;
        }        
        
        if( isset($ALL_USERRIGHTS_DESC[$name] ) ) {
        
            echo "<tr><td>";
            echo "<label for=\"" . $name . "\">". $ALL_USERRIGHTS_DESC[$name] . ":</td><td>";                            
            echo "<input type=\"checkbox\" id=\"" . $name . "\" class=\"ui-widget ui-corner-all\"";
            if( $value ) {
                 echo "checked";
            }
            
            echo "/></td></tr>";
        }
    }
}

?>

<h1>BenutzerInnen Bearbeiten</h1>

<form id="create_user">
<fieldset>
    <legend>BenutzerIn bearbeiten</legend>

    <table>
        <tr><td><label for="login">Login:</td><td><input type="text" id="login" class="ui-widget ui-corner-all"
                                                         value="<?php echo htmlspecialchars($user["username"]); ?>"/></td></tr>
        
        <tr><td><label for="title">Titel:</td><td><input type="text" id="title" class="ui-widget ui-corner-all"
                                                         value="<?php echo htmlspecialchars($user["title"]); ?>"/></td></tr>
        
        <tr><td><label for="forename">Vorname:</td><td><input type="text" id="forename" class="ui-widget ui-corner-all"
                                                              value="<?php echo htmlspecialchars($user["forename"]); ?>"/></td></tr>
        <tr><td><label for="surename">Nachname:</td><td><input type="text" id="surename" class="ui-widget ui-corner-all"
                                                               value="<?php echo htmlspecialchars($user["surename"]); ?>"/></td></tr>
        
        <tr><td><label for="password1">Passwort:</td><td><input type="password" id="password1" class="ui-widget ui-corner-all"/></td></tr>
        <tr><td><label for="password2">Wiederholung:</td><td><input type="password" id="password2" class="ui-widget ui-corner-all"/></td></tr>
        <tr><td><label for="locked">Gesperrt:</td><td><input type="checkbox" id="locked" class="ui-widget ui-corner-all"
                                                             <?php if( $user["locked"] ) {  echo "checked"; } else { echo ""; }?>/></td></tr>
    </table>
</fieldset>
<fieldset>
    <legend>Rechte</legend>

    <table>
<?php  list_userrights($user["idx"]); ?>
    </table>    
</fieldset>
<br/>
    <input type="submit" id="save" class="ui-button ui-button-text-only ui-widget ui-state-default ui-corner-all" value="Speichern"/>    
    
    <?php if( isset( $_SESSION["USERRIGHTS"][USERRIGHT_DEL_OTHER_USERS]) && $_SESSION["USERRIGHTS"][USERRIGHT_DEL_OTHER_USERS] ) {
        echo "<input type=\"submit\" id=\"del\" class=\"ui-button ui-button-text-only ui-widget ui-state-default ui-corner-all\" value=\"Löschen\"/>\n";
     }
    ?>
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
   
   if( pw1.length > 0 || pw2.length > 0 ) {
        if( pw1 != pw2 ) {
            alert('Die Passwörter unterscheiden sich.');
            $("input[type=password]").val("");
            $("#password1").focus;
            return false;
        } // pw1 != pw2     
 
   }           
        $.ajax({
            type: "POST",
            url: "lib/save_user.php",
            data: { 
                login: login,
                title: $("#title").val(),
                forename: $("#forename").val(),
                surename: $("#surename").val(),
                password: pw1,
                locked: $("#locked").attr('checked') ? 1 : 0,
                <?php insert_userrights_javascript(); ?>                
                user: <?php echo $_GET["user"];?>            
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

function del_user()
{
    var input_box = confirm("Wollen Sie du die/den BenutzerIn wirklich löschen?")
    
    if( input_box == true ) {
         
        $.ajax({
            type: "POST",
            url: "lib/del_user.php",
            data: {                 
                user_id: <?php echo $user["idx"];?>
            },
            success: function( reqCode ) {
                if( reqCode >= 0 ) {
                    window.location="admin_users.php";
                } else {
                    alert('Fehler beim Löschen.');
                }
            },                
            error: function() {
                 alert('Fehler beim Löschen.');
            }
        });              
    }
    
    return false;
}
    
$(document).ready( function() {    
    $("#save").button();
    $("#del").button();
    $("#save").click(verify_login);
    
    <?php 
      if(  $_SESSION["USERRIGHTS"][USERRIGHT_DEL_OTHER_USERS] ) {
        echo "$(\"#del\").click(del_user);\n";
      }
    ?>
});
</script>

<?php
print_footer();
?>
