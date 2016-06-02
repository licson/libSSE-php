libSSE-php
==========

[![License](https://img.shields.io/badge/License-MIT-428F7E.svg)](LICENSE.md)
[![Build Status](https://travis-ci.org/tonyhhyip/libSSE-php.svg?branch=master)](https://travis-ci.org/tonyhhyip/libSSE-php)

An easy-to-use, object-orienlated library for Server-Sent Events

Updates
=========

1. Namespace is added for libSSE.
2. `SSEEvent` become `Sse\Event` which is an interface
3. Cleaner code
4. Available on **packagist** as `tonyhhyip/sse`
5. Static method `time_mod` and `time_diff` of `SSEUtils` has been changed into `timeMod` and `timeDiff` of `Sse\Utils`
6. `has` method is added to DataInterface. Properties access method is implemented by magic methods.

Documentation
--------------

You may find it here.
[https://github.com/licson0729/libSSE-php/wiki/libSSE-docs](https://github.com/licson0729/libSSE-php/wiki/libSSE-docs)

Development
============

This is an active project. If you want to help me please suggest ideas to me and track issues or find bugs. If you like it, please consider star it to let more people know.

Quick use
==========

Server-side(PHP):

	<?php
	require_once('/path/to/vendor/autoload.php'); //Load with ClassLoader
	
	use Sse\Event;
	use Sse\SSE;
	
	//create the event handler
	class YourEventHandler implements SSEEvent {
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

After you created the libSSE instance, there's some settings for you to control the behaviour.
Below is the settings provided by the library.

	<?php
	require_once('/path/to/vendor/autoload.php'); //Load with ClassLoader
	
	use Sse\SSE;
	
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
However, polyfill for server-sent events is avaliable.
Also on shared hosting, it may disable PHP's `set_time_limit` function and the library may not work as excepted.
There's some settings in the library that can fix it.

Integration with Frameworks
============================

Symfony
-----------

    <?php
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
    
    class DefaultController extends Controller
    {
        /**
         * @Route("/sse", name="sse")
         */
        public function sseAction()
        {
            $sse = new Sse\SSE();
            // Add your event listener
            return $sse->createResponse();
        }
    }
    
Laravel
--------

    <?php
    use App\Http\Controller;
    use Sse\SSE;
    
    class FooController extends Controller
    {
        public function sse()
        {
             $sse = new SSE\SSE();
             // Add your event listener
             return $sse->createResponse();
        }
    }
