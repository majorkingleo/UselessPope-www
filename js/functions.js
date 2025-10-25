
$(document).ready(function() {
   
        setInterval( function() {
        $.get( "lib/get_uptime.php", function( data ) {
            
            var arr = $.parseJSON( data );
            for( var key in arr ) {
                var value = arr[key];
                // console.log(key, value);
                $('#' + key).text( value );
            }
            
            });
    }, 1000 );

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
