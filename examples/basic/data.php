<?php
require_once('../../src/libsse.php');

class TimeEvent extends SSEEvent {
	public function update(){
		return date('l, F jS, Y, h:i:s A');
	}
}

$sse = new SSE();
$sse->exec_limit=10;
$sse->addEventListener('time',new TimeEvent());
$sse->start();
?>