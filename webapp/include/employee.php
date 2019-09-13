<?php
/*
    This file expects json post data. Once decoded it checks 'task' index for what to do.
    The related function is then called and the result is echoed for the page that posted the task.
    If the function requires additional information, it will look for it in an array at the 'data' index.

    Valid 'task' values:
        'list_all' - returns json array of all employees with relevant info
        'new' - creates a employee in database, returns success or error info
        'edit' - edits an existing employee in the database, returns success or error info
        'delete' - deletes employee from database
*/

require_once 'database.php';
require_once 'helpers.php';

$task = null;
$result = null;

$post_data = json_decode( file_get_contents( 'php://input' ), true );

if (!empty($post_data['task'])) {
	$task = sanitize_input($post_data['task']);
}

switch($task) {
    case 'list_all':
        $result = json_encode(get_employees());
        break;
    case 'new':
        
        break;
    case 'edit':
        
        break;
    case 'delete':
        
        break;
}

echo $result;


/*
    Return an array of employees from database
*/
function get_employees() {
    $pdo = db_connect();
    
    $employees = [];
    $sql = 'SELECT * FROM Employees ORDER BY id';
    foreach ($pdo->query($sql) as $row) {
        $employees[] = 
        ['name'  => $row['name'],
         'login' => $row['login'],
         'notes' => $row['notes']];
    }
    return $employees;
}

/*
    Add a new employee to database
    
    name - name of employee
    login - 5 digit numeric code used by employee to login
    notes - notes about employee
*/
function new_employee($name, $login, $notes) {
    $pdo = db_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $query = $pdo->exec('INSERT INTO Employees(name,login,notes) VALUES ("'.$name.'","'.$login.'","'.$notes.'")');
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