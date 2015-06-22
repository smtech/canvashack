<?php

require_once('common.inc.php');

class CanvasAPIviaLTI extends LTI_Tool_Provider {
	
	/**
	 * Handle all basic-lti-launch-requests
	 **/
	public function onLaunch() {
		global $metadata;
		
		/* check permissions  in some appropriate manner */
		if ($this->user->isLearner() || $this->user->isStaff()) {
			
			/* set up any needed session variables */
	        $_SESSION['consumer_key'] = $this->consumer->getKey();
	        $_SESSION['resource_id'] = $this->resource_link->getId();
	        $_SESSION['user_consumer_key'] = $this->user->getResourceLink()->getConsumer()->getKey();
	        $_SESSION['user_id'] = $this->user->getId();
	        $_SESSION['isStudent'] = $this->user->isLearner();
	        $_SESSION['isContentItem'] = FALSE;
	        
	        /* pass controll off to the app */
	        $this->redirectURL = "{$metadata['APP_URL']}/app.php";

		/* ...otherwise set an appropriate error message and fail */
		} else {
			$this->reason = 'Invalid role';
			$this->isOK = false;
		}
	}
	
	public function onError() {
		global $metadata;
		
		$this->redirectURL = "{$metadata['APP_URL']}/error.php";
	}
	
	public function onDashboard() {
		global $metadata;
		
		$this->redirectURL = "{$metadata['APP_URL']}/dashboard.php";
	}
	
	public function onConfigure() {
		global $metadata;
		
		$this->redirectURL = "{$metadata['APP_URL']}/configure.php";
	}
	
	public function onContentItem() {
		global $metadata;
		
		$this->redirectURL = "{$metadata['APP_URL']}/content_item.php";
	}
}

/**
 * CanvasAPIviaLTI Exceptions
 **/
class CanvasAPIviaLTI_Exception extends Exception {
	const MISSING_SECRETS_FILE = 1;
}

?>