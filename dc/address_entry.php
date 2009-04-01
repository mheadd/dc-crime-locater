<?php
/*
 * This work is licensed under a Creative Commons License 
 * To review this license, go to http://creativecommons.org/licenses/by-sa/3.0/us/
 */

/*
 *  Include required files
 */
require("common/config.php");
require("hawhaw/hawhaw.mod.inc");

/*
 * Set up a UI object
 * Text only, no VXML output 
 */

$page = new HAW_deck("Enter an Address");

$text0 = new HAW_text("DC CRIME FINDER", HAW_TEXTFORMAT_BIG);
$text0->set_voice_text("");
$text0->set_br(2);

/*
 * Create a form for address entry
 */
$form = new HAW_form("address_lookup.php");

/*
 * Create a submit button for the form
 */
$submit = new HAW_submit("Submit");

$form_text = new HAW_text("Enter a Washinton DC address:");	
$form_text->set_br(2);	

$input1_text1 = new HAW_text("Street Number:");
$input1 = new HAW_input("street_num", "", "");
$input1->set_size(15);
$input1->set_maxlength(6);
$input1->set_br(2);

$input1_text2 = new HAW_text("Apt/Unit:");
$input2 = new HAW_input("apt", "", "");
$input2->set_size(15);
$input2->set_maxlength(4);
$input2->set_br(2);

$input1_text3 = new HAW_text("Street Name:");
$input3 = new HAW_input("street_name", "", "");
$input3->set_size(15);
$input3->set_maxlength(25);
$input3->set_br(2);

$input1_text4 = new HAW_text("Direction (NW, SW, etc):");
$input4 = new HAW_input("street_dir", "", "");
$input4->set_size(15);
$input4->set_maxlength(2);
$input4->set_br(2);

$input1_text5 = new HAW_text("Zip:");
$input5 = new HAW_input("zip", "", "");
$input5->set_size(15);
$input5->set_maxlength(5);
$input5->set_br(2);

/*
 * Add text, input items and submit button to form
 */
$form->add_text($form_text);
$form->add_text($input1_text1);
$form->add_input($input1);

$form->add_text($input1_text2);
$form->add_input($input2);

$form->add_text($input1_text3);
$form->add_input($input3);

$form->add_text($input1_text4);
$form->add_input($input4);

$form->add_text($input1_text5);
$form->add_input($input5);
$form->add_submit($submit);

/*
 * Add text and form to page
 */
$page->add_text($text0);
$page->add_form($form);

/*
 * If simulator set to TRUE in config, use it to display visual UI
 */
if($useSimulator) { $page->use_simulator($skin); }

/*
 * Render the page
 */
$page->create_page();

?>