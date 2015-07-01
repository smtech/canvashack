<?php

require_once('common.inc.php');

$api = new CanvasPest($_SESSION['apiEndpoint'], $_SESSION['apiToken']);

?>
<html>
	<body>
		<h1>App goes here.</h1>
		
		<pre><?= var_dump($api->get('/users/self/profile')) ?></pre>
	</body>
</html>