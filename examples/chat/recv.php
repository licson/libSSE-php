<?php
require_once('../../src/libsse.php');

$data = new SSEData('mysqli',array('host'=>'127.0.0.1','user'=>'root','password'=>'','db'=>'main'));

if(isset($_POST['user'])){
	$data->set('user',json_encode(array('msg'=>htmlentities($_POST['user']),'time'=>time())));
}
else if(isset($_POST['message'])){
	$data->set('message',json_encode(array('msg'=>htmlentities($_POST['message']),'time'=>time())));
}