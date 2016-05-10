<?php

require_once('common.inc.php');

header('Content-Type: application/javascript');

if ($_REQUEST['download']) {
    header("Content-Disposition: attachment; filename=canvashack-loader.js");
}

?>
$('head').append('<link id="canvashack-dynamic-css" rel="stylesheet" href="<?= $metadata['APP_URL'] ?>/canvashack.css.php?location=' + window.location.href + '" />');
$('body').append('<script id="canvashack-loader" src="<?= $metadata['APP_URL'] ?>/canvashack.js.php?location=' + window.location.href + '"></script>');
