<html>
	<head>
		<title>LTI Consumers</title>
	</head>
	<body>

<?php

require_once('../vendor/autoload.php');
require_once('../common.inc.php');

/* initialize consumer fields */
$name = '';
$key = hash('md4', time());
$secret = hash('md5', time()); /* no particular reason for these algorithms -- just chose two different, short-ish hashes */
$enabled = true;

/* validate new consumer information and save it */
if (isset($_REQUEST['name']) && isset($_REQUEST['key']) && isset($_REQUEST['secret'])) {
	$valid = true;
	$message = '<strong>Invalid consumer information.</strong> ';
	if (strlen($name = trim($_REQUEST['name'])) == 0) {
		$valid = false;
		$message .= 'Consumer name must not be empty. ';
	}
	if (strlen($key = trim($_REQUEST['key'])) == 0) {
		$valid = false;
		$message .= 'Consumer key must not be empty. ';
	}
	if (strlen($secret = trim($_REQUEST['secret'])) == 0) {
		$valid = false;
		$message .= 'Shared secret must not be empty. ';
	}
	
	if ($valid) {
		$consumer = new LTI_Tool_Consumer($key, $data_connector);
		$consumer->name = $name;
		$consumer->secret = $_REQUEST['secret'];
		$consumer->enabled = isset($_REQUEST['enabled']);
		if (!$consumer->save()) {
			$valid = false;
			$message = "<strong>Consumer could not be saved.</strong> {$sql->error}";
		}
	}
	
	if (!$valid) {
		echo "<p class=\"error\">$message</p>";
	}

/* look up consumer to edit, if requested */
} elseif (isset($_REQUEST['consumer_key'])) {
	$consumer = new LTI_Tool_Consumer($_REQUEST['consumer_key'], $data_connector);
	$name = $consumer->name;
	$key = $consumer->getKey();
	$secret = $consumer->secret;
	$enabled = $consumer->enabled;
}

/* display a list of consumers */
if ($response = $sql->query("SELECT * FROM `" . LTI_Data_Connector::CONSUMER_TABLE_NAME . "` ORDER BY `name` ASC, `consumer_key` ASC")) {
	$consumer = $response->fetch_assoc();
	
	echo '<table><tr>';
	foreach (array_keys($consumer) as $field) {
		echo "<th>$field</th>";
	}
	echo '</tr>';
	
	do {
		echo '<tr>';
		foreach ($consumer as $field) {
			echo "<td>$field</td>";
		}
		if (!isset($_REQUEST['consumer_key']) || (isset($_REQUEST['consumer_key']) && $_REQUEST['consumer_key'] != $consumer['consumer_key'])) {
			echo '<td><form action="' . $_SERVER['PHP_SELF'] . '" method="post"><input type="hidden" name="consumer_key" value="' . $consumer['consumer_key'] . '" /><input type="submit" value="Edit" /></form></td>';
		}
		echo '</tr>';
	} while ($consumer = $response->fetch_assoc());
	echo '</table>';
} else {
	echo '<p>No consumers</p>';
}
	
	/* edit/creation form for consumers */
?>

		<form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
			<label for="name">Name <input type="text" name="name" id="name" value="<?= $name ?>"/></label>
			<label for="key">Key <input type="text" name="key" id="key" value="<?= $key ?>" /></label>
			<label for="secret">Secret <input type="text" name="secret" id="secret" value="<?= $secret ?>" /></label>
			<label for="enabled">Enabled <input type="checkbox" name="enabled" id="enabled" value="1" <?= ($enabled ? 'checked' : '') ?> /></label>
			<input type="submit" value="<?= (strlen($name) ? 'Update' : 'Add') ?> Consumer" />
			<input type="button" value="Cancel" onclick="window.location.href='<?= $_SERVER['PHP_SELF'] ?>';" />
		</form>
		
		<p>To install this LTI, users should choose configuration type <em>By URL</em> and provide their consumer key and secret above. They should point their installer at <code><?= $metadata['APP_URL'] ?>/config.xml</code>
	</body>
</html>