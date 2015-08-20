<?php

/* some sample app metadata information -- review config.xml for a panoply of options */
$metadata['APP_DESCRIPTION'] = 'A starter app for building an LTI that makes use of the Canvas APIs.';
$metadata['APP_DOMAIN'] = '';
$metadata['APP_ICON_URL'] = '@APP_URL/lti/icon.png';
$metadata['APP_LAUNCH_URL'] = '@APP_URL/lti/launch.php';
$metadata['APP_PRIVACY_LEVEL'] = 'public'; # /public|name_only|anonymous/
$metadata['APP_CONFIG_URL'] = '@APP_URL/lti/config.xml';
$metadata['ACCOUNT_NAVIGATION'] = false; # is_bool()
$metadata['COURSE_NAVIGATION'] = true; # is_bool()
$metadata['COURSE_NAVIGATION_DEFAULT'] = 'enabled'; # /enabled|disabled/
$metadata['COURSE_NAVIGATION_ENABLED'] = 'true'; # /true|false/
$metadata['COURSE_NAVIGATION_ICON_URL'] = '@APP_ICON_URL';
$metadata['COURSE_NAVIGATION_LAUNCH_URL'] = '@APP_LAUNCH_URL';
$metadata['COURSE_NAVIGATION_LINK_TEXT'] = '@APP_NAME';
$metadata['COURSE_NAVIGATION_VISIBILITY'] = 'public'; # /public|members|admins/
$metadata['CUSTOM_FIELDS'] = true; # is_bool()
$metadata['CUSTOM_FIELD_debug'] = 'true'; # /true|false/
$metadata['EDITOR_BUTTON'] = false; # is_bool()
$metadata['HOMEWORK_SUBMISSION'] = false; # is_bool()
$metadata['RESOURCE_SELECTION'] = false; # is_bool()
$metadata['USER_NAVIGATION'] = false; # is_bool()

$metadata['GLOBAL_JAVASCRIPT_URL'] = '@APP_URL/canvashacks.js.php';
$metadata['GLOBAL_CSS_URL'] = '@APP_URL/canvashacks.css.php';

$smarty->addMessage(
	'App metadata updated',
	'Application metadata has been updated to create config.xml',
	NotificationMessage::GOOD
);

$metadata['APP_LOG_FILE'] = '@APP_PATH/@APP_ID.log';
$log = Log::singleton('file', $metadata['APP_LOG_FILE']);
$log->log('Installed');

use smtech\CanvasHack\CanvasHackScanner;
$metadata['CRON_SCHEDULE'] = '*/15 * * * *'; // every 15 minutes
$metadata['CRON_SCRIPT'] = '@APP_PATH/cron.php';
$cron = new CanvasHackScanner(
	$metadata['APP_ID'],
	$metadata['CRON_SCRIPT'],
	$metadata['CRON_SCHEDULE'],
	$metadata['APP_LOG_FILE']
);

$smarty->addMessage(
	'CanvasHack Scanner',
	'The CanvasHack scanner has been scheduled to rescan the CanvasHacks every fifteen minutes.',
	NotificationMessage::GOOD
);

?>