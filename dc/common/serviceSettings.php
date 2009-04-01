<?php
/*
 * This work is licensed under a Creative Commons License 
 * To review this license, go to http://creativecommons.org/licenses/by-sa/3.0/us/
 */

// License key for DOTS phone number lookup web service
$dots_license_key = "";

// Geocoder.us user account info
$username = "";
$password = "";

// Aarray to hold WSDL references for web services
$WSDL = Array();
$WSDL["phone"] = ""; // This is the WSDL for the DOTS phone number lookup web service
$WSDL["geo"] = "http://geocoder.us/dist/eg/clients/GeoCoderPHP.wsdl"; // This is the WSDL for geocoder.us web service (may be used with or without user info)
// $WSDL["geo"] = "http://username:password@geocoder.us/dist/eg/clients/GeoCoderPHP.wsdl";

?>
