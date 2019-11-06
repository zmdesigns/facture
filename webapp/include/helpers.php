<?php

require_once 'general.php';

//Removes leading/trailing whitespace, slashes, html characters
function sanitize_input($input)
{
	$input = trim($input);
	$input = stripslashes($input);
	$input = htmlspecialchars($input);
	return $input;
}

function format_datetime($timestamp_str) {
    $dt = date('g:iA m-d-Y', strtotime($timestamp_str));
    return $dt;
}

function format_date($timestamp_str) {
    $d = date('m-d-Y', strtotime($timestamp_str));
    return $d;
}


function exist($table,$column,$search) {
    $result = lookup(array('table'=>$table,'column'=>$column,'search'=>$search));

    if (!is_array($result) || empty($result)) {
        return false;
    }
    else {
        return true;
    }
}

/* Returns name associated with id stored in database - if not found returns id passed*/
function employee_name($employee_id) {
    $employee = lookup(array('table'=>'Employees','column'=>'id','search'=>$employee_id));

    if (is_array($employee) && !empty($employee)) {
        return $employee[0]['name'];
    }

    return $employee_id;
}

/* Returns name associated with id stored in database - if not found returns id passed*/
function workstation_name($workstation_id) {
    $workstation = lookup(array('table'=>'Workstations','column'=>'station_id','search'=>$workstation_id));

    if (is_array($workstation) && !empty($workstation)) {
        return $workstation[0]['name'];
    }

    return $workstation_id;
}

?>