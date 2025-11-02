<?php
require_once(dirname(__FILE__) . "/../common_login/lib/user_rights.php");

date_default_timezone_set('Europe/Vienna');

define('AUDIO_MAIN_DIRECTORY', "audio_main" );
define('AUDIO_RANDOM_DIRECTORY', "audio_random" );

$SITE_NAME = "Useless Papst &middot; Teekränzchen LIVE";

$IS_TEST = false;

error_log( $_SERVER['HTTP_HOST'] );
error_log( $_SERVER['HTTP_USER_AGENT'] );

  
if( $_SERVER["HTTP_HOST"] == "localhost" ||
    $_SERVER["HTTP_HOST"] == "10.0.0.10") {  
  $BASE_URL = "http://papst/";
  $MAIL_FROM = "\"Johannes Paul II\" <papst@hoffer.cx>";
  $DATABASE = "papst";
  $DBPASSWD = "johannespaul";
  
} else {    
  $BASE_URL = "http://papst/";
  $MAIL_FROM = "\"Johannes Paul II\" <papst@hoffer.cx>";
  $DATABASE = "papst";
  $DBPASSWD = "johannespaul"; 
}

$VERSION = "0.1";
$mysqli = NULL;

$HTML_HEADER = "";

$ENABLE_SUNDAY = false;

header('Cache-Control: no-store, no-cache, must-revalidate');     // HTTP/1.1
header('Cache-Control: pre-check=0, post-check=0, max-age=0');    // HTTP/1.1

if( !isset($NOCONTENT_TYPE) )
{
    header("Content-Type: text/html; charset=utf-8");
}

header("Pragma: no-cache");
header("Expires: 0");

session_start();

error_reporting(E_ALL);

$valid_user=false;

setlocale(LC_ALL,'de_AT.UTF-8', 'de_DE.UTF-8','de_AT', 'de', 'ge');

function connect_db()
{
    global $DATABASE, $DBPASSWD, $mysqli;

    /* You should enable error reporting for mysqli before attempting to make a connection */
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
    $mysqli = new mysqli( 'localhost', $DATABASE, $DBPASSWD, $DATABASE ) or die( 'cannot connet to db: ' . $mysqli->connect_error );

    if( !$mysqli->set_charset ( "utf8" ) ) {
	    error_log( "cannot set utf charset " . $mysqli->error );
    }
}

function add_in_html_header( $string )
{
    global $HTML_HEADER;
    
    $HTML_HEADER .= $string;
}

function print_header( $menu_name = "", $rel = "", $extra_body_tag_text="")
{    
    global $IS_TEST;
    global $HTML_HEADER;
    global $SITE_NAME;

  if( isset( $menu_name ) && $menu_name )
	{
	  $_SESSION["current_menu"] = $menu_name;
	}

  if ('iPhone' == $_SERVER['HTTP_USER_AGENT']):
  	$iphone = 1;
  elseif ( 'iPod' == $_SERVER['HTTP_USER_AGENT']):
	  $iphone = 1;
  elseif ( 'Android' == $_SERVER['HTTP_USER_AGENT']):
	  $iphone = 1;
  else:
	  $iphone = 0;
  endif;


echo "<!DOCTYPE html>\n";
echo "<html>\n";
echo "<head>\n";
echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">\n";
echo "<meta http-equiv=\"cache-control\" content=\"no-store\">\n";
echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n";
echo "<head>\n<title>" . $SITE_NAME . "</title>\n";
echo "<meta http-equiv=\"Content-Script-Type\" content=\"text/javascript\">\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $rel . "framework/w3.css\">\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $rel . "framework/style_2025-10-24.css\">\n";
echo "<script src=\"" . $rel . "framework/jquery-3.7.1.js\"></script>\n";
echo "<script src=\"" . $rel . "framework/jquery-ui-1.14.1/jquery-ui.min.js\"></script>\n";
echo "<script src=\"" . $rel . "framework/party-2.2.0.min.js\"></script>\n";
echo "<script src=\"" . $rel . "js/functions.js\" type=\"text/javascript\" charset=\"utf-8\"></script>\n";	  
  echo "<script type=\"text/javascript\">\n";
  echo "var rel = \"$rel\"\n";
  echo "</script>\n";   
echo "<meta name=\"robots\" content=\"index, follow\">\n";
echo "<meta name=\"author\" content=\"Martin Oberzalek, Phil Hoffer\">\n";
echo "<meta name=\"keywords\" lang=\"de\" content=\"Teekränzchen, LAN Parties, der Papst\">\n";
echo "<meta name=\"description\" lang=\"de\" content=\"Website des Useless Papsts.\">\n";
echo "<link rel=\"shortcut icon\" href=\"favicon.ico\">\n";
echo "<link rel=\"icon\" href=\"favicon.ico\" type=\"image/x-icon\">\n";
  
	echo "</head>\n\n";	

	$bodyclass = "";

	if( $IS_TEST ) {
		$bodyclass.=" test";
	}

}

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

$starttime = microtime_float();

function print_runtime()
{
  global $starttime, $VERSION;

  print( "\n<!--<div class=\"runtime\">Version: <a href=\"ChangeLog\">$VERSION</a> | Runtime: " );
  printf( "%.03f sec\n", microtime_float() - $starttime );
  print("</div>-->\n");
}


function debug_runtime( $info = "")
{
  global $starttime, $VERSION;
 
  error_log( sprintf("%.03f sec $info", microtime_float() - $starttime) ); 
}

function print_footer()
{
  echo "<footer>\n";
  echo "<div id=\"psalm\">\n";
  echo "<p id=\"latein\">Latein.</p>\n";
  echo "<p id=\"deutsch\">Deutsch.</p>\n";
  echo "</div>\n";
  echo "<div class=\"logo\">\n";
  echo "<p>Eine TK Produktion.</p>\n";
  echo "<p>Drinking <span class=\"beer\">tea</span> since 2005.</p>\n";
  echo "</div>\n";
  print_runtime();
  echo "</footer>\n";
  echo "<script src=\"../js/psalme.js\"></script>\n";
}

function logged_in()
{
  if( !isset( $_SESSION["USER"] ) )
	return false;
  
  if( !isset( $_SESSION["USER"]["idx"]  ) )
	return false;

  if( !$_SESSION["USER"]["idx"] )
	return false;

  return true;
}

function check_login()
{
  if( !logged_in() )
	{
	  header( 'Location: login.php' );
	  exit(0);
	}
}

function is_file_upload( $name )
{
  if( !isset( $_FILES[$name] ) )
	return false;

  if( !isset( $_FILES[$name]["name"] ) || !$_FILES[$name]["name"] )
	return false;

  if( !isset( $_FILES[$name]["tmp_name"] ) || !$_FILES[$name]["tmp_name"] )
	return false;

  return true;
}

function scale_image( $source_pic, $destination_pic, $max_width, $max_height, $force_lower_sices = false )
{
  $src = imagecreatefromjpeg($source_pic);
  list($width,$height)=@getimagesize($source_pic);

  if( $width == 0 || $height == 0 )
	return;

  $x_ratio = $max_width / $width;
  $y_ratio = $max_height / $height;

  if( ($width <= $max_width) && ($height <= $max_height) ){

	if( $force_lower_sices )
	  {
		$tn_width = $max_width;
		$tn_height = ceil($x_ratio * $height);
	  }
	else
	  {
		$tn_width = $width;
		$tn_height = $height;
	  }
  }elseif (($x_ratio * $height) < $max_height){
	$tn_height = ceil($x_ratio * $height);
	$tn_width = $max_width;
  }else{
	$tn_width = ceil($y_ratio * $width);
	$tn_height = $max_height;
  }

  $tmp=imagecreatetruecolor($tn_width,$tn_height);
  imagecopyresampled($tmp,$src,0,0,0,0,$tn_width, $tn_height,$width,$height);

  imagejpeg($tmp,$destination_pic,100);
  imagedestroy($src);
  imagedestroy($tmp);
}

function check_file_upload_and_scale( $name, $idx, &$error, $own_name = "", $max_width=500, $max_height=500, $force_lower = false )
{
  if( !is_file_upload( $name ) )
	return true;

  if( !$own_name )
	$new_name = "upload/" . $idx . "_" . $_FILES[$name]["name"];
  else
	$new_name = "upload/" . $own_name;

  scale_image( $_FILES[$name]["tmp_name"], $new_name, $max_width, $max_height, $force_lower );

  if( !file_exists( $new_name ) )
	{
	  $error = "Dies ist keine JPEG Datei.";
	  return false;
	}

  return true;
}

function url_upload_and_scale( $url, $idx, &$error, $own_name, $max_width=500, $max_height=500, $force_lower = false )
{
  $new_name = "upload/" . $own_name;

  scale_image( $url, $new_name, $max_width, $max_height, $force_lower );

  if( !file_exists( $new_name ) )
	{
	  $error = "Dies ist keine JPEG Datei.";
	  return false;
	}

  return true;
}

function check_file_upload( $name, $idx, &$error, $own_name = "" )
{
  if( !is_file_upload( $name ) )
	return true;

  if( !$own_name )
	$new_name = "upload/" . $idx . "_" . $_FILES[$name]["name"];
  else
	$new_name = "upload/" . $own_name;

  if( !move_uploaded_file( $_FILES[$name]["tmp_name"],
						    $new_name ) )
	{
	  chmod( $new_name, 0644 );
	  $error = "unable to move file from '" . $_FILES[$name]["tmp_name"] . "' to '" . $new_name . "'";
	  return false;
	}

  return true;
}

function js_start()
{
  print( "<script type=\"text/javascript\"><!--\n" );
}

function js_end()
{
  print( "--></script>\n" );
}

function js( $text )
{
  js_start();
  print( $text . "\n" );
  js_end();
}

function break_text( $text )
{
  $all = explode( "\n", $text );

  $res = "";

  foreach( $all as $line )
    {
      $res .= $line;
	  $res .= "\n";
      $res .= "<br />";
    }

  return $res;
}

function process_text( $text )
{
  $text = break_text( $text );

  $text = preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.\-\~]*(\?\S+)?)?)?)@', '<a href="$1" target=_new>$1</a>', $text);
  $text = preg_replace('/(([\-\w\.]+)@([\-\w\.]+))/','<a href="mailto:$1">$1</a>', $text);

  return $text;
}

function print_text( $text )
{
  print( process_text( $text ) );
}

function is_admin()
{
  return $_SESSION["USER"]["is_admin"];
}

function icasecmp( $a, $b )
{
  if( strtolower( $a ) == strtolower( $b ) )
	return 0;

  return (  strtolower( $a ) <  strtolower( $b ) ) ? -1 :1;
}

function formatdate( $date )
{
    if( $date < 1000 ) {
        return "";
    }
    
    return date("d.m.Y", $date);        
}

function to_timestamp($date) {

    if( !$date ) {
	return 0;
    }	    

    $parts = explode(".", $date, 3);
    
    if( sizeof($parts) < 0 ) {
        return 0;
    }
    
    $stamp = mktime(0,0,0,$parts[1],$parts[0],$parts[2]);   
    
    return $stamp;
}

function get_name( $row )
{
    $res = "";
    
    if( $row["title"] )
        $res .= $row["title"] . " ";
    
    return $res .= $row["surename"] . " " . $row["forename"];
}

function type2string( $app )
{
    switch ($app["type"]) {
        case "FREE_LEARNING": return "Freies Lernen";
        case "COACHING": return "Coaching";
        case "MODUL": return "Modultermin";
    }      
}

function get_title4app($app) {
    
    if( $app["title"] )
        return $app["title"];
    
    return type2string($app);
}

/**
 * converts an array to a human readable string
 * @param type $array
 * @return string 
 */
function array_to_string($array) {
    $res = "";

    foreach ($array as $key => $value) {
        if ($res)
            $res .= ", ";
        $res = $key . " => " . $value;
    }

    return $res;
}


/**
 * 
 * @param type INPUT_GET, INPUT_POST
 * @param type $var_name Variable name 
 * @return type mixed var
 */
function mysql_filter_input( $method, $var_name )
{
	  connect_db();
    return filter_input(  $method, $var_name, FILTER_CALLBACK, array("options"=>"mysql_real_escape_string"));
}

?>
