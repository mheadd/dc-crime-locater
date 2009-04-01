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

$phone = $_REQUEST['phone'];

/*
 * We need to clean up SOAP responses a bit before we can access them using SimpleXML
 */
function stripNameSpaces($soapResponse)  {
	return preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $soapResponse);
}

/*
 * Set up new SOAP client object and make service call 
 * Get the address information that will be used for geocoding
 */
if(checkPhone($phone)) {

	$phoneClient = new DCSoapClient("phone");
	$phone_result = stripNameSpaces($phoneClient->makeSoapCall("GetContactInfo", array("PhoneNumber" => $phone, "LicenseKey" => $dots_license_key)));

	$xml = new SimpleXmlElement($phone_result);
	$address_xml = $xml->soapBody->GetContactInfoResponse->GetContactInfoResult->Contacts->Contact;

	$address = $address_xml->Address;
	$city = $address_xml->City;
	$state = $address_xml->State;
	$zip = $address_xml->Zip;
	$type = (strlen($address_xml->Type) > 0) ? $address_xml->Type : "UNKNOWN"; 

/*
 * If a valid address type is returned, concatenate string to be used in lat/lon lookup
 * If we can geocode the address, let user search for crime locations
 */	
	if($type != trim("UNKNOWN")) {
	 
	$geo_string = $address.", ".$city.", ".$state.", ".$zip;
	$geoClient = new DCSoapClient("geo");
	$geo_result = $geoClient->makeSoapCallSimple("geocode", $geo_string);

		if(isset($geo_result[0]->lat) && isset($geo_result[0]->long)) {

		$lat = $geo_result[0]->lat;
		$lon = $geo_result[0]->long;

		$page = new HAW_deck("Your Home Address");
		
		$text0 = new HAW_text("DC CRIME FINDER", HAW_TEXTFORMAT_BIG);
		$text0->set_voice_text("");
		$text0->set_br(2);
		
		$text1 = new HAW_text("Your Home Address:", HAW_TEXTFORMAT_BOLD);
		$text1->set_br(2);
		$text1->set_voice_text("");

		$text2 = new HAW_text($address);
		$text2->set_voice_text("Your address is $address, $city, $state.");

		$text3 = new HAW_text($city.", ".$state);
		$text3->set_br(2);
		$text3->set_voice_text("");

		$page->add_text($text0);
		$page->add_text($text1);
		$page->add_text($text2);
		$page->add_text($text3);

		$form = new HAW_form("incident_lookup.php");

		$form_text1 = new HAW_text("Search for crimes within:");
		$form_text1->set_voice_text("");
		$form_text1->set_br(1);		

		$radio1 = new HAW_radio("max");
		$radio1->set_voice_text("To search for crimes within 1 mile, press 1. Within 2 miles, press 2. Within 3 miles, press 3");
		$radio1->add_button("1 mile", 1, HAW_CHECKED);
		$radio1->add_button("2 miles", 2);
		$radio1->add_button("3 miles", 3);
		
		$form_text_space = new HAW_text(" ");
		$form_text_space->set_br(2);	
		
		$form_text2 = new HAW_text("Search for crime types:");
		$form_text2->set_voice_text("");
		$form_text2->set_br(1);
		
		$radio2 = new HAW_radio("type");
		$radio2->set_voice_text("To search for homicides, press 1. Theft from auto, press 2. Assault with a deadly weapon, press 3. Theft, press 4. Stolen auto, press 5. Burglary, press 6. Robbery, press 7. Sex abuse, press 8. Arson, press 9.");
		$radio2->add_button("HOMICIDE", 1);
		$radio2->add_button("THEFT F/AUTO", 2);
		$radio2->add_button("ADW", 3);
		$radio2->add_button("THEFT", 4);
		$radio2->add_button("STOLEN AUTO", 5);
		$radio2->add_button("BURGLARY", 6);
		$radio2->add_button("ROBBERY", 7);
		$radio2->add_button("SEX ABUSE", 8);
		$radio2->add_button("ARSON", 9);

		$hidden1 = new HAW_hidden("lat", $lat);
		$hidden2 = new HAW_hidden("lon", $lon);
		
		$submit = new HAW_submit("Search");

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
 * If we can not geocode the address, let user enter different number or manually enter address (text only, no VXML)
 */		
		else {
		$page = new HAW_deck("Could not look up Address");
		
		$text0 = new HAW_text("DC CRIME FINDER", HAW_TEXTFORMAT_BIG);
		$text0->set_voice_text("");
		$text0->set_br(2);
		
		$text1 = new HAW_text("Invalid Address.  Address type is $type");
		$text1->set_voice_text("We were unable to get geographic coordinates for your address.");
		$text1->set_br(2);

		$link1 = new HAW_link("Try another phone number?", "index.php", "Try another number?");
		$link1->set_br(2);
		$link1->set_voice_text("Do you want to try another number?  To search using another number, press 1.");
		$link1->set_voice_dtmf("1");
		
		$link2 = new HAW_link("Manually enter address?", "address_entry.php", "Manually enter address?");
		$link2->set_voice_text("");

		$page->add_text($text0);
		$page->add_text($text1);
		$page->add_link($link1);
		$page->add_link($link2);

		}	
	}

/*
 * If an invalid address type is returned, let user enter different number or manually enter address (text only, no VXML)
*/	
	else {

	$page = new HAW_deck("Invalid Address Returned");
	
	$text0 = new HAW_text("DC CRIME FINDER", HAW_TEXTFORMAT_BIG);
	$text0->set_voice_text("");
	$text0->set_br(2);
	
	$text1 = new HAW_text("Invalid Address Returned.  Address type is $type");
	$text1->set_voice_text("We were unable to look up your address.");
	$text1->set_br(2);

	$link1 = new HAW_link("Try another phone number?", "index.php", "Try another number?");
	$link1->set_br(2);
	$link1->set_voice_text("Do you want to try another number? To search using another number, press 1.");
	$link1->set_voice_dtmf("1");
		
	$link2 = new HAW_link("Manually enter address?", "address_entry.php", "Manually enter address?");
	$link2->set_voice_text("");

	$page->add_text($text0);
	$page->add_text($text1);
	$page->add_link($link1);
	$page->add_link($link2);

	}


}

/*
 * If an invalid phone number is entered, let user enter different number or manually enter address (text only, no VXML)
*/
	else {
	
	$page = new HAW_deck("Invalid Phone Number");
	
	$text0 = new HAW_text("DC CRIME FINDER", HAW_TEXTFORMAT_BIG);
	$text0->set_voice_text("");
	$text0->set_br(2);
	
	$text1 = new HAW_text("Invalid phone number.  Only DC area codes are valid");
	$text1->set_voice_text("You entered an invalid phone number.  You must enter a valid Washington DC phone number.");
	$text1->set_br(2);
	
	$link1 = new HAW_link("Manually enter address?", "address_entry.php", "Manually enter address?");
	$link1->set_voice_text("");
	$link1->set_br(2);
		
	$link2 = new HAW_link("Try another phone number?", "index.php", "Try another number?");
	$link2->set_voice_text("Do you want to try another number? To search using another number, press 1.");
	$link2->set_voice_dtmf("1");
	$link2->set_br(2);
		
	$page->add_text($text0);
	$page->add_text($text1);
	$page->add_link($link1);
	$page->add_link($link2);
	
	}

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
