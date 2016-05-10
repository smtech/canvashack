<?php

define('IGNORE_LTI', true);
require_once('common.inc.php');

function canonicalNamespaceId($id) {
	return preg_replace('/[^a-z0-9]+/i', '_', $id);
}

function canvasHackNamespace($id, $javascript) {
	return preg_replace('/^(\s*var\s+)?canvashack\s*=\s*{\n*(.*)};/is', canonicalNamespaceId($id) . ": {\n$2\n}", $javascript);
}

header('Content-Type: application/javascript');

/* don't cache me! */
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

$canvashacks = array();
$enabledPages = $sql->query("
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
	if (
		(!empty($page['url']) && $page['url'] == $_REQUEST['location']) ||
		(!empty($page['pattern']) && preg_match($page['pattern'], $_REQUEST['location']))
	) {
		if ($page['include']) {
			$canvashacks[$page['canvashack']] = true;
		} else {
			unset($canvashacks[$page['canvashack']]);
		}
	}
}

$dom = array();
if (($applicableDOM = $sql->query("
	SELECT *
		FROM `dom`
		WHERE
			`canvashack` = '" . implode("' OR `canvashack` = '", array_keys($canvashacks)) . "'
")) == false) {
	exit;
}
while ($entry = $applicableDOM->fetch_assoc()) {
	$dom[$entry['canvashack']] = "$('{$entry['selector']}').{$entry['event']}(" . (empty($entry['action']) ? '' : "this." . canonicalNamespaceId($entry['canvashack']) . ".{$entry['action']}") . ");";
}

$javascript = array('go' => 'go: function() {
	' . implode(PHP_EOL . "\t", $dom) . '
}');

if (($response = $sql->query("
	SELECT *
		FROM `javascript`
		WHERE
			`canvashack` = '" . implode("' OR `canvashack` = '", array_keys($canvashacks)) . "'
")) == false) {
	exit;
}
while ($entry = $response->fetch_assoc()) {
	$javascript[$entry['canvashack']] = canvasHackNamespace($entry['canvashack'], shell_exec("php {$entry['path']} {$_REQUEST['location']} 2>&1"));
}

?>
"use strict";
var canvashack = {

<?= implode(',' . PHP_EOL . PHP_EOL, $javascript) ?>


};

canvashack.go();
