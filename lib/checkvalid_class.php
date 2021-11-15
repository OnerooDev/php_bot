<?php
require_once "config_class.php";

//Класс для проверки данных на корректность
class CheckValid{
	
	private $config;
	
	public function __construct(){
		$this->config = new Config();
	}
	
	/*--Проверка на корректность id--*/
	public function validID($id){
		if (!$this->isIntNumber($id)) return false;
		if ($id <= 0) return false;
		return true;
	}
	/*--Проверка на корректность id--*/
	
	/*-Проверка логина-*/
	public function validLogin($login){
		if($this->isContainQuotes($login)) return false;
		if(preg_match("/^\d*$/", $login)) return false;
		if(!preg_match("/^[1-9a-z-_]*$/i", $login)) return false;
		return $this->validString($login, $this->config->min_login, $this->config->max_login);
	}
	/*-Проверка логина-*/
	
	/*Проверка голосования*/
	public function checkVotes($votes){
		return $this->isNoNegativeInteger($votes);
	}
	/*Проверка голосования*/
	
	/*-проверка хеша пароля-*/
	public function validHash($hash){
		if(!$this->validString($hash, 32, 32)) return false;
		if(!$this->isOnlyLettersAndDigits($hash)) return false;
		return true;
	}
	/*-проверка хеша пароля-*/
	
	/*-проверка даты-*/
	public function validTimeStamp($time){
		return $this->isNoNegativeInteger($time);
	}
	/*-проверка даты-*/
	
	
	/*--Проверка числа (целое ли оно, и я вляеться ли числом)--*/
	public function isIntNumber($number){
		if (!is_int($number) && !is_string($number)) return false;
		if (!preg_match("/^-?([1-9][0-9]*|0)$/", $number)) return false;
		return true;
	}
	/*--Проверка числа (целое ли оно, и я вляеться ли числом)--*/
	
	/*-Проверка, положительное ли число-*/
	private function isNoNegativeInteger($number){
		if(!$this->isIntNumber($number)) return false;
		if($number < 0) return false;
		return true;
	}
	/*-Проверка, положительное ли число-*/
	
	/*-проверка на наличие в строке только букв и цыфр-*/
	private function isOnlyLettersAndDigits($string){
		if(!is_int($string) && (!is_string($string))) return false;
		if(!preg_match("/[a-zа-я0-9]*/i", $string)) return false;
		return true;
	}
	/*-проверка на наличие в строке только букв и цыфр-*/
	
	/*-проверка на валидность строки-*/
	private function validString($string, $min_length, $max_length){
		if(!is_string($string)) return false;
		if(mb_strlen($string) < $min_length) return false;
		if(mb_strlen($string) > $max_length) return false;
		return true;
	}
	/*-проверка на валидность строки-*/
	
	/*-Проверка в строке кавычек-*/
	private function isContainQuotes($string){
		$array = array("\"", "'", "`", "&quot;", "&apos;");
		foreach($array as $key => $value){
			if(strpos($string, $value) !== false) return true;
		}
		return false;
	}
	/*-Проверка в строке кавычек-*/
}

?>