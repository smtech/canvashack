<?php

require_once('../vendor/autoload.php');
require_once('../common.inc.php');

if (appIsInstalled()) {
	header('Location: consumers.php');
} else {
	header('Location: install.php');
}
	
?>