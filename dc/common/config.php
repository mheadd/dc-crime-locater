<?php
/*
 * This work is licensed under a Creative Commons License 
 * To review this license, go to http://creativecommons.org/licenses/by-sa/3.0/us/
 */

/*
 * Customizable settings
 */

// Turn on/off error reporting if desired.
error_reporting(1);

// Settings to control how crime results are displayed
// ROUND is used round off distances
// LIMIT is the number of results to display. For small devices and VXML, number of results displayed should be manageable, i.e. 5 or less.
$round = 2;
$limit = 5;

// Database access information
$host = '';
$db_name = '';
$user = '';
$password = '';

// Flag to determine whether cell phone simulator is used
$useSimulator = true;

// Alternate skin CSS file - leave empty to use default
//$skin = "http://skin.hawhaw.de/skin.css";
$skin = "css/skin.css";

// Array to hold active area codes (only these area codes used in lookup)
$area_codes = Array("202", "302");



/*
 * Non-Customizable settings
 * Do not change settings below unless you understand how it will affect the application
 */

// Set up default exception handler
function myExceptionHandler($e) {
  	
	// Erite exception details to log
	$error_text = 'ERROR: '.date(DATE_RFC822).': '.$e->getMessage();
	$log_handle = fopen("error_log/log.txt", "a");
  	fwrite($log_handle, $error_text);
  	fclose($fp);
	
	//redirect to generic error page
	header("Location: error.php");
}

set_exception_handler('myExceptionHandler');

/*
 * Array to hold crime types
 */
$crime_types = array();
$crime_types[1] = "HOMICIDE";
$crime_types[2] = "THEFT F/AUTO";
$crime_types[3] = "ADW";
$crime_types[4] = "THEFT";
$crime_types[5] = "STOLEN AUTO";
$crime_types[6] = "BURGLARY";
$crime_types[7] = "ROBBERY";
$crime_types[8] = "SEX ABUSE";
$crime_types[9] = "ARSON";

/*
 * SQL query to return distance to crime incidents, description, date, type
 */
function getSql($max,$type,$lat,$lon) {
GLOBAL $round, $limit, $crime_types;
$sql = "SELECT BLOCKSITEADDRESS, OFFENSE, REPORTDATETIME, ROUND(((ACOS( SIN($lat * PI()/180 ) * SIN(LATITUDE * PI()/180 ) + COS($lat * PI()/180 ) * COS(LATITUDE * PI()/180 ) * COS(($lon - LONGITUDE) * PI()/180))*180/PI())*60*1.1515),$round) AS DISTANCE FROM crime_incidents_2008 ";
	if($type > 0) {
		$sql .= 'WHERE OFFENSE = \''.$crime_types[$type].'\' ';
	}
$sql .= "HAVING distance <= $max ORDER BY DISTANCE ASC LIMIT 0 , $limit";
return $sql;
}

/*
 * Arrays denoting search and replace values for TTS readback
 */
$search = array("B/O", "THEFT F/AUTO", "ADW", "NW", "SE");
$replace = array("block of", "theft from auto", "assault with a deadly weapon", "Northwest", "Southeast");

/*
 * Function to format text for TTS playback
 */
function swapText($text) {
  GLOBAL $search, $replace;
  return str_replace($search, $replace, $text);
}

/*
 * Funtion to check telephone number for valid area code
 */

function checkPhone($number) {
	GLOBAL $area_codes;
	return in_array(substr($number, 0, 3), $area_codes);
	
}

?>
