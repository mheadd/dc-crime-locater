<?php

/*
* This work is licensed under a Creative Commons License 
* To review this license, go to http://creativecommons.org/licenses/by-sa/3.0/us/
*/

/*
 * A simple DB connection class.
 */


class dbConnect {
	
	private $dbConnection;	// connection resource
	private $dbName;	// name of the database to be used
	private $rowsAffected;	// number of rows affected by last operation
	private $debug;		// debug flag for verbose error output
	private $errorMessage;	// error message text

	function __construct($debug=false) {
				
	GLOBAL $host, $user, $password;		
	$this->debug = $debug;	
		
		if(!$this->dbConnection = mysql_connect($host, $user, $password)) {
			
			$this->errorMessage = 'Could not establish connection. ';
			if($this->debug) {
				$this->errorMessage .= mysql_error();
			}
				
			throw new Exception($this->errorMessage);
				
		}
	}
	
	// Select a database to use
	function selectDB($name) {
					
		if(!mysql_select_db($name, $this->dbConnection)) {
			
			$this->errorMessage = 'Could not connect to database. ';
			if($this->debug) {
				$this->errorMessage .= mysql_error();
			}
			
			throw new Exception($this->errorMessage);
			
		}
	}
	
	// Run a query against a database table
	function runQuery($query) {
			
			$result = mysql_query($query, $this->dbConnection);
			
			if(!$result) {
				
			$this->errorMessage = 'Could not execute query. ';
			if($this->debug) {
				$this->errorMessage .= mysql_error();
			}
				throw new Exception($this->errorMessage);
				
			} 
			
			if(get_resource_type($result)) {
				
				$this->rowsAffected = mysql_num_rows($result);
			
			} else {
				
				$this->rowsAffected = mysql_affected_rows($result);
				
			}
		
		return $result;
		
	}
	
	// Determine the number of rows affected in the last operation
	function getNumRowsAffected() {
		
		return $this->rowsAffected;
	}
	
	// Close the database connection
	function __destruct() {
		
		mysql_close($this->dbConnection);
		
	}
}

?>
