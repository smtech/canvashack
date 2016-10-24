<?php

define('IGNORE_LTI', true);

require_once __DIR__ . '/../common.inc.php';

if (empty($_REQUEST)) {
    $_REQUEST = unserialize($argv[1]);
}
