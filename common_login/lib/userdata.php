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
    global $ALL_USERPROPS;
    
    $res = mysql_query("select * from USERPROPERTIES where user_idx = '" . addslashes($user_idx) . "'");
    
    if( !$res ) {
        error_log( "get_user_rights: " . mysql_error());
        return null;
    }
    
    $ret = $ALL_USERPROPS;
    
    while( $row = mysql_fetch_assoc( $res) ) {
        $ret[ $row["name"] ]= $row["value"];
    }
    
    return $ret;
}

function insert_or_update_userprops( $user, $name, $value )
{
      $props = get_user_props( $user );
      
      if( $props[$name] === $value ) {
          return true;          
      }
      
      $res = mysql_query( "select value from USERPROPERTIES where "
              . " user_idx = '" . addslashes($user) . "'"
              . " and name = '" . addslashes($name) . "'");
      
      if( !$res ) {
          error_log( "insert_or_update_userprops 1:" . mysql_error());
          return false;
      }
      
      while( $row = mysql_fetch_assoc($res) ) {
          if( $row["value"] === $value ) {
              return true;
          }
          
          $res = mysql_query( "update USERPROPERTIES set "
                  . "value='" . addslashes($value) . "' "
                  . " where "
                  . " user_idx='" . addslashes($user) . "' and "
                  . " name='" . addslashes($name) . "'");
          
          if( !$res ) {
            error_log( "insert_or_update_userprops 2:" . mysql_error());
            return false;             
          }
          
          return true;
      }
      
      $res = mysql_query( "insert into USERPROPERTIES ( user_idx, name, value ) " 
              . " VALUES ( " 
              . "'" . addslashes($user) . "',"
              . "'" . addslashes($name) . "',"
              . "'" . addslashes($value) . "')");

      if( !$res ) {
            error_log( "insert_or_update_userprops 3:" . mysql_error());
            return false;             
      }      
      
      return true;
}

/**
 * Diese Funktion speichert alle Benutzereinstellungen 
 */
function save_update_or_read_user_properties()
{
    $updated = array();
    
    $res = mysql_query( "select * from USERPROPERTIES where user_idx=" . $_SESSION["USER"]["idx"] );       
    
    if (!$res) {
        error_log(" save_update_or_read_user_properties 1 " . mysql_error());
        return false;
    }    
    
    while( $row = mysql_fetch_assoc($res) ) {                
        
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
                    $res2 = mysql_query( "update USERPROPERTIES set value='" . addslashes($value) . "'" 
                                     . " where user_idx = " . $_SESSION["USER"]["idx"] 
                                     . " and name = '" . addslashes($key) . "'");                    
                    
                    if( !$res2 ) {
                        error_log(" save_update_or_read_user_properties 2 " . mysql_error());
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
            $res3 = mysql_query( "insert into USERPROPERTIES ( user_idx, name, value ) VALUES " 
                        . "( " .  $_SESSION["USER"]["idx"] . ","
                        . "'" . addslashes($key) . "'," 
                        . "'" . addslashes($value) . "')" );
            
            if( !$res3 ) {
                 error_log(" save_update_or_read_user_properties 3 " . mysql_error());
                 return false;
            }            
        }
    }
    
    return true;
}

function get_user_name_array($user_idx) {
    
    global $ALL_USERPROPS;
    
    static $all_users = array();    
    
    if( sizeof($all_users) == 0  ) {
        $res = mysql_query( "select * from USERPROPERTIES where "
                . " name = '" . USERPROP_FORENAME . "' or "
                . " name = '" . USERPROP_SURENAME . "' or "
                . " name = '" . USERPROP_TITLE . "'" );
        
        if( !$res ) {
            error_log( "get_user_name_array: " . mysql_error());
            return false;
        }                
        
        while( $row = mysql_fetch_assoc($res)) {
            
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
