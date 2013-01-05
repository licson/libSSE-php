<?php
/*
* @package libSSE-php
* @author Licson Lee <licson0729@gmail.com>
* @description A PHP library for handling Server-Sent Events (SSE)
*/
require_once('sse_events.php');
require_once('sse_utils.php');

/*
* @class SSE
* @description The main class
*/

class SSE {
	private $_handlers = array();
	private $id = 0;//the event id
	//seconds to sleep after the data has been sent
	//default: 0.5 seconds
	protected $sleep_time = 0.5;
	protected $exec_limit = 600;//stop execution after 10 minutes
	protected $client_reconnect = 1;//the time client to reconnect after connection has lost
	public $is_reconnect = false;
	
	public function __construct(){
		//if the HTTP header 'Last-Event-ID' is set
		//then it's a reconnect from
		if(isset($_SERVER['HTTP_LAST_EVENT_ID'])){
			$this->id = intval($_SERVER['HTTP_LAST_EVENT_ID']);
			$this->is_reconnect = true;
		}
	}
	/*
	* @method addEventListener
	* @param $event the event name
	* @param $handler the event handler, must be an instance of SSEEvent
	* @description remove a event handler
	*/
	public function addEventListener($event,$handler){
		if($handler instanceof SSEEvent){
			$this->_handlers[$event] = $handler;
		}
	}
	/*
	* @method SSE::removeEventListener
	* @param $event the event name
	* @description remove a event handler
	*/
	public function removeEventListener($event){
		unset($this->_handlers[$event]);
	}
	/*
	* @method SSE::start
	* @description start the event loop
	*/
	public function start(){
		@set_time_limit(0);//disable time limit
		//send the proper header
		header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache');
		
		$start = time();//record start time
		
		echo 'retry: '.($this->client_reconnect*1000)."\n";
		
		while(true){//keep the script running
			foreach($this->_handlers as $event=>$handler){
				if($handler->check()){//check if the data is avaliable
					$data = $handler->update();//get the data
					$this->id++;
					echo 'id: '.$this->id."\n";
					if($event != '') echo 'event: '.$event."\n";
					echo 'data: '.SSEUtils::sseData($data)."\n\n";//send the data
				}
			}
			//flush the data out
			ob_flush();
			flush();
			//break if the time excceed the limit
			if($this->exec_limit != 0 && time() - $start > $this->exec_limit) break;
			//sleep
			usleep($this->sleep_time*1000000);
		}
	}
};