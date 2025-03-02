$(document).ready(function(){

    $(".button").button();
    
    $(".button").on( "click", function( obj ) {
        //console.log( $(obj.target).attr('id') );
        var button_id = $(obj.target).attr('id');
        $.get( "lib/button_pressed.php?id=" + button_id );
    });
    
    //$('#l3').css( 'background-color', value );
    
    setInterval( function() {
        $.get( "lib/get_led_status.php", function( data ) {
            
            var arr = $.parseJSON( data );
            for( var key in arr ) {
                var value = arr[key];
                // console.log(key, value);
                $('#' + key).css( 'background-color', value );
            }
            
            });
    }, 1000 );

});
