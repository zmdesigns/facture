<?php

//Removes leading/trailing whitespace, slashes, html characters
function sanitize_input($input)
{
	$input = trim($input);
	$input = stripslashes($input);
	$input = htmlspecialchars($input);
	return $input;
}

function format_date($timestamp_str) {
    $d = date('m-d-Y', strtotime($timestamp_str));
    return $d;
}

?>