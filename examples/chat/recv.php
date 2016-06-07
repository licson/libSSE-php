<?php
// Load libSSE via autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

// We only need the data instance here
use Sse\Data;

// Create a libSSE data instance, which is shared across all data instances 
// in all PHP scripts. This allows easy cross-script communications that allows
// us to notify the SSE handler for new data.
$data = new Data('file', array('path' => './data'));

// This is a new user
if(isset($_POST['user']) && !isset($_POST['message'])) {
    // The libSSE data instance is a key-value storage.
    // It works like an associative array, just the data
    // is shared across scripts.
	$data->set('user', json_encode(array(
		    'msg' => htmlentities($_POST['user']),
            'time' => time()
        )
    ));
} elseif(isset($_POST['message'], $_POST['user'])) { // This is a new message  
	$data->set('message', json_encode(
        array(
            'msg' => htmlentities($_POST['message']),
            'time' => time(),
            'user' => $_POST['user']
        )
    ));
}