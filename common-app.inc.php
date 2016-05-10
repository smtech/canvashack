<?php

use smtech\CanvasPest\CanvasPest;

if (php_sapi_name() != 'cli') {
	if (defined('IGNORE_LTI')) {
		$_SESSION['canvasInstanceUrl'] = 'https://' . parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
	} else {
		$_SESSION['canvasInstanceUrl'] = 'https://' . $_SESSION['toolProvider']->user->getResourceLink()->settings['custom_canvas_api_domain'];
	}
}

if (php_sapi_name() != 'cli' && isset($_SESSION['apiUrl']) && isset($_SESSION['apiToken'])) {
	$api = new CanvasPest($_SESSION['apiUrl'], $_SESSION['apiToken']);
} else {
	$api = new CanvasPest($metadata['CANVAS_API_URL'], $metadata['CANVAS_API_TOKEN']);
}

if (php_sapi_name() != 'cli') {
	$smarty->assign('name', 'CanvasHack');
	$smarty->assign('category', 'Configuration');
	$smarty->assign('formLabelWidth', 4);
}

?>
