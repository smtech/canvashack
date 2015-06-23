<?php

require_once('vendor/autoload.php');
require_once('common.inc.php');

/* clear any existing session data */
session_start();
$_SESSION = array();
session_destroy();

/* set up a Tool Provider (TP) object to process the LTI request */
$toolProvider = new CanvasAPIviaLTI(LTI_Data_Connector::getDataConnector($sql));

/* process the LTI request from the Tool Consumer (TC) */
$toolProvider->handle_request();

/* ain't nothin' gonna happen here -- handle_request() will redirect to another page! */

?>