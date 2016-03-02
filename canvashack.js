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
header("X-Content-Type-Options: nosniff"); // Oh, IE, you dog...
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$canvashacks = array();
$response = $sql->query("
	SELECT p.*
		FROM `pages` AS p
		INNER JOIN `canvashacks` AS c
			ON c.`id` = p.`canvashack`
		WHERE
			c.`enabled` = TRUE
		ORDER BY
			p.`include` DESC
");
while ($page = $response->fetch_assoc()) {
	if (
		(
			!empty($page['url']) &&
			$page['url'] == $_SERVER['HTTP_REFERER']
		) || (
			!empty($page['pattern']) &&
			preg_match($page['pattern'], $_SERVER['HTTP_REFERER'])
		)
	) {
		if ($page['include']) {
			$canvashacks[$page['canvashack']] = true;
		} else {
			unset($canvashacks[$page['canvashack']]);
		}
	}
}

$dom = array();
if (($response = $sql->query("
	SELECT *
		FROM `dom`
		WHERE
			`canvashack` = '" . implode("' OR `canvashack` = '", array_keys($canvashacks)) . "'
")) == false) {
	exit;
}
while ($entry = $response->fetch_assoc()) {
	$dom[$entry['canvashack']] = "$('{$entry['selector']}').{$entry['event']}(" . (empty($entry['action']) ? '' : "this." . canonicalNamespaceId($entry['canvashack']) . ".{$entry['action']}") . ");";
}

$javascript = array('go' => 'go: function() {
	' . implode("\n\t", $dom) . '
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
	$javascript[$entry['canvashack']] = canvasHackNamespace($entry['canvashack'], shell_exec("php {$entry['path']}"));
}

?>
"use strict";
var canvashack = {
	
<?= implode(",\n\n", $javascript) ?>


};

canvashack.go();