<?php

require_once('common.inc.php');

header('Content-Type: application/javascript');

if (!empty($_REQUEST['download'])) {
    header("Content-Disposition: attachment; filename=canvashack-loader.js");
}

?>
var args = {
	current_user: ENV.current_user,
	current_user_roles: ENV.current_user_roles,
	location: window.location.href
};
$('head').append('<link id="canvashack-dynamic-css" rel="stylesheet" href="<?= $metadata['APP_URL'] ?>/canvashack.css.php?' + $.param(args) + '" />');
$.getScript('<?= $metadata['APP_URL'] ?>/canvashack.js.php?' + $.param(args));
