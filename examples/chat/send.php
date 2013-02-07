<?php
require_once('../../src/libsse.php');

$GLOBALS['data'] = new SSEData('mysqli',array('host'=>'127.0.0.1','user'=>'root','password'=>'','db'=>'main'));
$sse = new SSE();

class LatestUser extends SSEEvent {
	private $cache = 0;
	private $data;
	public function update(){
		return $this->data->msg;
	}
	public function check(){
		$this->data = json_decode($GLOBALS['data']->get('user'));
		if($this->data->time !== $this->cache){
			$this->cache = $this->data->time;
			return true;
		}
		return false;
	}
};

class LatestMessage extends SSEEvent {
	private $cache = 0;
	private $data;
	public function update(){
		return json_encode($this->data);
	}
	public function check(){
		$this->data = json_decode($GLOBALS['data']->get('message'));
		if($this->data->time !== $this->cache){
			$this->cache = $this->data->time;
			return true;
		}
		return false;
	}
};

$sse->addEventListener('user',new LatestUser());
$sse->addEventListener('',new LatestMessage());
$sse->start();