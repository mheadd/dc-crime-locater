<?php
/*
 * This work is licensed under a Creative Commons License 
 * To review this license, go to http://creativecommons.org/licenses/by-sa/3.0/us/
 */

/*
 *  Include required files
 */
require("common/config.php");
require("common/serviceSettings.php");
require("common/serviceBase.php");
require("hawhaw/hawhaw.mod.inc");

/*
 * Get submitted variables
*/
$street_num = (isset($_REQUEST['street_num'])) ? $_REQUEST['street_num'] : "";
$apt = (isset($_REQUEST['apt'])) ? $_REQUEST['apt'] : "";
$street_name = (isset($_REQUEST['street_name'])) ? $_REQUEST['street_name'] : "";
$street_dir = (isset($_REQUEST['street_dir'])) ? $_REQUEST['street_dir'] : "";
$zip = (isset($_REQUEST['zip'])) ? $_REQUEST['zip'] : "";

/*
 * Concatenate string to be used in lat/lon lookup
 */
$geo_string = $street_num." ".$apt." ".$street_name.", WASHINGTON DC ".$zip;

/*
 * Set up new SOAP client object and make service call
 */
$geoClient = new DCSoapClient("geo");
$geo_result = $geoClient->makeSoapCallSimple("geocode", $geo_string);

/*
 * If we can geocode the address, let user search for crime locations
 */
if(isset($geo_result[0]->lat) && isset($geo_result[0]->long)) {

	$lat = $geo_result[0]->lat;
	$lon = $geo_result[0]->long;

	$page = new HAW_deck("Address You Entered");
	
	$text0 = new HAW_text("DC CRIME FINDER");
	$text0->set_voice_text("");
	$text0->set_br(2);
	
	$text1 = new HAW_text("Address You Entered:", HAW_TEXTFORMAT_BOLD);
	$text1->set_br(2);
	$text2 = new HAW_text(strtoupper($street_num." ".$apt." ".$street_name." ".$street_dir), HAW_TEXTFORMAT_BOLD);
	$text3 = new HAW_text(" WASHINGTON, DC ".$zip);
	$text3->set_br(2);

	$page->add_text($text0);
	$page->add_text($text1);
	$page->add_text($text2);
	$page->add_text($text3);

	$form = new HAW_form("incident_lookup.php");

	$form_text1 = new HAW_text("Search for crimes within:");	
	$form_text1->set_br(1);	
	$radio1 = new HAW_radio("max");
	$radio1->add_button("1 mile", 1, HAW_CHECKED);
	$radio1->add_button("2 miles", 2);
	$radio1->add_button("3 miles", 3);
	
	$form_text_space = new HAW_text(" ");
	$form_text_space->set_br(2);	
	
	$form_text2 = new HAW_text("Search for crime types:");	
	$form_text2->set_br(1);
	
	$radio2 = new HAW_radio("type");
	$radio2->add_button("HOMICIDE", 1, HAW_CHECKED);
	$radio2->add_button("THEFT F/AUTO", 2);
	$radio2->add_button("ADW", 3);
	$radio2->add_button("THEFT", 4);
	$radio2->add_button("STOLEN AUTO", 5);
	$radio2->add_button("BURGLARY", 6);
	$radio2->add_button("ROBBERY", 7);
	$radio2->add_button("SEX ABUSE", 8);
	$radio2->add_button("ARSON", 9);

	$submit = new HAW_submit("Search");
	
	$hidden1 = new HAW_hidden("lat", $lat);
	$hidden2 = new HAW_hidden("lon", $lon);
	
	$form->add_text($form_text1);		
	$form->add_radio($radio1);
	$form->add_text($form_text_space);
	$form->add_text($form_text2);		
	$form->add_radio($radio2);
	$form->add_hidden($hidden1);
	$form->add_hidden($hidden2);
	$form->add_text($form_text_space);
	$form->add_submit($submit);

	$page->add_form($form);

}
		
/*
 * Otherwise, offer the option of changing address
 */
else {
	
	$page = new HAW_deck("Could not look up Address");
	
	$text0 = new HAW_text("DC CRIME FINDER", HAW_TEXTFORMAT_BIG);
	$text0->set_voice_text("");
	$text0->set_br(2);
	
	$text1 = new HAW_text("Ooops...  We could not look up the address you provided.");
	$text1->set_br(2);

	$link1 = new HAW_link("Try again?", "address_entry.php", "Try again?");

	$page->add_text($text0);
	$page->add_text($text1);
	$page->add_link($link1);

}	

/*
 * If simulator set to TRUE in config, use it to display visual UI
 */
if($useSimulator) { $page->use_simulator($skin); }

/*
 * Render the page
 */
$page->create_page();

?>