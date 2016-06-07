<?php
// Load libSSE via autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

use Sse\Data;
use Sse\SSE;
use Sse\Event;

// Create a libSSE data instance, which is shared across all data instances 
// in all PHP scripts. This allows easy cross-script communications for some of
// the magic we done here.
$data = new Data('file', array('path' => './data'));

// Create the main instances
$sse = new SSE();

// This event handler checks for new users and broadcast
// the message to all clients.
class LatestUser implements Event {
    private $cache = 0;
    private $data;
    private $storage;

    public function __construct($data) {
        $this->storage = $data;
    }

    public function update(){
        return $this->data->msg;
    }

    public function check(){
        // Fetch data from the data instance
        $this->data = json_decode($this->storage->get('user'));
        
        // And check if it's a new message by comparing its time
        if($this->data->time !== $this->cache){
            $this->cache = $this->data->time;
            return true;
        }
        
        return false;
    }
};

// This event handler checks for new messages and broadcast
// it to other clients. 
class LatestMessage implements Event {
    private $cache = 0;
    private $data;
    private $storage;

    public function __construct($data) {
        $this->storage = $data;
    }

    public function update(){
        return json_encode($this->data);
    }

    public function check(){
        global $sse;
        // Fetch data from the data instance
        $this->data = json_decode($this->storage->get('message'));
        
        // Check if this connection is a reconnect. If it is, just
        // record last message's time to prevent repeatly sending messages
        if($this->cache == 0 && $sse->is_reconnect){
            $this->cache = $this->data->time;
            return false;
        }
        
        if($this->data->time !== $this->cache){
            $this->cache = $this->data->time;
            return true;
        }
        
        return false;
    }
};

// A 30 second time limit can prevent running out of resources quickly.
$sse->exec_limit = 30;

// Add the event handlers, if an empty name is given as the event name,
// it means trigger the default message event on the client.
$sse->addEventListener('user', new LatestUser($data));
$sse->addEventListener('', new LatestMessage($data));

// Finally, start the loop.
$sse->start();