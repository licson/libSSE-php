<?php
require_once('../../src/libsse.php');

//This function fixes those who are in windows
function get_server_load() {
		if (stristr(PHP_OS, 'win')) {
			$wmi = new COM("Winmgmts://");
			$server = $wmi->execquery("SELECT LoadPercentage FROM Win32_Processor");
			$cpu_num = 0;
			$load_total = 0;
			foreach($server as $cpu){
				$cpu_num++;
				$load_total += $cpu->loadpercentage;
			}
			$load = round($load_total/$cpu_num);
        } else {
			$sys_load = sys_getloadavg();
			$load = $sys_load[0];
		}    
	return (int) $load;
}

class SysEvent extends SSETimedEvent { //Beware: use SSETimedEvent for sending data at a regular interval
	public $period = 5;//the interval in seconds
	public function update(){
		return json_encode(array('load'=>get_server_load(),'time'=>time()));
	}
}

$sse = new SSE();
$sse->addEventListener('data',new SysEvent());
$sse->start();
?>