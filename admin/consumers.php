<html>
	<head>
		<title>LTI Consumers</title>
	</head>
	<style type="text/css">
		.open-record {
			color: #ddd;
		}
	</style>
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
	if (empty($_name = trim($_REQUEST['name']))) {
		$valid = false;
		$message .= 'Consumer name must not be empty. ';
	}
	if (empty($_key = trim($_REQUEST['key']))) {
		$valid = false;
		$message .= 'Consumer key must not be empty. ';
	}
	if (empty(trim($_REQUEST['secret']))) { // secret may contain intentional whitespace -- leave untrimmed
		$valid = false;
		$message .= 'Shared secret must not be empty. ';
	}
	
	if ($valid) {
		$consumer = new LTI_Tool_Consumer($_key, LTI_Data_Connector::getDataConnector($sql));
		$consumer->name = $_name;
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
	$consumer = new LTI_Tool_Consumer($_REQUEST['consumer_key'], LTI_Data_Connector::getDataConnector($sql));
	if (isset($_REQUEST['action']))
		switch ($_REQUEST['action']) {
			case 'delete': {
				$consumer->delete();
				break;
			}
			case 'select': {
				$name = $consumer->name;
				$key = $consumer->getKey();
				$secret = $consumer->secret;
				$enabled = $consumer->enabled;
				break;
			}
			case 'update':
			case 'insert':
			default: {
				// leave default form values set
			}
		}
}

/* display a list of consumers */
$response = $sql->query("SELECT * FROM `" . LTI_Data_Connector::CONSUMER_TABLE_NAME . "` ORDER BY `name` ASC, `consumer_key` ASC");
$consumer = $response->fetch_assoc();
	
if ($consumer) {
	echo '<table><tr>';
	foreach (array_keys($consumer) as $field) {
		echo "<th>$field</th>";
	}
	echo '</tr>';
	
	do {
		$closed = !isset($_REQUEST['consumer_key']) || (isset($_REQUEST['consumer_key']) && $_REQUEST['consumer_key'] != $consumer['consumer_key']);
		echo '<tr' . ($closed ? '' : ' class="open-record"') . '>';
		foreach ($consumer as $field) {
			echo "<td>$field</td>";
		}
		if ($closed) {
			echo '<td><form action="' . $_SERVER['PHP_SELF'] . '" method="post"><input type="hidden" name="consumer_key" value="' . $consumer['consumer_key'] . '" /><input type="hidden" name="action" value="select" /><input type="submit" value="Edit" /></form></td>';
			echo '<td><form action="' . $_SERVER['PHP_SELF'] . '" method="post"><input type="hidden" name="consumer_key" value="' . $consumer['consumer_key'] . '" /><input type="hidden" name="action" value="delete" /><input type="submit" value="Delete" /></form></td>';
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
			<input type="hidden" name="action" value="<?= (!empty($name) ? 'update' : 'insert') ?>" />
			<input type="submit" value="<?= (!empty($name) ? 'Update' : 'Add') ?> Consumer" />
		</form>
		<?php if (!empty($name)): ?>
		<form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
			<input type="hidden" name="consumer_key" value="<?= $key ?>" />
			<input type="hidden" name="action" value="delete" />
			<input type="submit" value="Delete" />
		</form>
		<?php endif; ?>
		<input type="button" value="Cancel" onclick="window.location.href='<?= $_SERVER['PHP_SELF'] ?>';" />
		
		<p>To install this LTI, users should choose configuration type <em>By URL</em> and provide their consumer key and secret above. They should point their installer at <code><?= $metadata['APP_URL'] ?>/config.xml</code>
	</body>
</html>