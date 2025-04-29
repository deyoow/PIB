<?php
// Assuming you have a database connection established

include("connection/connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get employee number from the query parameters
    $empNo = $_GET['emp_no'];

    // Validate and sanitize the input (you may need a more robust validation)
    $empNo = mysqli_real_escape_string($db, $empNo);

    // Hash the new password
    $newPassword = password_hash('Initial@1', PASSWORD_DEFAULT);

    // Update the hashed password for the employee using a prepared statement
    $updateQuery = "UPDATE employeeinfo SET password = ? WHERE emp_no = ?";
    
    $stmt = mysqli_prepare($db, $updateQuery);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ss', $newPassword, $empNo);

        if (mysqli_stmt_execute($stmt)) {
            echo "Password for employee $empNo updated successfully!";
        } else {
            echo "Error updating password: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($db);
    }
} else {
    echo "Invalid request method. Use GET.";
}
?>
