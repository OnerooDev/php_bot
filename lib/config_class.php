<?php

class Config{

	public $sitename = "DEV панель";
	public $address = "https://bot.potapov.host"; //адресс админки
	//Секретное слово для дальнейшего его смешивания с паролем пользователя
	public $secret = "cdsfg432ntb74cdavgnhjt3rfdcd3";

	public $host= "localhost";
	public $db = "dev"; //название бд
	public $db_prefix = ""; //оставить пустым
	public $user = "dev"; //пользователь бд
	public $password = "";//пароль

	//апи ключ
	public $bot_api_key = '';
	//имя бота
	public $bot_username = 't_bot';
}
?>
