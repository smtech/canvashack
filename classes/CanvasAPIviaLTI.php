<?php

/**
 * A Tool Provider that can handle LTI requests
 *
 * @author Seth Battis <SethBattis@stmarksschool.org>
 **/	
class CanvasAPIviaLTI extends LTI_Tool_Provider {
	
	/**
	 * Handle launch requests, which start the application running
	 **/
	public function onLaunch() {
		global $metadata; // FIXME grown-ups don't program like this
		global $sql; // FIXME grown-ups don't program like this
				
		/* is this user in a role that can use this app? */
		if ($this->user->isAdmin()) {
			
			/* set up any needed session variables */
	        $_SESSION['consumer_key'] = $this->consumer->getKey();
	        $_SESSION['resource_id'] = $this->resource_link->getId();
	        $_SESSION['user_consumer_key'] = $this->user->getResourceLink()->getConsumer()->getKey();
	        $_SESSION['user_id'] = $this->user->getId();
	        $_SESSION['isStudent'] = $this->user->isLearner();
	        $_SESSION['isContentItem'] = FALSE;	   
			
			/* do we have an admin API access token? */
			$haveToken = true;
			if (empty($metadata['CANVAS_API_TOKEN'])) {
				
				/* ...if not, do we have a user API access token for this user? */
				$userToken = new UserAPIToken($_SESSION['user_consumer_key'], $_SESSION['user_id'], $sql);			
				if (empty($userToken->getToken())) {
					
					/* ...if this user has no token, let's start by getting one */
					$haveToken = false;
					$this->redirectURL = "{$metadata['APP_URL']}/lti/token_request.php?oauth=request";
				} else {
					
					/* ...but if the user does have a token, rock on! */
					$_SESSION['isUserToken'] = true;
					$_SESSION['apiToken'] = $userToken->getToken();
					//$_SESSION['apiUrl'] = $userToken->getAPIUrl();
				}
			} else {
				
				/* ...if we have an admin API token, rock on! */
				$_SESSION['isUserToken'] = false;
				$_SESSION['apiToken'] = $metadata['CANVAS_API_TOKEN'];
				//$_SESSION['apiUrl'] = $metadata['CANVAS_API_URL'];
			}
			$_SESSION['apiUrl'] = 'https://' . $this->user->getResourceLink()->settings['custom_canvas_api_domain'] . '/api/v1';
			
	        /* pass control off to the app */
	        if ($haveToken) {
		        $this->redirectURL = "{$metadata['APP_URL']}/app.php?lti-request=launch";
	        }

		/* ...otherwise set an appropriate error message and fail */
		} else {
			$this->reason = 'Invalid role';
			$this->isOK = false;
		}
	}
	
	/**
	 * Handle errors created while processing the LTI request
	 **/
	public function onError() {
		global $metadata; // FIXME grown-ups don't program like this
		
		$this->redirectURL = "{$metadata['APP_URL']}/app.php?lti-request=error&reason={$this->reason}";
	}
	
	/**
	 * Handle dashboard requests (coming in LTI v2.0, I guess)
	 **/
	public function onDashboard() {
		global $metadata; // FIXME grown-ups don't program like this
		
		$this->redirectURL = "{$metadata['APP_URL']}/app.php?lti-request=dashboard";
	}
	
	/**
	 * Handle configure requests (coming in LTI v2.0, I guess)
	 **/
	public function onConfigure() {
		global $metadata; // FIXME grown-ups don't program like this
		
		$this->redirectURL = "{$metadata['APP_URL']}/app.php?lti-request=configure";
	}
	
	/**
	 * Handle content-item requests (that is we're a tool provider that adds a button in the content editor)
	 **/
	public function onContentItem() {
		global $metadata; // FIXME grown-ups don't program like this
		
		$this->redirectURL = "{$metadata['APP_URL']}/app.php?lti-request=content-item";
	}
}

/**
 * Exceptions thrown by CanvasAPIviaLTI
 *
 * @author Seth Battis <SethBattis@stmarksschool.org>
 **/
class CanvasAPIviaLTI_Exception extends Exception {
	const MISSING_SECRETS_FILE = 1;
	const INVALID_SECRETS_FILE = 2;
	const MYSQL_CONNECTION = 3;
	const LAUNCH_REQUEST = 4;
}

?>