<?php

use smtech\CanvasPest\CanvasPest;

if (defined('IGNORE_LTI')) {
	$_SESSION['canvasInstanceUrl'] = 'https://' . parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
} else {
	$_SESSION['canvasInstanceUrl'] = 'https://' . $_SESSION['toolProvider']->user->getResourceLink()->settings['custom_canvas_api_domain'];
}

if (isset($_SESSION['apiUrl']) && isset($_SESSION['apiToken'])) {
	$api = new CanvasPest($_SESSION['apiUrl'], $_SESSION['apiToken']);
} else {
	$api = new CanvasPest($metadata['CANVAS_API_URL'], $metadata['CANVAS_API_TOKEN']);
}

/* common functions for your app could go here -- it's automatically included in common.inc.php */
$smarty->assign('name', 'CanvasHack');
$smarty->assign('category', 'Configuration');
$smarty->assign('formLabelWidth', 4);

?>