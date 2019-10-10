<?php

require_once 'database.php';
require_once 'helpers.php';

/*
    Return an array of employees from database
*/
function get_jobs() {
    $pdo = db_connect();
    
    $jobs = [];
    $sql = 'SELECT Jobs.*,Products.name product_name,Customers.name customer_name FROM Jobs INNER JOIN Products ON Jobs.product_id = Products.product_id INNER JOIN Customers ON Jobs.customer_id = Customers.customer_id ORDER BY Jobs.job_id';
    foreach ($pdo->query($sql) as $row) {

        //format column dates/null representation
        if ($row['date_started'] == null)  {
            $row['date_started']  = '-';
        }
        else {
            $row['date_started'] = format_date($row['date_started']);
        }
        if ($row['date_finished'] == null) {
            $row['date_finished'] = '-';
        }
        else {
            $row['date_finished'] = format_date($row['date_finished']);
        }

        $row['date_added'] = format_date($row['date_added']);

        $jobs[] = $row;
    }
    return $jobs;
}

function get_sorted_jobs() {
    $pdo = db_connect();
    
    $jobs= [];
    $sql = 'SELECT Jobs.*,Products.name product_name,Customers.name customer_name FROM Jobs INNER JOIN Products ON Jobs.product_id = Products.product_id INNER JOIN Customers ON Jobs.customer_id = Customers.customer_id ORDER BY Jobs.job_id';
    foreach ($pdo->query($sql) as $row) {
        $jobs[$row['job_id']][] = $row;
    }

    return $ids;
}

function new_job($args) {
    //Verify all arguments passed and not null
    if (!isset($args['job_id'],$args['customer_name'],$args['product_name'],$args['qty'],$args['notes'])) {
        return 'error: incorrect or null arguments passed to new_job function.';
    }

    $job_id = $args['job_id'];
    $customer_name = $args['customer_name'];
    $product_name = $args['product_name'];
    $qty = $args['qty'];
    $notes = $args['notes'];

    if (!exist('Customers','name',$customer_name) ||
        !exist('Products','name',$product_name)) {

        return 'Failed. Customer or product does not exist.';
    }

    $pdo = db_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $sql = 'INSERT INTO Jobs(job_id,customer_id,product_id,qty,notes) VALUES ("'.$job_id.'", (SELECT customer_id FROM Customers WHERE name="'.$customer_name.'"),(SELECT product_id FROM Products WHERE name="'.$product_name.'"),"'.$qty.'","'.$notes.'")';
        $query = $pdo->exec($sql);
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


function edit_job($args) {
    //Verify all arguments passed and not null
    if (!isset($args['id'],$args['job_id'],$args['customer_name'],$args['product_name'],$args['qty'],$args['notes'])) {
        return 'error: incorrect or null arguments passed to edit_job function.';
    }

    $id = $args['id'];
    $job_id = $args['job_id'];
    $customer_name = $args['customer_name'];
    $product_name = $args['product_name'];
    $qty = $args['qty'];
    $notes = $args['notes'];
    
    if (!exist('Customers','name',$customer_name) ||
        !exist('Products','name',$product_name)) {

        return 'Failed. Customer or product does not exist.';
    }

    $pdo = db_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $sql = 'UPDATE Jobs SET job_id="'.$job_id.'", customer_id=(SELECT customer_id FROM Customers WHERE name="'.$customer_name.'"), product_id=(SELECT product_id FROM Products WHERE name="'.$product_name.'"),qty="'.$qty.'", notes="'.$notes.'" WHERE id="'.$id.'"';
        $query = $pdo->exec($sql);
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

function delete_job($args) {
    //Verify all arguments passed and not null
    if (!isset($args['id'])) {
        return 'error: incorrect or null arguments passed to delete_job function.';
    }

    $id = $args['id'];

    $pdo = db_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $query = $pdo->exec('DELETE FROM Jobs WHERE id="'.$id.'"');
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