<?php

require_once 'database.php';
require_once 'helpers.php';

function get_logs() {
    $pdo = db_connect();
    
    $logs = [];
    $sql = 'SELECT * FROM Logs ORDER BY id';
    foreach ($pdo->query($sql) as $row) {
        $logs[] = 
        ['id'  => $row['id'],
         'date_logged' => $row['date_logged'],
         'employee_id' => $row['employee_id'],
         'workstation_id' => $row['workstation_id'],
         'job_id' => $row['job_id'],
         'action' => $row['action']];
    }
    return $logs;
}

//return log details for last entry that matches employee, workstation, job or any combination of
//argument is passed as null if it is not to be considered
function last_log($args) {
    //Verify all arguments passed and not null
    if (!isset($args['employee_id'],$args['workstation_id'],$args['job_id'])) {
        return 'error: incorrect or null arguments passed to new_product function.';
    }

    $employee_id = $args['employee_id'];
    $workstation_id = $args['workstation_id'];
    $job_id = $args['job_id'];

    $pdo = db_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    unset($sql);

    if ($employee_id) {
        $sql[] = "employee_id = '$employee_id' ";
    }
    if ($workstation_id) {
        $sql[] = "workstation_id = '$workstation_id' ";
    }
    if ($job_id) {
        $sql[] = "job_id = '$job_id' ";
    }

    $sel_query = "SELECT * FROM Logs";

    if (!empty($sql)) {
        $sel_query .= " WHERE " . implode(" AND ", $sql);
        $sel_query .= " ORDER BY id DESC LIMIT 1";
    }
    else {
        return 'fail: no arguments for last_log search';
    }

    try {
        $result = $pdo->query($sel_query);
    } catch (PDOException $e) {
        return $e->getMessage();
    }
    
    $row = $result->fetch(PDO::FETCH_ASSOC);
    if (!empty($row)) {
        return $row;
    }
    else {
        return 'no log with given arguments';
    }

}

function new_log($args) {
    //Verify all arguments passed and not null
    if (!isset($args['employee_id'],$args['workstation_id'],$args['job_id'],$args['action'])) {
        return 'error: incorrect or null arguments passed to new_product function.';
    }

    $employee_id = $args['employee_id'];
    $workstation_id = $args['workstation'];
    $job_id = $args['job_id'];
    $action = $args['action'];

    $pdo = db_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //get the most recent entry for job+workstation
    try {
        $sel_query = 'SELECT action FROM Logs WHERE job_id="'.$job_id.'" AND workstation_id="'.$workstation_id.'" ORDER BY id DESC LIMIT 1';
        $result = $pdo->query($sel_query);
    } catch (PDOException $e) {
        return $e->getMessage();
    }
    
    //last_action = last entered action for workstation+job match, null if it doesn't exist
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $last_action = $row['action'] ?? null;

    //if action to be entered is the same as the last action, this is not a valid entry - abort
    if ($last_action == $action) {
        return 'fail: duplicate action';
    }

    //if no entry for job+workstation yet and action for new entry is STOP, this is not a valid entry - abort
    if ($last_action == null && $action == 2) {
        return 'fail: trying to stop a job that has not been started at workstation';
    }

    //insert log entry into db
    try {
        $query = $pdo->exec('INSERT INTO Logs(employee_id,workstation_id,job_id,action) VALUES ("'.$employee_id.'","'.$workstation_id.'","'.$job_id.'","'.$action.'")');
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

?>