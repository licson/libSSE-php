<?php
require_once('../../src/libsse.php');

$GLOBALS['data'] = new SSEData('mysqli',array('host'=>'127.0.0.1','user'=>'root','password'=>'','db'=>'main'));
$sse = new SSE();

class LatestUser extends SSEEvent {
	public $cache = 0;
	public function update(){
		return json_decode($GLOBALS['data']->get('user'))->msg;
	}
	public function check(){
		if(json_decode($GLOBALS['data']->get('user'))->time !== $this->cache){
			$this->cache = json_decode($GLOBALS['data']->get('user'))->time;
			return true;
		}
		return false;
	}
};

class LatestMessage extends SSEEvent {
	public $cache = 0;
	public function update(){
		return json_decode($GLOBALS['data']->get('message'))->msg;
	}
	public function check(){
		if(json_decode($GLOBALS['data']->get('message'))->time != $this->cache){
			$this->cache = json_decode($GLOBALS['data']->get('message'))->time;
			return true;
		}
		return false;
	}
};

$sse->addEventListener('user',new LatestUser());
$sse->addEventListener('',new LatestMessage());
$sse->start();