<?php

include '../../dblogin.php';

function db_connect() {

    global $db_server, $db_name, $db_user, $db_pass;

    $charset = 'utf8';
    $dsn = "mysql: host={$db_server};dbname={$db_name};charset={$charset}";

    try {
		$pdo = new PDO($dsn, $db_user, $db_pass);
	} catch (PDOException $e) {
		error_log($e->getMessage());
		die();
	}
	
	return $pdo;
}