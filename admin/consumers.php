<?php

require_once('common.inc.php');

/* initialize consumer fields */
$name = '';
$key = hash('md4', time());
$secret = hash('md5', time()); /* no particular reason for these algorithms -- just chose two different, short-ish hashes */
$enabled = true;

/* validate new consumer information and save it */
if (isset($_REQUEST['name']) && isset($_REQUEST['key']) && isset($_REQUEST['secret'])) {
	$valid = true;
	$message = 'Invalid consumer information. ';
	if (empty($_name = trim($_REQUEST['name']))) {
		$valid = false;
		$message .= 'Consumer name must not be empty. ';
	}
	if (empty($_key = trim($_REQUEST['key']))) {
		$valid = false;
		$message .= 'Consumer key must not be empty. ';
	}
	if (empty(trim($_REQUEST['secret']))) { // secret may contain intentional whitespace -- leave untrimmed
		$valid = false;
		$message .= 'Shared secret must not be empty. ';
	}
	
	if ($valid) {
		$consumer = new LTI_Tool_Consumer($_key, LTI_Data_Connector::getDataConnector($sql));
		$consumer->name = $_name;
		$consumer->secret = $_REQUEST['secret'];
		$consumer->enabled = isset($_REQUEST['enabled']);
		if (!$consumer->save()) {
			$valid = false;
			$message = "<strong>Consumer could not be saved.</strong> {$sql->error}";
		}
	}
	
	if (!$valid) {
		$smarty->addMessage(
			'Required information missing',
			$message,
			NotificationMessage::ERROR
		);
	}

/* look up consumer to edit, if requested */
} elseif (isset($_REQUEST['consumer_key'])) {
	$consumer = new LTI_Tool_Consumer($_REQUEST['consumer_key'], LTI_Data_Connector::getDataConnector($sql));
	if (isset($_REQUEST['action']))
		switch ($_REQUEST['action']) {
			case 'delete': {
				$consumer->delete();
				break;
			}
			case 'select': {
				$name = $consumer->name;
				$key = $consumer->getKey();
				$secret = $consumer->secret;
				$enabled = $consumer->enabled;
				break;
			}
			case 'update':
			case 'insert':
			default: {
				// leave default form values set
			}
		}
}

/* display a list of consumers */
$response = $sql->query("SELECT * FROM `" . LTI_Data_Connector::CONSUMER_TABLE_NAME . "` ORDER BY `name` ASC, `consumer_key` ASC");
$consumers = array();
while ($consumer = $response->fetch_assoc()) {
	$consumers[] = $consumer;
}
if (!empty($consumers)) {
	$smarty->assign('fields', array_keys($consumers[0]));
}
$smarty->assign('consumers', $consumers);

/* use current values */
$smarty->assign('name', $name);
$smarty->assign('key', $key);
$smarty->assign('secret', $secret);
$smarty->assign('enabled', $enabled);

$smarty->assign('formAction', $_SERVER['PHP_SELF']);
$smarty->assign('requestKey', (isset($_REQUEST['consumer_key']) ? $_REQUEST['consumer_key'] : null));
	
$smarty->display('lti-consumers.tpl');