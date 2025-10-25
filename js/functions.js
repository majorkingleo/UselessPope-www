
$(document).ready(function() {
   
        if( $("#uptime").length )
        {
            setInterval( function() {
            $.get( "lib/get_uptime.php", function( data ) {            
                    var arr = $.parseJSON( data );
                    for( var key in arr ) {
                        var value = arr[key];
                        // console.log(key, value);
                        $('#' + key).text( value );
                    }
                    
                    });
            }(), 1000 );


            setInterval( function() {
            $.get( "lib/get_sound_files.php", function( data ) {            
                    var arr = $.parseJSON( data );
                    var idx = 0;
                    for( var key in arr ) {
                        var value = arr[key];
                        // console.log(key, value);
                        $('#' + key).children().text( value );
                        $('#' + key).css("display", "block");
                        $('#' + key).click( function(){ 
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

                    for( var i = idx; i < 100; ++i ) {                        
                        $('#sound_button_' + i).css("display", "none");
                    }
                    
                    });
            }(), 1000 );
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
