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
  
  <div id="admin">
  <a href="index.php"><button><span>Zurück zum Useless Papst</span></button></a>
  </div>

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
  
  
  <h2>Statistik der letzten <span id="bf_stats_games"></span> Spiele</h2>

    <button id="bf-per-game" type="button" aria-pressed="false"><span>Werte pro Spiel</span></button>
  
    <div id="bf-stats">
  
    <table>

    <thead>
    <tr>
        <th></th>
        <th>Spieler</th>
        <th class="bf-sortable" data-sort="games" title="Nach gespielten Spielen sortieren">Spiele</th>
        <th class="bf-sortable" data-sort="score" data-label="Punkte" data-label-per-game="Ø Pkt" data-title="Nach Punkten sortieren" data-title-per-game="Nach Punkten pro Spiel sortieren" title="Nach Punkten sortieren">Punkte</th>
        <th class="bf-sortable" data-sort="kills" data-label="Kills" data-label-per-game="Ø Kills" data-title="Nach Kills sortieren" data-title-per-game="Nach Kills pro Spiel sortieren" title="Nach Kills sortieren">Kills</th>
        <th class="bf-sortable" data-sort="deaths" data-label="Deaths" data-label-per-game="Ø Deaths" data-title="Nach Deaths sortieren" data-title-per-game="Nach Deaths pro Spiel sortieren" title="Nach Deaths sortieren">Deaths</th>
        <th class="bf-sortable" data-sort="win_loss" title="Nach Sieg-/Niederlage-Verhältnis sortieren">W/L</th>
    </tr>
    </thead>

    <tbody id="bf-stats-body">
    <tr>
        <td colspan="7">Lade Statistik…</td>
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

  <?php print_sakristei(); ?>
  
</main>

<?php
print_footer();
?>

</div> <!-- main container #tk -->







</body>

</html>
