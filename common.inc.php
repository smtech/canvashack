<?php

require_once __DIR__ . '/vendor/autoload.php';

use smtech\CanvasHack\Toolbox;
use smtech\ReflexiveCanvasLTI\LTI\ToolProvider;
use Battis\DataUtilities;

define('CONFIG_FILE', __DIR__ . '/config.xml');
define('CANVAS_INSTANCE_URL', 'canvasInstanceUrl');

@session_start(); // TODO I don't feel good about suppressing warnings

/* prepare the toolbox */
if (empty($_SESSION[Toolbox::class])) {
    $_SESSION[Toolbox::class] =& Toolbox::fromConfiguration(CONFIG_FILE);
}
$toolbox =& $_SESSION[Toolbox::class];
$toolbox->smarty_prependTemplateDir(__DIR__ . '/templates', basename(__DIR__));
$toolbox->smarty_assign([
    'category' => DataUtilities::titleCase(preg_replace('/[\-_]+/', ' ', basename(__DIR__)))
]);

/*
 * FIXME convience variables until plugins are all updated
 */
 $api =& $toolbox->getAPI();
 $sql =& $toolbox->getMySQL();
 $smarty =& $toolbox->getSmarty();
 $customPrefs =& $toolbox->getCustomPrefs();

/* set the Tool Consumer's instance URL, if present */
if (empty($_SESSION[CANVAS_INSTANCE_URL]) &&
    !empty($_SESSION[ToolProvider::class]['canvas']['api_domain'])
) {
    $_SESSION[CANVAS_INSTANCE_URL] = 'https://' . $_SESSION[ToolProvider::class]['canvas']['api_domain'];
}
