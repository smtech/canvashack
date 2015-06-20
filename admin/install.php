<pre><?php

require_once('../vendor/autoload.php');

define('SECRETS_NEEDED_STEP', 0);
define('SECRETS_ENTERED_STEP', 1);

try {
	require_once('../common.inc.php');
} catch (CanvasAPIviaLTI_Exception $e) {
	if ($e->getCode() == SECRETS_FILE_EXCEPTION_CODE) {
		if (isset($_REQUEST['step']) && $_REQUEST['step'] == SECRETS_ENTERED_STEP) {
			createSecretsFile(SECRETS_ENTERED_STEP);
		} else {
			createSecretsFile();
		}
	} else {
		throw new CanvasAPIviaLTI_Exception($e);
	}
}
echo "Secrets file already exists.\n";

/**
 * Generate a SECRETS_FILE from user input.
 * @throws CanvasAPIviaLTI_Exception If form submission does not contain all required MySQL credentals (host, username, password and database)
 * @throws CanvasAPIviaLTI_Exception If SECRETS_FILE cannot be created
 * @throws CanvasAPIviaLTI_Exception If $step is not a pre-defined *_STEP constant
 **/
function createSecretsFile($step = SECRETS_NEEDED_STEP) {
	switch ($step) {
		case SECRETS_NEEDED_STEP: {
			// FIXME passwords in clear text? oy.
			echo '
				<html>
				<body>
				<form action="' . $_SERVER['PHP_SELF'] . '" method="post">
					<label for="name">App Name <input type="text" name="name" id="name" value="Canvas API via LTI starter" /></label>
					<label for="id">App ID <input type="text" name="id" id="id" value="canvas-lti-via-api-starter" /></label>
					<label for="host">Host <input type="text" name="host" id="host" value="localhost" /></label>
					<label for="username">Username <input type="text" name="username" id="username" /></label>
					<label for="password">Password <input type="password" name="password" id="password" /></label>
					<label for="database">Database <input type="text" name="database" id="database" /></label>
					<input type="hidden" name="step" value="' . SECRETS_ENTERED_STEP . '" />
					<input type="submit" value="Create Secrets File" />
				</form>
				</body>
				</html>
			';
			exit;
		}
		
		case SECRETS_ENTERED_STEP: {
			if (isset($_REQUEST['name']) && isset($_REQUEST['id'])) {
				if (isset($_REQUEST['host']) && isset($_REQUEST['username']) && isset($_REQUEST['password']) && isset($_REQUEST['database'])) {
					$secrets = new SimpleXMLElement('<secrets />');
					$app = $secrets->addChild('app');
					$app->addChild('name', $_REQUEST['name']);
					$app->addChild('id', $_REQUEST['id']);
					$mysql = $secrets->addChild('mysql');
					$mysql->addChild('host', $_REQUEST['host']);
					$mysql->addChild('username', $_REQUEST['username']);
					$mysql->addChild('password', $_REQUEST['password']);
					$mysql->addChild('database', $_REQUEST['database']);
					if ($secrets->asXML(SECRETS_FILE) == false) {
						throw new CanvasAPIviaLTI_Exception("Failed to create " . SECRETS_FILE);
					}
				} else {
					throw new CanvasAPIviaLTI_Exception("Missing a required mysql credential (host, username, password and database all required).");
				}
			echo "Secrets file created.\n";
			} else {
				throw new CanvasAPIviaLTI_Exception("Missing a required app identity (name and id both required).");
			}
			break;
		}
		
		default: {
			throw new CanvasAPIviaLTI_Exception("Unknown step ($step) in SECRETS_FILE creation.");
		}
	}
}

/**
 * Create database tables to back LTI_Tool_Provider
 * @throws CanvasAPIviaLTI_Exception If database schema not found in vendors directory
 * @throws CanvasAPIviaLTI_Excpetion If database tables are not created
 **/
function createLTIDatabaseTables() {
	global $sql;
	
	// FIXME grown-ups probably don't rely on table names alone
	if ($sql->query("SHOW TABLES LIKE 'lti\_%'")->num_rows >= 5) {
		echo "LTI Database tables already exist.\n";
	} elseif (file_exists(APP_PATH . 'vendor/spvsoftwareproducts/LTI_Tool_Provider/lti-tables-mysql.sql')) {
		$tables = explode(";", file_get_contents(APP_PATH . 'vendor/spvsoftwareproducts/LTI_Tool_Provider/lti-tables-mysql.sql'));
		foreach($tables as $table) {
			if (strlen(trim($table))) {
				if (!$sql->query($table)) {
					throw new CanvasAPIviaLTI_Exception("Error creating LTI database tables: {$sql->error}");
				}
			}
		}
		
		echo "LTI database tables created.\n";
	} else {
		throw new CanvasAPIviaLTI_Exception(APP_PATH . 'vendor/spvsoftwareproducts/LTI_Tool_Provider/lti-tables-mysql.sql' . ' not found.');
	}
}

/**
 * Create database tables to back app
 * @throws CanvasAPIviaLTI_Exception If database tables are not created
 **/
function createAppDatabaseTables() {
	global $sql;
	
	if (file_exists(SCHEMA_FILE)) {
		$tables = explode(";", file_get_contents(SCHEMA_FILE));
		foreach ($tables as $table) {
			if (strlen(trim($table))) {
				if (!$sql->query($table)) {
					throw new CanvasAPIviaLTI_Exception("Error creating app database tables: {$sql->error}");
				}
			}
		}
		echo "App database tables created.\n";
	}
}

function initAppMetadata($appId) {
	global $secrets;
	global $sql;
	
	$sql = initMySql();
	
	$metadata = new AppMetadata($sql, $secrets->app->id);
	$metadata->initialize(array(
		'APP_URL' => 'https://' . $_SERVER['SERVER_NAME'] . preg_replace("|^{$_SERVER['DOCUMENT_ROOT']}(.*)admin$|", '$1', __DIR__),
		'APP_PATH' => preg_replace('/admin$/', '', __DIR__)
	));
		
	echo "App metadata initialized.\n";
}

if (isset($_REQUEST['step']) && $_REQUEST['step'] == SECRETS_ENTERED_STEP) {
	createSecretsFile(SECRETS_ENTERED_STEP);
}
$secrets = initSecrets();

$sql = initMySql();
echo "MySQL database connection established.\n";

createLTIDatabaseTables();
createAppDatabaseTables();

initAppMetadata($secrets->app->id);
	
?></pre>