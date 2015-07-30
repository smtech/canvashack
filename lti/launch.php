<?php

require_once('../common.inc.php');

/* clear any existing session data */
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
$_SESSION = array();
session_destroy();
session_start();

/* set up a Tool Provider (TP) object to process the LTI request */
$toolProvider = new CanvasAPIviaLTI(LTI_Data_Connector::getDataConnector($sql));
$toolProvider->setParameterConstraint('oauth_consumer_key', TRUE, 50);
$toolProvider->setParameterConstraint('resource_link_id', TRUE, 50, array('basic-lti-launch-request'));
$toolProvider->setParameterConstraint('user_id', TRUE, 50, array('basic-lti-launch-request'));
$toolProvider->setParameterConstraint('roles', TRUE, NULL, array('basic-lti-launch-request'));

$_SESSION['toolProvider'] = $toolProvider;

/* process the LTI request from the Tool Consumer (TC) */
$toolProvider->handle_request();

?>