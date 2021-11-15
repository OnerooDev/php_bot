<?php
require_once "config_class.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/bot/vendor/autoload.php";
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\TelegramLog;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Request;

//Клас для работы с системными операциями
class Manage{

	private $config;
	private $dir_root;
	protected $admin_info;

	public function __construct($db){
		session_start();
		$this->config = new Config();
		$this->dir_root = $_SERVER["DOCUMENT_ROOT"];
		$this->data = $this->secureData(array_merge($_POST, $_GET));
		$this->admin_info = $this->getAdmin();
	}

	/****************************************************** */
	//API


	/****************************************************** */

	/*-Форматирование даты-*/
	protected function formatDate($time){
		return date("Y-m-d H:i:s",$time);
	}
	/*-Форматирование даты-*/

	/*-Форматирование даты короткий вид-*/
	protected function formatDateLow($time){
		return date("d.m.Y",$time);
	}
	/*-Форматирование даты короткий вид-*/

	/*-Форматирование даты только часы и минуты и секунды-*/
	protected function formatHourSec($time){
		return date("H:i:s",$time);
	}
	/*-Форматирование даты только часы и минуты и секунды-*/

	/*-Форматирование даты только часы и минуты-*/
	protected function formatHour($time){
		return date("H.i",$time);
	}
	/*-Форматирование даты только часы и минуты-*/

	/**Генерация уникального купона */
	private function uniqCoupon(){
		$rand = mt_rand(20, 22);
		$value = substr(strtoupper(md5(uniqid(mt_rand(0, 9999), true))),0,-$rand);
		if($this->coupons->isExistCoupon($value)) $this->uniqCoupon();
		return $value;
	}
	/**Генерация уникального купона */

	/**Генерация уникального api ключа */
	private function uniqAPI(){
		$value = strtoupper(md5(uniqid(mt_rand(0, 9999), true)));
		if($this->api->isExistApi($value)) $this->uniqAPI();
		return $value;
	}
	/**Генерация уникального api ключа */

	/*-Возврат времени в юникс формате вход*/
    protected function getTSIn($date) {
		$regex = "/(\d{2}).(\d{2}).(\d{4})/";
		preg_match($regex, $date, $matches);
		if(!$matches) return false;
		return mktime(0, 0, 1, $matches[2], $matches[1], $matches[3]);
	}
    /*-Возврат времени в юникс формате вход*/

    /*-Возврат времени в юникс формате выход*/
    protected function getTSOut($date) {
		$regex = "/(\d{2}).(\d{2}).(\d{4})/";
		preg_match($regex, $date, $matches);
		if(!$matches) return false;
		return mktime(23, 59, 59, $matches[2], $matches[1], $matches[3]);
	}
    /*-Возврат времени в юникс формате выход*/


	/**Проверка на корректность карты */

	/*-Хеширование пароля-*/
	private function hashPassword($password){
		return md5($password.$this->config->secret);
	}
	/*-Хеширование пароля-*/

	/*-Неизвестная ошибка-*/
	private function unknownError($r){
		return $this->returnMessage("UNKNOWN_ERROR", $r);
	}
	/*-Неизвестная ошибка-*/

	/*-Возвращение сообщения-*/
	private function returnMessage($message, $r, $error = false){
		$_SESSION["message"] = $message;
        if($error) $_SESSION["message_error"] = true;
		return $r;
	}
	/*-Возвращение сообщения-*/

	/*-Возвращение cтраницы с сообщением-*/
	private function returnPageMessage($message, $r){
		unset($_SESSION["email"]);
		$_SESSION["page_message"] = $message;
		return $r;
	}
	/*-Возвращение cтраницы с сообщением-*/
}
?>
