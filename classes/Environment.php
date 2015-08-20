<?php

/** Environment and related classes */

namespace smtech\LTI;

/**
 * A container for LTI environemnt information
 *
 * @author Seth Battis <SethBattis@stmarksschool.org>
 **/
class Environment {
	private $user;
	private $course;
	private $account;
	private $placement;
	private $api;
	private $sql;
	private $metadata;
}

/**
 * All exceptions thrown by Environment
 *
 * @author Seth Battis <SethBattis@stmarksschool.org>
 **/
class Environment_Exception extends \Exception {}
	
?>