<?php

require_once $_SERVER["DOCUMENT_ROOT"]."/lib/database_class.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/lib/config_class.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/lib/user_class.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/bot/vendor/autoload.php";
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\TelegramLog;
use Longman\TelegramBot\Request;


$db = new DataBase();
$config = new Config();
$user = new User($db);
$mysqli = new \mysqli($config->host, $config->user, $config->password, $config->db);
$mysqli->query("SET NAMES 'utf8'");

$st_user = $mysqli->query("SELECT * FROM `user` WHERE `st_bal` != 0");
$telegram = new Telegram($config->bot_api_key, $config->bot_username);
while ($row = $st_user->fetch_assoc()) {
	$user_support = $setting->getSettingOnName('user_support');
	$nowbal = $row['st_bal'];
	// check proc
	//
	$newproc = $upproc;


	$text = ' 	'.PHP_EOL;
	$text .= 'Your stacking percent - '.$newproc.' %' .PHP_EOL;
	$text .= 'Your balance top-up on - '.$prize .PHP_EOL;

	$datas['text'] = $text;
	$datas['chat_id'] = $row['id'];
	Request::sendMessage($datas);
}

?>
