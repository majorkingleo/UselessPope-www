<?php

define('USERPROP_FORENAME', "forename" );
define('USERPROP_SURENAME', "surename" );
define('USERPROP_TITLE', "title" );

$ALL_USERPROPS = array(                
    USERPROP_FORENAME => "",
    USERPROP_SURENAME => "",
    USERPROP_TITLE => ""
);

$ALL_USERPROPS_DESC = array(                
    USERPROP_FORENAME => "Vorname",
    USERPROP_SURENAME => "Nachname",
    USERPROP_TITLE => "Titel"
);

function get_user_props( $user_idx ) {
    global $ALL_USERPROPS, $mysqli;
    
    $res = $mysqli->query("select * from USERPROPERTIES where user_idx = '" . addslashes($user_idx) . "'");
    
    if( !$res ) {
        error_log( "get_user_rights: " . $mysqli->error);
        return null;
    }
    
    $ret = $ALL_USERPROPS;
    
    while( $row = $res->fetch_assoc() ) {
        $ret[ $row["name"] ]= $row["value"];
    }
    
    return $ret;
}

function insert_or_update_userprops( $user, $name, $value )
{
    global $mysqli;    

      $props = get_user_props( $user );
      
      if( $props[$name] === $value ) {
          return true;          
      }
      
      $res = $mysqli->query( "select value from USERPROPERTIES where "
              . " user_idx = '" . addslashes($user) . "'"
              . " and name = '" . addslashes($name) . "'");
      
      if( !$res ) {
          error_log( "insert_or_update_userprops 1:" . $mysqli->error);
          return false;
      }
      
      while( $row = $res->fetch_assoc() ) {
          if( $row["value"] === $value ) {
              return true;
          }
          
          $res = $mysqli->query( "update USERPROPERTIES set "
                  . "value='" . addslashes($value) . "' "
                  . " where "
                  . " user_idx='" . addslashes($user) . "' and "
                  . " name='" . addslashes($name) . "'");
          
          if( !$res ) {
            error_log( "insert_or_update_userprops 2:" . $mysql->error);
            return false;             
          }
          
          return true;
      }
      
      $res = $mysqli->query( "insert into USERPROPERTIES ( user_idx, name, value ) " 
              . " VALUES ( " 
              . "'" . addslashes($user) . "',"
              . "'" . addslashes($name) . "',"
              . "'" . addslashes($value) . "')");

      if( !$res ) {
            error_log( "insert_or_update_userprops 3:" . $mysqli->error);
            return false;             
      }      
      
      return true;
}

/**
 * Diese Funktion speichert alle Benutzereinstellungen 
 */
function save_update_or_read_user_properties()
{
    global $mysqli;

    $updated = array();
    
    $res = $mysqli->query( "select * from USERPROPERTIES where user_idx=" . $_SESSION["USER"]["idx"] );       
    
    if (!$res) {
        error_log(" save_update_or_read_user_properties 1 " . $mysqli->error);
        return false;
    }    
    
    while( $row = $res->fetch_assoc() ) {                
        
        $found = false;
        
	if( isset( $_SESSION["USERPROPERTIES"] ) ) {
        foreach( $_SESSION["USERPROPERTIES"] as $key => $value )
        {
            // error_log( "found: " . $key . " value is " . $value);
            
            if( $row["name"] === $key ) {
                
                // error_log( "xx found: " . $key . " == " . $row["name"]  );
                
                if( $row["value"] !== $value ) {
                    error_log( "updating: " . $key . " value is " . $value . " old value was: " .  $row["value"] );
                    // update
                    $res2 = $mysqli->query( "update USERPROPERTIES set value='" . addslashes($value) . "'" 
                                     . " where user_idx = " . $_SESSION["USER"]["idx"] 
                                     . " and name = '" . addslashes($key) . "'");                    
                    
                    if( !$res2 ) {
                        error_log(" save_update_or_read_user_properties 2 " . $mysqli->error());
                        return false;
                    }
                }
                $found = true;
                // error_log( "yy found: " . $key . " == " . $row["name"]  );
                array_push($updated, $key);
                break;
            }
        }
	} // if isset
        
        if( !$found ) {
            /*
            // insert
            mysql_query( "insert into USERPROPERTIES ( user_idx, name, value ) VALUES " 
                        . "( " .  $_SESSION["USER"]["idx"] . ","
                        . "'" . addslashes($key) . "'" 
                        . "'" . addslashes($value) . "'" ) or die( mysql_error() );
            */
            $_SESSION["USERPROPERTIES"][$row["name"]] = $row["value"];
        }
    }
    
    foreach( $_SESSION["USERPROPERTIES"] as $key => $value  ) {
        
        $found = false;
        
        for( $i = 0; $i < sizeof($updated); $i++ ) {
            if( $updated[$i] === $key ) {  
                // error_log( "zzz found: " . $key . " == " . $updated[$i]   );
                $found = true;
                break;
            }
        }
        
        // error_log( "found: " . $key . " value is " . $value . " found: " . $found);
        
        if( !$found ) {
            $res3 = $mysqli->query( "insert into USERPROPERTIES ( user_idx, name, value ) VALUES " 
                        . "( " .  $_SESSION["USER"]["idx"] . ","
                        . "'" . addslashes($key) . "'," 
                        . "'" . addslashes($value) . "')" );
            
            if( !$res3 ) {
                 error_log(" save_update_or_read_user_properties 3 " . $mysql->error);
                 return false;
            }            
        }
    }
    
    return true;
}

function get_user_name_array($user_idx) {
    
    global $ALL_USERPROPS, $mysqli;
    
    static $all_users = array();    
    
    if( sizeof($all_users) == 0  ) {
        $res = $mysqli->query( "select * from USERPROPERTIES where "
                . " name = '" . USERPROP_FORENAME . "' or "
                . " name = '" . USERPROP_SURENAME . "' or "
                . " name = '" . USERPROP_TITLE . "'" );
        
        if( !$res ) {
            error_log( "get_user_name_array: " . $mysqli->error);
            return false;
        }                
        
        while( $row = $res->fetch_assoc()) {
            
            // error_log( "row: " . $row["name"]);
            
            array_push( $all_users, $row);
        }
    }
    
    $user_array = $ALL_USERPROPS;
    
    foreach( $all_users as $key => $value ) {
        if( $value["user_idx"] == $user_idx ) {
            $user_array[$value["name"]]=$value["value"];
        }
    }
    
    return $user_array;
}

function fetch_users( $idx = -1 )
{
    global $mysqli;

    if( $idx >= 0 ) {
        $res = mysql_query("select * from USERS where idx=" . addslashes($idx) );           
    } else {
        $res = mysql_query("select * from USERS " );           
    }
    
    $users = array();
    
    while( $row = mysql_fetch_assoc($res)) {
        
        // print_r(get_user_name_array($row["idx"]) );
        
        $row = array_merge( $row, get_user_name_array($row["idx"]) );
        
        array_push($users, $row);                
    }
    
    return $users;
}


?>
