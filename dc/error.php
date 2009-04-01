<?php
/*
 * This work is licensed under a Creative Commons License 
 * To review this license, go to http://creativecommons.org/licenses/by-sa/3.0/us/
 */

/*
 * Generic error page.
 * Include required files
 */
require("common/config.php");
require("hawhaw/hawhaw.mod.inc");

/*
 * Set up a UI object
*/
$page = new HAW_deck("An Error Occured");

$text0 = new HAW_text("DC Crime finder", HAW_TEXTFORMAT_BIG);
$text0->set_voice_text("");
$text0->set_br(2);

$text1 = new HAW_text("Sorry.  An error has occured.  Please try your action again later.");
$text1->set_voice_text("The system is experiencing technical difficulties.  Please try your action again later.");
$text1->set_br(2);

$page->add_text($text0);
$page->add_text($text1);

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