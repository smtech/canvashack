<?php

require_once('vendor/autoload.php');

define('SECRETS_FILE', __DIR__ . '/secrets.xml');
define('SCHEMA_FILE', __DIR__ . '/admin/schema-app.sql');
define('MYSQL_PREFIX', '');

use Battis\AppMetadata as AppMetadata;

/**
 * Test if the app is in the middle of launching
 *
 * Wait for $toolProvider to be fully initialized before starting the app logic.
 *
 * @return boolean
 **/
function midLaunch() {
	return strpos($_SERVER['REQUEST_URI'], '/lti/launch.php') !== false;
}

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
 * @uses initSecrets() If $secrets is not already initialized
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
	
	/* turn off warnings, since we're going to test the connection ourselves */
	set_error_handler(function() {});
	$sql = new mysqli(
		(string) $secrets->mysql->host,
		(string) $secrets->mysql->username,
		(string) $secrets->mysql->password,
		(string) $secrets->mysql->database
	);
	restore_error_handler();
	
	if ($sql->connect_error) {
		throw new CanvasAPIviaLTI_Exception(
			$sql->connect_error,
			CanvasAPIviaLTI_Exception::MYSQL_CONNECTION
		);
	}
	return $sql;
}

function initAppMetadata() {
	global $secrets;
	global $sql;
	
	$metadata = new AppMetadata($sql, (string) $secrets->app->id);
	
	return $metadata;
}

/*****************************************************************************
 *                                                                           *
 * The script begins here                                                    *
 *                                                                           *
 *****************************************************************************/

/* assume everything's going to be fine... */
$ready = true;
$reason = null; // the reason we're _not_ ready

/* preliminary interactive only initialization */
if (php_sapi_name() != 'cli') {
	session_start(); 

	/* fire up the templating engine for interactive scripts */
	$smarty = StMarksSmarty::getSmarty(true, __DIR__ . '/templates');
}

/* initialization that needs to happen for interactive and CLI scripts */
try {
	/* initialize global variables */
	$secrets = initSecrets();
	$sql = initMySql();
	$metadata = initAppMetadata();
} catch (CanvasAPIviaLTI_Exception $e) {
	$ready = false;
	$reason = $e;
}

/* interactive initialization only */
if ($ready && php_sapi_name() != 'cli') {
	try {
		if (isset($_SESSION['toolProvider'])) {
			$toolProvider = $_SESSION['toolProvider'];
		} else {
			if (!midLaunch()) {
				throw new CanvasAPIviaLTI_Exception(
					'The LTI launch request is missing',
					CanvasAPIviaLTI_Exception::LAUNCH_REQUEST
				);
			}
		}
		
	} catch (CanvasAPIviaLTI_Exception $e) {
		$ready = false;
	}

	if ($ready) {
		$smarty->addStylesheet($metadata['APP_URL'] . '/stylesheets/canvas-api-via-lti.css', 'starter-canvas-api-via-lti');
		$smarty->addStylesheet($metadata['APP_URL'] . '/stylesheets/app.css');
		
		if (!midLaunch()) {
			require_once('common-app.inc.php');
			$smarty->assign('ltiUser', $toolProvider->user);
		}
	}
}


?>