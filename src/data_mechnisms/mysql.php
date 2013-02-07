<?php
/*
* @package libSSE-php
* @author Licson Lee <licson0729@gmail.com>
* @description A PHP library for handling Server-Sent Events (SSE)
*/

/*
* @class SSEData
* @description The MySQL data mechnism
*/

class SSEData_MySQL {
	private $conn;
	private $credinals;
	
	public function __construct($credinals){
		if($credinals !== null){
			$this->credinals = $credinals;
			if(!$this->connect()){
				throw new Exception('Error establishing connection.');
			}
			$this->prepare();
		}
		else
		{
			throw new Exception('No credinals specified.');
		}
	}
	
	private function connect(){
		$host = $this->credinals['host'];
		$user = $this->credinals['user'];
		$pass = $this->credinals['password'];
		$db = $this->credinals['db'];
		
		$this->conn = mysql_pconnect($host,$user,$pass);
		mysql_select_db($db,$this->conn);
		return (bool)$this->conn;
	}
	
	private function check_reconnect(){
		if(!mysql_ping($this->conn)){
			if(!$this->connect()){
				throw new Exception('Error reconnect.');
			}
		}
	}
	
	private function escape($str){
		return mysql_real_escape_string($str,$this->conn);
	}
	
	private function prepare(){
		return (bool)(mysql_query('CREATE TABLE IF NOT EXISTS `sse_data_table` (`key` varchar(50) NOT NULL, `value` text, PRIMARY KEY (`key`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;',$this->conn));
	}
	
	public function get($key){
		$this->check_reconnect();
		$query = mysql_query(sprintf('SELECT * FROM `sse_data_table` WHERE `key` = \'%s\'',$this->escape($key)),$this->conn);
		$res = mysql_fetch_assoc($query);
		return $res['value'];
	}
	
	public function set($key,$value){
		if($this->get($key)){
			return mysql_query(sprintf("UPDATE `sse_data_table` SET `value` = '%s' WHERE `key` = '%s'",$this->escape($value),$this->escape($key)),$this->conn);
		}
		else {
			return mysql_query(sprintf("INSERT INTO `sse_data_table` SET `key` = '%s', `value` = '%s'",$this->escape($key),$this->escape($value)),$this->conn);
		}
	}
	
	public function delete($key){
		return mysql_query(sprintf('DELETE FROM `sse_data_table` WHERE `key` == \'%s\'',$this->escape($key)),$this->conn);
	}
};

SSEData::register('mysql','SSEData_MySQL');