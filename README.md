libSSE-php
==========

An easy-to-use, object-orienlated library for Server-Sent Events

Updates
=========

1. Add new functionality: cross-script communication is now possible with SSEData.
2. Add example that demostratrs the new functionality: a chatroom build with libSSE in less than 100 lines of PHP code!
3. Cleaner code
4. Add documentation. [Check it here](https://github.com/licson0729/libSSE-php/wiki/libSSE-docs)
5. Improved code on output buffering.

Development
============

This is an active project. If you want to help me please suggest ideas to me and track issues or find bugs. If you like it, please consider star it to let more people know.

Quick use
==========

Server-side(PHP):

	<?php
	require_once('./src/libsse.php');//include the library
	
	//create the event handler
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

Settings
===========

After you created the libSSE instance, there's some settings for you to control the behaviour. Below is the settings provided by the library.

	<?php
	require_once('./src/libsse.php');
	$sse = new SSE();
	
	$sse->exec_limit = 10; //the execution time of the loop in seconds. Default: 600. Set to 0 to allow the script to run as long as possible.
	$sse->sleep_time = 1; //The time to sleep after the data has been sent in seconds. Default: 0.5.
	$sse->client_reconnect = 10; //the time for the client to reconnect after the connection has lost in seconds. Default: 1.
	$sse->use_chunked_encodung = true; //Use chunked encoding. Some server may get problems with this and it defaults to false
	$sse->keep_alive_time = 600; //The interval of sending a signal to keep the connection alive. Default: 300 seconds.
	$sse->allow_cors = true; //Allow cross-domain access? Default: false. If you want others to access this must set to true.
	?>

Compatibility
==============

Because server-sent events is a new standard and still in flux, only certain browsers support it.
However, polyfill for server-sent events is avaliable. Also on shared hosting, it may disable PHP's `set_time_limit` function and the library may not work as excepted. There's some settings in the library that can fix it.
