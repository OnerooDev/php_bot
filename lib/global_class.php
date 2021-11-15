<?php
require_once "config_class.php";
require_once "checkvalid_class.php";
require_once "database_class.php";

//Глобальный управляющий класс
abstract class GlobalClass{
	
	private $db;
	private $table_name;
	protected $config;
	protected $valid;
	
	protected function __construct($table_name, $db){
		$this->db = $db;
		$this->table_name = $table_name;
		$this->config = new Config();
		$this->valid = new CheckValid();
	}
	
	/*-Добавление новой записи-*/
	protected function add($new_values){
		return $this->db->insert($this->table_name, $new_values);
	}
	/*-Добавление новой записи-*/
	
	/*-Редактирование записи по id-*/
	protected function edit($id, $upd_fields){
		//Метода updateOnID написал сам, могут быть ошибки
		return $this->db->updateOnID($this->table_name, $id, $upd_fields);
	}
	/*-Редактирование записи по id-*/
	
	/*-Удаление записи по Id-*/
	protected function delete($id){
		return $this->db->deleteOnID($this->table_name, $id);
	}
	/*-Удаление записи по Id-*/

	/*-Удаление записи если значение меньше указаного-*/
	protected function deleteOnMinRange($field, $value, $where = false){
		return $this->db->deleteOnMinRange($this->table_name, $field, $value, $where);
	}
	/*-Удаление записи если значение меньше указаного-*/
	
	/*-Удаление всех записей-*/
	protected function deleteAll(){
		return $this->db->deleteAll($this->table_name);
	}
	/*-Удаление всех записей-*/

	/*-Удаление по определенному полю и значению-*/
	protected function deleteOnField($where){
		return $this->db->delete($this->table_name, $where);
	}
	/*-Удаление по определенному полю и значению-*/
	
	/*-Выбрать определенное поле, если известно другое поле и значение-*/
	protected function getField($field_out, $field_in, $value_in){
		return $this->db->getFields($this->table_name, $field_out, $field_in, $value_in);
	}
	/*-Выбрать определенное поле, если известно другое поле и значение-*/
	
	/*-Выбрать поле по id-*/
	protected function getFieldOnID($id, $field){
		return $this->db->getFieldOnID($this->table_name, $id, $field);
	}
	/*-Выбрать поле по id-*/
	
	/*-Изменение записи по Id-*/
	protected function setField($field, $value, $field_in, $value_in){
		return $this->db->setField($this->table_name, $field, $value, $field_in, $value_in);
	}
	/*-Изменение записи по Id-*/

	/*-Изменение записи по Id-*/
	protected function setFieldOnID($id, $field, $value){
		return $this->db->setFieldOnID($this->table_name, $id, $field, $value);
	}
	/*-Изменение записи по Id-*/

	/*-Вытаскивание записей в указаном интервале даты, выводит по убыванию даты (Важно: функция принимает значение в секундах!)-*/
	public function getFieldsOnDate($fields, $value_start, $value_end){
		$result = $this->db->getFieldsOnDate($this->table_name, $fields, $value_start, $value_end);
		if (($result) === false) return false;
		return $result;
	}
	/*-Вытаскивание записей в указаном интервале даты-*/
	
	/*-Получение всей записи целиком- (сделано public после завершение работы поменять)*/
	public function get($id){
		return $this->db->getElementOnID($this->table_name, $id);
	}
	/*-Получение всей записи целиком-*/
	
	/*-Получение всех записей- (сделано public после завершение работы поменять)*/
	public function getAll($order = "", $up = true, $limit = false){
		return $this->db->getAll($this->table_name, $order, $up, $limit);
	}
	/*-Получение всех записей-*/
	
	/*-Выбрать все по определенному полю-*/
	public function getAllOnField($field, $value, $order = "", $up = true, $limit = false){
		return $this->db->getAllOnField($this->table_name, $field, $value, $order, $up,$limit);
	}
	/*-Выбрать все по определенному полю-*/
	
	/*-Выбрать все по определенным полям-*/
	public function getAllOnFields($fields_values, $order, $up){
		return $this->db->getAllOnFields($this->table_name, $fields_values, $order, $up);
	}
	/*-Выбрать все по определенным полям-*/

	/*-Выбрать все по определенным полям в интервале-*/
	public function getAllOnFieldsInterval($fields_values, $field = false, $value_after = false, $value_before = false, $order = '', $up = ''){
		return $this->db->getAllOnFieldsInterval($this->table_name, $fields_values, $field, $value_after, $value_before, $order, $up);
	}
	/*-Выбрать все по определенным полям в интервале-*/
	
	/*-Получение определенного числа случайных елементов-*/
	public function getRandomElement($count){
		return $this->db->getRandomElements($this->table_name, $count);
	}
	/*-Получение определенного числа случайных елементов-*/
	
	/*-Получение последнего ID-*/
	public function getLastID(){
		return $this->db->getLastID($this->table_name);
	}
	/*-Получение последнего ID-*/
	
	/*-Получение определенного количества записей по определенному полю-*/
	public function getCountFields($fields, $count){
		return $this->db->getCountFields($this->table_name, $fields, $count);
	}
	/*-Получение определенного количества записей по определенному полю-*/

	/*-Получение количества записей по полю-*/
	public function getCountOnRange($field, $value_after, $value_before){
		return $this->db->getCountOnRange($this->table_name, $field, $value_after, $value_before);
	}
	/*-Получение количества записей по полю-*/

	/*-Получение количества записей по полю-*/
	public function getCountOnField($fields, $value){
		return $this->db->getCountOnField($this->table_name, $fields, $value);
	}
	/*-Получение количества записей по полю-*/
	
	/*-Получение количества елементов в таблице-*/
	public function getCount(){
		return$this->db->getCount($this->table_name);
	}
	/*-Получение количества елементов в таблице-*/
	
	/*-Проверка на существование данного значения по полю-*/
	protected function isExist($field, $value){
		return $this->db->isExists($this->table_name, $field, $value);
	}
	/*-Проверка на существование данного значения по полю-*/
	
	/*-поиск-*/
	protected function search($words, $fields){
		return $this->db->search($this->table_name, $words, $fields);
	}
	/*-поиск-*/
}
?>