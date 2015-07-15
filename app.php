<?php

require_once('common.inc.php');

/* replace the contents of this file with your own app logic */

$api = new CanvasPest($_SESSION['apiUrl'], $_SESSION['apiToken']);
$profile = $api->get('/users/self/profile');

$smarty->assign('content', "
	<h1>App</h1>
	
	<h2>{$_REQUEST['lti-request']} Request</h3>" .
	(isset($_REQUEST['reason']) ?
		"<p>{$_REQUEST['reason']}</p>" : ''
	) . "
	<h2>GET /users/self/profile</h3>		
	<pre>" . print_r($profile, true) . '</pre>'
);

$smarty->display();

?>