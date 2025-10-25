<?php

require_once('../lib/global.php');
require_once('lib/userdata.php');

if( !logged_in() ) {
    header('Location: login.php');
    exit;
}

print_header("","../");

connect_db();

save_update_or_read_user_properties();

?>

<h1>Meine Einstellungen</h1>

<form id="userdata" class="userdata">
<fieldset>
    <legend>Name</legend>
    <table>
        <tr><td><label for="title">Titel:</td><td><input type="text" id="my_title" class="ui-widget ui-corner-all" 
                                                              value="<?php echo htmlspecialchars($_SESSION["USERPROPERTIES"]["title"]);?>"/></td></tr>
        
        <tr><td><label for="forename">Vorname:</td><td><input type="text" id="forename" class="ui-widget ui-corner-all" 
                                                              value="<?php echo htmlspecialchars($_SESSION["USERPROPERTIES"]["forename"]);?>"/></td></tr>
        <tr><td><label for="surename">Nachname:</td><td><input type="text" id="surename" class="ui-widget ui-corner-all"
                                                              value="<?php echo htmlspecialchars($_SESSION["USERPROPERTIES"]["surename"]);?>"/></td></tr>
    </table>
    <br/>
    <button id="save_userdata">Speichern</button>
    <span class="ui-state-default ui-corner-all saved-ok" id="saved_ok_userdata">gespeichert</span>
</fieldset>
</form>

<form id="password">
<fieldset>
    <legend>Passwort</legend>
    <table>
        <tr><td><label for="old_password">Altes Passwort:</td><td><input type="password" id="old_password" class="ui-widget ui-corner-all"/></td></tr>
        <tr><td><label for="password1">Neues Passwort:</td><td><input type="password" id="password1" class="ui-widget ui-corner-all"/></td></tr>
        <tr><td><label for="password2">Wiederholung:</td><td><input type="password" id="password2" class="ui-widget ui-corner-all"/></td></tr>
    </table>
    <br/>
    <button id="save_password">Speichern</button>
    <span class="ui-state-default ui-corner-all saved-ok" id="saved_ok_password">gespeichert</span>
</fieldset>
</form>

<script>
    
function change_password() {
   var pw1 = $("#password1").val();
   var pw2 = $("#password2").val();
        
   if( pw1 != pw2 ) {
        alert('Die Passwörter unterscheiden sich.');
        $("input[type=password]").val("");
        $("#password1").focus;
    } // pw1 != pw2 
    else
    {           
        $.ajax({
                type: "POST",
                url: "lib/change_password.php",
                data: "password=" + encodeURIComponent(pw1) + "&old_password=" + encodeURIComponent($("#old_password").val()),
                success: function( reqCode ) {
                    if( reqCode == 1 ) {
                        $("input[type=password]").val("");     
                        $("#saved_ok_password").show( function(){
                            window.setTimeout(function(){
                                $("#saved_ok_password").hide();
                            },10000);
                        });
                    } else {
                        alert('Fehler beim Speichern des Passwortes');
                    }
                },                
                error: function() {
                    alert('Fehler beim Speichern des Passwortes');
                }
            });        
    }    
}       

function verify_old_password() {

        $.ajax({
            type: "POST",
            url: "lib/check_password.php",
            data: "password=" + encodeURIComponent($("#old_password").val()),
            success: function( reqCode ) {
                if( reqCode == 1 ) {
                    change_password();                        
                } else {
                    alert('Das Password ist falsch');
                }
            },                
            error: function() {
                alert('Fehler beim überprüfen des Passwortes');
            }
        });            
        
        return false;
}

function save_userdata() {

        var forename = $("#forename").val();
        var surename = $("#surename").val();
        var title = $("#my_title").val();                
        
        $.ajax({
            type: "POST",
            url: "lib/save_userdata.php",
            data: "forename=" + encodeURIComponent(forename) 
                + "&title=" + encodeURIComponent(title)
                + "&surename=" + encodeURIComponent(surename),
            success: function( reqCode ) {
                if( reqCode == 1 ) {
                    $("#saved_ok_userdata").show( function(){
                        window.setTimeout(function(){
                            $("#saved_ok_userdata").hide();
                        },10000);
                    });
                } else {
                    alert('Fehler beim Speichern der Benutzerdaten');
                }
            },                
            error: function() {
                 alert('Fehler beim Speichern der Benutzerdaten');
            }
        });  
        
        return false;
}

function hide_saved_ok()
{   
    $(".saved-ok").hide();
}

$(document).ready( function() {    
    $("#save_password").button();
    $("#save_userdata").button();        
    
    $("#save_password").click(verify_old_password);
    $("#save_userdata").click(save_userdata);    
    $(":input").keypress(hide_saved_ok);
});
</script>

<?php
print_footer();
?>
