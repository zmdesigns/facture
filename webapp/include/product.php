<?php
/*
    This file expects json post data. Once decoded it checks 'task' index for what to do.
    The related function is then called and the result is echoed for the page that posted the task.
    If the function requires additional information, it will look for it in an array at the 'data' index.

    Valid 'task' values:
        'list_all' - returns json array of all products with relevant date/descriptions
        'new' - creates a product in database, returns success or error info
        'edit' - edits an existing product in the database, returns success or error info
        'delete' - deletes database entry for product
*/

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
    case 'new':
        $name = sanitize_input($post_data['prod_name']);
        $description = sanitize_input($post_data['description']);
        $result = new_product($name, $description);
        break;
    case 'edit':
        $name = sanitize_input($post_data['name']);
        $new_name = sanitize_input($post_data['new_name']);
        $description = sanitize_input($post_data['description']);
        $result = edit_product($name, $new_name, $description);
        break;
    case 'delete':
        $name = sanitize_input($post_data['name']);
        $result = delete_product($name);
        break;
}

echo $result;

/*
    Return an array of products from database
*/
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

/*
    Create a new product in database
    
    name - name of product to create
    description - description of product
*/
function new_product($name, $description) {
    $pdo = db_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $query = $pdo->exec('INSERT INTO Products(name,description) VALUES ("'.$name.'","'.$description.'")');
    } catch (PDOException $e) { 
        return $e->getMessage();
    }
    if ($pdo->errorCode() == '00') {
        return 'success!';
    }
    else {
        return $pdo->errorCode();
    }
}

function edit_product($name, $new_name, $description) {
    $pdo = db_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $query = $pdo->exec('UPDATE Products SET name="'.$new_name.'", description="'.$description.'" WHERE name="'.$name.'"');
    } catch (PDOException $e) { 
        return $e->getMessage();
    }
    if ($pdo->errorCode() == '00') {
        return 'success!';
    }
    else {
        return $pdo->errorCode();
    }
}

function delete_product($name) {
    $pdo = db_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $query = $pdo->exec('DELETE FROM Products WHERE name="'.$name.'"');
    } catch(PDOException $e) {
        return $e->getMessage();
    }
    if ($pdo->errorCode() == '00') {
        return 'success!';
    }
    else {
        return $pdo->errorCode();
    }
}

?>