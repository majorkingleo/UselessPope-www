<?php
include_once( 'lib/global.php' );

if( !logged_in() ) {
    header( "Location: common_login/login.php" );
    exit(0);
}

connect_db();

print_header();

function bf_log_team_label( $team )
{
    switch( intval( $team ) ) {
        case 1: return "Achse";
        case 2: return "Alliierte";
        default: return "Unbekannt";
    }
}

function bf_log_winner_label( $team )
{
    if( intval( $team ) === 0 ) {
        return "Unentschieden";
    }

    return bf_log_team_label( $team );
}

function bf_log_date( $datetime )
{
    $ts = strtotime( $datetime );

    return $ts ? date( "Y-m-d H:i", $ts ) : $datetime;
}
?>

<body lang="de" id="log">

<style>
/* Details rows in the iframe game log. The outer table reuses table.log. */
body#log details summary {
    cursor: pointer;
    font-weight: bold;
}

body#log details[open] summary {
    margin-bottom: 4px;
}

body#log .bf-game-meta {
    font-size: 12px;
    line-height: 16px;
    margin: 4px 0 6px;
}

body#log table.bf-game-stats {
    border-collapse: collapse;
    margin: 4px 0 8px;
    font-size: 13px;
}

body#log table.bf-game-stats th,
body#log table.bf-game-stats td {
    border: none;
    padding: 2px 18px 3px 0;
    text-align: left;
    vertical-align: top;
}

body#log table.bf-game-stats th {
    font-size: 11px;
    font-weight: bold;
    opacity: 0.7;
}

body#log table.bf-game-stats th:nth-child(n+3),
body#log table.bf-game-stats td:nth-child(n+3) {
    text-align: right;
}

/* table.log styles target every descendant td, so reset the nested table. */
body#log table.log table.bf-game-stats td:nth-child(1) {
    font-size: 13px;
    white-space: normal;
    padding-top: 2px;
}

body#log table.log table.bf-game-stats td:nth-child(2) {
    padding-left: 0;
    padding-right: 18px;
}
</style>

<table class="log">

<?php
$sql = "select g.idx as game_id, g.mapname, g.gametype, g.started_at, g.ended_at, " .
       "       g.winning_team, g.tickets1, g.tickets2, g.victory_type, " .
       "       p.player_name, p.team, p.slot, p.score, p.kills, p.deaths, p.ping " .
       "from BF1942_GAME g " .
       "left join P_BF1942_PLAYER p on p.game_id = g.idx " .
       "where g.idle = 0 " .
       "order by g.ended_at desc, g.idx desc, " .
       "         p.score desc, p.kills desc, p.deaths asc, p.slot asc";

$res = $mysqli->query( $sql );

if( !$res ) {
    error_log( "bf_log.php: 1 " . $mysqli->error );
    printf( "<tr><td colspan=\"2\">Fehler beim Laden der Spiele.</td></tr>\n" );
} else {
    $games = array();

    while( $row = $res->fetch_assoc() ) {
        $game_id = intval( $row["game_id"] );

        if( !isset( $games[ $game_id ] ) ) {
            $games[ $game_id ] = array(
                "game_id"      => $game_id,
                "mapname"      => $row["mapname"],
                "gametype"     => $row["gametype"],
                "started_at"   => $row["started_at"],
                "ended_at"     => $row["ended_at"],
                "winning_team" => intval( $row["winning_team"] ),
                "tickets1"     => intval( $row["tickets1"] ),
                "tickets2"     => intval( $row["tickets2"] ),
                "victory_type" => $row["victory_type"],
                "players"      => array(),
            );
        }

        if( $row["player_name"] !== null ) {
            $games[ $game_id ]["players"][] = array(
                "name"   => $row["player_name"],
                "team"   => intval( $row["team"] ),
                "slot"   => intval( $row["slot"] ),
                "score"  => intval( $row["score"] ),
                "kills"  => intval( $row["kills"] ),
                "deaths" => intval( $row["deaths"] ),
                "ping"   => intval( $row["ping"] ),
            );
        }
    }

    if( count( $games ) === 0 ) {
        printf( "<tr><td colspan=\"2\">Keine Spiele vorhanden.</td></tr>\n" );
    }

    foreach( $games as $game ) {
        $map = $game["mapname"] ? $game["mapname"] : "Unbekannte Karte";

        printf( "<tr>\n" );
        printf( "  <td>%s</td>\n", htmlspecialchars( bf_log_date( $game["started_at"] ), ENT_QUOTES, 'UTF-8' ) );
        printf( "  <td><details>\n" );
        printf( "    <summary>%s</summary>\n", htmlspecialchars( $map, ENT_QUOTES, 'UTF-8' ) );
        printf( "    <p class=\"bf-game-meta\">Sieger: %s &middot; Tickets %d : %d &middot; %s</p>\n",
                htmlspecialchars( bf_log_winner_label( $game["winning_team"] ), ENT_QUOTES, 'UTF-8' ),
                $game["tickets1"], $game["tickets2"],
                htmlspecialchars( $game["gametype"], ENT_QUOTES, 'UTF-8' ) );

        printf( "    <table class=\"bf-game-stats\">\n" );
        printf( "      <thead><tr><th>Spieler</th><th>Team</th><th>Punkte</th><th>Kills</th><th>Deaths</th></tr></thead>\n" );
        printf( "      <tbody>\n" );

        if( count( $game["players"] ) === 0 ) {
            printf( "        <tr><td colspan=\"5\">Keine Spielerdaten.</td></tr>\n" );
        }

        foreach( $game["players"] as $player ) {
            printf( "        <tr><td>%s</td><td>%s</td><td>%d</td><td>%d</td><td>%d</td></tr>\n",
                    htmlspecialchars( $player["name"], ENT_QUOTES, 'UTF-8' ),
                    htmlspecialchars( bf_log_team_label( $player["team"] ), ENT_QUOTES, 'UTF-8' ),
                    $player["score"], $player["kills"], $player["deaths"] );
        }

        printf( "      </tbody>\n" );
        printf( "    </table>\n" );
        printf( "  </details></td>\n" );
        printf( "</tr>\n" );
    }
}
?>

</table>

</body>

</html>
