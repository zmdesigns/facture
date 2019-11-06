<?php

require_once 'database.php';
require_once 'helpers.php';
require_once 'general.php';

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
         'product_id' => $row['product_id'],
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
    if (!isset($args['employee_id'],$args['workstation_id'],$args['job_id'],$args['product_id'],$args['action'])) {
        return 'error: incorrect or null arguments passed to new_product function.';
    }

    $employee_id = $args['employee_id'];
    $workstation_id = $args['workstation_id'];
    $job_id = $args['job_id'];
    $product_id = $args['product_id'];
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
        $query = $pdo->exec('INSERT INTO Logs(employee_id,workstation_id,job_id,product_id,action) VALUES ("'.$employee_id.'","'.$workstation_id.'","'.$job_id.'","'.$product_id.'","'.$action.'")');
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
        $hours = calc_hours($rows);
        return $hours;
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
    foreach ($rows as $in_row) {
        //for each log that is a clock in
        if ($in_row['action'] == 1) {
            $in = new DateTime($in_row['date_logged']);
            //set out date to current time
            $out = new DateTime();
            //attempt to find a clock out
            $out_row = find_log_match($rows,$in_row);
            //if a clock out is found, set out to that time
            if ($out_row) {
                $out = new DateTime($out_row['date_logged']);
            }
            //find difference and add it to hours worked
            $hours_worked += hour_diff($in,$out);
        }
    }
    return round($hours_worked,2);
}

function hour_diff($dt1, $dt2) {
    $hours = 0;
    $interval = $dt2->diff($dt1,true);
    $hours += $interval->format('%i') / 60; //format interval by minutes / 60 for more accurate tracking
    $hours += $interval->format('%s') / 60 / 60; //format interval by seoncds / 60 / 60 for more accurate tracking
    $hours += $interval->format('%h');

    return $hours;
}

function find_log_match($rows, $row) {

    //determine what action value is opposite
    $to_match = 0;
    if ($row['action'] == 2) {
        $to_match = 1;
    }
    else {
        $to_match = 2;
    }

    //find match with opposite action
    foreach($rows as $match_row) {
        if ($match_row['action']         == $to_match &&
            $match_row['employee_id']    == $row['employee_id'] &&
            $match_row['workstation_id'] == $row['workstation_id'] &&
            $match_row['job_id']         == $row['job_id']) {
                return $match_row;
        }
    }
    //no match found
    return NULL;
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


function job_log_sorted($args) {
    //Verify all arguments passed
    if (!isset($args['job_id'],$args['product_id'])) {
        return 'error: incorrect or null arguments passed to job_log_sorted function.';
    }
    $job_id = $args['job_id'];
    $product_id = $args['product_id'];

    //find all logs for job_id
    $rows = lookup(['table'=>'Logs',
                    'column'=>'job_id',
                    'search'=>$job_id]);

    $prod_rows = lookup_from_rows($rows,'product_id',$product_id);

    //return $prod_rows; //TEST

    //get a list of workstation_ids that were used to work on job_id
    $workstations = unique_values_from_rows($prod_rows,'workstation_id');

    $data = [];
    foreach($workstations as $station) {
        $station_name = workstation_name($station);
        $data[$station_name] = [];
        //get an array of log entries for workstation from rows with job_id
        $station_log = lookup_from_rows($prod_rows,'workstation_id',$station);
        foreach($station_log as $log_entry) {
            
            if ($log_entry['action'] == 1) {
                //find clock-out action for clock in
                $out_entry = find_log_match($prod_rows, $log_entry);
                if ($out_entry) {
                    $in = new DateTime($log_entry['date_logged']);
                    $out = new DateTime($out_entry['date_logged']);
                    $employee_name = employee_name($log_entry['employee_id']);
                    $hours = hour_diff($in,$out);
                    $data[$station_name][] = ['start'=>$log_entry['date_logged'],
                                         'end'=>$out_entry['date_logged'],
                                         'employee'=>$employee_name,
                                         'hours'=>$hours];
                }
            }
        }
    }
    return $data;
}

?>