<?php

require_once('common.inc.php');

header('Content-Type: application/javascript');
header("Content-Disposition: attachment; filename=canvashack-loader.js");

?>
$('body').append('<script id="canvashack-loader" src="<?= $metadata['APP_URL'] ?>/canvashack.js.php?location=' + window.location.href + '"></script');
