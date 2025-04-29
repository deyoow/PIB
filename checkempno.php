<?php
// Include the database connection
include('connection/connect.php');

if (isset($_POST['emp_no'])) {
    $emp_no = $_POST['emp_no'];

    // Query to check if the employee number already exists
    $checkSql = "SELECT emp_no FROM employeeinfo WHERE emp_no = '$emp_no'";
    $result = $db->query($checkSql); // Use $db for the database connection

    if ($result->num_rows > 0) {
        // Employee number exists
        echo 'exists';
    } else {
        // Employee number does not exist
        echo 'not_exists';
    }
}
?>
