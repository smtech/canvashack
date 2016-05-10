<?php

define('IGNORE_LTI', true);
require_once('common.inc.php');

use \Battis\AppMetadata;

function canonicalNamespaceId($id) {
	return preg_replace('/[^a-z0-9]+/i', '_', $id);
}

function canvasHackNamespace($id, $javascript) {
	return preg_replace('/^(\s*var\s+)?canvashack\s*=\s*{\n*(.*)};/is', canonicalNamespaceId($id) . ": {\n$2\n}", $javascript);
}

header("Content-Type: text/css");
header("X-Content-Type-Options: nosniff"); // trying to settle IE's hash
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$response = $sql->query("
	SELECT css.*
		FROM `css` AS css
		INNER JOIN `canvashacks` AS c
			ON c.`id` = css.`canvashack`
		WHERE
			c.`enabled` = TRUE
");

$css = array();
while ($entry = $response->fetch_assoc()) {
	$css[$entry['canvashack']] = shell_exec("php {$entry['path']}");
}

foreach ($css as $id => $stylesheet) {
	$plugin = new AppMetadata($sql, $id);
	echo "/* CanvasHack ID $id begin */\n";
	echo $plugin->derivedValues($stylesheet);
	echo "\n/* CanvasHack ID $id end */\n\n";
}

?>
