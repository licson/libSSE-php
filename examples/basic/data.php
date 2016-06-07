<?php
// Load libSSE via autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

use Sse\Event;
use Sse\SSE;

// A simple time event to push server time to clients 
class TimeEvent implements Event {
	public function check(){
		// Time always updates, so always return true
		return true;
	}

	public function update(){
		// Send formatted time
		return date('l, F jS, Y, h:i:s A');
	}
}

// Create the SSE handler
$sse = new SSE();

// You can limit how long the SSE handler to save resources 
$sse->exec_limit = 10;

// Add the event handler to the SSE handler
$sse->addEventListener('time', new TimeEvent());

// Kick everything off!
$sse->start();