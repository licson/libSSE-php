# libSSE-php

[![License](https://img.shields.io/badge/License-MIT-428F7E.svg)](LICENSE.md)
[![Build Status](https://travis-ci.org/tonyhhyip/libSSE-php.svg?branch=master)](https://travis-ci.org/tonyhhyip/libSSE-php)

An easy-to-use, object-oriented library for Server-Sent Events

## Installation

To install this package you'll need [composer](https://getcomposer.org/).

Run `composer require tonyhhyip/sse`


## Usage

Server-side(PHP):

```php
	<?php
	require_once('/path/to/vendor/autoload.php'); //Load with ClassLoader
	
	use Sse\Event;
	use Sse\SSE;
	
	//create the event handler
	class YourEventHandler implements Event {
		public function update(){
			//Here's the place to send data
			return 'Hello, world!';
		}
		
		public function check(){
			//Here's the place to check when the data needs update
			return true;
		}
	}
	
	$sse = new SSE(); //create a libSSE instance
	$sse->addEventListener('event_name', new YourEventHandler());//register your event handler
	$sse->start();//start the event loop
	?>
```

Client-side(javascript):
```javascript
	var sse = new EventSource('path/to/your/sse/script.php');
	sse.addEventListener('event_name',function(e){
		var data = e.data;
		//handle your data here
	},false);
```



## Settings

After you created the libSSE instance, there's some settings for you to control the behaviour.
Below is the settings provided by the library.

```php

<?php
	require_once('/path/to/vendor/autoload.php'); //Load with ClassLoader
	
	use Sse\SSE;
	
	$sse = new SSE();
    $sse->set($property, $value);
```


Direct access of property is kept with magic method for backward compatible.
```php
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

## Updates

1. Add Support of Symfony Http Foundation Compoent
2. SSE use magic method instead of direct access
3. Add Redis and Memcahce Mechnism
4. Add SessionLike Mechnism
5. Fixed event loop handling where removing handlers at runtime can result in a broken state.
6. Use Symfony HttpFoundation StreamedResponse to replace the old version
7. Add Changelog and contributing guide.

## Special For PHP 5.3 and 5.4
If you see and error message like `your PHP version does not satisfy that requirement.`,
please remove composer.lock and re-install it.

## Documentation

You may find it here.
[https://github.com/licson0729/libSSE-php/wiki/libSSE-docs](https://github.com/licson0729/libSSE-php/wiki/libSSE-docs)

## Development

This is an active project. If you want to help me please suggest ideas to me and track issues or find bugs. If you like it, please consider star it to let more people know.

## Compatibility
```
Compatibility
==============

Because server-sent events is a new standard and still in flux, only certain browsers support it.
However, polyfill for server-sent events is available.
Also on shared hosting, it may disable PHP's `set_time_limit` function and the library may not work as excepted.
There's some settings in the library that can fix it.

## Integration with Frameworks

### Symfony
```php

    <?php
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
    use Sse\SSE;
    
    class DefaultController extends Controller
    {
        /**
         * @Route("/sse", name="sse")
         */
        public function sseAction()
        {
            $sse = new SSE();
            // Add your event listener
            return $sse->createResponse();
        }
    }
 ```   
### Laravel
Please use [laravel-sse](https://github.com/tonyhhyip/laravel-sse).


### Yii2
Please use [yii2-sse](https://github.com/odannyc/yii2-sse).

## Contribution
Please see the [CONTRIBUTING.md](CONTRIBUTING.md).
