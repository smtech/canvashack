<?php

require_once('common.inc.php');

use smtech\CanvasHack\CanvasHackScanner;

$cron = new CanvasHackScanner(
	$metadata['APP_ID'],
	$metadata['CRON_SCRIPT'],
	$metadata['CRON_SCHEDULE'],
	$metadata['APP_LOG_FILE']
);
$cron->scheduledJob();
	
?>