<?php
/*
* @package libSSE-php
* @author Licson Lee <licson0729@gmail.com>
* @description A PHP library for handling Server-Sent Events (SSE)
*/

/*
* @class SSEEvent
* @description The event placeholder class
*/

class SSEEvent {	
	public function check(){
		//data always updates
		return true;
	}
	
	public function update(){
		//returns nothing
		return '';
	}
};