<?php
/*
* @package libSSE-php
* @author Licson Lee <licson0729@gmail.com>
* @description A PHP library for handling Server-Sent Events (SSE)
*/

/*
* @class SSEData_MySQLi
* @description The MySQLi data mechnism
*/

class SSEData_MySQLi {
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
		
		$this->conn = mysqli_connect($host,$user,$pass,$db);
		return (bool)$this->conn;
	}
	
	private function check_reconnect(){
		if(!mysqli_ping($this->conn)){
			if(!$this->connect()){
				throw new Exception('Error reconnect.');
			}
		}
	}
	
	private function escape($str){
		return mysqli_real_escape_string($this->conn,$str);
	}
	
	private function prepare(){
		return (bool)(mysqli_query($this->conn,'CREATE TABLE IF NOT EXISTS `sse_data_table` (`key` varchar(50) NOT NULL, `value` text, PRIMARY KEY (`key`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;'));
	}
	
	public function get($key){
		$this->check_reconnect();
		$query = mysqli_query($this->conn,sprintf('SELECT * FROM `sse_data_table` WHERE `key` = \'%s\'',$this->escape($key)));
		$res = mysqli_fetch_assoc($query);
		return $res['value'];
	}
	
	public function set($key,$value){
		if($this->get($key)){
			return mysqli_query($this->conn,sprintf("UPDATE `sse_data_table` SET `key` = '%s', `value` = '%s'",$this->escape($key),$this->escape($value)));
		}
		else {
			return mysqli_query($this->conn,sprintf("INSERT INTO `sse_data_table` SET `key` = '%s', `value` = '%s'",$this->escape($key),$this->escape($value)));
		}
	}
	
	public function delete($key){
		return mysqli_query($this->conn,sprintf('DELETE FROM `sse_data_table` WHERE `key` == \'%s\'',$this->escape($key)));
	}
};

SSEData::register('mysqli','SSEData_MySQLi');