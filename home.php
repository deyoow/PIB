<!DOCTYPE html>
<html lang="en">
<?php
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['emp_no'])) {
    // If the user is not authenticated, redirect them to the login page
    echo '<script>
            window.location.href = "/PIB";
          </script>';
    exit; // Make sure to exit to prevent further execution of the dashboard page
}

// The rest of your code for the login page goes here
include($_SERVER['DOCUMENT_ROOT'] . '/PIB/connection/connect.php');
error_reporting(0);

// Get the user ID from the session
$userId = $_SESSION['emp_no'];

// Function to fetch the count of "YES" values

function getCountYes($userId) {
    global $db;

    // Fetch dealer name associated with the user
    $queryDealer = "SELECT dealer FROM employeeinfo WHERE emp_no = ?";
    $stmtDealer = mysqli_prepare($db, $queryDealer);
    mysqli_stmt_bind_param($stmtDealer, "s", $userId);
    mysqli_stmt_execute($stmtDealer);
    mysqli_stmt_bind_result($stmtDealer, $dealerName);
    mysqli_stmt_fetch($stmtDealer);
    mysqli_stmt_close($stmtDealer);

    // Query to count "YES" values for the user's dealer
    $queryYes = "SELECT COUNT(*) AS countYes FROM listpurchased WHERE status = 'For Approval' AND dealer_name = ?";
    $stmtYes = mysqli_prepare($db, $queryYes);
    mysqli_stmt_bind_param($stmtYes, "s", $dealerName);
    mysqli_stmt_execute($stmtYes);
    mysqli_stmt_bind_result($stmtYes, $countYes);
    mysqli_stmt_fetch($stmtYes);
    mysqli_stmt_close($stmtYes);

    return $countYes;
}


function getPending($userId) {
    global $db;

    $queryPending = "SELECT COUNT(*) AS countYes FROM listpurchased WHERE status IN ('For Approval') AND emp_no = ?";

    $stmt = mysqli_prepare($db, $queryPending);
    mysqli_stmt_bind_param($stmt, "s", $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $countPending);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    return $countPending;
}

$currentCountYes = getCountYes($userId);
$currentPending = getPending($userId);

function getBasket($userId) {
    global $db;

    $queryBasket = "SELECT COUNT(*) AS countBasket FROM listpurchased WHERE status IN ('For Approval', 'Approved') AND emp_no = ?";
    $stmt = mysqli_prepare($db, $queryBasket);
    mysqli_stmt_bind_param($stmt, "s", $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $basketCount);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    return $basketCount;
}

function getPendingItem($userId) {
    global $db;

    $queryPending = "SELECT COUNT(*) AS pending FROM listpurchased WHERE status = 'For Approval' AND emp_no = ?";
    $stmt = mysqli_prepare($db, $queryPending);
    mysqli_stmt_bind_param($stmt, "s", $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $pendingCount);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    return $pendingCount;
}

function getCheckoutItem($userId) {
    global $db;

    $queryCheckout = "SELECT COUNT(*) AS checkout FROM listpurchased WHERE status = 'Approved' AND emp_no = ?";
    $stmt = mysqli_prepare($db, $queryCheckout);
    mysqli_stmt_bind_param($stmt, "s", $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $checkoutCount);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    return $checkoutCount;
}

function getOrderedItemCount($userId) {
    global $db;

    $queryOrdered = "SELECT COUNT(*) AS ordered FROM listpurchased WHERE status IN ('Approved', 'For Approval', 'No Purchase Order') AND emp_no = ?";
    $stmt = mysqli_prepare($db, $queryOrdered);
    mysqli_stmt_bind_param($stmt, "s", $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $orderedCount);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    return $orderedCount;
}


// Initial count
$currentCountYes = getCountYes($userId);
$currentPending = getPending($userId);

// Now use the function to get the profile picture URL
$profilePictureUrl = getProfilePictureUrl($userId);

// Function to fetch the profile picture URL
function getProfilePictureUrl($userId) {
    global $db;

    $query = "SELECT profile_pic FROM employeeinfo WHERE emp_no = ?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "s", $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $profilePictureFileName);

    // Fetch the profile picture file name or path
    if (mysqli_stmt_fetch($stmt)) {
        // Replace with your actual path or logic to handle profile pictures
        return "./img/profilepic/" . $profilePictureFileName;
    } else {
        // If no profile picture found, return a default image URL
        return "./img/profilepic/defaultbg.jpg";
    }
}

// Example query to fetch position based on user's emp_no
$positionQuery = "SELECT position FROM employeeinfo WHERE emp_no = ?";
$stmtPosition = mysqli_prepare($db, $positionQuery);
mysqli_stmt_bind_param($stmtPosition, "s", $_SESSION['emp_no']);
mysqli_stmt_execute($stmtPosition);
mysqli_stmt_bind_result($stmtPosition, $position);

// Fetch the position
if (mysqli_stmt_fetch($stmtPosition)) {
    // Now, $position contains the user's position
}

// Close the statement
mysqli_stmt_close($stmtPosition);

function getImagePath($filename) {
    // Replace this with your actual path or logic to handle image paths
    return "/PIB/user/img/stocks/" . $filename;
}

// Fetch access level from the database
$accessQuery = "SELECT access FROM employeeinfo WHERE emp_no = ?";
$stmtAccess = mysqli_prepare($db, $accessQuery);
mysqli_stmt_bind_param($stmtAccess, "s", $_SESSION['emp_no']);
mysqli_stmt_execute($stmtAccess);
mysqli_stmt_bind_result($stmtAccess, $access);

// Fetch the access level
if (mysqli_stmt_fetch($stmtAccess)) {
    // Now, $access contains the user's access level
}

// Close the statement
mysqli_stmt_close($stmtAccess);

// Check if the user is an administrator or super administrator
$isAdministrator = ($access === "Administrator");
$isSuperAdmin = ($access === "Super Admin");

$accessQuery = "SELECT position FROM employeeinfo WHERE emp_no = ?";
$stmtPosition = mysqli_prepare($db, $accessQuery);
mysqli_stmt_bind_param($stmtPosition, "s", $_SESSION['emp_no']);
mysqli_stmt_execute($stmtPosition);
mysqli_stmt_bind_result($stmtPosition, $position);

// Fetch the access level
if (mysqli_stmt_fetch($stmtPosition)) {
    // Now, $access contains the user's access level
}

// Close the statement
mysqli_stmt_close($stmtPosition);

$isPartManager = ($position === "Parts Manager");

?>



<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <!-- Boxicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-a0q0kMDDv4xwPXQdKvHGTzZ5DhXL1eP2ucCeF3vHjzpOnj26jicn2Q8G96eqL8I7ov9/Svr2+K5Iok4CVUpfTQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/ionicons@5.0.1/dist/ionicons.min.css">
    
    
    <!-- My CSS -->
    <link rel="stylesheet" href="assets/css/stylebasket.css">

    <title>Home | OG Parts Inventory Basket</title>
    <link rel="icon" type="image/png" href="/PIB/assets/images/logo.png">
</head>

<body>


	<!-- SIDEBAR -->
    <section id="sidebar">
		<a href="./dashboard" class="brand">
			<img src="./assets/images/logo.png" alt="Logo">
			<span class="text">PIB</span><br>
		</a>
		
		<ul class="menu-list">
            <li class="menu-item">
            <a href="./home" style="background-color: maroon;"><i class='bx bxs-home bx-fw' style="color: orange;"></i> Home</a>


            </li>
            <li class="menu-item">
                <a href="./dashboard"><i class='bx bxs-chart bx-fw'></i> Dashboard</a>
            </li>

            <li class="menu-item has-submenu">
                <a href="./user/myBasket" onclick="openCart();"><i class='bx bxs-cart bx-fw'></i> My Basket</a>
                <ul class="submenu">
                <!-- <li class="submenu-item"><a href="/OGBasket/user/myProfile"><i class='bx bx-user bx-fw'></i> My Profile</a></li> -->
                <!-- <li class="submenu-item"><a href="./user/myBasket" onclick="openCart();"><i class='bx bxs-cart bx-fw'></i> My Basket</a></li>
                <li class="submenu-item"><a href="./user/pendingOrder"><i class='bx bx-time bx-fw'></i> My Pending Order</a></li>
                <li class="submenu-item"><a href="./user/checkoutList"><i class='bx bx-list-check bx-fw'></i> My Checkout List</a></li>
                <li class="submenu-item"><a href="./user/cancelledOrder"><i class='bx bx-x-circle bx-fw'></i> Cancelled Order</a></li>
                <li class="submenu-item"><a href="./user/pendingOrderList"><i class='bx bx-box bx-fw'></i> Pending Order List</a></li> -->
                <!-- <li class="submenu-item"><a href="./user/changePassword"><i class='bx bx-key bx-fw'></i> Change Password</a></li>
                <li class="submenu-item"><a href="./admin/reports"><i class='bx bx-file bx-fw'></i> Generate Report</a></li> -->

                </ul>
            </li>
            
            <li class="menu-item">
                <a href="./user/manageStocks"><i class='bx bx-list-plus bx-fw'></i> Manage Stocks</a>
            </li>

            <!-- <li class="menu-item">
                <a href="./dealer/dealerStocks"><i class='bx bxs-package bx-fw'></i> Dealer Stocks</a> -->
                <!-- <ul class="submenu">
                    <li class="submenu-item"><a href="./dealer/TBKstocks"><i class='bx bx-building bx-fw'></i>TBK</a></li>
                    <li class="submenu-item"><a href="./dealer/TOTstocks"><i class='bx bx-building bx-fw'></i>TOT</a></li>
					<li class="submenu-item"><a href="./dealer/TNEstocks"><i class='bx bx-building bx-fw'></i>TNE</a></li>
					<li class="submenu-item"><a href="./dealer/TMRstocks"><i class='bx bx-building bx-fw'></i>TMR</a></li>
					<li class="submenu-item"><a href="./dealer/TSJstocks"><i class='bx bx-building bx-fw'></i>TSJ</a></li>
					<li class="submenu-item"><a href="./dealer/allstocks"><i class='bx bx-building bx-fw'></i>All Stocks</a></li>
				</ul> -->
            <!-- </li> -->

            <li class="menu-item">
                <a href="./admin/reports"><i class='bx bx-file bx-fw'></i>Reports</a>
            </li>


            <?php if ($isAdministrator || $isSuperAdmin || $isPartManager) : ?>
            <li class="menu-item has-submenu">
                <a href="#"><i class='bx bxs-shield bx-fw'></i> Admin Console<span class="arrow">&#9656;</span></a>
                <ul class="submenu">
                    <li class="submenu-item"><a href="./admin/employeeInformation"><i class='bx bx-user bx-fw'></i> Employee Information</a></li>
                    <li class="submenu-item"><a href="./admin/pendingApproval"><i class='bx bx-time bx-fw'></i> Pending Approval</a></li>
                    <?php if ($isSuperAdmin) : ?>
                        <li class="submenu-item"><a href="./admin/changePasswordadmin"><i class='bx bx-key bx-fw'></i> Change Password</a></li>
                        <li class="submenu-item"><a href="./admin/manageDealer"><i class='bx bx-plus bx-fw'></i> Manage Dealer</a></li>
                        <li class="submenu-item"><a href="./admin/manageDepartment"><i class='bx bx-plus bx-fw'></i> Manage Department</a></li>
                        <li class="submenu-item"><a href="./admin/managePosition"><i class='bx bx-plus bx-fw'></i> Manage Position</a></li>
                        <li class="submenu-item"><a href="./admin/manageUnits"><i class='bx bx-plus bx-fw'></i> Manage Units</a></li>
                    <?php endif; ?>
                    <?php if ($isAdministrator || $isSuperAdmin) : ?>
                        <li class="submenu-item"><a href="./admin/auditLogs"><i class='bx bx-file bx-fw'></i> Audit Logs</a></li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>



            <li class="menu-item">
				<a href="#" class="profile" id="logout"><i class='bx bxs-log-out bx-fw'></i> Logout</a>
            </li>
                    </ul>


        <p class="sidebar-footer"><b>Developed by:</b><br>Toyota San Jose Del Monte, Bulacan <br>MIS | <span id="currentYear"></span></p>

        <script>
            var currentYear = new Date().getFullYear();
            document.getElementById("currentYear").innerText = currentYear;
        </script>


	</section>
	<!-- SIDEBAR -->

    <div class="modal fade" id="addToCartModal" tabindex="-1" aria-labelledby="addToCartModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addToCartModalLabel">Item Description</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addToCartForm" action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="partNo" class="form-label"><strong>Part Number</strong></label>
                        <input type="text" class="form-control" id="partNo" name="partNo" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="partDescription" class="form-label"><strong>Part Description</strong></label>
                        <input type="text" class="form-control" id="partDescription" name="partDescription" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="dealer" class="form-label"><strong>Dealer</strong></label>
                        <input type="text" class="form-control" id="dealer" name="dealer" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="itemNote" class="form-label"><strong>Item Note</strong></label>
                        <input type="text" class="form-control" id="itemNote" name="itemNote" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="onhandQty" class="form-label"><strong>On Hand Quantity</strong></label>
                        <input type="text" class="form-control" id="onhandQty" name="onhandQty" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label"><strong>Quantity</strong><span style="color: red">*</span></label>
                        <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" oninput="checkQuantity(this)">

                    </div>

                    <script>
                        function checkQuantity(input) {
                            var enteredQuantity = parseInt(input.value.replace(/,/g, ''));
                            var availableQuantity = parseInt(document.getElementById('onhandQty').value.replace(/,/g, ''));

                            if (enteredQuantity > availableQuantity) {
                                alert("Quantity exceeds available stock!");
                                // Set the input value back to the available quantity
                                input.value = numberWithCommas(availableQuantity);
                            }
                        }

                        // Function to add commas for thousand separators
                        function numberWithCommas(x) {
                            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        }
                    </script>



                        </script>
                    
                    <div class="mb-3">
                        <label for="personInCharge" class="form-label"><strong>Person in Charge</strong></label>
                        <input type="text" class="form-control" id="personInCharge" name="personInCharge" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="contactNo" class="form-label"><strong>Contact Number</strong></label>
                        <input type="text" class="form-control" id="contactNo" name="contactNo" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="fileInput" class="form-label"><strong>Upload PO</strong></label>
                        <input type="file" class="form-control" id="fileInput" name="fileInput">
                    </div>
                    <div class="mb-3">
                        <label for="remarks" class="form-label"><strong>Remarks</strong></label>
                        <textarea class="form-control" id="remarks" name="remarks"></textarea>
                    </div>
                    <br>
                    <!-- <button type="submit" name="submit" class="btn btn-primary" style="font-size:17px; font-weight:bold; border-radius:8px; width:160px; height: 50px; background-color:maroon; border: none;" onclick="return confirmAddToCart();"><ion-icon name="cart-sharp"></ion-icon>Add to Basket</button> -->
                    <button type="submit2" name="submit2" class="btn btn-success" style="font-size:17px; font-weight:bold; border-radius:8px; width:160px; height: 50px; background-color:#FFA550; border: none;" onclick="return checkout()">
                        <ion-icon name="checkmark-circle-sharp"></ion-icon> Check out
                    </button>


                </form>
            </div>
        </div>
    </div>
</div>

    <script>
        function checkout() {
                var fileInput = document.getElementById('fileInput');
                
                // if (!fileInput.files || fileInput.files.length === 0) {
                //     alert("Please upload Purchase Order (PO) file.");
                //     return false;
                // }

                var quantityInput = document.getElementById('quantity');
                var quantityValue = parseInt(quantityInput.value);
                
                if (isNaN(quantityValue) || quantityValue === 0) {
                    alert("Quantity cannot be 0 or empty. Please enter a valid quantity.");
                    return false;
                }

                var checkoutConfirmation = confirm("Are you sure you want to checkout?");

                if (checkoutConfirmation) {
                    alert("The item is already checked out! Please wait for the approval.");
                    return true;
                } else {
                    return false;
                }
            }
    </script>

<!-- submit2 -->

<?php
error_reporting(E_ALL);

if (isset($_POST['submit2'])) {
    include("connection/connect.php");

    // Check if the database connection is successful
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    // Get the quantity ordered from the form input
    $quantityOrdered = $_POST['quantity'];

    // Retrieve the current on-hand quantity from the inventory table
    $part_no = $_POST['partNo'];
    $stmt = $db->prepare("SELECT onhand_qty FROM inventory WHERE part_no = ?");
    $stmt->bind_param("s", $part_no);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $currentOnhandQty = $row['onhand_qty'];
    $stmt->close();

    // Calculate the new on-hand quantity based on the current on-hand quantity and the quantity ordered
    $newOnhandQty = $currentOnhandQty - $quantityOrdered;

    // Check if a file is uploaded
    if (isset($_FILES['fileInput']) && $_FILES['fileInput']['error'] == 0) {
        // Define the target directory where the file will be saved
        $targetDir = "pofiles/";

        // Generate a unique filename using a timestamp and a random string
        $uniqueFilename = time() . '_' . $_FILES['fileInput']['name']; // Using the original filename

        // Create the target path with the unique filename
        $targetPath = $targetDir . $uniqueFilename;

        // Check if the file was successfully moved to the target directory
        if (move_uploaded_file($_FILES['fileInput']['tmp_name'], $targetPath)) {
            // Successfully uploaded
            // Set the file name to be saved in the database
            $poFilename = $uniqueFilename;

            // Update date_po only when a file is uploaded
            $date_po = date("Y-m-d H:i:s"); // Current date and time
        } else {
            echo "Sorry, there was an error uploading your file.";
            // Set default filename or handle error as needed
            $poFilename = ""; // Set default filename or handle error as needed
            $date_po = ""; // Set default date or handle error as needed
        }
    } else {
        // No file uploaded, handle as needed
        $poFilename = ""; // Set default filename or handle error as needed
        $date_po = ""; // No file uploaded, so date_po remains empty
    }

    // Generate a random number (6 digits)
    $randomPart = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

    // Concatenate "4101" with the random part
    $purchase_id = "4101" . $randomPart;

    // Retrieve current user's first name and last name from employeeinfo (replace 'current_user_id' with the actual user ID)
    $current_user_id = $_SESSION['emp_no'];
    $query = "SELECT emp_no FROM employeeinfo WHERE emp_no = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $current_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    $emp_no = $row['emp_no'];

    // Retrieve purchaser name from the employeeinfo table
    $current_user_identifier = $_SESSION['emp_no']; // Assuming 'emp_no' is a word identifier for the current user
    $query = "SELECT first_name, last_name FROM employeeinfo WHERE emp_no = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $current_user_identifier); // Assuming 'emp_no' is a string identifier
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    $purchaser_name = $row['first_name'] . " " . $row['last_name'];

    // Retrieve the pic_no from the inventory table
    $query = "SELECT pic_no FROM inventory WHERE part_no = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $part_no);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    $pic_no = $row['pic_no'];

    // Prepare and execute the insert statement
    $stmt = $db->prepare("INSERT INTO listpurchased (purchase_id, dealer_name, part_no, part_description, item_note, total_stocks, qty_ordered, date_sold, contact_no, pic_no, person_incharge, emp_no, purchaser_name, po, date_checkout, remarks, status) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)"); 
    $stmt->bind_param("ssssssssssssssss", $purchase_id, $dealer_name, $part_no, $part_description, $item_note, $total_stocks, $qty_ordered, $contact_no, $pic_no, $person_incharge, $emp_no, $purchaser_name, $poFilename, $date_po, $remarks, $status);

    date_default_timezone_set('Asia/Manila');
    $date_po = date("Y-m-d (h:i:s a)");
    // Set parameters
    $dealer_name = $_POST['dealer'];
    $part_no = $_POST['partNo'];
    $part_description = $_POST['partDescription'];
    $total_stocks = $_POST['onhandQty']; // Original total stocks
    $qty_ordered = $quantityOrdered; // Use the quantity ordered from the form input
    $contact_no = $_POST['contactNo'];
    $person_incharge = $_POST['personInCharge'];
    $remarks = $_POST['remarks'];
    $item_note = $_POST['itemNote'];
    $status = "For Approval";

    // Execute the statement
    if ($stmt->execute()) {
        // Update the inventory table with the new on-hand quantity
        $stmt = $db->prepare("UPDATE inventory SET onhand_qty = ? WHERE part_no = ?");
        $stmt->bind_param("ss", $newOnhandQty, $part_no);
        $stmt->execute();
        
        echo "<script>window.location.href='/PIB/home';</script>";
        exit();
    } else {
        // Handle the case where the query fails
        echo "Error: " . $stmt->error;
    }

    // Close statement
    $stmt->close();
    // Close connection
    $db->close();
}
?>


<script>


// Function to add commas to numbers for thousands separators
function addCommasToNumber(number) {
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// Function to remove commas from numbers
function removeCommasFromNumber(number) {
    return number.toString().replace(/,/g, "");
}

// Event listener for input in the quantity text field
document.addEventListener('input', function(event) {
    const target = event.target;
    if (target.classList.contains('qty')) {
        // Remove commas from the input value
        const cleanedValue = removeCommasFromNumber(target.value);
        // Add commas to the cleaned value
        const formattedValue = addCommasToNumber(cleanedValue);
        // Update the input value with formatted value
        target.value = formattedValue;
    }
});

</script>


<script>
    function showConfirmation() {
        // Show a confirmation dialog
        return confirm("Are you sure all the information is correct?");
    }
</script>




<script>
    // Function to populate modal fields with data from the selected row
    function populateModalFields(row) {
        // Get the table row elements
        var cells = row.getElementsByTagName("td");

        // Get the modal input fields
        var partNoInput = document.getElementById("partNo");
        var partDescriptionInput = document.getElementById("partDescription");
        var dealerInput = document.getElementById("dealer");
        var onhandQtyInput = document.getElementById("onhandQty");
        var priceInput = document.getElementById("price");
        var personInChargeInput = document.getElementById("personInCharge");
        var contactNoInput = document.getElementById("contactNo");
        var itemNoteInput = document.getElementById("itemNote");

        // Populate modal input fields with data from the selected row
        partNoInput.value = cells[1].innerText;
        partDescriptionInput.value = cells[2].innerText;
        dealerInput.value = cells[7].innerText;
        onhandQtyInput.value = cells[3].innerText;
        contactNoInput.value = cells[8].innerText;
        personInChargeInput.value = cells[6].innerText;
        itemNoteInput.value = cells[10].innerText;
        
    }

    // Add event listeners to each table row
    document.addEventListener("DOMContentLoaded", function () {
        var rows = document.querySelectorAll("tbody.stocks-table-body tr");
        rows.forEach(function (row) {
            row.addEventListener("click", function () {
                populateModalFields(row);
            });
        });
    });
</script>

    


	<!-- CONTENT -->
	<section id="content">
		<!-- NAVBAR -->
		<nav>
			<!-- Add an id to the menu icon -->
	<i id="menuIcon" class='bx bx-menu' style="font-size: 30px; color: white;"></i>

<form action="#">
  <div class="form-input">
    <input type="search" class="search-input" placeholder="Search" id="searchInput1">
    <button type="submit" class="search-btn"><i class='bx bx-search'></i></button>

    <!-- Dropdown list -->
    <div class="search-dropdown" id="searchDropdown"></div>
  </div>
</form>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput1');
    const searchDropdown = document.getElementById('searchDropdown');

    const dropdownItems = [
      { link: "./home", name: "Home" },
      { link: "./dashboard", name: "Dashboard" },
      { link: "./user/myBasket", name: "My Basket" },
      { link: "./user/manageStocks", name: "Manage Stocks" },
      { link: "./dealer/dealerStocks", name: "OG Stocks" },
      { link: "./user/pendingOrderList", name: "Pending Order List" },
      { link: "./user/changePassword", name: "Change Password" },
      { link: "./admin/reports", name: "Generate Report" }
    ];

    searchInput.addEventListener('input', function () {
      const searchTerm = searchInput.value.toLowerCase();

      const filteredItems = dropdownItems.filter(item =>
        item.link.toLowerCase().includes(searchTerm) || item.name.toLowerCase().includes(searchTerm)
      );

      const dropdownHTML = filteredItems.map(item => `
        <div class="search-dropdown-item">
          <a href="${item.link}">${item.name}</a>
        </div>
      `).join('');

      searchDropdown.innerHTML = dropdownHTML;
      searchDropdown.style.display = filteredItems.length > 0 ? 'block' : 'none';
    });

    document.addEventListener('click', function (event) {
      if (!searchInput.contains(event.target) && !searchDropdown.contains(event.target)) {
        searchDropdown.style.display = 'none';
      }
    });

    searchDropdown.addEventListener('click', function (event) {
      // Check if the clicked element is within a search-dropdown-item
      let clickedItem = event.target;
      while (clickedItem && !clickedItem.classList.contains('search-dropdown-item')) {
        clickedItem = clickedItem.parentNode;
      }

      if (clickedItem) {
        // Navigate to the selected link
        window.location.href = clickedItem.querySelector('a').getAttribute('href');
      }
    });
  });
</script>




			<input type="checkbox" id="switch-mode" hidden>
			<label for="switch-mode" class="switch-mode"></label>
            
			<!-- Dropdown -->
			<div class="notification" id="notificationDropdown">
                <a href="./user/pendingOrderList" class="btn btn-secondary" aria-haspopup="true" aria-expanded="false">
                <i class="bx bxs-bell bx-fw" style="color: white;"></i>

                    <span class="num" id="totalCountYes"><?php echo $currentCountYes; ?></span>
                </a>
                <a href="./user/pendingOrderList">
                <div class="notification-box">
                    <span class="cart-message" style = "color: black;">You have <?php echo $currentCountYes; ?> need to approve!</span>
                </div>
                </a>
            </div>
            <div class="notification" id="notificationDropdown">
                <a href="./user/myBasket" class="btn btn-secondary" aria-haspopup="true" aria-expanded="false">
                <i class="bx bxs-time bx-fw" style="color: white;"></i>
                    <span class="num" id="totalCountYes"><?php echo $currentPending; ?></span>
                </a>
                <a href="./user/myBasket">
                <div class="notification-box">
                    <span class="cart-message" style = "color: black;">You have <?php echo $currentPending; ?> order(s) waiting for approval!</span>
                </div>
                </a>
            </div>



			<?php
				if (isset($_SESSION['first_name']) && isset($_SESSION['last_name'])) {
					echo '<strong class="welcome-message" style="color: white;">Welcome, ' . $_SESSION['first_name'] . '!</strong>';
				}
			?>
			<div class="dropdown">
                <a href="#" class="profile" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?php echo $profilePictureUrl; ?>" alt="Profile Picture">
                </a>
                <ul class="dropdown-menu" aria-labelledby="profileDropdown">
                    <!-- Dropdown items go here -->
                    <li><span class="dropdown-item-text" style="font-size: 11px; color: black;"> <?php echo $position; ?></span></li>
                    <li><span class="dropdown-item-text" style="font-weight: bold; color: black; font-size: 20px;"> <?php echo $userId; ?></span></li>
                    <li><a class="dropdown-item" href="./user/myProfile" style="font-weight: bold; color: black; font-size: 12px;"> View Profile</a></li>
                    <li><a class="dropdown-item" href="./user/changePassword" style="font-weight: bold; color: black; font-size: 12px;"> Change Password</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" style="font-weight: bold; color: maroon;" href="#" onclick="logoutUser()"> <i class='bx bx-log-out bx-fw'></i>Logout</a></li>
                </ul>
            </div>
		</nav>
		<!-- NAVBAR -->

		<!-- MAIN -->

        
<main>


<style>
    .circle-btn {
        width: 5rem; /* Adjust the size as needed */
        height: 5rem; /* Adjust the size as needed */
        border-radius: 50%; /* Make it circular */
        display: flex;
        flex-direction: column; /* Stack icon and text vertically */
        justify-content: center;
        align-items: center;
        overflow: hidden; /* Hide overflowing content */
    }

    .circle-btn span {
    margin-top: 10rem; /* Adjust spacing between icon and text */
    font-size: 1.5rem; /* Change the font size as needed */
    max-width: 100%; /* Limit maximum width to prevent excessive wrapping */
    text-align: center; /* Center text */
    }

   

    .circle-btn ion-icon.white-icon {
        color: white; /* Set the icon color to white */
    }

    @media (max-width: 767px) {
    .col-md-3 {
        width: 50%; /* Make each column take up half of the width */
    }

    .col-md-3 {
        width: calc(50% - 20px); /* Make each column take up half of the width minus margins */
        margin: 0 0 0 10px; /* Add a 10px margin to the right of each column */
    }
    .notification {
        float: right; /* Align the notification to the right */
    }
    .notification {
        margin-right: -10px; /* Adjust the value as needed for mobile */
    }
    .circle-btn {
        width: 5rem; /* Adjust the size as needed */
        height: 5rem; /* Adjust the size as needed */
        border-radius: 50%; /* Make it circular */
        display: flex;
        flex-direction: column; /* Stack icon and text vertically */
        justify-content: center;
        align-items: center;
        overflow: hidden; /* Hide overflowing content */
    }
    
}

    
</style>

        
<!-- <div class="table-data-2">
    <div class="order">
        <div class="head">
            <h3><strong>My Purchases</strong></h3>
        </div><br>
        <div class="row">
            <!-- Option 1: My Basket -->
            <!-- <div class="col-md-4 d-flex justify-content-center">
                <a href="./user/myBasket">
                    <button class="btn btn-primary btn-block circle-btn">
                        <ion-icon name="basket" class="ion-icon" style="font-size: 4rem;"></ion-icon>
                    </button>
                    <span style="font-size: 18px; font-weight:bold; overflow: hidden; margin-left: -0.8rem;">My Basket</span>
                    <span class="num" id="totalCountYes"><?php echo getBasket($userId); ?></span>
                </a>

            </div> -->
            <!-- Option 2: Pending Item Approval -->
            <!-- <div class="col-md-4 d-flex justify-content-center">
            <a href="./user/pendingOrder">
                <button class="btn btn-warning btn-block circle-btn">
                    <ion-icon name="time" class="ion-icon white-icon" style="font-size: 4rem;"></ion-icon>
                </button>
                <span style="font-size: 18px; overflow: hidden; margin-left: -1.5rem; font-weight:bold;">Pending Order</span>
                <span class="num" id="totalCountYes"><?php echo getPendingItem($userId); ?></span>
            </a>
        </div> -->

            <!-- Option 3: Check out -->
            <!-- <div class="col-md-4 d-flex justify-content-center">
            <a href="./user/checkoutList">
                    <button class="btn btn-success btn-block circle-btn">
                        <ion-icon name="checkmark-circle" class="ion-icon" style="font-size: 5rem;"></ion-icon>
                    </button>
                    <span style="font-size: 18px; font-weight:bold; overflow: hidden; margin-left: -0.5rem;">Check Out</span>
                    <span class="notification" id="totalCountYes"><?php echo getCheckoutItem($userId); ?></span> 
                </a>
            </div> -->


            <!-- Option 4: Ordered List -->
            <!-- <div class="col-md-3 d-flex justify-content-center">
            <a href="/OGBasket/user/checkout">
                    <button class="btn btn-info btn-block circle-btn">
                        <ion-icon name="list" class="ion-icon" style="font-size: 4rem;"></ion-icon>
                    </button>
                    <span style="font-size: 18px; overflow: hidden; margin-left: -2.1rem; font-weight:bold;">Total Purchases</span>
                    <span class="notification"><?php echo getOrderedItemCount($userId); ?></span> 
                </a>
            </div> -->

        <!-- </div>
    </div>
</div><br>

<hr> -->

        <div class="head-title">
    <div class="left">
        <h1>Parts Information Inquiry</h1>
    </div>
</div>

<hr>
<h5>Part Number/s</h5>
<br>



<!-- Container for horizontal alignment -->
<div class="search-container" style="display: flex; justify-content: space-between; flex-wrap: wrap;" style="display: none;">

    <!-- Search Bar 1 -->
    <div class="input-group" style="max-width: 300px;">
    <input type="text" class="form-control" placeholder="Part No 1" aria-label="Search" aria-describedby="basic-addon2" oninput="addHyphens(this)">
        <div class="input-group-append">
            <span class="input-group-text" id="basic-addon2"><i class="bx bx-search"></i></span>
        </div>
    </div>

    <!-- Search Bar 2 -->
    <div class="input-group" style="max-width: 300px;">
        <input type="text" class="form-control" placeholder="Part No 2" aria-label="Search" aria-describedby="basic-addon2" oninput="addHyphens(this)">
        <div class="input-group-append">
            <span class="input-group-text" id="basic-addon2"><i class="bx bx-search"></i></span>
        </div>
    </div>

    <!-- Repeat similar code for Search Bar 3 to Search Bar 10 -->
    <!-- Search Bar 3 -->
    <div class="input-group" style="max-width: 300px;">
        <input type="text" class="form-control" placeholder="Part No 3" aria-label="Search" aria-describedby="basic-addon2" oninput="addHyphens(this)">
        <div class="input-group-append">
            <span class="input-group-text" id="basic-addon2"><i class="bx bx-search"></i></span>
        </div>
    </div>

    <!-- Search Bar 4 -->
    <div class="input-group" style="max-width: 300px;">
        <input type="text" class="form-control" placeholder="Part No 4" aria-label="Search" aria-describedby="basic-addon2" oninput="addHyphens(this)">
        <div class="input-group-append">
            <span class="input-group-text" id="basic-addon2"><i class="bx bx-search"></i></span>
        </div>
    </div>

    <!-- Search Bar 5 -->
    <div class="input-group" style="max-width: 300px;">
        <input type="text" class="form-control" placeholder="Part No 5" aria-label="Search" aria-describedby="basic-addon2" oninput="addHyphens(this)">
        <div class="input-group-append">
            <span class="input-group-text" id="basic-addon2"><i class="bx bx-search"></i></span>
        </div>
    </div>

    <!-- Search Bar 6 -->
    <div class="input-group" style="max-width: 300px;">
        <input type="text" class="form-control" placeholder="Part No 6" aria-label="Search" aria-describedby="basic-addon2" oninput="addHyphens(this)">
        <div class="input-group-append">
            <span class="input-group-text" id="basic-addon2"><i class="bx bx-search"></i></span>
        </div>
    </div>

    <!-- Search Bar 7 -->
    <div class="input-group" style="max-width: 300px;">
        <input type="text" class="form-control" placeholder="Part No 7" aria-label="Search" aria-describedby="basic-addon2" oninput="addHyphens(this)">
        <div class="input-group-append">
            <span class="input-group-text" id="basic-addon2"><i class="bx bx-search"></i></span>
        </div>
    </div>

    <!-- Search Bar 8 -->
    <div class="input-group" style="max-width: 300px;">
        <input type="text" class="form-control" placeholder="Part No 8" aria-label="Search" aria-describedby="basic-addon2" oninput="addHyphens(this)">
        <div class="input-group-append">
            <span class="input-group-text" id="basic-addon2"><i class="bx bx-search"></i></span>
        </div>
    </div>

    <!-- Search Bar 9 -->
    <div class="input-group" style="max-width: 300px;">
        <input type="text" class="form-control" placeholder="Part No 9" aria-label="Search" aria-describedby="basic-addon2" oninput="addHyphens(this)">
        <div class="input-group-append">
            <span class="input-group-text" id="basic-addon2"><i class="bx bx-search"></i></span>
        </div>
    </div>

    <!-- Search Bar 10 -->
    <div class="input-group" style="max-width: 300px;">
        <input type="text" class="form-control" placeholder="Part No 10" aria-label="Search" aria-describedby="basic-addon2" oninput="addHyphens(this)">
        <div class="input-group-append">
            <span class="input-group-text" id="basic-addon2"><i class="bx bx-search"></i></span>
        </div>
    </div>

</div>

<script>
        function addHyphens(input) {
            let value = input.value.toUpperCase(); // Convert input to uppercase
            value = value.replace(/[^\w\s]/gi, ''); // Remove non-alphanumeric characters
            value = value.replace(/\s/g, ''); // Remove whitespace
            value = value.replace(/-/g, ''); // Remove existing hyphens

            let newValue = '';
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 5 === 0) {
                    newValue += '-';
                }
                newValue += value[i];
            }
            input.value = newValue;
        }
    </script>

<hr>


<script>
    // Function to check if any search bar has non-empty value and toggle table data visibility
function checkSearchBars() {
    console.log("Checking search bars...");
    var searchBars = document.querySelectorAll('.search-container input[type="text"]');
    var isEmpty = true;

    searchBars.forEach(function(input) {
        if (input.value.trim() !== '') {
            isEmpty = false;
            console.log('Search query for ' + input.getAttribute('placeholder') + ': ' + input.value);
        }
    });

    var tableData = document.querySelector('.table-data');
    if (isEmpty) {
        tableData.style.display = 'none'; // Hide table data if all search bars are empty
        console.log("Table data hidden");
    } else {
        tableData.style.display = 'block'; // Show table data if any search bar has non-empty value
        console.log("Table data shown");
    }
}

// Function to filter table rows based on part_no column
// Function to filter table rows based on part_no column
function filterTableRows() {
    var searchBars = document.querySelectorAll('.search-container input[type="text"]');
    var tableRows = document.querySelectorAll('.stocks-table-body tr');

    tableRows.forEach(function(row) {
        var shouldDisplay = false; // Initialize as false

        searchBars.forEach(function(input) {
            if (input.value.trim() !== '') {
                var partNoColumn = row.querySelector('td:nth-child(2)'); // Assuming the part_no column is the fifth column
                var partNo = partNoColumn.textContent || partNoColumn.innerText;
                
                if (partNo.toLowerCase().includes(input.value.trim().toLowerCase())) {
                    shouldDisplay = true; // If any search bar matches, set shouldDisplay to true
                }
            }
        });

        row.style.display = shouldDisplay ? 'table-row' : 'none';
    });

    // Show the table data container if there are search results
    checkSearchBars();
}


document.addEventListener('DOMContentLoaded', function () {
    const tableData = document.querySelector('.table-data');
    if (tableData) {
        tableData.style.display = 'none';
        console.log("Table data initially hidden");
    }
});

// Add event listeners to search bars to trigger filtering
document.addEventListener('DOMContentLoaded', function() {
    var searchBars = document.querySelectorAll('.search-container input[type="text"]');
    
    searchBars.forEach(function(input) {
        input.addEventListener('input', function() {
            filterTableRows();
        });
    });

    // Initially check search bars on page load
    checkSearchBars();
});

</script>

<!-- Add this HTML code where you want the Viber icon and dropdown to appear -->

<div class="overlay" id="overlay"></div>

<!-- Viber icon container -->
<div class="viber-icon-container">
    <div class="viber-icon">
        <a class="dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <img src="/PIB/assets/images/viber.png" alt="Viber Chat">
        </a>

        <!-- Bootstrap Dropdown Menu -->
    <div class="dropdown-menu" aria-labelledby="userDropdown">
        <h2 class="dropdown-header font-weight-bold">Chats</h2>

        <!-- Search input for filtering chats with icon -->
        <div class="input-group" style="max-width: 200px;">
            <input type="text" class="form-control" id="chatSearchInput" placeholder="Search" aria-label="Search" aria-describedby="basic-addon2">
            <div class="input-group-append">
                <span class="input-group-text"><i class="bx bx-search"></i></span>
            </div>
        </div>


        <?php
        // Fetch data from the employeeinfo table
        $employeeQuery = "SELECT * FROM employeeinfo WHERE emp_status = 'Active'";
        $employeeResult = mysqli_query($db, $employeeQuery);

        echo '<div class="chats-list">'; // Container for the list of chats

        while ($employeeRow = mysqli_fetch_assoc($employeeResult)) {
            // Add the dealer information in parentheses after the first name
            $dealerInfo = $employeeRow['dealer'] ? ' (' . $employeeRow['dealer'] . ')' : ''; // Check if dealer information is available
        
            // Check if the current user has an active session
            $loggedInClass = (isset($_SESSION['user_authenticated']) && $_SESSION['user_authenticated']) ? 'user-logged-in' : '';
        
            // Add a green rounded indicator circle for the logged-in user
            $indicatorCircle = '<span class="indicator-circle ' . $loggedInClass . '"></span>';
        
            // Create a link that opens Viber with the user's phone number
            $viberLink = 'viber://chat?number=' . urlencode($employeeRow['contact_no']);
        
            // Output HTML with the green rounded circle
            echo '<a class="dropdown-item" href="' . $viberLink . '">'
                . '<span class="rounded-circle indicator-green"></span>'  // Add this line for the green rounded circle
                . $indicatorCircle . $employeeRow['first_name'] . $dealerInfo
                . '</a>';
        }

        echo '</div>'; // Close the container for the list of chats
        ?>
        <!-- Message for no user found during searching -->
<div id="noUserFoundMessage" style="display: none; text-align: center; margin-top: 10px; color: red; font-weight: bold;">No User Found</div>

    </div>


    </div>
    <div class="tooltip">
        <!-- You can customize the tooltip content here if needed -->
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    const chatSearchInput = document.getElementById('chatSearchInput');
    const chatsList = document.querySelector('.chats-list');

    chatSearchInput.addEventListener('input', function () {
        const searchTerm = chatSearchInput.value.trim().toLowerCase();
        const chatItems = chatsList.querySelectorAll('.dropdown-item');

        chatItems.forEach(chatItem => {
            const chatText = chatItem.textContent.trim().toLowerCase();
            const isMatch = chatText.includes(searchTerm);
            chatItem.style.display = isMatch ? '' : 'none';
        });

        // Display a message when no user is found
        const noUserFoundMessage = document.getElementById('noUserFoundMessage');
        if (noUserFoundMessage) {
            const noUserFound = [...chatItems].every(chatItem => chatItem.style.display === 'none');
            noUserFoundMessage.style.display = noUserFound ? 'block' : 'none';
        }
    });
});

</script>


        <?php
				// Fetch distinct dealer names and count occurrences
				$dealerCountQuery = "SELECT dealer_name, COUNT(*) as count FROM inventory GROUP BY dealer_name";
				$dealerCountResult = mysqli_query($db, $dealerCountQuery);

				// Initialize an array to store dealer counts
				$dealerCounts = [];

				// Populate the dealer counts array
				while ($row = mysqli_fetch_assoc($dealerCountResult)) {
					$dealerCounts[$row['dealer_name']] = $row['count'];
				}
		?>

                
        <div class="table-data">
				<div class="order">
					<div class="head">
                        <h3>Available Stocks</h3>
                        <div class="input-group" style="max-width: 300px;">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search" aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <span class="input-group-text" id="basic-addon2"><i class="bx bx-search"></i></span>
                            </div>
                            </div>
						<a href = "/PIB/dealer/dealerStocks"><i class='bx bx-filter' ></i></a>
					</div>
					<table id="dataTable">
						<thead>
							<tr>
								<th>Item</th>
								<!-- <th>No</th> -->
								<th>Part No</th>
								<th>Part Description</th>
								<th>On-Hand QTY</th>
								<th>Model Code</th>
								<th>Unit</th>
                                <th>PIC</th>
                                <th>Dealer</th>
								<th>Contact No</th>
								<th>Date Uploaded</th>
                                <th>Item Note</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody class="stocks-table-body">
						<?php
							// Fetch data from the database
							$query = "SELECT * FROM inventory WHERE stock_status = 'Available' AND onhand_qty > 0";
							$result = mysqli_query($db, $query);

							if ($result) {
								$counter = 1;
								while ($row = mysqli_fetch_assoc($result)) {
									echo '<tr>';
									
                                    echo '<td style="text-align: center; vertical-align: middle;">
                                    <a href="#" class="fullscreen-trigger" data-toggle="modal" data-target="#fullscreen-modal">';

                                    // Check if the image path is not empty, otherwise use a default image
                                    $imagePath = getImagePath($row['item']);
                                    if (!empty($imagePath)) {
                                        echo '<img src="' . $imagePath . '" alt="Image" style="width: 100px; height: 50px; object-fit: cover; border-radius: 0;">';
                                    } else {
                                        echo '<img src="/PIB/assets/images/defaultbg.jpg" alt="Default Image" style="width: 100px; height: 50px; object-fit: cover; border-radius: 0;">';
                                    }

                                    echo '</a>
                                    </td>';
									// echo '<td>' . $row['inventory_no'] . '</td>';
									
									echo '<td>' . $row['part_no'] . '</td>';
									echo '<td>' . $row['part_description'] . '</td>';
									echo '<td>' . (strpos($row['onhand_qty'], ',') !== false ? $row['onhand_qty'] : number_format($row['onhand_qty'])) . '</td>';
									echo '<td>' . $row['model_code'] . '</td>';
									echo '<td>' . $row['unit'] . '</td>';
                                    echo '<td>' . $row['person_incharge'] . '</td>';
                                    echo '<td>' . $row['dealer_name'] . '</td>';
									echo '<td>';
                                    echo '<a href="viber://add?number=' . $row['contact_no'] . '">';
                                    echo '<img src="/PIB/assets/images/viber.png" alt="Viber Icon" style="width: 22px; height: 22px; margin-right: 5px;">';
                                    echo $row['contact_no'];
                                    echo '</a>';
                                    echo '</td>';
									echo '<td>' . $row['date_uploaded'] . '</td>';
                                    echo '<td>' . $row['item_remarks'] . '</td>';

									// Display the stock status with a colored circle
                                    echo '<td style="text-align: center; vertical-align: middle;">';

                                    $stockStatus = $row['stock_status'];

                                    if ($stockStatus === 'Available') {
                                        echo '<div style="background-color: #4CAF50; width: 15px; height: 15px; border-radius: 50%; margin: auto; box-shadow: 0 0 10px rgba(255, 255, 255, 0.9); animation: blink 1s infinite;"></div>';
                                    } elseif ($stockStatus === 'Sold') {
                                        echo '<div style="background-color: maroon; width: 15px; height: 15px; border-radius: 50%; margin: auto; box-shadow: 0 0 10px rgba(255, 255, 255, 0.9); "></div>';
                                    } else {
                                        echo htmlspecialchars($stockStatus);
                                    }

                                    echo '</td>';

									echo "<td style='text-align: center; vertical-align: middle; display: flex; align-items: center; justify-content: center;'>";

                                    // Fetch dealer name associated with the user
                                    $queryDealer = "SELECT dealer FROM employeeinfo WHERE emp_no = ?";
                                    $stmtDealer = mysqli_prepare($db, $queryDealer);
                                    mysqli_stmt_bind_param($stmtDealer, "s", $userId);
                                    mysqli_stmt_execute($stmtDealer);
                                    mysqli_stmt_bind_result($stmtDealer, $dealerName);
                                    mysqli_stmt_fetch($stmtDealer);
                                    mysqli_stmt_close($stmtDealer);

                                    if ($row['dealer_name'] !== $dealerName) {
                                        echo "<div style='background-color: orange; padding: 5px; border-radius: 20%; display: flex; align-items: center; justify-content: center;' title='Checkout' data-bs-toggle='modal' data-bs-target='#addToCartModal'>"; // Orange background for checkout icon with hover text
                                        echo "<ion-icon name='cash' style='font-size: 18px; color: #fff; cursor: pointer; pointer-events: none;'></ion-icon>"; // Cash icon with white color and disabled pointer events
                                        echo "</div>";
                                    }
                                    
                                    // Check if the current user is assigned as the PIC (person-in-charge) for this item
                                    if ($row['dealer_name'] === $dealerName)  {
                                        // Display edit and delete icons
                                        // echo "<div style='background-color: #2196F3; padding: 8px; margin-left: 5px; border-radius: 20%; display: flex; align-items: center; justify-content: center;'>"; // Blue background for edit icon
                                        // echo "<ion-icon name='create' style='font-size: 18px; color: #fff; cursor: pointer;'></ion-icon>"; // Edit icon with white color
                                        // echo "</div>";
                                        echo "<div style='background-color: #F44336; padding: 8px; margin-left: 5px; border-radius: 20%; display: flex; align-items: center; justify-content: center;' title='Remove' onclick='confirmDelete(\"" . $row['part_no'] . "\");'>"; // Red background for remove icon with hover text
                                        echo "<ion-icon name='trash' style='font-size: 18px; color: #fff; cursor: pointer; pointer-events: none;'></ion-icon>"; // Trash icon with white color and disabled pointer events
                                        echo "</div>";

                                    }
									echo "</td>";
									echo '</tr>';
								}
							} else {
								echo '<tr><td colspan="11">No data available</td></tr>';
							}
							?>
						</tbody>
                    </table>
				</div>
		
			</div>
		</main>
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->

    <script>
        function removeItem(inventoryNo) {
            // Display a confirmation dialog
            var confirmDelete = confirm("Are you sure you want to remove this item?");
            
            // Proceed with the deletion only if the user confirms
            if (confirmDelete) {
                // Make an AJAX request to update the status of the item to "Removed Item"
                // Assuming you're using jQuery for AJAX
                $.ajax({
                    url: 'removeItem.php', // Change this to the actual PHP script handling the update
                    type: 'POST',
                    data: { inventory_no: inventoryNo, new_status: 'Removed Item' },
                    success: function(response) {
                        // If the update is successful, update the status column in the UI
                        if (response === 'success') {
                            // Find the corresponding row and update the status column
                            var row = document.querySelector("tr[data-inventory-no='" + inventoryNo + "']");
                            if (row) {
                                var statusCell = row.querySelector(".status-column");
                                if (statusCell) {
                                    statusCell.textContent = 'Removed Item';
                                    // Display a success message
                                    alert('Successfully Removed!');
                                }
                            }
                        } else {
                            alert('Failed to update status.');
                        }
                    },
                    error: function() {
                        alert('An error occurred.');
                    }
                });
            }
        }
        </script>

<script>
function confirmDelete(partNo) {
    if (confirm("Are you sure you want to delete this item?")) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "/PIB/user/updateStatus.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                if (xhr.responseText == "success") {
                    // Remove the row from the table
                    var row = document.getElementById("row_" + partNo);
                    if (row) {
                        row.parentNode.removeChild(row);
                    }
                } else {
                    alert("Item removed successfully!");
                    // Refresh the page
                    location.reload();
                }
            }
        };
        xhr.send("part_no=" + encodeURIComponent(partNo));
    }
}
</script>


    

    <div class="modal fade" id="fullscreen-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body text-center">
                <img id="fullscreen-image" src="" alt="Fullscreen Image" class="img-fluid">
            </div>
        </div>
    </div>
</div>

	<script>
    // JavaScript code to disable the back button if the user is not logged in
    document.addEventListener("DOMContentLoaded", function () {
        <?php
        // Check if the user is not logged in
        if (!isset($_SESSION['user_authenticated']) || $_SESSION['user_authenticated'] !== true) {
            echo '
                if (window.history && window.history.pushState) {
                    window.history.pushState("forward", null, "./home#");
                    window.onpopstate = function () {
                        window.history.pushState("forward", null, "./home#");
                    };
                }
            ';
        }
        ?>
    });
</script>

<script>
    
function openCart() {
    window.location.href = "./user/myBasket";
}
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    const signOutButton = document.getElementById("logout");

    // Add a click event listener to the "Sign Out" link
    signOutButton.addEventListener("click", function (e) {
        e.preventDefault();

        // Perform the sign-out action by redirecting to the PHP script
        // that will clear the session and handle the sign-out.
        window.location.href = "logout";
    });
});

</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
  const searchInput = document.getElementById('searchInput');
  const dataTable = document.getElementById('dataTable');

  searchInput.addEventListener('input', function () {
    const searchTerm = searchInput.value.toLowerCase();
    const rows = dataTable.querySelectorAll('tbody tr');

    rows.forEach(row => {
      const cells = row.querySelectorAll('td');
      let rowContainsSearchTerm = false;

      cells.forEach(cell => {
        const cellText = cell.textContent.toLowerCase();
        if (cellText.includes(searchTerm)) {
          rowContainsSearchTerm = true;
        }
      });

      if (rowContainsSearchTerm) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  });
});

</script>

	<!-- ====== ionicons ======= -->
	<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
	<script src="assets/js/script.js"></script>
	
<script>
    $(document).ready(function () {
        $('.fullscreen-trigger').on('click', function (e) {
            e.preventDefault();
            var imageUrl = $(this).find('img').attr('src');
            $('#fullscreen-image').attr('src', imageUrl);
            $('#fullscreen-modal').modal('show');
        });
    });
</script>

    
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const boxItems = document.querySelectorAll(".box-info .box-item");

    boxItems.forEach(function (item) {
      item.addEventListener("click", function () {
        // Remove active class from all items
        boxItems.forEach(function (boxItem) {
          boxItem.classList.remove("active");
        });

        // Add active class to the clicked item
        item.classList.add("active");
      });
    });
  });
</script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const switchMode = document.getElementById("switch-mode");

    switchMode.addEventListener("change", function () {
      document.body.classList.toggle("dark", this.checked);
    });
  });
</script>



<script>
  document.addEventListener("DOMContentLoaded", function () {
    const switchMode = document.getElementById("switch-mode");

    // Check if the user has a preference stored in cookies
    const savedMode = localStorage.getItem("mode");
    if (savedMode) {
      document.body.classList.toggle("dark", savedMode === "dark");
      switchMode.checked = savedMode === "dark";
    }

    // Add event listener to switch mode checkbox
    switchMode.addEventListener("change", function () {
      const isDarkMode = this.checked;
      document.body.classList.toggle("dark", isDarkMode);

      // Save user's preference to cookies
      localStorage.setItem("mode", isDarkMode ? "dark" : "light");
    });
  });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var parentItems = document.querySelectorAll('.side-menu > ul > li');

        parentItems.forEach(function (parentItem) {
            var sublist = parentItem.querySelector('.sub-menu');
            var arrow = document.createElement('div');
            arrow.className = 'arrow';
            arrow.innerHTML = '&#9654;';

            parentItem.appendChild(arrow);

            parentItem.addEventListener('click', function () {
                sublist.style.display = (sublist.style.display === 'none' || sublist.style.display === '') ? 'block' : 'none';
                arrow.innerHTML = (sublist.style.display === 'none' || sublist.style.display === '') ? '&#9654;' : '&#9660;';
            });
        });
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var sideMenuItems = document.querySelectorAll('.menu-item.has-submenu');
    var subMenus = document.querySelectorAll('.has-submenu ul');

    // Function to handle click on side menu items
    function handleSideMenuItemClick(event, index) {
        // Prevent the default behavior of the anchor element
        event.preventDefault();

        // Toggle the 'active' class on the clicked side menu item
        sideMenuItems.forEach(function (item, i) {
            if (i !== index) {
                item.classList.remove('active');
            }
        });
        sideMenuItems[index].classList.toggle('active');

        // Toggle the 'show' class on the corresponding submenu
        var submenu = subMenus[index];
        if (submenu) {
            submenu.classList.toggle('show');
        }
    }

    // Event listeners for side menu items
    sideMenuItems.forEach(function (sideMenuItem, index) {
        sideMenuItem.addEventListener('click', function (event) {
            handleSideMenuItemClick(event, index);
        });
    });

    // Event listener to stop propagation on submenus
    subMenus.forEach(function (submenu) {
        submenu.addEventListener('click', function (event) {
            event.stopPropagation();
        });
    });

    // Event listener to close submenus on document click
    document.addEventListener('click', function () {
        // Commenting out the code to close submenus on document click
        // sideMenuItems.forEach(function (item, index) {
        //     item.classList.remove('active');
        //     var submenu = subMenus[index];
        //     if (submenu) {
        //         submenu.classList.remove('show');
        //     }
        // });
    });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const menuItems = document.querySelectorAll(".menu-item");

  menuItems.forEach(function (menuItem) {
    menuItem.addEventListener("click", function () {
      // Remove the "active" class from all menu items
      menuItems.forEach(function (item) {
        item.classList.remove("active");
      });

      // Add the "active" class to the clicked menu item
      menuItem.classList.add("active");
    });

    // Handle submenu clicks
    const submenus = menuItem.querySelectorAll(".submenu-item");

    submenus.forEach(function (submenu) {
      submenu.addEventListener("click", function (event) {
        // Prevent the event from propagating to the parent menu item
        event.stopPropagation();

        // Remove the "active" class from all submenu items
        submenus.forEach(function (item) {
          item.classList.remove("active");
        });

        // Add the "active" class to the clicked submenu item
        submenu.classList.add("active");
        
      });
    });
  });
});

</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    const signOutButton = document.getElementById("logout");

    // Add a click event listener to the "Sign Out" link
    signOutButton.addEventListener("click", function (e) {
        e.preventDefault();

        // Perform the sign-out action by redirecting to the PHP script
        // that will clear the session and handle the sign-out.
        window.location.href = "./logout";
    });
});

</script>

<!-- Add the following script to toggle the dropdown on click -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const notificationDropdown = document.getElementById("notificationDropdown");

    if (notificationDropdown) {
        // Track the visibility state
        let isDropdownVisible = false;

        // Add a listener to toggle the dropdown on click
        notificationDropdown.addEventListener("click", function () {
            const dropdownToggle = this.querySelector('.dropdown-toggle');

            if (!isDropdownVisible) {
                dropdownToggle.classList.add('show'); // Show the dropdown
                isDropdownVisible = true;
            } else {
                dropdownToggle.classList.remove('show'); // Hide the dropdown
                isDropdownVisible = false;
            }
        });

        // Add a listener to show the dropdown on mouse enter
        notificationDropdown.addEventListener("mouseenter", function () {
            const dropdownToggle = this.querySelector('.dropdown-toggle');
            dropdownToggle.classList.add('show'); // Show the dropdown
            isDropdownVisible = true;
        });

        // Add a listener to hide the dropdown on mouse leave
        notificationDropdown.addEventListener("mouseleave", function () {
            const dropdownToggle = this.querySelector('.dropdown-toggle');

            if (isDropdownVisible) {
                dropdownToggle.classList.remove('show'); // Hide the dropdown
                isDropdownVisible = false;
            }
        });
    }
});

</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var menuItems = document.querySelectorAll('.menu-item');

        menuItems.forEach(function(item) {
            item.addEventListener('click', function() {
                // Toggle 'active' class on the arrow
                this.querySelector('.arrow').classList.toggle('active');
            });
        });
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var submenuItems = document.querySelectorAll('.has-submenu');

    submenuItems.forEach(function (item) {
        item.addEventListener('click', function () {
            var submenu = item.querySelector('ul');

            // Hide all other submenus
            hideOtherSubmenus(item);

            // Toggle the 'active' class on the clicked submenu item
            item.classList.toggle('active');

            // Toggle the display of the clicked submenu
            submenu.style.display = (submenu.style.display === 'block') ? 'none' : 'block';


            // Update the arrow rotation based on the submenu's display status
            updateArrowRotation(item);
        });
    });

    function hideOtherSubmenus(clickedItem) {
        submenuItems.forEach(function (item) {
            if (item !== clickedItem) {
                var otherSubmenu = item.querySelector('ul');
                if (otherSubmenu) {
                    otherSubmenu.style.display = 'none';
                    item.classList.remove('active'); // Remove 'active' class from other submenu items
                }
            }
        });
    }

    function updateArrowRotation(item) {
        var arrow = item.querySelector('.arrow');
        if (item.classList.contains('active')) {
            arrow.classList.add('active');
        } else {
            arrow.classList.remove('active');
        }
    }
});
</script>

<script>
function logoutUser() {
    window.location.href = "./logout.php";
}
</script>

</body>
</html>