<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Sse\Event;
use Sse\SSE;

class TimeEvent implements Event {
	public function check(){
		return true;
	}

	public function update(){
		return date('l, F jS, Y, h:i:s A');
	}
}

$sse = new SSE();
$sse->exec_limit=10;
$sse->addEventListener('time', new TimeEvent());
$sse->start();

