<?php
	mb_internal_encoding("UTF-8");

	//ini_set('error_reporting', E_ALL);
	//ini_set('display_errors', 1);
	//ini_set('display_startup_errors', 1);

	require_once "lib/database_class.php";
	require_once "lib/manage_class.php";
	require_once "lib/config_class.php";

	$config = new Config;
	$db = new DataBase();
	$manage = new Manage($db);

	$manage->redirect($r);
?>
