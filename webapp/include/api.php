<?php
/*
    This file expects json post data! 
    It looks for a variables named 'task' to contain an ID corresponding to the task from the list below.
    If the task requires additional information, include the post variables listed

           ID   Description     Required POST Variables         Returns     Notes
           -----------------------------------------------------------------------------------------------------------
        Employees 
            1   List all        N/A                             JSON        Returns all columns as associative array
            2   New             name,login,notes                string
            3   Edit            name,new_name,login,notes       string
            4   Delete          name                            string

        Log
            10   List all        N/A                             JSON        Returns all columns as associative array
            11   New             employee_id,workstation_id,     string      action: 1=clock-in 2=clock-out
                                 job_id,action
            12   Last log        employee_id,workstation_id,     JSON        Returns last row that matches passed variables,null is passed for a wildcard
                                 job_id
        
        Products
            20   List all        N/A                             JSON        Returns all columns as associative array
            21   New             product_id,name,description     string
            22   Edit            product_id,new_product_id,      string
                                 name,description
            23   Delete          product_id                      string

        Jobs
            30   List all        N/A                             JSON         Returns all columns as associative array
            31   New             job_id,customer_name,           string
                                 product_name,qty,notes            
            32   Edit            id,job_id,customer_name,        string
                                 product_name,qty,notes
            33   Delete          job_id                          string
            34   List sorted     N/A                             JSON        Returns array of arrays of jobs, indexed by job_id
        Customer
            40   List all        N/A                             JSON        Returns all columns as associative array
            41   New             customer_id,name,notes          string
            42   Edit            customer_id,new_name,           string
                                 name,notes
            43   Delete          name                            string
        General
            90   Lookup          table,column,search             JSON         Returns all matching rows where column data = search in table

*/

require_once 'database.php';
require_once 'helpers.php';
// files that contain functions for tasks:
require_once 'employee.php';
require_once 'log.php';
require_once 'product.php';
require_once 'job.php';
require_once 'customer.php';


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
    /* Employee Tasks */
    case 1:
        $result = json_encode(get_employees());
        break;
    case 2:
        $result = new_employee($args);
        break;
    case 3:
        $result = edit_employee($args);
        break;
    case 4:
        $result = delete_employee($args);
        break;
    /* Log Tasks */
    case 10:
        $result = json_encode(get_logs());
        break;
    case 11:
        $result = new_log($args);
        break;
    case 12:
        $result = json_encode(last_log($args));
        break;
    /* Product Tasks */
    case 20:
        $result = json_encode(get_products());
        break;
    case 21:
        $result = new_product($args);
        break;
    case 22:
        $result = edit_product($args);
        break;
    case 23:
        $result = delete_product($args);
        break;
    /* Job Tasks */
    case 30:
        $result = json_encode(get_jobs());
        break;
    case 31:
        $result = new_job($args);
        break;
    case 32:
        $result = edit_job($args);
        break;
    case 33:
        $result = delete_job($args);
        break;
    case 34:
        $result = json_encode(get_sorted_jobs());
        break;
    /* Customer Tasks */
    case 40:
        $result = json_encode(get_customers());
        break;
    case 41:
        $result = new_customer($args);
        break;
    case 42:
        $result = edit_customer($args);
        break;
    case 43:
        $result = delete_customer($args);
        break;
    /* General Tasks - Works on all tables */
    case 90:
        $result = json_encode(lookup($args));
        break;
}

echo $result;

?>