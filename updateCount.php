<?php
session_start();
include("connection/connect.php");

function getCountYes() {
    global $db;

    $queryYes = "SELECT COUNT(*) AS countYes FROM inventory WHERE stock_status = 'YES'";
    $resultYes = mysqli_query($db, $queryYes);
    $countYes = mysqli_fetch_assoc($resultYes)['countYes'];

    return $countYes;
}

// Return count as JSON
echo json_encode(['countYes' => getCountYes()]);
?>
