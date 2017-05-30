<?php

define('IGNORE_LTI', true);

require_once __DIR__ . '/../common.inc.php';

if (empty($_REQUEST) && !empty($argv[1])) {
    $_REQUEST = unserialize($argv[1]);
}
