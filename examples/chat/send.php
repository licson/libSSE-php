<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Sse\Data;
use Sse\SSE;
use Sse\Event;

$data = new Data('file', array('path'=>'./data'));
$sse = new SSE();

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
        $this->data = json_decode($this->storage->get('user'));
        if($this->data->time !== $this->cache){
            $this->cache = $this->data->time;
            return true;
        }
        return false;
    }
};

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
        $this->data = json_decode($this->storage->get('message'));
        if($this->data->time !== $this->cache){
            $this->cache = $this->data->time;
            return true;
        }
        return false;
    }
};

$sse->exec_limit = 30;
$sse->addEventListener('user',new LatestUser($data));
$sse->addEventListener('',new LatestMessage($data));
$sse->start();