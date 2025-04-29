<!DOCTYPE html>
<html lang="en">
<?php
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['emp_no'])) {
    // If the user is not authenticated, redirect them to the login page
    echo '<script>
            window.location.href = "./";
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
        return "/PIB/img/profilepic/" . $profilePictureFileName;
    } else {
        // If no profile picture found, return a default image URL
        return "/PIB/img/profilepic/defaultbg.jpg";
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
    
    <!-- My CSS -->
    <link rel="stylesheet" href="./assets/css/stylebasket.css">

    <title>Dashboard | OG Parts Inventory Basket</title>
    <link rel="icon" type="image/png" href="./assets/images/logo.png">
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
            <a href="./home"><i class='bx bxs-home bx-fw'></i> Home</a>


            </li>
            <li class="menu-item">
                <a href="./dashboard" style="background-color: maroon;"><i class='bx bxs-chart bx-fw' style="color: orange;"></i> Dashboard</a>
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

            <!-- <li class="menu-item"> -->
                <!-- <a href="./dealer/dealerStocks"><i class='bx bxs-package bx-fw'></i> Dealer Stocks</a> -->
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
			<div class="head-title">
				<div class="left">
					<h1>Dashboard</h1>
					<!-- <ul class="breadcrumb">
						<li>
							<a href="#">Dashboard</a>
						</li>
						<li><i class='bx bx-chevron-right' ></i></li>
						<li>
							<a class="active" href="#">Home</a>
						</li>
					</ul> -->
				</div>
				<!-- <a href="./admin/reports" class="btn-download">
					<i class='bx bxs-cloud-download' ></i>
					<span class="text">Generate Report</span>
				</a> -->
			</div>

			<?php
                // Fetch distinct dealer names and count occurrences where stock_status is 'Available'
                $dealerCountQuery = "SELECT dealer_name, COUNT(*) as count FROM inventory WHERE stock_status = 'Available' AND onhand_qty > 0 GROUP BY dealer_name";
                $dealerCountResult = mysqli_query($db, $dealerCountQuery);

                $dealerCounts = [];

                while ($row = mysqli_fetch_assoc($dealerCountResult)) {
                    $dealerCounts[$row['dealer_name']] = $row['count'];
                }
            ?>

		<ul class="box-info">
        <?php
            $totalStocks = 0; // Initialize total stocks count
            // Loop through each dealer
            foreach ($dealerCounts as $dealer => $count) {
                // Aggregate counts from TBK to TSJ
                if (strpos($dealer, 'TBK') !== false || strpos($dealer, 'TOT') !== false || strpos($dealer, 'TNE') !== false || strpos($dealer, 'TNESC') !== false || strpos($dealer, 'TMR') !== false || strpos($dealer, 'TSJ') !== false) {
                    $totalStocks += $count; // Add count to total
                }
            }
            ?>
		<a href="./dealer/TBKstocks">
			<li>
            <i class='bx bxs-building-house' style="background-color: darkred;"></i>
            <span class="text">
                <h3><?php echo isset($dealerCounts['TBK']) ? $dealerCounts['TBK'] : 0; ?></h3>
                <p>TBK</p>
            </span>
    	</li></a>

			<a href="./dealer/TOTstocks">
    	<li>
            <i class='bx bxs-building-house' style="background-color: yellowgreen;"></i>
            <span class="text">
                <h3><?php echo isset($dealerCounts['TOT']) ? $dealerCounts['TOT'] : 0; ?></h3>
                <p>TOT</p>
            </span>
    	</li> </a>

			<a href="./dealer/TNEstocks">
			<li>
				<i class='bx bxs-building-house' style="background-color: darkblue;"></i>
				<span class="text">
					<h3><?php echo isset($dealerCounts['TNE']) ? $dealerCounts['TNE'] : 0; ?></h3>
					<p>TNE</p>
				</span>
			</li></a>

            <a href="./dealer/TNESCstocks">
			<li>
				<i class='bx bxs-building-house' style="background-color: darkblue;"></i>
				<span class="text">
					<h3><?php echo isset($dealerCounts['TNESC']) ? $dealerCounts['TNESC'] : 0; ?></h3>
					<p>TNESC</p>
				</span>
			</li></a>
				
			<a href="./dealer/TMRstocks">
			<li>
				<i class='bx bxs-building-house' style="background-color: darkgreen;"></i>
				<span class="text">
					<h3><?php echo isset($dealerCounts['TMR']) ? $dealerCounts['TMR'] : 0; ?></h3>
					<p>TMR</p>
				</span>
			</li></a>

			<a href="./dealer/TSJstocks">
			<li>
				<i class='bx bxs-building-house' style="background-color: orange;"></i>
				<span class="text">
					<h3><?php echo isset($dealerCounts['TSJ']) ? $dealerCounts['TSJ'] : 0; ?></h3>
					<p>TSJ</p>
				</span>
			</li></a>

            <a href="./dealer/dealerStocks">
                <li>
                    <i class='bx bxs-building-house' style="background-color: gray;"></i>
                    <span class="text">
                        <h3><?php echo $totalStocks; ?></h3>
                        <p>Stocks</p>
                    </span>
                </li>
            </a>

			</ul>
     
            <div class="table-data">
                <div class="todo">
                    <div class="head">
                        <h3 id="stocksSoldHeader">Stocks Sold - <span id="currentMonthYear"></span></h3>
                    </div>
                    <div class="doughnut-chart-container">
                        <canvas id="stocksSoldDoughnutChart"></canvas>
                    </div>
                </div>
                <div class="order">
                    <h6 id="topSellingStocksHeader" style="font-size: 15px; font-weight: bold;">Top Selling Stocks - <span id="currentMonthYear"></span></h6>
                    <canvas id="myChart"></canvas>
                </div>
            </div>

            <script>
                // JavaScript code to get the previous month and year
                const currentDate = new Date();
                currentDate.setMonth(currentDate.getMonth() - 1); // Set to the previous month
                const previousMonth = currentDate.toLocaleString('default', { month: 'long' });
                const previousYear = currentDate.getFullYear();

                // Set the content of the spans with id 'currentMonthYear' to the previous month and year
                const spans = document.querySelectorAll('#stocksSoldHeader #currentMonthYear, #topSellingStocksHeader #currentMonthYear');
                spans.forEach(span => {
                    span.textContent = `${previousMonth} ${previousYear}`;
                });
            </script>


            <!-- Add this HTML code where you want the Viber icon and dropdown to appear -->

<div class="overlay" id="overlay"></div>

<!-- Viber icon container -->
<div class="viber-icon-container">
    <div class="viber-icon">
        <a class="dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <img src="./assets/images/viber.png" alt="Viber Chat">
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


			<div class="table-data" style="display: none;">
				<div class="order">
					<div class="head">
                        <h3>Available Stocks</h3>
                        <div class="input-group" style="max-width: 300px;">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search" aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <span class="input-group-text" id="basic-addon2"><i class="bx bx-search"></i></span>
                            </div>
                            </div>
						<a href = "dealer/allstocks"><i class='bx bx-filter' ></i></a>
					</div>
					<table id="dataTable">
						<thead>
							<tr>
								<th>Item</th>
								<!-- <th>No</th> -->
								<th>Dealer</th>
								<th>Contact No</th>
								<th>Date Uploaded</th>
								<th>Part No</th>
								<th>Part Description</th>
								<th>On-Hand QTY</th>
								<th>Price</th>
								<th>Model Code</th>
								<th>Unit</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody class="stocks-table-body">
						<?php
							// Fetch data from the database
							$query = "SELECT * FROM inventory WHERE stock_status = 'Available'";
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
                                        echo '<img src="./assets/images/defaultbg.jpg" alt="Default Image" style="width: 100px; height: 50px; object-fit: cover; border-radius: 0;">';
                                    }

                                    echo '</a>
                                    </td>';
									// echo '<td>' . $row['inventory_no'] . '</td>';
									echo '<td>' . $row['dealer_name'] . '</td>';
									echo '<td>';
                                    echo '<a href="viber://add?number=' . $row['contact_no'] . '">';
                                    echo '<img src="/PIB/assets/images/viber.png" alt="Viber Icon" style="width: 22px; height: 22px; margin-right: 5px;">';
                                    echo $row['contact_no'];
                                    echo '</a>';
                                    echo '</td>';
									echo '<td>' . $row['date_uploaded'] . '</td>';
									echo '<td>' . $row['part_no'] . '</td>';
									echo '<td>' . $row['part_description'] . '</td>';
									echo '<td>' . $row['onhand_qty'] . '</td>';
									echo '<td>₱' . $row['price'] . '</td>';
									echo '<td>' . $row['model_code'] . '</td>';
									echo '<td>' . $row['unit'] . '</td>';

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
									echo "<div style='background-color: #4CAF50; padding: 5px; border-radius: 20%; display: flex; align-items: center; justify-content: center;'>"; // Green background for check icon
									echo "<ion-icon name='cart' style='font-size: 23px; color: #fff; cursor: pointer;'></ion-icon>"; // Check icon with white color
									echo "</div>";
									echo "<div style='background-color: orange; padding: 8px; margin-left: 5px; border-radius: 20%; display: flex; align-items: center; justify-content: center;'>"; // Blue background for edit icon
									echo "<ion-icon name='cash' style='font-size: 18px; color: #fff; cursor: pointer;'></ion-icon>"; // Edit icon with white color
									echo "</div>";
                                    echo "<div style='background-color: #2196F3; padding: 8px; margin-left: 5px; border-radius: 20%; display: flex; align-items: center; justify-content: center;'>"; // Blue background for edit icon
									echo "<ion-icon name='create' style='font-size: 18px; color: #fff; cursor: pointer;'></ion-icon>"; // Edit icon with white color
									echo "</div>";
									echo "<div style='background-color: #F44336; padding: 8px; margin-left: 5px; border-radius: 20%; display: flex; align-items: center; justify-content: center;'>"; // Red background for trash icon
									echo "<ion-icon name='trash' style='font-size: 18px; color: #fff; cursor: pointer;'></ion-icon>"; // Trash icon with white color
									echo "</div>";
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
                    window.history.pushState("forward", null, "./dashboard#");
                    window.onpopstate = function () {
                        window.history.pushState("forward", null, "./dashboard#");
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


<?php
include($_SERVER['DOCUMENT_ROOT'] . '/PIB/connection/connect.php');
error_reporting(0);

// Calculate the start and end dates for the last month
$start_date = date('Y-m-d', strtotime('first day of last month'));
$end_date = date('Y-m-d', strtotime('last day of last month'));

// Fetch count of data for each dealer from the database for the last month
$sql = "SELECT dealer_name, COUNT(*) AS count 
        FROM listpurchased 
        WHERE date_sold BETWEEN '$start_date' AND '$end_date' 
        AND status = 'Approved' 
        GROUP BY dealer_name";
$result = $db->query($sql);

$dealerData = [];
if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        $dealerData[$row['dealer_name']] = $row['count'];
    }
}
?>

<!-- JavaScript to render the chart -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Assuming dealerData is your array of dealer data counts fetched from PHP
        var dealerData = <?php echo json_encode($dealerData); ?>;

        // Extract dealer names and count data for labels and chart data
        var dealerNames = Object.keys(dealerData);
        var counts = Object.values(dealerData);

        // Chart data
        var data = {
            labels: dealerNames,
            datasets: [{
                data: counts,
                backgroundColor: ["maroon", "yellowgreen", "darkblue", "darkgreen", "orange"], // Example colors
                borderColor: "white",
                borderWidth: 2,
            }],
        };

        // Chart configuration
        var options = {
            responsive: true,
            maintainAspectRatio: false,
            cutoutPercentage: 70, // Set the cutout percentage for the doughnut chart
            plugins: {
                datalabels: {
                    display: true,
                    color: 'white',
                    font: {
                        weight: 'bold'
                    },
                    formatter: function(value, context) {
                        return context.chart.data.labels[context.dataIndex] + ": " + value;
                    }
                }
            }
        };

        // Get the canvas element
        var ctx = document.getElementById('stocksSoldDoughnutChart').getContext('2d');

        // Create the doughnut chart
        var stocksSoldDoughnutChart = new Chart(ctx, {
            type: 'doughnut',
            data: data,
            options: options,
        });
    });
</script>


<?php
include($_SERVER['DOCUMENT_ROOT'] . '/PIB/connection/connect.php');
error_reporting(0);

// Calculate the start and end dates for the previous month
$start_date = date('Y-m-d', strtotime('first day of last month'));
$end_date = date('Y-m-d', strtotime('last day of last month'));

// Fetch top-selling items based on qty_ordered for the previous month
$sql = "SELECT part_description, SUM(qty_ordered) AS total_ordered
        FROM listpurchased 
        WHERE status = 'Approved'
        AND date_sold BETWEEN '$start_date' AND '$end_date'
        GROUP BY part_description
        ORDER BY total_ordered DESC
        LIMIT 5"; // Limit to top 5 selling items
$result = $db->query($sql);

$itemData = [];
if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        $part_description = $row['part_description'];
        if (array_key_exists($part_description, $itemData)) {
            // If the part_description already exists, add the quantity to the existing total
            $itemData[$part_description] += $row['total_ordered'];
        } else {
            // Otherwise, create a new entry for the part_description
            $itemData[$part_description] = $row['total_ordered'];
        }
    }
}
?>



<script>
document.addEventListener("DOMContentLoaded", function () {
    // Assuming itemData is your array of top-selling item data counts fetched from PHP
    var itemData = <?php echo json_encode($itemData); ?>;

    // Extract item names and count data for labels and chart data
    var itemNames = Object.keys(itemData).slice(0, 5); // Limit to top 5 items
    var quantities = Object.values(itemData).slice(0, 5); // Limit to top 5 items

    // Chart data
    var data = {
        labels: itemNames,
        datasets: [{
            label: "Top Selling Items",
            backgroundColor: ["maroon", "yellowgreen", "darkblue", "darkgreen", "orange"], // Example colors
            borderColor: "white",
            borderWidth: 2,
            data: quantities,
        }],
    };

    // Chart configuration
    var options = {
        responsive: true,
        maintainAspectRatio: false,
    };

    // Get the canvas element
    var ctx = document.getElementById('myChart').getContext('2d');

    // Create the chart
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: data,
        options: options,
    });
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