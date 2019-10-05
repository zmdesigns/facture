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
        $products[] = $row;
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
    if (!isset($args['product_id'],$args['name'],$args['description'])) {
        return 'error: incorrect or null arguments passed to new_product function.';
    }
    $product_id = $args['product_id'];
    $name = $args['name'];
    $description = $args['description'];

    $pdo = db_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $query = $pdo->exec('INSERT INTO Products(product_id,name,description) VALUES ("'.$product_id.'","'.$name.'","'.$description.'")');
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
    if (!isset($args['product_id'],$args['new_product_id'],$args['name'],$args['description'])) {
        return 'error: incorrect or null arguments passed to edit_product function.';
    }
    $product_id = $args['product_id'];
    $new_product_id = $args['new_product_id'];
    $name = $args['name'];
    $description = $args['description'];

    $pdo = db_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $query = $pdo->exec('UPDATE Products SET product_id="'.$new_product_id.'", name="'.$name.'", description="'.$description.'" WHERE product_id="'.$product_id.'"');
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
    if (!isset($args['product_id'])) {
        return 'error: incorrect or null arguments passed to delete_product function.';
    }
    $product_id = $args['product_id'];

    $pdo = db_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $query = $pdo->exec('DELETE FROM Products WHERE product_id="'.$product_id.'"');
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