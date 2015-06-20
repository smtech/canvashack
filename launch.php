<?php

require_once('vendor/autoload.php');
require_once('common.inc.php');

/* clear any existing session data */
session_name(SESSION_NAME);
session_start();
$_SESSION = array();
session_destroy();

$dataConnector = LTI_Data_Connector::getDataConnector($sql);
$toolProvider = new CanvasAPIviaLTI($dataConnector);
$toolProvider->handle_request();
	
?>