<?php

require_once('common.inc.php');

use smtech\CanvasPest\CanvasPest;

/* replace the contents of this file with your own app logic */

$api = new CanvasPest($_SESSION['apiUrl'], $_SESSION['apiToken']);
$profile = $api->get('/users/self/profile');

$smarty->assign('content', "
	<h1>Tool Provider Settings</h1>
	<pre>" . print_r($toolProvider->user->getResourceLink()->settings, true) . '</pre>'
);

$smarty->display();

?>