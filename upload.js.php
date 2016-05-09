<?php

require_once('common.inc.php');

header('Content-Type: application/javascript');
header("Content-Disposition: attachment; filename=upload-to-canvas.js");

?>
$('body').append('<script id="canvashack-loader" src="<?= $metadata['APP_URL'] ?>/canvashack.js?location=' + window.location.href + '&user=' .  . '"></script');
