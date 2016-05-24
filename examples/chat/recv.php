<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Sse\Data;

$data = new Data('file',array('path'=>'./data'));

if(isset($_POST['user']) && !isset($_POST['message'])) {
	$data->set('user', json_encode(array(
		    'msg' => htmlentities($_POST['user']),
            'time' =>time()
        )
    ));
} elseif(isset($_POST['message'],$_POST['user'])) {
	$data->set('message', json_encode(
        array(
            'msg' => htmlentities($_POST['message']),
            'time' => time(),
            'user' => $_POST['user']
        )
    ));
}