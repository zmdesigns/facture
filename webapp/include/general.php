<?php

require_once 'database.php';

function lookup($args) {
    //Verify all arguments passed and not null
    if (!isset($args['table'],$args['column'],$args['search'])) {
        return 'error: incorrect or null arguments passed to lookup function.';
    }

    $table = $args['table'];
    $column = $args['column'];
    $search = $args['search'];
    $pdo = db_connect();
    
    $results = [];
    $sql = 'SELECT * FROM '.$table.' WHERE '.$column.'="'.$search.'" ORDER BY id DESC';
    foreach ($pdo->query($sql) as $row) {
        $results[] = $row;
    }
    return $results;
}

/* Return an array of rows where the indexed value of row == $search */
function lookup_from_rows($rows, $index, $search) {

    $results = [];
    foreach($rows as $row) {
        if ($row[$index] == $search) {
            $results[] = $row;
        }
    }

    return $results;
}

/* Return an array of values with no duplicates from an indexed value of rows */
function unique_values_from_rows($rows, $index) {

    $results = [];
    foreach($rows as $row) {
        if (!in_array($row[$index],$results)) {
            $results[] = $row[$index];
        }
    }

    return $results;
}
?>