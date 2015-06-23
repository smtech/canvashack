<?php

require_once('vendor/autoload.php');

define('SECRETS_FILE', __DIR__ . '/secrets.xml');
define('MYSQL_PREFIX', '');
define('SCHEMA_FILE', __DIR__ . '/schema.sql');

define('SESSION_NAME', 'Canvas API via LTI');

/**
 * Initialize a SimpleXMLElement from the SECRETS_FILE
 *
 * @return SimpleXMLElement
 *
 * @throws CanvasAPIviaLTI_Exception MISSING_SECRETS_FILE if the SECRETS_FILE cannot be found
 * @throws CanvasAPIviaLTI_Exception INVALID_SECRETS_FILE if the SECRETS_FILE exists, but cannot be parsed
 **/
function initSecrets() {
	if (file_exists(SECRETS_FILE)) {
		// http://stackoverflow.com/a/24760909 (oy!)
		if (($secrets = simplexml_load_string(file_get_contents(SECRETS_FILE))) !== false) {
			return $secrets;
		} else {
			throw new CanvasAPIviaLTI_Exception(
				SECRETS_FILE . ' could not be loaded. ',
				CanvasAPIviaLTI_Exception::INVALID_SECRETS_FILE
			);
		}
	} else {
		throw new CanvasAPIviaLTI_Exception(
			SECRETS_FILE . " could not be found.",
			CanvasAPIviaLTI_Exception::MISSING_SECRETS_FILE
		);
	}
}

/**
 * Initialize a mysqli connector using the credentials stored in SECRETS_FILE
 *
 * @return mysqli A valid mysqli connector to the database backing the CanvasAPIviaLTI instance
 *
 * @throws CanvasAPIviaLTI_Exception MYSQL_CONNECTION if a mysqli connection cannot be established
 **/
function initMySql() {
	global $secrets;	
	if (!($secrets instanceof SimpleXMLElement)) {
		$secrets = initSecrets();
	}
	
	$sql = new mysqli(
		(string) $secrets->mysql->host,
		(string) $secrets->mysql->username,
		(string) $secrets->mysql->password,
		(string) $secrets->mysql->database
	);
	if (!$sql) {
		throw new CanvasAPIviaLTI_Exception(
			"MySQL database connection failed.",
			CanvasAPIviaLTI_Exception::MYSQL_CONNECTION
		);
	}
	return $sql;
}

$ready = true;
try {
	/* initialize global variables */
	$secrets = initSecrets();
	$sql = initMySql();
	$metadata = new AppMetadata($sql, (string) $secrets->app->id);
} catch (CanvasAPIviaLTI_Exception $e) {
	$ready = false;
}

?>