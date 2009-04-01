<?php
/*
 * This work is licensed under a Creative Commons License 
 * To review this license, go to http://creativecommons.org/licenses/by-sa/3.0/us/
 */


/*
 * Get posted input
 * ANI is delivered from CCXML on inbound telephone call
 * TURN is posted to page if caller opts not to use ANI for address look up
 */
$phone = (empty($_REQUEST['ani'])) ?  0 : $_REQUEST['ani'];
$turn = (empty($_REQUEST['turn'])) ?  0 : $_REQUEST['turn'];

/*
 *  Include required files
 */
require("common/config.php");
require("hawhaw/hawhaw.mod.inc");

/*
 * Set up a UI object
 * Create default text items
 * Use intro message only on first visit to this page for VXML content
 */
$page = new HAW_deck("Washington DC Crime Finder");
$text0 = new HAW_text("DC CRIME FINDER", HAW_TEXTFORMAT_BIG);
$text0->set_voice_text(($turn < 1) ? "Welcome to the D C crime finder." : "");
$text0->set_br(2);
$text1 = new HAW_text("Find out about crimes in your neighborhood", HAW_TEXTFORMAT_BOLD);
$text1->set_br(2);
$text1->set_voice_text(($turn < 1) ? "This service will let you find out about crimes that have occured in your neighborhood." : "");

/*
 * If ANI passed in with HTTP request (phone only) give the option of using in addres lookup.
 */
if(checkPhone($phone)) {

	$phone_string = implode(" ", str_split($phone));		
	$text2 = new HAW_text(" ");
	$text2->set_voice_text("You are calling from ".$phone_string.". Do you want to use this number to look up an address?");

	$link1 = new HAW_link(" ","phone_lookup.php?phone=$phone","");
	$link1->set_voice_text("To use this phone number, press 1.");
	$link1->set_voice_dtmf("1");

	$link2 = new HAW_link(" ","index.php?turn=1","");
	$link2->set_voice_text("To enter a different phone number, press 2.");
	$link2->set_voice_dtmf("2");
	
}

/*
 * Otherwise, allow the user to enter a phone number
 */
else {

	$form = new HAW_form("phone_lookup.php");

	$text2 = new HAW_text("Enter your home phone and we'll look up your address.");
	$text2->set_voice_text("Enter your home telephone number and we'll use it to look up your address.");
	$text2->set_br(2);

	$input1 = new HAW_input("phone", "", "Home Phone Number:");
	$input1->set_size(15);
	$input1->set_maxlength(10);
	$input1->set_voice_text("Please enter your 10 digit home phone number, starting with the area code.");
	$input1->set_voice_type("digits?length=10");
	$input1->set_br(2);
	
	$submit = new HAW_submit("Look Up");

}

/*
 * Add UI items to page
 */
$page->add_text($text0);
$page->add_text($text1);
$page->add_text($text2);
if(isset($link1)) { $page->add_link($link1); }
if(isset($link2)) { $page->add_link($link2); }
if(isset($input1)) { $form->add_input($input1); }
if(isset($submit)) { $form->add_submit($submit); }
if(isset($form)) { $page->add_form($form); }

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
