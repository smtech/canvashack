<?php

class StMarksSmarty extends Smarty {
	
	public function __construct() {
		parent::__construct();
		$this->setTemplateDir(realpath(__DIR__ . '/../smarty/templates'));
		$this->setCompileDir(realpath(__DIR__ . '/../smarty/templates_c'));
		$this->setConfigDir(realpath(__DIR__ . '/../smarty/configs'));
		$this->setCacheDir(realpath(__DIR__ . '/../smarty/cache'));
		
		// FIXME ...wow. Just... wow.
		$fake_metadata = array(
			'APP_URL' => (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on' ? 'http://' : 'https://') . $_SERVER['SERVER_NAME'] . preg_replace("|^{$_SERVER['DOCUMENT_ROOT']}(.*)$|", '$1', preg_replace('/\/classes$/', '', __DIR__)),
			'APP_NAME' => 'Installing...'
		);
		$this->assign('metadata', $fake_metadata);
	}
	
	public function display($template = 'page.tpl', $cache_id = null, $compile_id = null, $parent = null) {
		$this->assign('messages', NotificationMessage::getMessages());
		parent::display($template, $cache_id, $compile_id, $parent);
	}
}
	
?>