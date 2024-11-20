<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();
header('Content-Type: application/json');

$output = array();
$all_generic = $override->get('clients', 'status', 1);
foreach ($all_generic as $name) {
    $output[] = $name['firstname'];
}
echo json_encode($output);


// Database connection info 
// $dbDetails = array(
//     'host' => 'localhost',
//     'user' => 'root',
//     'pass' => 'root',
//     'db'   => 'codexworld'
// );

// // DB table to use 
// $table = 'members';

// // Table's primary key 
// $primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables. 
// The `db` parameter represents the column name in the database.  
// The `dt` parameter represents the DataTables column identifier. 
$columns = array(
    array('db' => 'firstname', 'dt' => 0),
    array('db' => 'middlename',  'dt' => 1),
    array('db' => 'lastname',  'dt' => 2),
    array('db' => 'gender',     'dt' => 3),
    array('db' => 'study_id',  'dt' => 4),
    array(
        'db'        => 'created',
        'dt'        => 5,
        'formatter' => function ($d, $row) {
            return date('jS M Y', strtotime($d));
        }
    ),
    array(
        'db'        => 'status',
        'dt'        => 6,
        'formatter' => function ($d, $row) {
            return ($d == 1) ? 'Active' : 'Inactive';
        }
    )
);

$searchFilter = array();
if (!empty($_GET['search_keywords'])) {
    $searchFilter['search'] = array(
        'firstname' => $_GET['search_keywords'],
        'middlename' => $_GET['search_keywords'],
        'lastname' => $_GET['search_keywords'],
        'gender' => $_GET['search_keywords'],
        'study_id' => $_GET['search_keywords']

    );
}
if (!empty($_GET['filter_option'])) {
    $searchFilter['filter'] = array(
        'gender' => $_GET['filter_option']
    );
}

// Include SQL query processing class 
require 'ssp.class.php';

// Output data as json format 
echo json_encode(
    SSP::simple($_GET, $dbDetails, $table, $primaryKey, $columns, $searchFilter)
);
