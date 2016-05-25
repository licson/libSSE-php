<?php
/**
 * libSSE-php
 *
 * Copyright (C) Tony Yip 2016.
 *
 * Permission is hereby granted, free of charge,
 * to any person obtaining a copy of this software
 * and associated documentation files (the "Software"),
 * to deal in the Software without restriction,
 * including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons
 * to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice
 * shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS",
 * WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @category libSSE-php
 * @author   Tony Yip <tony@opensource.hk>
 * @license  http://opensource.org/licenses/MIT MIT License
 */

namespace Sse;

use \Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SSE {

    /**
     * @var array<\SSE\Event>
     */
    private $handlers = array();

    /**
     * Event ID.
     * @var int
     */
    private $id = 0;

    private $config = array(
        'sleep_time' => 0.5,                // seconds to sleep after the data has been sent
        'exec_limit' => 600,                // the time limit of the script in seconds
        'client_reconnect' => true,            // the time client to reconnect after connection has lost in seconds
        'allow_cors' => false,              // Allow Cross-Origin Access?
        'keep_alive_time' => 300,           // The interval of sending a signal to keep the connection alive
        'is_reconnect' => false,            // A read-only flag indicates whether the user reconnects
        'use_chunked_encoding' => false,    // Allow chunked encoding
    );

    /**
     * SSE constructor.
     *
     * @param Request|null $request
     */
    public function __construct(Request $request = null)
    {
        //if the HTTP header 'Last-Event-ID' is set
        //then it's a reconnect from the client

        if ($request === null) {
            $request = Request::createFromGlobals();
        }

        $this->id = intval($request->headers->get('Last-Event-ID', 0));
        $this->config['is_reconnect'] = $request->headers->has('Last-Event-ID');

    }
    /**
     * Attach a event handler
     * @param string $event the event name
     * @param Event $handler the event handler
     */
    public function addEventListener($event, Event $handler)
    {
        $this->handlers[$event] = $handler;
    }

    /**
     * remove a event handler
     *
     * @param string $event the event name
     */
    public function removeEventListener($event)
    {
        unset($this->handlers[$event]);
    }

    /**
     * Start the event loop
     */
    public function start(){
        $response = $this->createResponse();
        $response->send();
    }

    /**
     * Create a Response to send event
     * @return StreamedResponse
     */
    public function createResponse()
    {
        $this->init();
        $callback = function () {
            $start = time(); // Record start time
            echo 'retry: ' . ($this->client_reconnect * 1000) . "\n";	//set the retry interval for the client
            while (true) {
                if (Utils::timeMod($start, $this->keep_alive_time) == 0) {
                    // No updates needed, send a comment to keep the connection alive.
                    // From https://developer.mozilla.org/en-US/docs/Server-sent_events/Using_server-sent_events
                    echo ': ' . sha1(mt_rand()) . "\n\n";
                }
                
                // Leave the loop if there are no morer handlers
                if (count($this->handlers) == 0) {
                    break;
                }
                
                // Start to check for updates
                foreach ($this->handlers as $event => $handler) {
                    if ($handler->check()) { // Check if the data is avaliable
                        $data = $handler->update(); // Get the data
                        $this->id++;
                        Utils::sseBlock($this->id, $data, $event);
                        
                        // Make sure the data has been sent to the client
                        @ob_flush();
                        @flush();
                    }
                }

                // Break if the time exceed the limit
                if ($this->exec_limit !== 0 && Utils::timeDiff($start) > $this->exec_limit) {
                    break;
                }
                // Sleep
                usleep($this->sleep_time * 1000000);
            }
        };

        $callback->bindTo($this);

        $response = new StreamedResponse($callback, Response::HTTP_OK, array(
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'off' // Disables FastCGI Buffering on Nginx
        ));

        if($this->allow_cors){
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        };

        if($this->use_chunked_encoding)
            $response->headers->set('Transfer-encoding', 'chunked');

        return $response;
    }

    protected function init()
    {
        @set_time_limit(0); // Disable time limit

        // Prevent buffering
        if(function_exists('apache_setenv')){
            @apache_setenv('no-gzip', 1);
        }

        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);

        while (ob_get_level() != 0) {
            ob_end_flush();
        }
        ob_implicit_flush(1);
    }

    public function get($key)
    {
        return $this->config[$key];
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    public function set($key, $value)
    {
        if (in_array($key, array('is_reconnect'))) {
            throw new \InvalidArgumentException('is_reconnected is an read-only flag');
        }
        $this->config[$key] = $value;
    }

    public function __set($key, $value)
    {
        $this->set($key, $value);
    }
}
