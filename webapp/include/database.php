<?php

include '../../dblogin.php';

function db_connect() {

    global $db_server, $db_name, $db_user, $db_pass;

    $dsn = "mysql:host={$db_server};dbname={$db_name}";

    try {
		$pdo = new PDO($dsn, $db_user, $db_pass);
	} catch (PDOException $e) {
		error_log($e->getMessage());
		die();
	}
	
	return $pdo;
}