<?php
    // Includes
    require_once('config.php');

    // Disable Errors
    error_reporting(0);

    // Define Constants
    define('ERROR_ATTRIBUTE', 'error');
    define('MYSQL_ERROR_ATTRIBUTE', 'mysql_error');

    // Create XML Object
    $xml = new SimpleXMLElement('<xml/>');
    
    // Get Query
    if($_POST['strPostOrigin'] == 'nemoTest'){
	$strQuery = trim($_POST['strQuery']);
    }
    else{
	$strQuery = trim(strrev(base64_decode($_POST['strQuery'])));
    }
    // Check for POST
    if($_POST) {
	if($_POST['strAuthKey'] == AUTHORIZATION_KEY) {
	    $db = mysql_connect(DATABASE_SERVER, DATABASE_USERNAME, DATABASE_PASSWORD);
	    if($db && mysql_selectdb(DATABASE_SCHEMA, $db)) {
		if($result = mysql_query($strQuery)) {
		    $rows = $xml->addChild('rows');
		    if($result) {
			$arrQuery = explode(' ', $strQuery);
			if(strtolower($arrQuery[0]) == 'insert') {
			    $strQuery = 'SELECT LAST_INSERT_ID() AS ID';
			    $result = mysql_query($strQuery);
			}
			while($dbRow = mysql_fetch_assoc($result)) {
			    $row = $rows->addChild('row');
			    foreach($dbRow as $key => $value) {
				$row->addChild($key, htmlspecialchars($value));
			    }
			}
		    }
		    else {
			// Get MySQL error
			$strMySqlError = mysql_error($db);
			
			// Check Magic Quotes GPC
			if(get_magic_quotes_gpc()){
			    $xml->addAttribute(ERROR_ATTRIBUTE, 'SQL Query Failed - (Warning Magic Quotes GPC is Enabled)');
			    $xml->addAttribute(MYSQL_ERROR_ATTRIBUTE, $strMySqlError);
			}
			else{
			    $xml->addAttribute(ERROR_ATTRIBUTE, 'SQL Query Failed');
			    $xml->addAttribute(MYSQL_ERROR_ATTRIBUTE, $strMySqlError);
			}
		    }
		}
		else {
		    // Get MySQL error
		    $strMySqlError = mysql_error($db);
			
		    // Check Magic Quotes GPC
		    if(get_magic_quotes_gpc()){
			$xml->addAttribute(ERROR_ATTRIBUTE, 'SQL Query Failed - (Warning Magic Quotes GPC is Enabled)');
			$xml->addAttribute(MYSQL_ERROR_ATTRIBUTE, $strMySqlError);
		    }
		    else{
			$xml->addAttribute(ERROR_ATTRIBUTE, 'SQL Query Failed');
			$xml->addAttribute(MYSQL_ERROR_ATTRIBUTE, $strMySqlError);
		    }
		}
	    }
	    else {
		// Get MySQL error
		$strMySqlError = mysql_error($db);
		
		$xml->addAttribute(ERROR_ATTRIBUTE, 'Failed to Connect to Database');
		$xml->addAttribute(MYSQL_ERROR_ATTRIBUTE, $strMySqlError);
	    }
	}
	else {
	    $xml->addAttribute(ERROR_ATTRIBUTE, 'Missing or Invalid Auth Key');
	}
    }
    else {
	$xml->addAttribute(ERROR_ATTRIBUTE, 'No POST Data Received');
    }
    // Return XML
    if($_POST['boolCompress'] == 'true' || $_POST['boolCompress'] == 'on') {
	header('Content-Type: text/xml');
	header('Content-Transfer-Encoding: binary');
	header('Content-Encoding: gzip');
	ob_start('ob_gzhandler');
	//echo gzcompress($xml->asXML());
	echo $xml->asXML();
    }
    elseif($_POST['strPostOrigin'] == 'nemoTest'){
	if($_POST['strOutputType'] == 'CSV'){
	    // Parse XML to CSV
	    $strCSV = "";
	    // Get CSV Headers
	    foreach($xml->rows->row->children() as $child => $value){
		$strCSV .= '"' . $child . '",';
	    }
	    $strCSV = substr($strCSV, 0, -1) . "\n";
	    
	    // Loop Through Content
	    foreach($xml->rows->row as $row){
		foreach($row->children() as $child => $value){
		    $strCSV .= '"' . $value . '",';
		}
		$strCSV = substr($strCSV, 0, -1) . "\n";
	    }
	    
	    // Download File
	    header("Content-type: text/plain");
	    header('Content-Disposition: attachment; filename=nemoQuery.csv');
	    echo $strCSV;
	}
	else{
	    header('Content-Type: text/xml');
	    echo $xml->asXML();
	}
    }
    else {
	header('Content-Type: text/xml');
	echo base64_encode($xml->asXML());
    }
?>