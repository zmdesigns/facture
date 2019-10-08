<?php

require_once 'database.php';
require_once 'helpers.php';

/*
    Return an array of customers from database
*/
function get_customers() {
    $pdo = db_connect();
    
    $customers = [];
    $sql = 'SELECT * FROM Customers ORDER BY id';
    foreach ($pdo->query($sql) as $row) {
        $customers[] = $row;
    }
    return $customers;
}

/*
    Add a new customer to database
    customer_id - id of customer
    name - name of customer
    notes - notes about customer
*/
function new_customer($args) {
    //Verify all arguments passed and not null
    if (!isset($args['customer_id'],$args['name'],$args['notes'])) {
        return 'error: incorrect or null arguments passed to new_customer function.';
    }

    $customer_id = $args['customer_id'];
    $name = $args['name'];
    $notes = $args['notes'];

    $pdo = db_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $query = $pdo->exec('INSERT INTO Customers(customer_id,name,notes) VALUES ("'.$customer_id.'","'.$name.'","'.$notes.'")');
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

function edit_customer($args) {
    //Verify all arguments passed and not null
    if (!isset($args['customer_id'],$args['name'],$args['new_name'],$args['notes'])) {
        return 'error: incorrect or null arguments passed to edit_customer function.';
    }

    $customer_id = $args['customer_id'];
    $name = $args['name'];
    $new_name = $args['new_name'];
    $notes = $args['notes'];

    $pdo = db_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $query = $pdo->exec('UPDATE Customers SET customer_id="'.$customer_id.'", name="'.$new_name.'", notes="'.$notes.'" WHERE name="'.$name.'"');
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

function delete_customer($args) {
    //Verify all arguments passed and not null
    if (!isset($args['name'])) {
        return 'error: incorrect or null arguments passed to delete_customer function.';
    }

    $name = $args['name'];

    $pdo = db_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $query = $pdo->exec('DELETE FROM Customers WHERE name="'.$name.'"');
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