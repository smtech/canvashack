<?php

class NotificationMessage {
	
	private static $messages = array();
	
	private $title= null;
	private $content = null;
	private $class = 'message';
	
	public function __construct($title, $content, $class = 'message') {
		$this->title = $title;
		$this->content = $content;
		$this->class = $class;
		
		self::$messages[] = $this;
	}
	
	public static function addMessage($title, $content, $class = 'message') {
		new NotificationMessage($title, $content, $class);
	}
	
	public static function waiting() {
		return count(self::$messages);
	}
	
	public static function getMessages() {
		return self::$messages;
	}
}
	
?>