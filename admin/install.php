<pre><?php

require_once('../vendor/autoload.php');
require_once('../common.inc.php');

class CanvasAPIviaLTI_Installer {
	const SECRETS_NEEDED_STEP = 0;
	const SECRETS_ENTERED_STEP = 1;
	
	/**
	 * Append another message to the output of the install script.
	 *
	 * TODO maybe some nicer HTML generation
	 *
	 * @param string $message The message to append (HTML formatting is fine).
	 **/
	public static function appendMessage($message) {
		echo "$message\n";
	}
	
	/**
	 * Generate a SECRETS_FILE from user input.
	 * @throws CanvasAPIviaLTI_Exception If form submission does not contain all required MySQL credentals (host, username, password and database)
	 * @throws CanvasAPIviaLTI_Exception If SECRETS_FILE cannot be created
	 * @throws CanvasAPIviaLTI_Exception If $step is not a pre-defined *_STEP constant
	 **/
	public static function createSecretsFile($step = CanvasAPIviaLTI_Installer::SECRETS_NEEDED_STEP) {
		switch ($step) {
			case CanvasAPIviaLTI_Installer::SECRETS_NEEDED_STEP: {
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
						<input type="hidden" name="step" value="' . CanvasAPIviaLTI_Installer::SECRETS_ENTERED_STEP . '" />
						<input type="submit" value="Create Secrets File" />
					</form>
					</body>
					</html>
				';
				exit;
			}
			
			case CanvasAPIviaLTI_Installer::SECRETS_ENTERED_STEP: {
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
							throw new CanvasAPIviaLTI_Exception(
								'Failed to create ' . SECRETS_FILE,
								CanvasAPIviaLTI_Installer_Exception::SECRETS_FILE_CREATION
							);
						}
					} else {
						throw new CanvasAPIviaLTI_Installer_Exception(
							'Missing a required mysql credential (host, username, password and database all required).',
							CanvasAPIviaLTI_Installer_Exception::SECRETS_FILE_MYSQL
						);
					}
				CanvasAPIviaLTI_Installer::appendMessage('Secrets file created.');
				} else {
					throw new CanvasAPIviaLTI_Installer_Exception(
						'Missing a required app identity (name and id both required).',
						CanvasAPIviaLTI_Installer_Exception::SECRETS_FILE_APP
					);
				}
				break;
			}
			
			default: {
				throw new CanvasAPIviaLTI_Installer_Exception(
					"Unknown step ($step) in SECRETS_FILE creation.",
					CanvasAPIviaLTI_Installer_Exception::SECRETS_NEEDED_STEP
				);
			}
		}
	}
	
	/**
	 * Create database tables to back LTI_Tool_Provider
	 * @throws CanvasAPIviaLTI_Exception If database schema not found in vendors directory
	 * @throws CanvasAPIviaLTI_Excpetion If database tables are not created
	 **/
	public static function createLTIDatabaseTables() {
		global $sql;
		
		$ltiSchema = realpath(__DIR__ . '/../vendor/spvsoftwareproducts/LTI_Tool_Provider/lti-tables-mysql.sql');
		
		if ($sql->query("SHOW TABLES LIKE 'lti_%'")->num_rows >= 5) {
			CanvasAPIviaLTI_Installer::appendMessage('LTI database tables already exist');
		} elseif (file_exists($ltiSchema)) {
			$queries = explode(";", file_get_contents($ltiSchema));
			$created = true;
			foreach($queries as $query) {
				if (!empty(trim($query))) {
					if (!$sql->query($query)) {
						throw new CanvasAPIviaLTI_Installer_Exception(
							"Error creating LTI database tables: {$sql->error}",
							CanvasAPIviaLTI_Installer_Exception::LTI_PREPARE_DATABASE
						);
					}
				}
			}
			
			CanvasAPIviaLTI_Installer::appendMessage('LTI database tables created.');
		} else {
			throw new CanvasAPIviaLTI_Exception("$ltiSchema not found.");
		}
	}
	
	/**
	 * Create database tables to back app
	 * @throws CanvasAPIviaLTI_Exception If database tables are not created
	 **/
	public static function createAppDatabaseTables() {
		global $sql;
		
		if (file_exists(SCHEMA_FILE)) {
			$queries = explode(";", file_get_contents(SCHEMA_FILE));
			$created = true;
			foreach ($queries as $query) {
				if (!empty(trim($query))) {
					if (preg_match('/CREATE\s+TABLE\s+(`([^`]+)`|\w+)/i', $query, $tableName)) {
						$tableName = (empty($tableName[2]) ? $tableName[1] : $tableName[2]);
						if ($sql->query("SHOW TABLES LIKE '$tableName'")->num_rows > 0) {
							$created = false;
						} else {
							if (!$sql->query($query)) {
								throw new CanvasAPIviaLTI_Installer_Exception(
									"Error creating app database tables: {$sql->error}",
									CanvasAPIviaLTI_Installer_Exception::APP_CREATE_TABLE
								);
							}
						}
					} else {
						if (!$sql->query($query)) {
							throw new CanvasAPIviaLTI_Installer_Exception(
								"Error creating app database tables: {$sql->error}",
								CanvasAPIviaLTI_Installer_Exception::APP_PREPARE_DATABASE
							);
						}
					}
				}
			}
			
			if ($created) {
				CanvasAPIviaLTI_Installer::appendMessage('App database tables created.');
			} else {
				CanvasAPIviaLTI_Installer::appendMessage('App database tables already exist.');
			}
		}
	}
	
	/**
	 * Initialize the app metadata store, especially the APP_PATH and APP_URL
	 **/
	public static function initAppMetadata() {
		global $secrets;
		global $sql;
		
		if (AppMetadata::prepareDatabase($sql)) {
			CanvasAPIviaLTI_Installer::appendMessage('App metadata database tables created.');
		} else {
			CanvasAPIviaLTI_Installer::appendMessage('App metadata database tables already exist.');
		}
		
		$metadata = new AppMetadata($sql, $secrets->app->id);
		$metadata['APP_PATH'] = preg_replace('/\/admin$/', '', __DIR__);
		$metadata['APP_URL'] = 'https://' . $_SERVER['SERVER_NAME'] . preg_replace("|^{$_SERVER['DOCUMENT_ROOT']}(.*)$|", '$1', $metadata['APP_PATH']);
	
		CanvasAPIviaLTI_Installer::appendMessage('App metadata initialized.');
	}
}


/* test if we already have a working install... */
if ($ready) {
	CanvasAPIviaLTI_Installer::appendMessage('App already installed.');
	
/* ...otherwise, let's start with the SECRETS_FILE */
} else {
	if(!file_exists(SECRETS_FILE)) {
		if (isset($_REQUEST['step']) && $_REQUEST['step'] == CanvasAPIviaLTI_Installer::SECRETS_ENTERED_STEP) {
			CanvasAPIviaLTI_Installer::createSecretsFile(CanvasAPIviaLTI_Installer::SECRETS_ENTERED_STEP);
		} else {
			CanvasAPIviaLTI_Installer::createSecretsFile();
		}
	} else {
		CanvasAPIviaLTI_Installer::appendMessage('Secrets file exists.');
	}
}

class CanvasAPIviaLTI_Installer_Exception extends CanvasAPIviaLTI_Exception {
	const SECRETS_FILE_CREATION = 1;
	const SECRETS_FILE_APP = 2;
	const SECRETS_FILE_MYSQL = 3;
	const LTI_SCHEMA = 4;
	const LTI_PREPARE_DATABASE = 5;
	const LTI_CREATE_TABLE = 6;
	const APP_SCHEMA = 7;
	const APP_PREPARE_DATABASE = 8;
	const APP_CREATE_TABLE = 9;
}

try {
	$secrets = initSecrets();
	
	/* once we have the SECRETS_FILE, establish a database connection... */
	$sql = initMySql();
	CanvasAPIviaLTI_Installer::appendMessage('MySQL database connection established.');
	
	/* ...and load all of our various schema into the database... */
	CanvasAPIviaLTI_Installer::createLTIDatabaseTables();
	CanvasAPIviaLTI_Installer::createAppDatabaseTables();
	
	/* ...and initialize the app metadata */
	CanvasAPIviaLTI_Installer::initAppMetadata();
} catch (CanvasAPIviaLTI_Installer_Exception $e) {
	CanvasAPIviaLTI_Installer::appendMessage($e->getMessage() . ' [Error ' . $e->getCode() . ']');
	exit;
}
CanvasAPIviaLTI_Installer::appendMessage('Installation complete.');
	
?></pre>