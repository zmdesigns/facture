<?php

require_once 'database.php';
require_once 'helpers.php';

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
function new_product($args) {
    //Verify all arguments passed and not null
    if (!isset($args['name'],$args['description'])) {
        return 'error: incorrect or null arguments passed to new_product function.';
    }
    $name = $args['name'];
    $description = $args['description'];

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

function edit_product($args) {
    //Verify all arguments passed and not null
    if (!isset($args['name'],$args['new_name'],$args['description'])) {
        return 'error: incorrect or null arguments passed to edit_product function.';
    }
    $name = $args['name'];
    $new_name = $args['new_name'];
    $description = $args['description'];

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

function delete_product($args) {
    //Verify all arguments passed and not null
    if (!isset($args['name'])) {
        return 'error: incorrect or null arguments passed to new_employee function.';
    }
    $name = $args['name'];

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