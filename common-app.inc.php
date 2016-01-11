<?php

$_SESSION['canvasInstanceUrl'] = 'https://' . $_SESSION['toolProvider']->user->getResourceLink()->settings['custom_canvas_api_domain'];
$api = new CanvasPest($_SESSION['apiUrl'], $_SESSION['apiToken']);

/* common functions for your app could go here -- it's automatically included in common.inc.php */
$smarty->assign('name', 'CanvasHack');
$smarty->assign('category', 'Configuration');
$smarty->assign('formLabelWidth', 4);

?>