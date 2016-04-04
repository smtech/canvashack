<?php

use Battis\BootstrapSmarty\NotificationMessage;

/* some sample app metadata information -- review config.xml for a panoply of options */
$metadata['APP_DESCRIPTION'] = 'A suite of Canvas administrative tools to make life easier';
$metadata['APP_DOMAIN'] = '';
$metadata['APP_ICON_URL'] = '@APP_URL/lti/icon.png';
$metadata['APP_LAUNCH_URL'] = '@APP_URL/lti/launch.php';
$metadata['APP_PRIVACY_LEVEL'] = 'public'; # /public|name_only|anonymous/
$metadata['APP_CONFIG_URL'] = '@APP_URL/lti/config.xml';
$metadata['COURSE_NAVIGATION'] = false; # is_bool()
$metadata['ACCOUNT_NAVIGATION'] = true; # is_bool()
$metadata['ACCOUNT_NAVIGATION_ENABLED'] = 'true'; # /true|false/
$metadata['ACCOUNT_NAVIGATION_LAUNCH_URL'] = '@APP_LAUNCH_URL';
$metadata['ACCOUNT_NAVIGATION_LINK_TEXT'] = '@APP_NAME';
$metadata['CUSTOM_FIELDS'] = true; # is_bool()
$metadata['CUSTOM_FIELD_debug'] = 'true'; # /true|false/
$metadata['EDITOR_BUTTON'] = false; # is_bool()
$metadata['HOMEWORK_SUBMISSION'] = false; # is_bool()
$metadata['RESOURCE_SELECTION'] = false; # is_bool()
$metadata['USER_NAVIGATION'] = false; # is_bool()

$metadata['GLOBAL_JAVASCRIPT_URL'] = '@APP_URL/canvashack.js';
$metadata['GLOBAL_CSS_URL'] = '@APP_URL/canvashack.css';

$smarty->addMessage(
	'App metadata updated',
	'Application metadata has been updated to create config.xml',
	NotificationMessage::GOOD
);

$metadata['APP_LOG_FILE'] = '@APP_PATH/@APP_ID.log';
$log = Log::singleton('file', $metadata['APP_LOG_FILE']);
$log->log('Installed');

?>
