<?php

header("Content-Type: text/css");

/* don't cache me! */
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

define('IGNORE_LTI', true);

require_once 'common.inc.php';

use \Battis\AppMetadata;

$location = (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_REQUEST['location']);

function canonicalNamespaceId($id)
{
    return preg_replace('/[^a-z0-9]+/i', '_', $id);
}

function canvasHackNamespace($id, $javascript)
{
    return preg_replace(
        '/^(\s*var\s+)?canvashack\s*=\s*{\n*(.*)};/is',
        canonicalNamespaceId($id) . ": {\n$2\n}",
        $javascript
    );
}

$canvashacks = array();
$enabledPages = $toolbox->sql_query("
    SELECT p.*
        FROM `pages` AS p
        INNER JOIN `canvashacks` AS c
            ON c.`id` = p.`canvashack`
        WHERE
            c.`enabled` = TRUE
        ORDER BY
            p.`include` DESC
");

while ($page = $enabledPages->fetch_assoc()) {
    if ((!empty($page['url']) && $page['url'] == $location) ||
        (!empty($page['pattern']) && preg_match($page['pattern'], $location))) {
        if ($page['include']) {
            $canvashacks[$page['canvashack']] = true;
        } else {
            unset($canvashacks[$page['canvashack']]);
        }
    }
}

$css = array();
if (($applicableCSS = $toolbox->sql_query("
    SELECT *
        FROM `css`
        WHERE
            `canvashack` = '" . implode("' OR `canvashack` = '", array_keys($canvashacks)) . "'
")) == false) {
    exit;
}
while ($entry = $applicableCSS->fetch_assoc()) {
    $css[$entry['canvashack']] = shell_exec("php \"{$entry['path']}\" \"{$location}\" 2>&1");
}

foreach ($css as $id => $stylesheet) {
    $plugin = new AppMetadata($toolbox->getMySQL(), $id);
    echo "/* CanvasHack ID $id begin */\n";
    echo $plugin->derivedValues($stylesheet);
    echo "\n/* CanvasHack ID $id end */\n\n";
}
