<?php
/*
This file expects json post data. Once decoded it checks 'task' index for what to do.
The related function is then called and the result is echoed for the page that posted the task.
If the function requires additional information, it will look for it in an array at the 'data' index.

Valid 'task' values:
    'list_all' - returns json array of all log entries...
    'new' - creates a log entry in database, returns success or error info
*/

/* ACTIONS
    1 = START
    2 = STOP
    3 = LAST ACTION
*/

require_once 'database.php';
require_once 'helpers.php';

$task = null;
$result = null;

$post_data = json_decode( file_get_contents( 'php://input' ), true );

$args = [];
foreach ($post_data as $key => $value) {
    $skey = sanitize_input($key);
    $svalue = sanitize_input($value);
    $args[$skey] = $svalue;
}

if (!empty($args['task'])) {
    $task = $args['task'];
}

switch($task) {
    case 'list_all':
        $result = json_encode(get_logs());
        break;
    case 'new':
        $result = new_log($args['employee_id'], $args['workstation_id'], $args['job_id'], $args['action']);
        break;
    case 'last_log':
        $result = json_encode(last_log($args['employee_id'], $args['workstation_id'], $args['job_id']));
        break;
}

echo $result;

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
function last_log($employee_id, $workstation_id, $job_id) {
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

function new_log($employee_id, $workstation_id, $job_id, $action) {
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