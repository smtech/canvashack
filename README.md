# Canvas API via LTI (Starter)

This is a starter template for projects that want to…

  1. Authenticate users' identities
  2. Access the Canvas APIs (either to provide information to the users or _as_ the users)
  3. Embed the presentation layer of the project back into Canvas

This came about as a result of Hack Night at InstructureCon 2015, when it became clear to me that desire to both authenticate users _and_ access the API using OAuth-provided tokens was more than could be done through simple OAuth authentication. (Well, it _can_ be done, but it generates two OAuth tokens in the user's account and causes them to think that they are being logged in twice: once to authenticate their identity (which can be persistent -- "check to remember this login") and once to generate an API key. This seemed confusing, at best.)

### Usage

1. Start by [forking this repository](https://help.github.com/articles/fork-a-repo/).
2. Load your fork on to your favorite LAMP server (ideally including SSL-authentication -- Canvas plays nicer that way, and it's just plain more secure).
2. Be sure to run `composer install` ([Composer rocks](https://getcomposer.org/)) -- this has a few dependencies defined in [composer.json](https://github.com/smtech/starter-canvas-api-via-lti/blob/master/composer.json).
3. Point your browser at `https://<install-url>/admin/` and you will run the install script. You will need to have your MySQL credentials handy, as well as your Canvas developer credentials. Answer whatever questions it asks. This will generate a `secrets.xml` file for you (if you don't want to make one yourself, based on [secrets-example.xml](https://github.com/smtech/starter-canvas-api-via-lti/blob/master/secrets-example.xml)) and it will password-protect the `admin` directory.
  - When you return to `https://<install-url>/admin/` thereafter, you will be redirected to [consumers.php](https://github.com/smtech/starter-canvas-api-via-lti/blob/master/admin/consumers.php) which provides some rudimentary LTI Consumer management for the tool.
  - [metadata-example.xml](https://github.com/smtech/starter-canvas-api-via-lti/blob/master/metadata-example.xml) lists some of the basic `$metadata` keys, along with some default values and the validation regexes (which the [AppMetadata](https://github.com/battis/appmetadata) object may start actually using eventually). A lot of other possible keys are evident in [config.xml](https://github.com/smtech/starter-canvas-api-via-lti/blob/master/config.xml).
4. Modify the CanvasAPIviaLTI class as needed -- most of your app logic can just go into [app.php](https://github.com/smtech/starter-canvas-api-via-lti/blob/master/app.php), which is loaded after a user has been authenticated (via [launch.php](https://github.com/smtech/starter-canvas-api-via-lti/blob/master/launch.php)).
5. Including [common.inc.php](https://github.com/smtech/starter-canvas-api-via-lti/blob/master/common.inc.php) will provide access to several handy global variables (as well as populating the `$_SESSION` variable with relevant information):
  1. `SimpleXMLElement $secrets`, the secrets.xml file.
  2. `mysqli $sql`, a database connection to your MySQL server.
  3. `AppMetadata $metadata`, an associative array bound to the app_metadata table in your database.
6. When it comes time for users to install the api, they can do it 'By Url' using `https://<install-url>/config.xml` and a key and secret from `https://<install-url>/admin/consumers.php`.
	- Nota bene: the root [.htaccess](https://github.com/smtech/starter-canvas-api-via-lti/blob/master/.htaccess) file does some jiggery-pokery to protect `*.inc.php` and `secrets.xml` _and_ it forces [config.xml](https://github.com/smtech/starter-canvas-api-via-lti/blob/master/config.xml) to be treated by Apache as a PHP file.