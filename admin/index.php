<?php

require_once('common.inc.php');

if ($ready) {
	header('Location: consumers.php');
} else {
	header('Location: install.php');
}
	
?>