<?php

require_once('common.inc.php');

header('Content-Type: application/javascript');

if (!empty($_REQUEST['download'])) {
    header("Content-Disposition: attachment; filename=canvashack-loader.js");
}

?>
function loadJQuery() {
    var cdn = document.createElement('script');
    cdn.setAttribute('src', 'https://code.jquery.com/jquery-1.7.2.min.js');
    cdn.setAttribute('crossorigin', 'anonymous');
    document.getElementsByTagName('body')[0].appendChild(cdn);
}

function loadCanvasHack() {
    var args = {
      current_user: ENV.current_user,
      current_user_roles: ENV.current_user_roles,
      location: window.location.href
    };

    $('head').append('<link id="canvashack-dynamic-css" rel="stylesheet" href="<?= $toolbox->config('APP_URL') ?>/canvashack.css.php?' + $.param(args) + '" />');
    $.getScript('<?= $toolbox->config('APP_URL') ?>/canvashack.js.php?' + $.param(args));
}

function checkJQuery() {
    if (window.jQuery !== undefined) {
        loadCanvasHack();
    } else {
        window.setTimeout(checkJQuery, 25);
    }
}

if (window.jQuery === undefined) {
    loadJQuery();
}
checkJQuery();
