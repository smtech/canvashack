<?php

require_once('vendor/autoload.php');

define('SECRETS_FILE', __DIR__ . '/secrets.xml');
define('MYSQL_PREFIX', '');
define('SCHEMA_FILE', __DIR__ . '/schema.sql');

define('SESSION_NAME', 'Canvas API via LTI');

/**
 * Initialize a SimpleXMLElement from the SECRETS_FILE
 * @return SimpleXMLElement
 * @throws CanvasAPIviaLTI_Exception If the SECRETS_FILE cannot be found
 **/
function initSecrets() {
	if (file_exists(SECRETS_FILE)) {
		if ($secrets = simplexml_load_file(SECRETS_FILE)) {
			return $secrets;
		} else {
			throw new CanvasAPIviaLTI_Exception(SECRETS_FILE . " could not be loaded.");
		}
	} else {
		throw new CanvasAPIviaLTI_Exception(SECRETS_FILE . " could not be found.", CanvasAPIviaLTI_Exception::MISSING_SECRETS_FILE);
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

$ready = true;
try {
	/* initialize global variables */
	$secrets = initSecrets();
	$sql = initMySql();
	$metadata = new AppMetadata($sql, $secrets->app->id);
} catch (CanvasAPIviaLTI_Exception $e) {
	$ready = false;
}

?>