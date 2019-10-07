<?php

require_once 'database.php';
require_once 'helpers.php';

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


?>