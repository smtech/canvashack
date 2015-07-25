<?php

/* some sample app metadata information -- review config.xml for a panoply of options */
$metadata['ACCOUNT_NAVIGATION'] = 'FALSE'; # /TRUE|FALSE/
$metadata['APP_DESCRIPTION'] = 'A starter app for building an LTI that makes use of the Canvas APIs.';
$metadata['APP_DOMAIN'] = '';
$metadata['APP_ICON_URL'] = '@APP_URL/lti/icon.png';
$metadata['APP_LAUNCH_URL'] = '@APP_URL/lti/launch.php';
$metadata['APP_PRIVACY_LEVEL'] = 'public'; # /public|name_only|anonymous/
$metadata['APP_CONFIG_URL'] = '@APP_URL/lti/config.xml';
$metadata['COURSE_NAVIGATION'] = 'TRUE'; # /TRUE|FALSE/
$metadata['COURSE_NAVIGATION_DEFAULT'] = 'enabled'; # /enabled|disabled/
$metadata['COURSE_NAVIGATION_ENABLED'] = 'true'; # /true|false/
$metadata['COURSE_NAVIGATION_ICON_URL'] = '@APP_ICON_URL';
$metadata['COURSE_NAVIGATION_LAUNCH_URL'] = '@APP_LAUNCH_URL';
$metadata['COURSE_NAVIGATION_LINK_TEXT'] = '@APP_NAME';
$metadata['COURSE_NAVIGATION_VISIBILITY'] = 'public'; # /public|members|admins/
$metadata['CUSTOM_FIELDS'] = 'TRUE'; # /TRUE|FALSE/
$metadata['CUSTOM_FIELD_debug'] = 'true'; # /true|false/
$metadata['EDITOR_BUTTON'] = 'FALSE'; # /TRUE|FALSE/
$metadata['HOMEWORK_SUBMISSION'] = 'FALSE'; # /TRUE|FALSE/
$metadata['RESOURCE_SELECTION'] = 'FALSE'; # /TRUE|FALSE/
$metadata['USER_NAVIGATION'] = 'FALSE'; # /TRUE|FALSE/

$smarty->addMessage(
	'App metadata updated',
	'Application metadata has been updated to create confix.xml',
	NotificationMessage::GOOD
);

?>