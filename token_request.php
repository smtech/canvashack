<?php

require_once('common.inc.php');

try {
	$oauth = new OAuthNegotiator();
} catch (OAuthNegotiator_Exception $e) {}

if (isset($_REQUEST['oauth'])) {
	switch ($_REQUEST['oauth']) {
		case 'request': {
			echo '
<html>
	<body>
		<h1>Token Request</h1>
		<p>This application requires access to the Canvas APIs. Canvas is about to ask you to give permission for this.</p>
		<p><a href="' . $_SERVER['PHP_SELF'] . '?oauth=process">Click to continue</a></p>
	</body>
</html>';
			exit;
		}
		case 'process': {
			$oauth = new OAuthNegotiator(
				"{$metadata['CANVAS_INSTANCE_URL']}/login/oauth2",
				(string) $secrets->oauth->id,
				(string) $secrets->oauth->key,
				"{$_SERVER['PHP_SELF']}?oauth=complete",
				(string) $secrets->app->name
			);
			break;
		}
		case 'complete': {
			$user = new UserAPIToken($_SESSION['user_consumer_key'], $_SESSION['user_id'], $sql);
			$user->setToken($oauth->getToken());
			$user->setAPIEndpoint("{$metadata['CANVAS_INSTANCE_URL']}/api/v1");
			
			$_SESSION['apiToken'] = $user->getToken();
			$_SESSION['apiEndpoint'] = $user->getAPIEndpoint();
			$_SESSION['isUserToken'] = true;
			
			header("Location: {$metadata['APP_URL']}/app.php");
			exit;
		}
	}
}

?>