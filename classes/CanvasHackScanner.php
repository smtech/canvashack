<?php

namespace smtech\CanvasHack;

class CanvasHackScanner extends \Battis\AutoCrontabJob {
	
	public function scheduledJob() {
		$this->log->log("Scanning");
	}
	
}
	
?>