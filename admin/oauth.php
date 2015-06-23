<?php

require_once('../vendor/autoload.php');
require_once('../common.inc.php');

if (isset($_REQUEST['url']) && !empty($_REQUEST['url'])) {
	$metadata['CANVAS_INSTANCE_URL'] = $_REQUEST['url'];
}
	
if (isset($_REQUEST['step'])) {
	/* clear any existing session data */
	session_start();
	$_SESSION = array();
	session_destroy();
	session_start();

	$oauth = new OAuthNegotiator(
		"{$metadata['CANVAS_INSTANCE_URL']}/login/oauth2",
		(string) $secrets->oauth->id,
		(string) $secrets->oauth->key,
		$metadata['APP_URL'] . "/admin/install.php?step={$_REQUEST['step']}",
		(string) $secrets->app->name
	);
}

/* OAuthNegotiator will return here periodically and we will just keep re-instantiating it until it finishes */
session_start();
$oauth = new OAuthNegotiator();

?>