<?php
require_once( '../lib/global.php' );

print_header("", "../");

?>

<body lang="de" id="papst">

<!-- main container -->
<div class="w3-content w3-container" id="tk">

<!-- Content -->

<main>
  
  <h1>Useless Papst</h1>

  <h2>Dem Papst die Treue schwören</h2>
  
    <div class="login">
    <form>
    <div class="row">
                <div class="column"><label for="username">Gläubigername:</label></div>
                <div class="column"><input type="text" name="username" id="username" class="input"/></div>
    </div>
    <div class="row">
                <div class="column"><label for="password">Zugriffswort:</label></div>
                <div class="column"><input type="password" name="password" id="password" class="input"/></div>
    </div>
    <div class="row">
                <div class="column"><input type="submit" name="submit" class="button ui-button ui-button-text-only ui-widget ui-state-default ui-corner-all" id="submit_btn" value="Beitreten"/></div>
    </div>
    </form>
    </div>

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
                        alert("Falscher Benutzername, oder falsches Passwort.");

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

</main>

<?php
print_footer();
?>

</div> <!-- main container #tk -->

</body>

</html>