<?php
include_once( 'lib/global.php' );

// constant for session
$CURRENT_PAGE="INDEX";

if( !logged_in() )
{
    header( "Location: common_login/login.php" );
    exit(0);
}

print_header();
?>


<body lang="de" id="papst">

<!-- main container -->
<div class="w3-content w3-container" id="tk">

<!-- Content -->

<main>
  
  <h1>Useless Battlefield</h1>

  <h2>Aktuelles Game</h2>
  
   <div id="stats">
    
     <div class="row">
        <div class="column">Map:</div>
        <div class="column"><span class="highlight bf_map" id="bf_map">–</span></div>
     </div>
     <div class="row">
        <div class="column">Server:</div>
        <div class="column"><span class="highlight bf_hoster" id="bf_hoster">–</span></div>
     </div>
     <div class="row">
        <div class="column">Spieler:</div>
        <div class="column"><span class="highlight" id="bf_numplayers">–</span></div>
     </div>
     <div class="row">
        <div class="column">Tickets:</div>
        <div class="column"><span class="highlight" id="bf_tickets">–</span></div>
     </div>
     <div class="row">
        <div class="column">Rundenzeit:</div>
        <div class="column"><span class="highlight" id="bf_round_time">–</span></div>
     </div>
     <div class="row">
        <div class="column">Status:</div>
        <div class="column"><span class="highlight" id="bf_online">–</span></div>
     </div>
   
   </div>
  
  
  <h2>Statistik aller Spiele</h2>
  
    <div id="bf-stats">
  
    <table>

    <thead>
    <tr>
        <th></th>
        <th>Spieler</th>
        <th>Punkte</th>
        <th>Kills</th>
        <th>Deaths</th>
    </tr>
    </thead>

    <tbody id="bf-stats-body">
    <tr>
        <td colspan="5">Lade Statistik…</td>
    </tr>
    </tbody>
    
    </table>
      
    </div>
  
    <button id="bf-suggest" type="button"><span>Team vorschlagen</span></button>

    <div id="bf-teams" class="grid">
    
        <div>
            <p>Alliierte</p>
            <p>Lade Teams…</p>
        </div>

        <div>
            <p>Achse</p>
            <p>Lade Teams…</p>
        </div>
    
    </div>

  <h2>Game Log</h2>

    <iframe src="bf_log.php" title="game log" id="game-log" height="500px"></iframe>

  <h2>Sakristei</h2>
  
    <div id="admin">
      <a href="index.php"><button><span>Zurück zum Useless Papst</span></button></a>
      <a href="common_login/logout.php"><button><span>Exkommunikation einleiten</span></button></a>
      <a href="common_login/admin_myself.php"><button><span>Geheimes Zugriffswort erneuern</span></button></a>
    </div>
  
</main>

<?php
print_footer();
?>

</div> <!-- main container #tk -->







</body>

</html>
