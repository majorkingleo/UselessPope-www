
var banner_dirty = false;
var banner_synced_value = null;

function update_banner_field( value )
{
    var input = $("#banner");
    if( !input.length )
        return;

    if( banner_synced_value === null )
        banner_synced_value = input.val();

    // Don't overwrite while the user is editing or has unsaved changes.
    if( document.activeElement === input[0] || banner_dirty )
        return;

    if( banner_synced_value !== value ) {
        input.val( value );
        banner_synced_value = value;
    }
}

function parse_uptime_data( data ) 
{            
    var arr = $.parseJSON( data );
    for( var key in arr ) {
        var value = arr[key];
        // console.log(key, value);
        if( key === "scroll_text" ) {
            update_banner_field( value );
            continue;
        }
        $('#' + key).text( value );
    }   
}

function fetch_uptime_data()
{
    $.get( "lib/get_uptime.php", parse_uptime_data );
}


function parse_play_data( data ) 
{            
    var arr = $.parseJSON( data );
    var idx = 0;
    for( var key in arr ) {
        var value = arr[key];
        // console.log(key, value);
        $('#' + key).children().text( value );
        $('#' + key).css("display", "block");
        $('#' + key).unbind("click");
        $('#' + key).bind( "click", function(){ 
            var file_name = $(this).children().text();
            $.ajax({
                type: "POST",
                url: "lib/play_file.php",
                data: "file=" + encodeURIComponent(file_name)
            });
        });

        // get XX from sound_button_XX
        const components = key.split("_");                        
        if( components.length == 3 ) {                            
            idx = parseInt(components[2]);
        }
    }

    for( var i = idx + 1; i < 100; ++i ) {                        
        $('#sound_button_' + i).css("display", "none");
    }
}

function fetch_play_data( data )
{
    $.get( "lib/get_sound_files.php", parse_play_data );
}


/*
 * Battlefield 1942 page (bf.php). Same polling pattern as the Papst dashboard:
 * lib/get_bf_stats.php returns a flat key -> element id map plus two arrays
 * that are rendered as table rows / team columns.
 */

var bf_team_mode = "live";

function render_bf_players( players )
{
    var tbody = $( "#bf-stats-body" );

    if( !tbody.length )
        return;

    tbody.empty();

    if( !players || !players.length ) {
        tbody.append(
            $( "<tr>" ).append(
                $( "<td>" ).attr( "colspan", 5 ).text( "Keine Spieler online." )
            )
        );
        return;
    }

    var medals = [ "🥇", "🥈", "🥉" ];

    $.each( players, function( idx, player ) {
        var tr = $( "<tr>" );

        tr.append( $( "<td>" ).text( medals[ idx ] || "" ) );
        tr.append( $( "<td>" ).text( player.name ) );
        tr.append( $( "<td>" ).text( player.score ) );
        tr.append( $( "<td>" ).text( player.kills ) );
        tr.append( $( "<td>" ).text( player.deaths ) );

        tbody.append( tr );
    } );
}

function render_bf_teams( teams )
{
    var container = $( "#bf-teams" );

    if( !container.length )
        return;

    container.empty();

    if( !teams || !teams.length ) {
        container.append( $( "<div>" ).append( $( "<p>" ).text( "Keine Teams verfügbar." ) ) );
        return;
    }

    $.each( teams, function( idx, team ) {
        var div = $( "<div>" );

        div.append( $( "<p>" ).text( team.title ) );

        var names = $( "<p>" );

        if( !team.players || !team.players.length ) {
            names.text( "–" );
        } else {
            $.each( team.players, function( pidx, player ) {
                if( pidx > 0 )
                    names.append( $( "<br>" ) );

                names.append( document.createTextNode( player.name ) );
            } );
        }

        div.append( names );
        container.append( div );
    } );
}

function parse_bf_data( data )
{
    var arr = $.parseJSON( data );

    for( var key in arr ) {
        var value = arr[ key ];

        if( key === "bf_players" ) {
            render_bf_players( value );
            continue;
        }

        if( key === "bf_teams" ) {
            render_bf_teams( value );
            continue;
        }

        $( '#' + key ).text( value );
    }
}

function fetch_bf_data()
{
    var url = "lib/get_bf_stats.php";

    if( bf_team_mode === "suggest" )
        url += "?suggest=1";

    $.get( url, parse_bf_data );
}

$(document).ready(function() {

        if( $("#bf-stats").length )
        {
            setInterval( fetch_bf_data, 5000 );
            fetch_bf_data();

            $("#bf-suggest").click( function() {
                bf_team_mode = ( bf_team_mode === "live" ) ? "suggest" : "live";

                $(this).find("span").text(
                    bf_team_mode === "suggest" ? "Aktuelle Teams anzeigen" : "Team vorschlagen"
                );

                fetch_bf_data();
            } );
        }
   
        if( $("#uptime").length )
        {
            setInterval( fetch_uptime_data, 1000 );
            fetch_uptime_data();

            setInterval( fetch_play_data, 1000 );
            fetch_play_data();

            $("#inclight").click( function() {
                $.ajax({
                    type: "GET",
                    url: "lib/change_brightness.php",
                    data: "incbrightness=0.02"
                });
            } );

            $("#declight").click( function() {
                $.ajax({
                    type: "GET",
                    url: "lib/change_brightness.php",
                    data: "incbrightness=-0.02"
                });
            } );            

            $("#playmotor").click( function() {
                $.ajax({
                    type: "POST",
                    url: "lib/play_file.php",
                    data: "file=" + encodeURIComponent("/home/papst/audio_motor/Humvee_Idle.mp3")
                });
            } );
            
            $("#led").click( function() {
                $.ajax({
                    type: "POST",
                    url: "lib/change_animation.php"
                });
            } );  

            $("#fog").click( function() {
                $.ajax({
                    type: "POST",
                    url: "lib/change_fog.php"
                });
            } );     
            
            $("#playhallelujah").click( function() {
                $.ajax({
                    type: "POST",
                    url: "lib/play_file.php",
                    data: "file=" + encodeURIComponent("/home/papst/audio_music/Worms - HOLYGRENADE.WAV")
                });
            } );

            function save_banner() {
                var value = $("#banner").val();

                $.ajax({
                    type: "POST",
                    url: "lib/save_scroll_text.php",
                    data: "scroll_text=" + encodeURIComponent(value),
                    success: function() {
                        banner_dirty = false;
                        banner_synced_value = value;
                    }
                });
            }

            $("#save_banner").click( save_banner );

            $("#banner").keypress( function(event) {
                if( event.which === 13 ) {   // Enter key
                    event.preventDefault();
                    save_banner();
                }
            } );

            // Remember the server value and flag unsaved edits so the polling
            // sync doesn't overwrite what the user is typing.
            if( $("#banner").length ) {
                banner_synced_value = $("#banner").val();
                $("#banner").on("input", function() {
                    banner_dirty = true;
                });
            }
        }
});

/**
 * checks if the variable exists
 */
function isset( val ) {
    if( typeof val != 'undefined' )
        return true;
    return false;
}


function can_debug()
{
    if( typeof console == "undefined" )
        return false;
    
    return true;
}

function formatdate(timestamp) {
    var d = new Date(timestamp);
    
        return d.getDate() + "." + (d.getMonth() + 1) + "." + d.getFullYear();
}    

/**
 * accept date values in format 2012-05-30
 */
function formatdate_from_iso(date_str) {
    var parts = date_str.split("-");
    
    return parts[2] + "." + parts[1] + "." + parts[0];
}
