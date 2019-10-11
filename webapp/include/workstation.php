<?php

require_once 'database.php';
require_once 'helpers.php';

/*
    Return an array of employees from database
*/
function get_workstations() {
    $pdo = db_connect();
    
    $stations = [];
    $sql = 'SELECT * FROM Workstations ORDER BY id';
    foreach ($pdo->query($sql) as $row) {
        $stations[] = $row;
    }
    return $stations;
}

/*
    Add a new workstation to database
    
    name - name of employee
    station_id - 10 char string unique id of workstation
    notes - notes about employee
*/
function new_workstation($args) {
    //Verify all arguments passed and not null
    if (!isset($args['name'],$args['station_id'],$args['notes'])) {
        return 'error: incorrect or null arguments passed to new_workstation function.';
    }

    $name = $args['name'];
    $station_id = $args['station_id'];
    $notes = $args['notes'];

    $pdo = db_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $query = $pdo->exec('INSERT INTO Workstations(name,station_id,notes) VALUES ("'.$name.'","'.$station_id.'","'.$notes.'")');
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

function edit_workstation($args) {
    //Verify all arguments passed and not null
    if (!isset($args['name'],$args['new_name'],$args['station_id'],$args['notes'])) {
        return 'error: incorrect or null arguments passed to edit_workstation function.';
    }

    $name = $args['name'];
    $new_name = $args['new_name'];
    $station_id = $args['station_id'];
    $notes = $args['notes'];

    $pdo = db_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $query = $pdo->exec('UPDATE Workstations SET name="'.$new_name.'", station_id="'.$station_id.'", notes="'.$notes.'" WHERE name="'.$name.'"');
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

function delete_workstation($args) {
    //Verify all arguments passed and not null
    if (!isset($args['name'])) {
        return 'error: incorrect or null arguments passed to delete_workstation function.';
    }

    $name = $args['name'];

    $pdo = db_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $query = $pdo->exec('DELETE FROM Workstations WHERE name="'.$name.'"');
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