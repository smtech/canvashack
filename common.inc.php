<?php

$httpReferer = (empty($_SERVER['HTTP_REFERER']) ? false : 'https://' . parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST));
$requestApiDomain = (empty($_REQUEST['custom_canvas_api_domain']) ? false : $_REQUEST['custom_canvas_api_domain']);


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
if (php_sapi_name() !== 'cli') {
    $toolbox->smarty_prependTemplateDir(__DIR__ . '/templates', basename(__DIR__));
    $toolbox->smarty_assign([
        'category' => DataUtilities::titleCase(preg_replace('/[\-_]+/', ' ', basename(__DIR__)))
    ]);
    $smarty =& $toolbox->getSmarty();
}

/* set the Tool Consumer's instance URL, if present */
if (empty($_SESSION[CANVAS_INSTANCE_URL])) {
    if (!empty($requestApiDomain)) {
        $_SESSION[CANVAS_INSTANCE_URL] = "https://{$requestApiDomain}";
    } elseif (!empty($_SESSION[ToolProvider::class]['canvas']['api_domain'])) {
        $_SESSION[CANVAS_INSTANCE_URL] = 'https://' . $_SESSION[ToolProvider::class]['canvas']['api_domain'];
    } else {
        $_SESSION[CANVAS_INSTANCE_URL] = $httpReferer;

        /* FIXME hack to trick the Toolbox into using the right API domain */
        $_SESSION[ToolProvider::class]['canvas']['api_domain'] = parse_url($httpReferer, PHP_URL_HOST);
    }
}

/*
 * FIXME convience variables until plugins are all updated (must come after the
 * instance URL detection, so that the API URL is set correctly)
 */
$api =& $toolbox->getAPI();
$sql =& $toolbox->getMySQL();
$customPrefs =& $toolbox->getCustomPrefs();
