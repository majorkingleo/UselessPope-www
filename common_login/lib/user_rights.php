<?php

define( 'USERRIGHT_EDIT_OTHER_USERS',"EDIT_OTHER_USERS");
define( 'USERRIGHT_DEL_OTHER_USERS' , "DEL_OTHER_USERS");
define( 'USERRIGHT_CREATE_OTHER_USERS' , "CREATE_OTHER_USERS");
define( 'USERRIGHT_CREATE_CONTACTS' , "CREATE_CONTACTS");
define( 'USERRIGHT_EDIT_ROOMS' , "EDIT_ROOMS");
define( 'USERRIGHT_EDIT_APPOINTMENTS' , "EDIT_APPOINTMENTS");
define( 'USERRIGHT_EDIT_COURSE' , "EDIT_COURSE");
define( 'USERRIGHT_EDIT_GROUPS' , "EDIT_GROUPS");
define( 'USERRIGHT_SHOW_STATISTICS' , "SHOW_STATISTICS");
define( 'USERRIGHT_DO_BACKUP' , "DO_BACKUP");
define( 'USERRIGHT_EDIT_SEASONS' , "EDIT_SEASONS");

$ALL_USERRIGHTS = array( 
    USERRIGHT_EDIT_OTHER_USERS => 0,
    USERRIGHT_DEL_OTHER_USERS => 0,
    USERRIGHT_CREATE_OTHER_USERS => 0,
    USERRIGHT_CREATE_CONTACTS => 0,
    USERRIGHT_EDIT_ROOMS => 0,
    USERRIGHT_EDIT_APPOINTMENTS => 0,
    USERRIGHT_EDIT_GROUPS => 0,
    USERRIGHT_EDIT_COURSE => 0,
    USERRIGHT_SHOW_STATISTICS => 0,
    USERRIGHT_DO_BACKUP => 0,
    USERRIGHT_EDIT_SEASONS => 0,
);

$ALL_USERRIGHTS_DESC = array( 
    USERRIGHT_CREATE_OTHER_USERS => "Neue BenutzerInnen anlegen",
    USERRIGHT_EDIT_OTHER_USERS => "Andere BenutzerInnen bearbeiten",
    USERRIGHT_DEL_OTHER_USERS => "Andere BenutzerInnen löschen",    
    USERRIGHT_CREATE_CONTACTS => "Hauptkontakte anlegen",
    USERRIGHT_EDIT_ROOMS => "Räume editieren",
    USERRIGHT_EDIT_APPOINTMENTS => "Termine editieren",
    USERRIGHT_EDIT_GROUPS => "Projekte editieren",
    USERRIGHT_EDIT_COURSE => "Kurse editieren",
    USERRIGHT_SHOW_STATISTICS => "Statistiken",
    USERRIGHT_DO_BACKUP => "Backup",
    USERRIGHT_EDIT_SEASONS => "Saison"
);

function get_user_rights( $user_idx ) {
    global $ALL_USERRIGHTS;
    
    $res = mysql_query("select * from USERRIGHTS where user_idx = '" . addslashes($user_idx) . "'");
    
    if( !$res ) {
        error_log( "get_user_rights: " . mysql_error());
        return null;
    }
    
    $ret = $ALL_USERRIGHTS;
    
    while( $row = mysql_fetch_assoc( $res) ) {
        $ret[ $row["name"] ]= $row["value"];
    }
    
    return $ret;
}

function insert_or_update_userrights( $user, $name, $value )
{
      $rights = get_user_rights( $user );
      
      if( $rights[$name] === $value ) {
          return true;          
      }
      
      $res = mysql_query( "select value from USERRIGHTS where "
              . " user_idx = '" . addslashes($user) . "'"
              . " and name = '" . addslashes($name) . "'");
      
      if( !$res ) {
          error_log( "insert_or_update_userrights 1:" . mysql_error());
          return false;
      }
      
      while( $row = mysql_fetch_assoc($res) ) {
          if( $row["value"] === $value ) {
              return true;
          }
          
          $res = mysql_query( "update USERRIGHTS set "
                  . "value='" . addslashes($value) . "' "
                  . " where "
                  . " user_idx='" . addslashes($user) . "' and "
                  . " name='" . addslashes($name) . "'");
          
          if( !$res ) {
            error_log( "insert_or_update_userrights 2:" . mysql_error());
            return false;             
          }
          
          return true;
      }
      
      $res = mysql_query( "insert into USERRIGHTS ( user_idx, name, value ) " 
              . " VALUES ( " 
              . "'" . addslashes($user) . "',"
              . "'" . addslashes($name) . "',"
              . "'" . addslashes($value) . "')");

      if( !$res ) {
            error_log( "insert_or_update_userrights 3:" . mysql_error());
            return false;             
      }      
      
      return true;
}

function insert_userrights_javascript()
{
    global $ALL_USERRIGHTS;
    
    foreach( $ALL_USERRIGHTS as $name => $value ) {
       echo "$name: $(\"#$name\").attr('checked') ? 1 : 0,\n";                                          
    }
}

?>
