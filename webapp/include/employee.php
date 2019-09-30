<?php

require_once 'database.php';
require_once 'helpers.php';

/*
    Return an array of employees from database
*/
function get_employees() {
    $pdo = db_connect();
    
    $employees = [];
    $sql = 'SELECT * FROM Employees ORDER BY id';
    foreach ($pdo->query($sql) as $row) {
        $employees[] = 
        ['name'  => $row['name'],
         'login' => $row['login'],
         'notes' => $row['notes']];
    }
    return $employees;
}

/*
    Add a new employee to database
    
    name - name of employee
    login - 5 digit numeric code used by employee to login
    notes - notes about employee
*/
function new_employee($args) {
    //Verify all arguments passed and not null
    if (!isset($args['name'],$args['login'],$args['notes'])) {
        return 'error: incorrect or null arguments passed to new_employee function.';
    }

    $name = $args['name'];
    $login = $args['login'];
    $notes = $args['notes'];

    $pdo = db_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $query = $pdo->exec('INSERT INTO Employees(name,login,notes) VALUES ("'.$name.'","'.$login.'","'.$notes.'")');
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

function edit_employee($args) {
    //Verify all arguments passed and not null
    if (!isset($args['name'],$args['new_name'],$args['login'],$args['notes'])) {
        return 'error: incorrect or null arguments passed to edit_employee function.';
    }

    $name = $args['name'];
    $new_name = $args['new_name'];
    $login = $args['login'];
    $notes = $args['notes'];

    $pdo = db_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $query = $pdo->exec('UPDATE Employees SET name="'.$new_name.'", login="'.$login.'", notes="'.$notes.'" WHERE name="'.$name.'"');
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

function delete_employee($args) {
    //Verify all arguments passed and not null
    if (!isset($args['name'])) {
        return 'error: incorrect or null arguments passed to delete_employee function.';
    }

    $name = $args['name'];

    $pdo = db_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $query = $pdo->exec('DELETE FROM Employees WHERE name="'.$name.'"');
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