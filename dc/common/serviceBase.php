<?php
/*
 * This work is licensed under a Creative Commons License 
 * To review this license, go to http://creativecommons.org/licenses/by-sa/3.0/us/
 */

/*******************************************************************************
*
* Class to create a SOAP client to consume web services
*
*******************************************************************************/

class DCSoapClient {
       
    private $client;
	
    function __construct($type) {
      	
	GLOBAL $WSDL; 	
	$this->client = new SoapClient($WSDL[$type], array('trace' => 1, 'exceptions' => 1));
						
    }

    function makeSoapCall($name, $params) {
     
	$result = $this->client->__soapCall($name, array($params));
      	return $this->client->__getLastResponse();

    }

    function makeSoapCallSimple($name, $param) {

	return $this->client->$name($param);

    }

    function __destruct() {

      	unset($this->client);

    }

  }

?>
