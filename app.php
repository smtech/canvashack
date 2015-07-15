<?php

require_once('common.inc.php');

$api = new CanvasPest($_SESSION['apiUrl'], $_SESSION['apiToken']);

/* replace the contents of this file with your own app logic */

$smarty->assign('content', "
	<h1>App</h1>
	
	<h2>{$_REQUEST['lti-request']} Request</h3>" .
	(isset($_REQUEST['reason']) ?
		"<p>{$_REQUEST['reason']}</p>" : ''
	) . "
	<h2>GET /users/self/profile</h3>		
	<pre>" . print_r($api->get('/users/self/profile'), false) . '</pre>'
);

$smarty->display();

?>