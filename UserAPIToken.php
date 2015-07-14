<?php
	
require_once('common.inc.php');

/**
 * Represents a user who either has or is in the process of acquiring an API
 * access token via OAuth
 *
 * @auther Seth Battis <SethBattis@stmarksschool.org>
 **/
class UserAPIToken {

	const USER_TOKENS_TABLE = 'user_tokens';
	
	/**
	 * @var string $consumerKey The unique ID of the tool consumer from whence the user is making requests to the LTI
	 **/	
	private $consumerKey;
	
	/**
	 * @var string $id The unique ID (within the context of a particular Tool Consumer) of a particular user
	 **/
	private $id;
	
	/**
	 * @var mysqli $sql A MySQL connection
	 **/
	private $sql;
	
	/**
	 * @var string|null $token This user's API access token (if acquired) or NULL if not yet acquired
	 **/
	private $token = null;
	
	/**
	 * @var string|null $apiUrl The URL of the API for which this user's token is valid, NULL if no token
	 **/
	private $apiUrl = null;

	/**
	 * Create a new UserAPIToken to either register a new user in the
	 * USER_TOKENS_TABLE or	look up an existing user.
	 *
	 * @param string $consumerKey The unique ID of the Tool Consumer from whence the user is making requests to the LTI
	 * @param string $userId The unique ID of the user within that Tool Consumer
	 * @param mysqli $mysqli An active MySQL database connection to update USER_TOKEN_TABLE
	 *
	 * @throws UserAPIToken_Exception CONSUMER_KEY_REQUIRED If no consumer key is provided
	 * @throws UserAPIToken_Exception USER_ID_REQUIRED If no user ID is provided
	 * @throws UserAPIToken_Exception MYSQLI_REQUIRED If no MySQL database connection is provided
	 * @throws UserAPIToken_Excpetion MYSQLI_ERROR If the user cannot be found or inserted in USER_TOKEN_TABLE
	 **/
	public function __construct($consumerKey, $userId, $mysqli) {
		
		if (empty((string) $consumerKey)) {
			throw new UserAPIToken_Exception(
				'A consumer key is required',
				UserAPIToken_Exception::CONSUMER_KEY_REQUIRED
			);
		}
		
		if (empty((string) $userId)) {
			throw new UserAPIToken_Exception(
				'A user ID is required',
				UserAPIToken_Exception::USER_ID_REQUIRED
			);
		}
		
		if (!($mysqli instanceof mysqli)) {
			throw new UserAPIToken_Exception(
				'A valid mysqli object is required',
				UserAPIToken_Exception::MYSQLI_REQUIRED
			);
		}
		
		$this->sql = $mysqli;
		$this->consumerKey = $this->sql->real_escape_string($consumerKey);
		$this->id = $this->sql->real_escape_string($userId);
				
		$result = $this->sql->query("SELECT * FROM `" . self::USER_TOKENS_TABLE . "` WHERE `consumer_key` = '{$this->consumerKey}' AND `id` = '{$this->id}'");
		$row = $result->fetch_assoc();
		if ($row) {
			$this->token = $row['token'];
			$this->apiUrl = $row['api_url'];
		} else {
			if (!$this->sql->query("INSERT INTO `" . self::USER_TOKENS_TABLE . "` (`consumer_key`, `id`) VALUES ('{$this->consumerKey}', '{$this->id}')")) {
				throw new UserAPIToken_Exception(
					"Error inserting a new user: {$this->sql->error}",
					UserAPIToken_Exception::MYSQLI_ERROR
				);
			}
		}
	}
	
	/**
	 * @return string|boolean The API access token for this user, or FALSE if no token has been acquired
	 **/
	public function getToken() {
		if ($this->token) {
			return $this->token;
		}
		return false;
	}
	
	/**
	 * Stores a new API Token into USER_TOKEN_TABLE for this user
	 *
	 * @param string $token A new API access token for this user
	 *
	 * @return boolean Returns TRUE if the token is successfully stored in USER_TOKEN_TABLE, FALSE otherwise
	 *
	 * @throws UserAPIToken_Exception TOKEN_REQUIRED If no token is provided
	 **/
	public function setToken($token) {
		if (empty($token)) {
			throw new UserAPIToken_Exception(
				'A token is required',
				UserAPIToken_Exception::TOKEN_REQUIRED
			);
		}
		if($this->consumerKey && $this->id && $this->sql) {
			$_token = $this->sql->real_escape_string($token);
			if (!$this->sql->query("UPDATE `" . self::USER_TOKENS_TABLE . "` set `token` = '$_token' WHERE `consumer_key` = '{$this->consumerKey}' AND `id` = '{$this->id}'")) {
				throw new UserAPIToken_Exception(
					"Error updating token: {$this->sql->error}",
					UserAPIToken_Exception::MYSQLI_ERROR
				);
			}
			$this->token = $token;
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * @return string|boolean The URL of the API for which the user's API token is valid, or FALSE if no token has been acquired
	 **/
	function getAPIUrl() {
		if ($this->apiUrl) {
			return $this->apiUrl;
		}
		return false;
	}
	
	/**
	 * Stores a new URL for the API URL for which the user's API access token is valid in USER_TOKEN_TABLE
	 *
	 * @param string $apiUrl The URL of the API
	 *
	 * @return boolean TRUE if the URL of the API is stored in USER_TOKEN_TABLE, FALSE otherwise
	 *
	 * @throws UserAPITokenException API_URL_REQUIRED If no URL is provided
	 **/
	public function setAPIUrl($apiUrl) {
		if (empty($apiUrl)) {
			throw new UserAPIToken_Exception(
				'API URL is required',
				UserAPIToken_Exception::API_URL_REQUIRED
			);
		}
		
		if ($this->consumerKey && $this->id && $this->sql) {
			$_apiUrl = $this->sql->real_escape_string($apiUrl);
			if (!$this->sql->query("UPDATE `" . self::USER_TOKENS_TABLE . "` set `api_url` = '$_apiUrl' WHERE `consumer_key` = '{$this->consumerKey}' AND `id` = '{$this->id}'")) {
				throw new UserAPIToken_Exception(
					"Error updating API URL for user token: {$this->sql->error}",
					UserAPIToken_Exception::MYSQLI_ERROR
				);
			}
			$this->apiUrl = $apiUrl;
			return true;
		}
		return false;
	}
}

/**
 * Exceptions thrown by the UserAPIToken
 *
 * @author Seth Battis <SethBattis@stmarksschool.org>
 **/
class UserAPIToken_Exception extends CanvasAPIviaLTI_Exception {
	const CONSUMER_KEY_REQUIRED = 1;
	const USER_ID_REQUIRED = 2;
	const MYSQLI_REQUIRED = 3;
	const MYSQLI_ERROR = 4;
	const TOKEN_REQUIRED = 5;
	const API_URL_REQUIRED = 6;
}

?>