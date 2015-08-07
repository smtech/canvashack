<?php

require_once('../common.inc.php');

/* save the URL we were given for the OAuth endpoint */
if (isset($_REQUEST['url']) && !empty($_REQUEST['url'])) {
	$metadata['CANVAS_INSTANCE_URL'] = $_REQUEST['url'];
	$metadata['CANVAS_API_URL'] = '@CANVAS_INSTANCE_URL/api/v1';
	
	/* if a token is provided use it, otherwise we will begin the interactive token
	   authorization process */
	if (isset($_REQUEST['token']) && !empty($_REQUEST['token'])) {
		$metadata['CANVAS_API_TOKEN'] = $_REQUEST['token'];
		header("Location: {$metadata['APP_URL']}/admin/install.php?step=" . CanvasAPIviaLTI_Installer::API_TOKEN_PROVIDED_STEP);
		exit;
	}
}

try {
	/* are we at the beginning of the process, so we need to give the OAuthNegotiator as much information as possible? */
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
			"{$metadata['APP_URL']}/admin/install.php?step={$_REQUEST['step']}",
			(string) $secrets->app->name
		);
	}
	
	/* OAuthNegotiator will return here periodically and we will just keep re-instantiating it until it finishes */
	$oauth = new OAuthNegotiator();
} catch (OAuthNegotiator_Exception $e) {
	$smarty->addMessage(
		'OAuthNegotiator error',
		$e->getMessage() . ' [Error ' . $e->getCode() . ']',
		NotificationMessage::ERROR
	);
	$smarty->assign(
		'content',
		'<h1>Install Interrupted</h1>
		<p>An error interrupted the installation process. To restart, <a href="install.php">click here</a>.</p>');
	$smarty->display();
	exit;
}

?>