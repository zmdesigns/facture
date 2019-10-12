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

?>