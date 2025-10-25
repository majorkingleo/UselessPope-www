<?php

define( 'USERRIGHT_EDIT_OTHER_USERS',"EDIT_OTHER_USERS");
define( 'USERRIGHT_DEL_OTHER_USERS' , "DEL_OTHER_USERS");
define( 'USERRIGHT_CREATE_OTHER_USERS' , "CREATE_OTHER_USERS");
define( 'USERRIGHT_DO_BACKUP' , "DO_BACKUP");

$ALL_USERRIGHTS = array( 
    USERRIGHT_EDIT_OTHER_USERS => 0,
    USERRIGHT_DEL_OTHER_USERS => 0,
    USERRIGHT_CREATE_OTHER_USERS => 0,
    USERRIGHT_DO_BACKUP => 0
);

$ALL_USERRIGHTS_DESC = array( 
    USERRIGHT_CREATE_OTHER_USERS => "Neue BenutzerInnen anlegen",
    USERRIGHT_EDIT_OTHER_USERS => "Andere BenutzerInnen bearbeiten",
    USERRIGHT_DEL_OTHER_USERS => "Andere BenutzerInnen löschen",    
    USERRIGHT_DO_BACKUP => "Backup"
);

function get_user_rights( $user_idx ) {
    global $ALL_USERRIGHTS, $mysqli;
    
    $res = $mysqli->query("select * from USERRIGHTS where user_idx = '" . addslashes($user_idx) . "'");
    
    if( !$res ) {
        error_log( "get_user_rights: " . $mysqli->error);
        return null;
    }
    
    $ret = $ALL_USERRIGHTS;
    
    while( $row = $res->fetch_assoc() ) {
        $ret[ $row["name"] ]= $row["value"];
    }
    
    return $ret;
}

function insert_or_update_userrights( $user, $name, $value )
{
    global $mysqli;  

        $rights = get_user_rights( $user );
      
      if( $rights[$name] === $value ) {
          return true;          
      }
      
      $res = $mysqli->query( "select value from USERRIGHTS where "
              . " user_idx = '" . addslashes($user) . "'"
              . " and name = '" . addslashes($name) . "'");
      
      if( !$res ) {
          error_log( "insert_or_update_userrights 1:" . $mysqli->error);
          return false;
      }
      
      while( $row = $res->fetch_assoc() ) {
          if( $row["value"] === $value ) {
              return true;
          }
          
          $res = $mysqli->query( "update USERRIGHTS set "
                  . "value='" . addslashes($value) . "' "
                  . " where "
                  . " user_idx='" . addslashes($user) . "' and "
                  . " name='" . addslashes($name) . "'");
          
          if( !$res ) {
            error_log( "insert_or_update_userrights 2:" . $mysqli->error);
            return false;             
          }
          
          return true;
      }
      
      $res = $mysqli->query( "insert into USERRIGHTS ( user_idx, name, value ) " 
              . " VALUES ( " 
              . "'" . addslashes($user) . "',"
              . "'" . addslashes($name) . "',"
              . "'" . addslashes($value) . "')");

      if( !$res ) {
            error_log( "insert_or_update_userrights 3:" . $mysql->error);
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
