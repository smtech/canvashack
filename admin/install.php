<?php

require_once('common.inc.php');

use Battis\AppMetadata as AppMetadata;
use Battis\BootstrapSmarty\NotificationMessage;

/* test if we already have a working install... */
if ($ready && (!isset($_REQUEST['step']))) {
	$smarty->addMessage(
		'App already installed',
		'It appears that the application has already been installed and is ready for
		 use.'
	);
	
/* ...otherwise, let's start with the SECRETS_FILE */
} else {
	if(!file_exists(SECRETS_FILE)) {
		if (isset($_REQUEST['step']) && $_REQUEST['step'] == CanvasAPIviaLTI_Installer::SECRETS_ENTERED_STEP) {
			CanvasAPIviaLTI_Installer::createSecretsFile(CanvasAPIviaLTI_Installer::SECRETS_ENTERED_STEP);
		} else {
			CanvasAPIviaLTI_Installer::createSecretsFile();
		}
	}
}

/* establish our database connection */
$secrets = initSecrets();
$sql = initMySql();

try {	
	if (!isset($_REQUEST['step'])) {
		/* load all of our various schema into the database... */
		CanvasAPIviaLTI_Installer::createLTIDatabaseTables();
		CanvasAPIviaLTI_Installer::createAppDatabaseTables();
		
		/* ...and initialize the app metadata... */
		$metadata = CanvasAPIviaLTI_Installer::createAppMetadata();

		/* ...optionally, acquire an API token for the app */
		CanvasAPIviaLTI_Installer::acquireAPIToken(CanvasAPIviaLTI_Installer::API_DECISION_NEEDED_STEP);
	} else {
		$metadata = new AppMetadata($sql, $secrets->app->id);
		$skip = (isset($_REQUEST['skip']) ? $_REQUEST['skip'] : false);
		CanvasAPIviaLTI_Installer::acquireAPIToken($_REQUEST['step'], $skip);
	}
} catch (CanvasAPIviaLTI_Installer_Exception $e) {
	$smarty->addMessage(
		'LTI Installer error',
		$e->getMessage() . ' [Error ' . $e->getCode() . ']',
		NotificationMessage::ERROR
	);
	$smarty->display();
	exit;
}

try {
	/* any additional app-specific install steps */
	require_once('install-app.inc.php');
} catch (CanvasAPIviaLTI_Installer_Exception $e) {
	$smarty->addMessage(
		'App Installer error',
		$e->getMessage() . ' [Error ' . $e->getCode() . ']',
		NotificationMessage::ERROR
	);
}

/* reset $metadata to get update any computed values */
$metadata = initAppMetadata();

$smarty->assign('content', '
	<h1>Installation complete</h1>
	<p>The application installation is complete. You may configure LTI Tool Consumer (TC) information by navigating to <a href="consumers.php">Consumers</a>.</p>'
);

$smarty->display();
	
?>