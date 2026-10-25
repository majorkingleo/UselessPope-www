<?php

include_once( "global.php" );

if( !logged_in() ) {
    error_log( "get_bf_stats.php: not logged in" );
    exit;
}

connect_db();

/*
 * Returns the current Battlefield 1942 state for bf.php.
 *
 * Shape (same key -> element id convention as lib/get_uptime.php):
 *   bf_map, bf_hoster, bf_numplayers, bf_tickets, bf_round_time, bf_online
 *   bf_stats_games: number of most recent games the statistics table covers
 *   bf_players: totals and per-game averages per player over the last
 *               bf_stats_games games
 *   bf_teams:   array of { title, players } for #bf-teams
 *
 * With ?suggest=1 the teams are a balanced proposal derived from the live
 * roster and the archived per-player history, instead of the current teams.
 */

function bf_team_title( $team )
{
    switch( intval( $team ) ) {
        case 1: return "Achse";
        case 2: return "Alliierte";
        default: return "Unbekannt";
    }
}

function bf_format_round_time( $seconds )
{
    $seconds = max( 0, intval( $seconds ) );

    return sprintf( "%d:%02d", intval( $seconds / 60 ), $seconds % 60 );
}

function bf_player_strength( $player, $history )
{
    // Live values are what we know about the current round; the archive
    // contributes the long-term performance used to balance the teams.
    $strength = max( 0, intval( $player["score"] ) ) + max( 0, intval( $player["kills"] ) ) * 2;

    if( isset( $history[ $player["name"] ] ) ) {
        $h = $history[ $player["name"] ];

        $strength += intval( $h["score"] )
                   + intval( $h["kills"] ) * 2
                   - intval( $h["deaths"] )
                   + intval( $h["wins"] ) * 10;
    }

    return $strength;
}

$suggest = isset( $_GET["suggest"] ) && $_GET["suggest"];

$out = array(
    "bf_map"        => "–",
    "bf_hoster"     => "–",
    "bf_numplayers" => "–",
    "bf_tickets"    => "–",
    "bf_round_time" => "–",
    "bf_online"     => "offline",
    "bf_stats_games" => 0,
    "bf_players"    => array(),
    "bf_teams"      => array(),
);

// One row per server: prefer the most recently refreshed status. The query
// response has no beacon GUID, so discovery and query data are joined by IP.
$sql = "select st.*, srv.name as server_name, " .
       "       timestampdiff( second, st.last_seen, now() ) as last_seen_age " .
       "from BF1942_STATUS st " .
       "left join BF1942_SERVER srv on srv.idx = ( " .
       "    select s2.idx from BF1942_SERVER s2 " .
       "    where s2.ip_address = st.server_ip " .
       "    order by s2.last_seen desc limit 1 " .
       ") " .
       "order by st.last_seen desc limit 1";

$res = $mysqli->query( $sql );

if( !$res ) {
    error_log( "get_bf_stats.php: 1 " . $mysqli->error );
    printf( "%s", json_encode( $out ) );
    exit;
}

$status = $res->fetch_assoc();

$history = array();

// Complete history per player over non-idle games, used to balance the
// suggested teams. Games not archived in BF1942_GAME yet (a running round)
// are treated as not idle.
$sql = "select p.player_name, count(*) as games, sum( p.score ) as score, " .
       "       sum( p.kills ) as kills, sum( p.deaths ) as deaths, " .
       "       sum( case when p.winning_team > 0 and p.team = p.winning_team then 1 else 0 end ) as wins " .
       "from P_BF1942_PLAYER p " .
       "left join BF1942_GAME g on g.idx = p.game_id " .
       "where coalesce( g.idle, 0 ) = 0 " .
       "group by p.player_name";

$res = $mysqli->query( $sql );

if( !$res ) {
    error_log( "get_bf_stats.php: 3 " . $mysqli->error );
} else {
    while( $row = $res->fetch_assoc() ) {
        $history[ $row["player_name"] ] = $row;
    }
}

// The statistics table covers the most recent games only, idle games are
// skipped. Use the archived games themselves as the window, so an unfinished
// round still shows up.
$stats_game_limit = 10;
$stats_game_ids = array();

$sql = sprintf( "select distinct p.game_id from P_BF1942_PLAYER p " .
                "left join BF1942_GAME g on g.idx = p.game_id " .
                "where coalesce( g.idle, 0 ) = 0 " .
                "order by p.game_id desc limit %d", $stats_game_limit );

$res = $mysqli->query( $sql );

if( !$res ) {
    error_log( "get_bf_stats.php: 5 " . $mysqli->error );
} else {
    while( $row = $res->fetch_assoc() ) {
        $stats_game_ids[] = intval( $row["game_id"] );
    }
}

$out["bf_stats_games"] = count( $stats_game_ids );

$stats_players = array();

if( $stats_game_ids ) {
    $sql = "select player_name, count(*) as games, sum( score ) as score, " .
           "       sum( kills ) as kills, sum( deaths ) as deaths, " .
           "       sum( case when winning_team > 0 and team = winning_team then 1 else 0 end ) as wins, " .
           "       sum( case when winning_team > 0 and team <> winning_team then 1 else 0 end ) as losses " .
           "from P_BF1942_PLAYER " .
           "where game_id in ( " . implode( ", ", $stats_game_ids ) . " ) " .
           "group by player_name";

    $res = $mysqli->query( $sql );

    if( !$res ) {
        error_log( "get_bf_stats.php: 6 " . $mysqli->error );
    } else {
        while( $row = $res->fetch_assoc() ) {
            $games = max( 1, intval( $row["games"] ) );
            $score = intval( $row["score"] );
            $kills = intval( $row["kills"] );
            $deaths = intval( $row["deaths"] );

            $stats_players[] = array(
                "name"   => $row["player_name"],
                "games"  => intval( $row["games"] ),
                "score"  => $score,
                "kills"  => $kills,
                "deaths" => $deaths,
                "score_per_game"  => $score / $games,
                "kills_per_game"  => $kills / $games,
                "deaths_per_game" => $deaths / $games,
                "wins"   => intval( $row["wins"] ),
                "losses" => intval( $row["losses"] ),
            );
        }
    }
}

// The statistics table is a leaderboard over the recent games.
usort( $stats_players, function( $a, $b ) {
    if( $a["score"] !== $b["score"] ) {
        return ( $a["score"] > $b["score"] ) ? -1 : 1;
    }

    if( $a["kills"] !== $b["kills"] ) {
        return ( $a["kills"] > $b["kills"] ) ? -1 : 1;
    }

    if( $a["deaths"] !== $b["deaths"] ) {
        return ( $a["deaths"] < $b["deaths"] ) ? -1 : 1;
    }

    return strcasecmp( $a["name"], $b["name"] );
} );

$out["bf_players"] = $stats_players;

$live_players = array();
$online = false;
$winning_team = 0;

if( $status ) {
    $online = intval( $status["last_seen_age"] ) <= 90;
    $server_ip = $status["server_ip"];

    $out["bf_map"]        = $status["mapname"] ? $status["mapname"] : "–";
    $out["bf_hoster"]     = $status["server_name"] ? $status["server_name"] : ( $status["hostname"] ? $status["hostname"] : "–" );
    $out["bf_numplayers"] = sprintf( "%d / %d", intval( $status["numplayers"] ), intval( $status["maxplayers"] ) );
    $out["bf_tickets"]    = sprintf( "%d : %d", intval( $status["tickets1"] ), intval( $status["tickets2"] ) );
    $out["bf_round_time"] = bf_format_round_time( $status["round_time_remain"] );
    $out["bf_online"]     = $online ? "online" : "offline";

    $tickets1 = intval( $status["tickets1"] );
    $tickets2 = intval( $status["tickets2"] );

    if( $tickets1 > $tickets2 ) {
        $winning_team = 1;
    } else if( $tickets2 > $tickets1 ) {
        $winning_team = 2;
    }

    if( $online ) {
        // BF1942_PLAYER is a live snapshot. Rows of players who already
        // left keep their old last_seen, so only show the fresh ones.
        $sql = sprintf( "select * from BF1942_PLAYER " .
                        "where server_ip = '%s' and last_seen >= now() - interval 90 second " .
                        "order by score desc, kills desc, deaths asc, slot asc",
                        $mysqli->real_escape_string( $server_ip ) );

        $res = $mysqli->query( $sql );

        if( !$res ) {
            error_log( "get_bf_stats.php: 4 " . $mysqli->error );
        } else {
            while( $row = $res->fetch_assoc() ) {
                $live_players[] = array(
                    "name"   => $row["player_name"],
                    "slot"   => intval( $row["slot"] ),
                    "score"  => intval( $row["score"] ),
                    "kills"  => intval( $row["kills"] ),
                    "deaths" => intval( $row["deaths"] ),
                    "ping"   => intval( $row["ping"] ),
                    "team"   => intval( $row["team"] ),
                );
            }
        }
    }
}

/*
 * Current teams: group the live roster by its team id.
 * Team 1 is the Axis, team 2 the Allies in BF1942. The template used to show
 * the Allies on the left, so keep that order.
 */
$teams = array( 0 => array(), 1 => array(), 2 => array() );

foreach( $live_players as $player ) {
    $team = intval( $player["team"] );

    if( !isset( $teams[ $team ] ) ) {
        $team = 0;
    }

    $teams[ $team ][] = $player;
}

if( $suggest && count( $live_players ) > 0 ) {
    // Snake draft: strongest player to team 1, next to team 2, then reverse
    // the direction every pair, so both teams get a comparable total strength.
    usort( $live_players, function( $a, $b ) use ( $history ) {
        $sa = bf_player_strength( $a, $history );
        $sb = bf_player_strength( $b, $history );

        if( $sa === $sb ) {
            return strcasecmp( $a["name"], $b["name"] );
        }

        return ( $sa > $sb ) ? -1 : 1;
    } );

    $teams = array( 0 => array(), 1 => array(), 2 => array() );

    foreach( $live_players as $i => $player ) {
        $round = intval( $i / 2 );
        $pair  = $i % 2;

        if( $round % 2 === 0 ) {
            $team = ( $pair === 0 ) ? 1 : 2;
        } else {
            $team = ( $pair === 0 ) ? 2 : 1;
        }

        $teams[ $team ][] = $player;
    }
}

// Show the currently winning team first. On a tie (or no status) keep the
// previous order (Allies left, Axis right). Inside a team, score decides.
$team_order = array( 2, 1 );

if( $winning_team === 1 ) {
    $team_order = array( 1, 2 );
}

if( count( $teams[0] ) > 0 ) {
    $team_order[] = 0;
}

$team_output = array();

foreach( $team_order as $team ) {
    usort( $teams[ $team ], function( $a, $b ) {
        if( $a["score"] !== $b["score"] ) {
            return ( $a["score"] > $b["score"] ) ? -1 : 1;
        }

        if( $a["kills"] !== $b["kills"] ) {
            return ( $a["kills"] > $b["kills"] ) ? -1 : 1;
        }

        if( $a["deaths"] !== $b["deaths"] ) {
            return ( $a["deaths"] < $b["deaths"] ) ? -1 : 1;
        }

        return strcasecmp( $a["name"], $b["name"] );
    } );

    $team_output[] = array(
        "team"    => $team,
        "title"   => bf_team_title( $team ),
        "players" => $teams[ $team ],
    );
}

// Always render the two main team columns, even when nobody is on the server.
if( count( $live_players ) === 0 ) {
    $team_output = array(
        array( "team" => 2, "title" => bf_team_title( 2 ), "players" => array() ),
        array( "team" => 1, "title" => bf_team_title( 1 ), "players" => array() ),
    );
}

$out["bf_teams"] = $team_output;

printf( "%s", json_encode( $out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );

?>
