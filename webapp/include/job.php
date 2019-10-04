<?php

require_once 'database.php';
require_once 'helpers.php';

/*
    Return an array of employees from database
*/
function get_jobs() {
    $pdo = db_connect();
    
    $jobs = [];
    $sql = 'SELECT * FROM Jobs ORDER BY id';
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

/*
    Add a new job to database
    
    job_id      - id of job 
    customer_id - id of customer
    product_id  - id of product
    qty         - qty of product
    notes       - notes for job
*/
function new_job($args) {
    //Verify all arguments passed and not null
    if (!isset($args['job_id'],$args['customer_id'],$args['product_id'],$args['qty'],$args['notes'])) {
        return 'error: incorrect or null arguments passed to new_job function.';
    }

    $job_id = $args['job_id'];
    $customer_id = $args['customer_id'];
    $product_id = $args['product_id'];
    $qty = $args['qty'];
    $notes = $args['notes'];

    $pdo = db_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $query = $pdo->exec('INSERT INTO Jobs(job_id,customer_id,product_id,qty,notes) VALUES ("'.$job_id.'", "'.$customer_id.'","'.$product_id.'","'.$qty.'","'.$notes.'")');
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
    if (!isset($args['id'],$args['job_id'],$args['customer_id'],$args['product_id'],$args['qty'],$args['notes'])) {
        return 'error: incorrect or null arguments passed to edit_job function.';
    }

    $id = $args['id'];
    $job_id = $args['job_id'];
    $customer_id = $args['customer_id'];
    $product_id = $args['product_id'];
    $qty = $args['qty'];
    $notes = $args['notes'];

    $pdo = db_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $query = $pdo->exec('UPDATE Jobs SET job_id="'.$job_id.'", customer_id="'.$customer_id.'", product_id="'.$product_id.'", qty="'.$qty.'", notes="'.$notes.'" WHERE id="'.$id.'"');
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