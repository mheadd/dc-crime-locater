<?php
/*
 * This work is licensed under a Creative Commons License 
 * To review this license, go to http://creativecommons.org/licenses/by-sa/3.0/us/
 */

/*
 * Get posted input and typecast to prevent injection
 */
$max = (int) $_REQUEST['max'];
$type = (int) $_REQUEST['type'];
$lat = (double) $_REQUEST['lat'];
$lon = (double) $_REQUEST['lon'];

/*
 *  Include required files
 */
require("common/config.php");
require("db/connect.php");
require("hawhaw/hawhaw.mod.inc");

/*
 * Create database connection object and run query
 */
$connection = new dbConnect();
$connection->selectDB($db_name);
$result = $connection->runQuery(getSql($max,$type,$lat,$lon));
$rows = $connection->getNumRowsAffected();

/*
 * Set up a UI object
*/
$page = new HAW_deck("Search Results");

$text0 = new HAW_text("DC CRIME FINDER", HAW_TEXTFORMAT_BIG);
$text0->set_voice_text("");
$text0->set_br(2);

/*
 * Format header text depending on number of incidents found
 */
$miles_text = ($max==1) ? "mile": "miles";
$header_text = ($rows==0) ? "No crimes of the type $crime_types[$type] were found within $max $miles_text of your location." : "$rows NEAREST CRIMES:";

/*
 * Format header voice text depending on number of incidents found
 */
$were_voce_text = ($rows==1) ? "was" : "were";
$incident_voice_text = ($rows==1) ? "incident": "incidents";
$miles_voice_text = ($max==1) ? "mile": "miles";


/*
 * Set and add page header
 */
$text1 = new HAW_text($header_text);
$text1->set_br(2);
$text1->set_voice_text("There $were_voce_text $rows crime incidents found within $max $miles_voice_text of your location.");
$page->add_text($text0);
$page->add_text($text1);
//$rule1 = new HAW_rule("100%", 2);
//$page->add_rule($rule1);

/*
 * Iterate over result set and display results
 */
for($i=0; $i<$rows; $i++) {
	
	$row = mysql_fetch_array($result);

	$text2 = new HAW_text($row[0]);
	$text2->set_voice_text(swapText($row[0]));

	$text3 = new HAW_text("Distance: $row[3] mi");
	$text3->set_voice_text("$row[3] miles away");

	$text4 = new HAW_text("Type: $row[1]");
	$text4->set_voice_text(swapText($row[1]));
	
	$text5 = new HAW_text("Date: $row[2]");
	$text5->set_voice_text("occuring on $row[2]");
	$text5->set_br(2);

	$page->add_text($text2);	
	$page->add_text($text3);
	$page->add_text($text4);
	$page->add_text($text5);
	//$page->add_rule($rule1);
	
}

/*
 * Add navigation links in page footer
 */
$link1 = new HAW_link("New Search","index.php");
$link1->set_voice_text("To search again, using a different address, press 1.");
$link1->set_voice_dtmf("1");
$link1->set_br(2);

$page->add_link($link1);

/*
 * If simulator set to TRUE in config, use it to display visual UI
 */
if($useSimulator) { $page->use_simulator($skin); }

/*
 * Set application root document for VXML output
 */
$page->set_application("vxml/appRoot.vxml");

/*
 * Render page
 */
$page->create_page();


?>
