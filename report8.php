<?php

require_once 'pdf.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

// PHP code
$data = [1, 2, 3, 4, 5];  // Sample data

// Convert the data to a JSON string
$dataJson = json_encode($data);


// Execute the Python script and pass the data
$pythonScript = "python reports.py '{$dataJson}'";
$result = shell_exec($pythonScript);


// Process the result
$resultArray = json_decode($result, true);

// Use the processed data in PHP
print_r($result);
