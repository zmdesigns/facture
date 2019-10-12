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
        return 'error: incorrect or null arguments passed to last_log function.';
    }

    $employee_id = $args['employee_id'];
    $workstation_id = $args['workstation_id'];
    $job_id = $args['job_id'];

    $pdo = db_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = [];
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
    $workstation_id = $args['workstation_id'];
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

function hours_worked($args) {
    //Verify all arguments passed
    if (!isset($args['employee_id'],$args['workstation_id'],$args['job_id'])) {
        return 'error: incorrect or null arguments passed to hours_worked function.';
    }

    $employee_id = $args['employee_id'];
    $workstation_id = $args['workstation_id'];
    $job_id = $args['job_id'];

    //populate sql array with any non-empty arguments
    $sql = [];
    if ($employee_id) {
        $sql[] = "employee_id = '$employee_id' ";
    }
    if ($workstation_id) {
        $sql[] = "workstation_id = '$workstation_id' ";
    }
    if ($job_id) {
        $sql[] = "job_id = '$job_id' ";
    }
    //build query string
    $sel_query = "SELECT * FROM Logs";
    if (!empty($sql)) {
        $sel_query .= " WHERE " . implode(" AND ", $sql);
        $sel_query .= " ORDER BY id";
    }
    else {
        return 'error: no arguments for hours_worked function';
    }

    $pdo = db_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $result = $pdo->query($sel_query);
    } catch (PDOException $e) {
        return $e->getMessage();
    }
    
    $rows = $result->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($rows)) {
        $in_outs = calc_hours($rows);
        return $in_outs;
    }
    else {
        return '';
    }
}

/* given an array of rows from Logs table, finds how many hours have been logged
   function does not care about if employee/workstation/job match, be sure the rows passed make sense!
   if there is not a clock out at the end of a clock in, it is assumed to be still in operation and 
     will calculate clock out as current time of call to function 
*/ 
function calc_hours($rows) {
    $hours_worked = 0;
    $in_outs = 0;
    foreach ($rows as $in_row) {
        if ($in_row['action'] == 1) {
            foreach($rows as $out_row) {
                //find exact match but with clock out action
                if ($out_row['action']         == 2 &&
                    $out_row['employee_id']    == $in_row['employee_id'] &&
                    $out_row['workstation_id'] == $in_row['workstation_id'] &&
                    $out_row['job_id']         == $in_row['job_id']) {
                        $in_outs++;
                        $in = new DateTime($in_row['date_logged']);
                        $out = new DateTime($out_row['date_logged']);
                        $interval = $out->diff($in,true);
                        $hours_worked += $interval->format('%i') / 60; //format interval by minutes / 60 for more accurate tracking
                        $hours_worked += $interval->format('%h');
                }
            }
        }
    }
    return round($hours_worked,2);
}

//given employee_id, workstation_id, job_id where a blank string is wildcard,
//return a formatted string detailing the last active date/time
function activity_string($args) {
    $last = last_log($args);

    //if a log entry was not found
    if (!is_array($last)) {
        return 'No activity found';
    }

    //format date for string
    $log_date = format_datetime($last['date_logged']);

    //if last action was a clock in, still active
    if ($last['action'] == 1) {
        return 'Active since '.$log_date;
    }

    return 'Last active on '.$log_date;
}

?>