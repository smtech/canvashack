<?php

require_once('vendor/autoload.php');
require_once('common.inc.php');
	
?>
<html>
	<body>
		<h1>App goes here.</h1>
		<pre><?php foreach ($metadata as $key => $value) echo "'$key' = '$value'\n"; ?></pre>
	</body>
</html>