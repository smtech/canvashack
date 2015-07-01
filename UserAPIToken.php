<?php
	
require_once('common.inc.php');

class UserAPIToken {
		
	private $consumerKey = null;
	private $id = null;
	private $sql = null;
	private $token = null;
	private $endpoint = null;
	
	public function __construct($consumerKey, $userId, $mysqli) {
		
		// TODO error checking
		
		$this->consumerKey = $consumerKey;
		$this->id = $userId;
		$this->sql = $mysqli;
		
		$result = $this->sql->query("SELECT * FROM `user_tokens` WHERE `consumer_key` = '$consumerKey' AND `id` = '$userId'");
		$row = $result->fetch_assoc();
		if ($row) {
			$this->token = $row['token'];
			$this->endpoint = $row['api_endpoint'];
		} else {
			$this->sql->query("INSERT INTO `user_tokens` (`consumer_key`, `id`) VALUES ('$consumerKey', '$userId')");
		}
	}
	
	public function getToken() {
		return $this->token;
	}
	
	public function setToken($token) {
		if($this->consumerKey && $this->id && $this->sql) {
			$this->sql->query("UPDATE `user_tokens` set `token` = '$token' WHERE `consumer_key` = '{$this->consumerKey}' AND `id` = '{$this->id}'");
			$this->token = $token;
		}
	}
	
	function getAPIEndpoint() {
		return $this->endpoint;
	}
	
	public function setAPIEndpoint($endpoint) {
		if ($this->consumerKey && $this->id && $this->sql) {
			$this->sql->query("UPDATE `user_tokens` set `api_endpoint` = '$endpoint' WHERE `consumer_key` = '{$this->consumerKey}' AND `id` = '{$this->id}'");
			$this->endpoint = $endpoint;
		}
	}
}

class UserAPIToken_Exception extends Exception {}

?>