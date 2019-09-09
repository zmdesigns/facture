<?php
require_once 'database.php';
require_once 'helpers.php';

$task = null;
$result = null;

$post_data = json_decode( file_get_contents( 'php://input' ), true );

if (!empty($post_data['task'])) {
	$task = sanitize_input($post_data['task']);
}

switch($task) {
    case 'list_all':
        $result = json_encode(get_products());
        break;
}

echo $result;


function get_products() {
    $pdo = db_connect();
    
    $products = [];
    $sql = 'SELECT * FROM Products ORDER BY id';
    foreach ($pdo->query($sql) as $row) {
        $products[] = ['id' => $row['id'],
        'name'          => $row['name'],
        'description'   => $row['description'],
        'date'          => $row['date_added']];
    }
    return $products;
}

?>