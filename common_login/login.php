<?php
require_once( '../lib/global.php' );

print_header("", "../");

?>
<form id="login">
<fieldset>
    <legend>Login</legend>
    <table>
        <tr><td>
                <label for="username">Benutzername:</label> 
            </td><td>
                <input type="text" name="username" id="username" class="ui-widget ui-corner-all"/>
            </td>
        </tr>
        <tr>
            <td><label for="password">Passwort:</label> </td>
            <td><input type="password" name="password" id="password" class="ui-widget ui-corner-all"/></td>
        </tr>
        <tr>
            <td>
                <input type="submit" name="submit" class="button ui-button ui-button-text-only ui-widget ui-state-default ui-corner-all" id="submit_btn" value="Anmelden"/>  
            </td>
        </tr>
    </table>
</fieldset>
</form>

<script>
    $(document).ready(function() {
        $("#username").focus();
        
        $(".button").click(function(){
            $.ajax({
                type: "POST",
                url: "lib/logon.php",
                data: "username=" + encodeURIComponent($("#username").val()) + "&password=" + encodeURIComponent($("#password").val()),
                success: function( reqCode ) {
                    if( reqCode == 1 ) {
                        window.location="../index.php";
                    } else {
                        // alert("Falscher Benutzername, oder falsches Passwort.");
                        $( "#dialog-message" ).dialog({
                            modal: true,
                            buttons: {
				Ok: function() {
					$( this ).dialog( "close" );
				}
                            }
                        });
                    }
                },                
                error: function() {
                    alert('Fehler bei der Anmeldung');
                }
            });
            return false;
        });
    });
</script>

<div style="display: none">
<div id="dialog-message" title="Falscher Benutzername, oder falsches Passwort.">
	<p>
		<span class="ui-icon ui-icon-circle-check" style="float:left; margin:0 7px 50px 0;"></span>
		Ihre Anmeldung ist fehlgeschlagen. Vielleicht ist Ihr Benutzername, oder
                Ihr Passwort nicht korrekt.
	</p>
</div>
</div>

<?php
print_footer();
?>
