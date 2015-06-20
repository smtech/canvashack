<?php

require_once('vendor/autoload.php');

define('SECRETS_FILE', __DIR__ . '/secrets.xml');
define('MYSQL_PREFIX', '');
define('SCHEMA_FILE', __DIR__ . '/schema.sql');

define('SECRETS_FILE_EXCEPTION_CODE', 1);
define('SESSION_NAME', 'Canvas API via LTI');

/**
 * Initialize a SimpleXMLElement from the SECRETS_FILE
 * @return SimpleXMLElement
 * @throws CanvasAPIviaLTI_Exception If the SECRETS_FILE cannot be found
 **/
function initSecrets() {
	if (file_exists(SECRETS_FILE)) {
		return simplexml_load_file(SECRETS_FILE);
	} else {
		throw new CanvasAPIviaLTI_Exception(SECRETS_FILE . " could not be found.", SECRETS_FILE_EXCEPTION_CODE);
	}
}

/**
 * Initialize a mysqli connector using the credentials stored in SECRETS_FILE
 * @return mysqli A valid mysqli connector to the database backing the CanvasAPIviaLTI instance
 * @throws CanvasAPIviaLTI_Exception If a mysqli connection cannot be established
 **/
function initMySql() {
	global $secrets;	
	if (!($secrets instanceof SimpleXMLElement)) {
		$secrets = initSecrets();
	}
	
	$sql = new mysqli($secrets->mysql->host, $secrets->mysql->username, $secrets->mysql->password, $secrets->mysql->database);
	if (!$sql) {
		throw new CanvasAPIviaLTI_Exception("MySQL database connection failed.");
	}
	return $sql;
}

/**
 * Test whether or not the application has been completely installed
 * @return boolean
 **/
function appIsInstalled() {
	global $sql;
	return $sql instanceof mysqli;
}

$secrets = initSecrets();
$sql = initMySql();
$data_connector = LTI_Data_Connector::getDataConnector(MYSQL_PREFIX, $sql);

$metadata = new AppMetadata($sql, $secrets->app->id);

?>