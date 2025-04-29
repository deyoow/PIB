<?php
// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the required parameters are set
    if (isset($_POST["inventory_no"]) && isset($_POST["new_status"])) {
        // Include your database connection file
        include($_SERVER['DOCUMENT_ROOT'] . '/PIB/connection/connect.php');

        // Sanitize and validate input
        $inventory_no = $_POST["inventory_no"];
        $new_status = $_POST["new_status"];

        // Update the status in the database
        $query = "UPDATE inventory SET stock_status = ? WHERE inventory_no = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $new_status, $inventory_no);
        if ($stmt->execute()) {
            // Return success message
            echo "success";
        } else {
            // Return error message
            echo "error";
        }
    } else {
        // Return error message if parameters are missing
        echo "error";
    }
} else {
    // Return error message if not a POST request
    echo "error";
}
?>
