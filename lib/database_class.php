<?php
require_once "config_class.php";
require_once "checkvalid_class.php";
//Клас для работы с БД
class DataBase{
	
	private $config;
	private $mysqli;
	private $valid;
	
	public function __construct(){
		$this->config = new Config();
		$this->valid = new CheckValid();
		$this->mysqli = new mysqli($this->config->host, $this->config->user, $this->config->password, $this->config->db);
		$this->mysqli->query("SET NAMES 'utf8'");
	}
	/*-Отправка запроса и получение ответа-*/
	private function query($query){
		return $this->mysqli->query($query);
	}
	/*-Отправка запроса и получение ответа-*/
	
	/*-Выборка с БД-*/
	private function select($table_name, $fields, $where = "", $order = "", $up = true, $limit = ""){
		//перебор всех полей
		for ($i = 0; $i < count($fields); $i++){
			if((strpos($fields[$i], "(") === false) && ($fields[$i] != "*")) $fields[$i] = "`".$fields[$i]."`";
		}
		$fields = implode(",", $fields);
		$table_name = $this->config->db_prefix.$table_name;
		if(!$order) $order = "ORDER BY `id`";
		else{
			if ($order != "RAND()"){
				$order = "ORDER BY `$order`";
				if (!$up) $order .= " DESC";
			}
			else $order = "ORDER BY $order";
		}
		if ($limit) $limit = "LIMIT $limit";
		if ($where) $query = "SELECT $fields FROM $table_name WHERE $where $order $limit";
		else $query = "SELECT $fields FROM $table_name $order $limit";

		$result_set = $this->query($query);
		if (!$result_set) return false;
		$i = 0;
		while($row = $result_set->fetch_assoc()){
			$data[$i] = $row;
			$i++;
		}
		$result_set->close();
		return $data;
	}
	/*-Выборка с БД-*/
	
	/*-Добавление записей-*/
	public function insert($table_name, $new_values){
		$table_name = $this->config->db_prefix.$table_name;
		$query = "INSERT INTO $table_name (";
		foreach($new_values as $fields => $value) $query .= "`".$fields."`,";
		$query = substr($query, 0, -1);
		$query .= ") VALUES (";
		foreach($new_values as $value) $query .= "'".addslashes($value)."',";
		$query = substr($query, 0, -1);
		$query .= ")";
		return $this->query($query);
	}
	/*-Добавление записей-*/
	
	/*-Обновление таблиц-*/
	private function update($table_name, $upd_fields, $where){
		$table_name = $this->config->db_prefix.$table_name;
		$query = "UPDATE $table_name SET ";
		foreach($upd_fields as $field => $value) $query .= "`$field` = '".addslashes($value)."',";
		$query = substr($query, 0, -1);
		if ($where) {
			$query .= " WHERE $where";
			//echo $query."</br>";
			return $this->query($query);
		}
		else return false;
	}
	/*-Обновление таблиц-*/
	
	/*-Удаление записей-*/
	public function delete($table_name, $where){
		$table_name = $this->config->db_prefix.$table_name;
		if ($where){
			$query = "DELETE FROM $table_name WHERE $where";
			//echo $query;
			return $this->query($query);
		}
		else return false;
	}
	/*-Удаление записей-*/
	
	/*-Очищение таблицы-*/
	public function deleteAll($table_name){
		$table_name = $this->config->db_prefix.$table_name;
		$query = "TRUNCATE TABLE `$table_name`";
		return $this->query($query);
	}
	/*-Очищение таблицы-*/
	
	/*-Метод возвращает одно поле по значению другого поля (Поле должно быть уникальным, по которому ищем)-*/
	public function getFields($table_name, $field_out, $field_in, $value_in){
		$data = $this->select($table_name, array($field_out), "`$field_in`='".addslashes($value_in)."'");
		if (count($data) != 1) return false;
		return $data[0][$field_out];
	}
	/*-Метод возвращает одно поле по значению другого поля (Поле должно быть уникальным, по которому ищем)-*/
	
	/*-Получение значения поля по Id-*/
	public function getFieldOnID($table_name, $id, $field_out){
		if (!$this->existsID($table_name, $id)) return false;
		return $this->getFields($table_name, $fild_out, "id", $id);
	}
	/*-Получение значения поля по Id-*/
	
	/*Выбор определенного количества елементов из таблицы по полю*/
	public function getCountFields($table_name, $fields, $count){
		return $this->select($table_name, array("*"), "", $fields, false, $count);
	}
	/*Выбор определенного количества елементов из по полю*/
	
	/*-Получение всех записей из таблицы-*/
	public function getAll($table_name, $order, $up, $limit){
		return $this->select($table_name, array("*"), "", $order, $up, $limit);
	}
	/*-Получение всех записей из таблицы-*/
	
	/*-Получение всех записей по определенному полю-*/
	public function getAllOnField($table_name, $field, $value, $order, $up, $limit = false){
		return $this->select($table_name, array("*"), "`$field`='".addslashes($value)."'", $order, $up, $limit);
	}
	/*-Получение всех записей по определенному полю-*/

	/*-Получение всех записей по определенным полям в интервале-*/
	public function getAllOnFieldsInterval($table_name, $fields_values, $field, $value_after, $value_before, $order, $up){
		$where = '';
		foreach($fields_values as $key => $value){
			$where .= "`$key`='".addslashes($value)."' AND ";
		}
		$where = substr($where, 0, -5);
		
		if($value_after && $value_before && $field){
			if($fields_values){
				$where .= " AND ";
			}
			$where .= "`$field`>=$value_after AND `$field`<= $value_before";
		}
		return $this->select($table_name, array("*"), $where, $order, $up);
	}
	/*-Получение всех записей по определенным полям  полям в интервале-*/

	/*-Получение всех записей по определенным полям-*/
	public function getAllOnFields($table_name, $fields_values, $order, $up){
		$where = '';
		foreach($fields_values as $key => $value){
			$where .= "`$key`='".addslashes($value)."' AND ";
		}
		$where = substr($where, 0, -5);
		return $this->select($table_name, array("*"), $where, $order, $up);
	}
	/*-Получение всех записей по определенным полям-*/
	
	/*-Удаление записи по id-*/
	public function deleteOnID($table_name, $id){
		if (!$this->existsID($table_name, $id)) return false;
		return $this->delete($table_name, "`id`='$id'");
	}
	/*-Удаление записи по id-*/

	/*-Удаление записи по id-*/
	public function deleteOnMinRange($table_name, $field, $value, $where){
		return $this->delete($table_name, "`$field` < '$value' $where");
	}
	/*-Удаление записи по id-*/
	
	/*-Изменение значения определенного поля (в конце in - это поля и значения которые известны)-*/
	public function setField($table_name, $field, $value, $field_in, $value_in){
		return $this->update($table_name, array($field => $value), "`$field_in`='".addslashes($value_in)."'");
	}
	/*-Изменение значения определенного поля-*/
	
	/*-Обновление определенной записи по ID-*/
	public function updateOnID($table_name, $id, $upd_fields){
		//upd_fields асоциативный масив, тоесть клюсь название поля, значение собственно значение
		if(!$this->existsID($table_name, $id)) return false;
		return $this->update($table_name, $upd_fields, "`id`='".addslashes($id)."'");
	}
	/*-Обновление определенной записи по ID-*/
	
	/*-Изменение поля по определенному id-*/
	public function setFieldOnID($table_name, $id, $field, $value){
		if (!$this->existsID($table_name, $id)) return false;
		return $this->setField($table_name, $field, $value, "id", $id);
	}
	/*-Изменение поля по определенному id-*/
	
	/*-Возвращение всей записи по одному только id-*/
	public function getElementOnID($table_name, $id){
		if (!$this->existsID($table_name, $id)) return false;
		$arr = $this->select($table_name, array("*"), "`id`='$id'");
		return $arr[0];
	}
	/*-Возвращение всей записи по одному только id-*/
	
	/*-Возвращение случайных записей-*/
	public function getRandomElements($table_name, $count){
		return $this->select($table_name, array("*"), "", "RAND()", true, $count);
	}
	/*-Возвращение случайных записей-*/
	
	/*-Количество записей в таблице-*/
	public function getCount($table_name){
		$data = $this->select($table_name, array("COUNT(`id`)"));
		return $data[0]["COUNT(`id`)"];
	}
	/*-Количество записей в таблице-*/

	/*-Количество записей в таблице-*/
	public function getCountOnField($table_name, $field, $value){
		$data = $this->select($table_name, array("COUNT(`id`)"), "`$field`='".addslashes($value)."'");
		return $data[0]["COUNT(`id`)"];
	}
	/*-Количество записей в таблице-*/

	/*-Количество записей в таблице из диапозона-*/
	public function getCountOnRange($table_name, $field, $value_after,  $value_before){
		$data = $this->select($table_name, array("COUNT(`id`)"), "`$field`>=$value_after AND `$field`<= $value_before");
		return $data[0]["COUNT(`id`)"];
	}
	/*-Количество записей в таблице  из диапозона-*/
	
	/*-Проверка на существование определенной записи с определенным значением в таблице-*/
	public function isExists($table_name, $field, $value){
		$data = $this->select($table_name, array("id"), "`$field`='".addslashes($value)."'");
		if (count($data) === 0) return false;
		return true;
	}
	/*-Проверка на существование определенной записи в таблице-*/
	
	/*-последний максимальный id в таблице-*/
	/*-Метод переписывается в уроке №6 если с моим будут проблемы-*/
	public function getLastID($table_name){
		$id = $this->select($table_name, array("id"), "", "id", false, 1);
		if(count($id[0]) === 0) return false;
		return $id[0]['id'];
	}
	/*-последний максимальный id в таблице-*/
	
	/*-Вытаскивание записей в указаном интервале даты, выводит по убыванию даты (Важно: функция принимает значение в секундах!)-*/
	public function getFieldsOnDate($table_name, $fields, $value_start, $value_end){
		$result = $this->select($table_name, array("*"), "`$fields`>='".addslashes($value_start)."' AND `$fields`<='".addslashes($value_end)."'", "date", false);
		if (($result) === false) return false;
		return $result;
	}
	/*-Вытаскивание записей в указаном интервале даты-*/
	
	/*-Проверка на корректность Id-*/
	private function existsID($table_name, $id){
		if (!$this->valid->validID($id)) return false;
		$data = $this->select($table_name, array("id"), "`id`='".addslashes($id)."'");
		if (count($data) === 0) return false;
		return true;
	}
	/*-Проверка на корректность Id-*/
	
	/*-Поиск на сайте-*/
	public function search($table_name, $words, $fields){
		$words = quotemeta(trim(mb_strtolower($words)));
		$words = preg_replace("/ {2,}/", " ", $words);
		if($words == "") return false;
		$where = "";
		$arraywords = explode(" ", $words);
		$logic = "OR";
		
		foreach($arraywords as $key => $value) {
			if(isset($arraywords[$key - 1])) $where .= $logic;
			for($i = 0; $i < count($fields); $i++){
				$where .= "`".$fields[$i]."` LIKE '%".addslashes($value)."%'";
				if(($i + 1) != count($fields)) $where .= " OR";
			}
		}
		
		$results = $this->select($table_name, array("*"), $where);
		if(!$results) return false;
		$k = 0;
		$data = array();
		for($i = 0; $i < count($results); $i++){
			for($j = 0; $j < count($fields); $j++){
				$results[$i][$fields[$j]] = mb_strtolower(strip_tags($results[$i][$fields[$j]]));
			}
			$data[$k] = $results[$i];
			$data[$k]["relevant"] = $this->getRelevantForSearch($results[$i], $fields, $words);
			$k++;
		}
		$data = $this->orderResultSearch($data, "relevant");
		return $data;
	}
	/*-Поиск на сайте-*/
	
	/*-Подсчет совпадений-*/
	private function getRelevantForSearch($results, $fields, $words){
		$relevant = 0;
		$arraywords = explode(" ", $words);
		for($i = 0; $i < count($fields); $i++){
			for($j = 0; $j < count($arraywords); $j++){
				$relevant = substr_count($results[$fields[$i]], $arraywords[$j]);
			}
		}
		return $relevant;
	}
	/*-Подсчет совпадений-*/
	
	/*-Сортировка поиска-*/
	private function orderResultSearch($data, $order){
		for($i = 0; $i < count($data) - 1; $i++){
			$k = $i;
			for($j = $i + 1; $j < count($data); $j++){
				if($data[$j][$order] > $data[$k][$order]) $k = $j;
			}
			$temp = $data[$k];
			$data[$k] = $data[$i];
			$data[$i] = $temp;
		}
		return $data;
	}
	/*-Сортировка поиска-*/
	
	/*-Закрытие соединение с БД-*/
	public function __destruct(){
		if ($this->mysqli) $this->mysqli->close();
	}
	/*-Закрытие соединение с БД-*/
}
?>