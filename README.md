libSSE-php
==========

An easy-to-use, object-orienlated library for Server-Sent Events

Quick use
==========

Server-side(PHP):

	<?php
	require_once('./src/libsse.php');//include the library
	
	//regester the event handler
	class YourEventHandler extends SSEEvent {
		public function update(){
			//Here's the place to send data
			return 'Hello, world!';
		}
		public function check(){
			//Here's the place to check when the data needs update
			return true;
		}
	}
	
	$sse = new SSE();//create a libSSE instance
	$sse->addEventListener('event_name',new YourEventHandler());//register your event handler
	$sse->start();//start the event loop
	?>

Client-side(javascript):

	var sse = new EventSource('path/to/your/sse/script.php');
	sse.addEventListener('event_name',function(e){
		var data = e.data;
		//handle your data here
	},false);

