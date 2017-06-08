<?php

require_once __DIR__ . '/vendor/autoload.php';

use smtech\CanvasPest\CanvasPest;
use smtech\CanvasHack\Toolbox;
use smtech\ReflexiveCanvasLTI\LTI\ToolProvider;
use Battis\DataUtilities;

define('CONFIG_FILE', __DIR__ . '/config.xml');
define('CANVAS_INSTANCE_URL', 'canvasInstanceUrl');

@session_start(); // TODO I don't feel good about suppressing warnings

/* prepare the toolbox */
if (empty($_SESSION[Toolbox::class])) {
    $_SESSION[Toolbox::class] = Toolbox::fromConfiguration(CONFIG_FILE);
}
$toolbox =& $_SESSION[Toolbox::class];

/* identify the tool's Canvas instance URL */
if (empty($_SESSION[CANVAS_INSTANCE_URL])) {
    if (!empty($_SESSION[ToolProvider::class]['canvas']['api_domain'])) {
        $_SESSION[CANVAS_INSTANCE_URL] =
            'https://' . $_SESSION[ToolProvider::class]['canvas']['api_domain'];
    } elseif (!empty($_SERVER['HTTP_REFERER'])) {
        $_SESSION[CANVAS_INSTANCE_URL] =
            'https://' . parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
    } else {
        $_SESSION[CANVAS_INSTANCE_URL] =
            'https://' . parse_url($toolbox->config(Toolbox::TOOL_CANVAS_API)['url'], PHP_URL_HOST);
    }
}

/* force API configuration based on detected CANVAS_INSTANCE_URL */
$toolbox->setApi(new CanvasPest(
    $_SESSION[CANVAS_INSTANCE_URL] . '/api/v1',
    $toolbox->config(Toolbox::TOOL_CANVAS_API)['token']
));

/* configure templating engine, if we are not a CLI instance */
if (php_sapi_name() !== 'cli') {
    $toolbox->smarty_prependTemplateDir(__DIR__ . '/templates', basename(__DIR__));
    $toolbox->smarty_assign([
        'category' => DataUtilities::titleCase(preg_replace('/[\-_]+/', ' ', basename(__DIR__)))
    ]);

    // FIXME convenience variable
    $smarty =& $toolbox->getSmarty();
}

// FIXME convience variables until plugins are all updated
$api =& $toolbox->getAPI();
$sql =& $toolbox->getMySQL();
$customPrefs =& $toolbox->getCustomPrefs();
