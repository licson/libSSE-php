<?php
/*
* @package libSSE-php
* @author Licson Lee <licson0729@gmail.com>
* @description A PHP library for handling Server-Sent Events (SSE)
*/

/*
* @class SSEUtils
* @description Helper class
*/

class SSEUtils {
	/*
	* @method SSEUtils::sseData
	* @param $str the data to be processed
	* @description Make strings SSE friendly (For internal use only)
	*/
	static public function sseData($str){
		return str_replace("\n","\ndata: ",$str);
	}
}