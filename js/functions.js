
$(document).ready(function() {
    $(".toolbar-button").button();
    
    $("#tb_my").click(function(){
        window.location= rel + "common_login/admin_myself.php";
    });
    
    $("#tb_admin_users").click(function(){
        window.location= rel + "common_login/admin_users.php";
    });    
    
    $("#tb_logout").click(function(){
        window.location= rel + "common_login/logout.php";
    });     
    
    $("#tb_contacts").click(function(){
        window.location= rel + "contacts.php";
    });         
    
    $("#tb_rooms").click(function(){
        window.location= rel + "rooms.php";
    });       
    
    $("#tb_appointments").click(function(){
        window.location= rel + "appointments.php";
    });           
    
    $("#tb_groups").click(function(){
        window.location= rel + "groups.php";
    });        
    
    $("#tb_course").click(function(){
        window.location= rel + "course.php";
    });   

    $("#tb_seasons").click(function(){
        window.location= rel + "seasons.php";
    });  

    $("#tb_statistics").click(function(){
        window.location= rel + "statistics.php";
    });   
    

    $("#tb_backup").click(function(){
        window.location= rel + "backup.php";
    });     

    $("#toolbar-admin").hide();
    
    $("#tb_admin").click(function(){
        $("#toolbar-admin").show(500);
    });        
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
