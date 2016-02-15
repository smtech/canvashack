<?php

require_once('common.inc.php');

use \smtech\CanvasHack\CanvasHack;
use \smtech\CanvasHack\CanvasHack_Exception;

if (isset($_REQUEST)) {
	while (list($id, $setting) = each($_REQUEST['hack'])) {
		try{
			$hack = new CanvasHack($sql, $id);
			if ($setting === 'enable') {
				$hack->enable();
			} else {
				$hack->disable();
			}
		} catch (CanvasHack_Exception $e) {
			$smarty->addMessage('Exception ' . $e->getCode(), $e->getMessage(), NotificationMessage::ERROR);
		}
	}
}

$hacksContents = scandir(realpath(__DIR__ . '/hacks'), SCANDIR_SORT_DESCENDING);
$hacks = array();
foreach($hacksContents as $item) {
	if (is_dir($path = realpath(__DIR__ . "/hacks/$item")) && file_exists($manifest = "$path/manifest.xml")) {
		try {
			$hacks[$item] = new CanvasHack($sql, $path);
		} catch (CanvasHack_Exception $e) {
			$smarty->addMessage(
				'CanvasHack Manifest Error ['. $e->getCode() . ']',
				$e->getMessage(),
				NotificationMessage::ERROR
			);
		}
	}
}

$smarty->assign('hacks', $hacks);
$smarty->display('control-panel.tpl');

?>