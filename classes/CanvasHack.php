<?php

/** CanvasHack and related classes */

namespace smtech\CanvasHack;

/**
 * CanvasHack
 *
 * @author Seth Battis <SethBattis@stmarksschool.org>
 **/
class CanvasHack {
	
	private $xml;
	
	/**
	 * Construct a CanvasHack
	 *
	 * @param string $manifest Path to canvashack.xml manifest file
	 * @param \smtech\LTI\Environment $environment
	 **/
	public function __construct($manifest, $environment) {
		if (file_exists($manifest)) {
			$this->xml = simplexml_load_string(file_get_contents($manifest));
		} else {
			throw new CanvasHack_Exception(
				"'$manifest' does not exist",
				CanvasHack_Exception::MANIFEST
			);
		}
		
		// TODO remember @variables when parsing
		// TODO in admin interface, not just enable/disable, but add additional page filters maybe?
	}
}

/**
 * All exceptions thrown by CanvasHack
 *
 * @author Seth Battis <SethBattis@stmarksschool.org>
 **/
class CanvasHack_Exception extends \Exception {
	
	/** Issue with the canvashack.xml manifest file */
	const MANIFEST = 1;
}
	
?>