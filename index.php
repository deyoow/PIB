<!-- LOGIN -->

<?php
session_start();

// Set the timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');

// Check if the user is already logged in and redirect if necessary
if (isset($_COOKIE['remember_me']) && isset($_SESSION['emp_no'])) {
    header("Location: dashboard"); // Replace "dashboard.php" with the actual dashboard page
    exit();
}

include("./connection/connect.php");

if (isset($_POST['login'])) {
    $username = $_POST['emp_no'];
    $password = $_POST['password'];

    $username = mysqli_real_escape_string($db, $username);

    // Use prepared statement to prevent SQL injection
    $query = "SELECT emp_no, first_name, last_name, password, emp_status, position, dealer FROM employeeInfo WHERE emp_no = ?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Check if emp_status is "Blocked"
        if ($user['emp_status'] === 'Blocked') {
            $error_message = "Your account is blocked. <br> Please contact the administrator.";
        } elseif ($user['emp_status'] === 'Pending') {
            $error_message = "Your account is still pending approval.";
        } else {
            // Verify the password using password_verify
            if (password_verify($password, $user['password'])) {
                // Password is correct, set up the session

                // Fetch the user's position from the database
                $userPosition = $user['position'];

                if (
                    $userPosition === 'Parts Advisor' ||
                    $userPosition === 'Parts Warehouseman' ||
                    $userPosition === 'Accessories Installer' ||
                    $userPosition === 'Parts Supervisor' ||
                    $userPosition === 'Parts Admin' ||
                    $userPosition === 'Parts Inventory Controller' ||
                    $userPosition === 'Accessories PIC' ||
                    $userPosition === 'Parts Manager' ||
                    $userPosition === 'Guest'
                ) {
                    // Redirect Parts-related positions to /OGBasket/home
                    header("Location: ./home");
                } else {
                    // Redirect other positions and guests to /OGBasket/dashboard
                    header("Location: ./dashboard");
                }

                // Log the login action in the audit log
                $emp_no = $user['emp_no'];
                $name = $user['first_name'] . " " . $user['last_name'];
                $dealer = $user['dealer'];
                $action = "LOGIN";
                $timestamp = date("Y-m-d (h:i:s A)");

                // Insert into audit_log table
                $auditSql = "INSERT INTO audit_log (emp_no, name, dealer, action, timestamp) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($db, $auditSql);
                mysqli_stmt_bind_param($stmt, "sssss", $emp_no, $name, $dealer, $action, $timestamp);
                mysqli_stmt_execute($stmt);

                // Additional session setup
                $_SESSION['emp_no'] = $user['emp_no'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];

                // Regenerate session ID for security
                session_regenerate_id(true);

                exit();
            } else {
                $error_message = "Incorrect password!";
            }
        }
    } else {
        $error_message = "Account not found!";
    }

    // Close the statement
    mysqli_stmt_close($stmt);
}

// Fetch dealers from the database
$dealersQuery = "SELECT dealer_id, dealer_name FROM dealer";
$dealersResult = mysqli_query($db, $dealersQuery);

// Check for query success
if ($dealersResult) {
    $dealers = mysqli_fetch_all($dealersResult, MYSQLI_ASSOC);
} else {
    // Handle database query error
    die("Error fetching dealers: " . mysqli_error($db));
}

// Fetch positions from the database
$positionsQuery = "SELECT position_id, position_name FROM position";
$positionsResult = mysqli_query($db, $positionsQuery);

// Check for query success
if ($positionsResult) {
    $positions = mysqli_fetch_all($positionsResult, MYSQLI_ASSOC);
} else {
    // Handle database query error
    die("Error fetching positions: " . mysqli_error($db));
}

// Close the database connection
mysqli_close($db);
?>



<!DOCTYPE html>
<html lang="en">


<!-- REGISTRATION -->

<?php
// Enable error reporting for debugging
error_reporting(E_ALL);

if (isset($_POST['submit'])) {
    // Get user input from the form
    $emp_no = $_POST['emp_no'];
    $last_name = $_POST['last_name'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $contact_no = $_POST['contact_no'];
    $email_add = $_POST['email_add'];
    $dealer = $_POST['dealer'];
    $position = $_POST['position'];

    // The rest of your code for the login page goes here
    include("./connection/connect.php");

    if (!$db) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Set the default password
    $defaultPassword = "Initial@1";

    // Hash the default password
    $defaultHashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);


    // Check if the password is empty; if so, use the default hashed password
    if (empty($password)) {
        $hashedPassword = $defaultHashedPassword;
    } else {
        // Hash the user-provided password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    }

   // Check if a file is uploaded
   if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
    // Define the target directory where the image will be saved
    $targetDir = "img/profilepic/";

    // Generate a unique filename using a timestamp and a random string
    $uniqueFilename = time() . '' . '.jpg'; // Adjust the extension based on the file type

    // Create the target path with the unique filename
    $targetPath = $targetDir . $uniqueFilename;

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetPath)) {
        // Successfully uploaded
        // Set the profile picture filename to be saved in the database
        $profilePicFilename = $uniqueFilename;
    } else {
        echo "Sorry, there was an error uploading your file.";
        $profilePicFilename = ''; // Set a default value or handle accordingly
    }
} else {
    // No file uploaded or an error occurred during upload
    $profilePicFilename = 'defaultbg.jpg';  // Set a default value or handle accordingly
}

    // Check if emp_no already exists
    $checkSql = "SELECT emp_no FROM employeeinfo WHERE emp_no = '$emp_no'";
    $result = $db->query($checkSql);


    if ($result->num_rows > 0) {
        // An employee with the same emp_no already exists
        echo '<script>alert("Employee Number already exists!");</script>';
    } else {
        // If emp_no is unique, proceed with the insertion
        // Sample SQL query to insert data into the table, including the hashed password
        $sql = "INSERT INTO employeeInfo (emp_no, last_name, first_name, middle_name, password, contact_no, email_add, dealer, position, profile_pic, emp_status, access) 
        VALUES ('$emp_no', '$last_name', '$first_name', '$middle_name', '$hashedPassword', '$contact_no', '$email_add', '$dealer', '$position', '$profilePicFilename', 'Pending', 'Standard User')";

        if ($db->query($sql) === TRUE) {
            echo '<script>alert("Successfully Saved!");</script>';
            echo '<script>window.location.href = "./";</script>'; // Redirect to the index page
            exit; // Make sure to exit to prevent further execution
        } else {
            echo "Error: " . $sql . "<br>" . $db->error;
        }

    }

    // Close the database connection
    $db->close();
}
?>

  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>OG Parts Inventory Basket</title>
    <link rel="icon" type="image/png" href="./assets/images/logo.png">

    <!--font awesome-->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
    />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.0/css/pikaday.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.0/pikaday.min.js"></script>

    <!--css file-->
    <link rel="stylesheet" href="./assets/css/styles.css" />
  </head>
  <body>
    <!-- <header class="header">
      <a href="#" class="logo">OG<span>Basket</span></a>
      </nav>
    </header> -->

    <div class="wrapper open">
      
      <div class="form-wrapper login">
        <h2>OG PARTS INVENTORY BASKET</h2><br>
        <form id="login-form" method="post" action="">
          <div class="input-box">
            <span class="icon">
              <i class="fa-solid fa-user"></i>
            </span>
            <input type="text" name="emp_no" required value="<?php echo isset($_POST['emp_no']) ? htmlspecialchars($_POST['emp_no']) : ''; ?>">
            <label for="">Username</label>
          </div>
          <div class="input-box">
            <span class="icon">
                <i class="fas fa-lock"></i>
            </span>

          <input type="password" name="password" id="login-password" required />
            <label for="">Password</label>
            <span class="toggle-password" onclick="togglePassword('login-password')">
          <i id="eye-icon" class="fas fa-eye-slash"></i>
        </span>

        </div>
        
          <!-- <div class="options">
            <label><input type="checkbox" />Remember me</label> -->
            <!-- <a href="#">Forgot password?</a> -->
          <!-- </div> -->
          <button type="submit" name="login">Login</button>
          <div class="error-message">
        <?php if (isset($error_message)) { 
            echo "<br>";
            echo $error_message;
        } ?>
    </div>
          <div class="toggle">
            <p>
              Don't have an account? <a class="signup-link" href="#">Register</a>
            </p>
          </div>
        </form>
      </div>

      <div class="form-wrapper signup">
        <h1><a class="back-btn login-link" href="#"><i class="fa-solid fa-circle-arrow-left"></i></a>Registration</h1>
        <form id="registration-form" action="" method="post" enctype="multipart/form-data">
          <div class="input-box">
            <span class="icon">
            <i class="fa-solid fa-user-tag"></i>
            </span>
            <input type="text" name="emp_no" id="emp_no" required onkeyup="checkEmployeeNumber()"/>

            <label for="">Username<span style="color: red;">*</span></label>
          </div>
          
          <div class="input-box">
            <span class="icon">
            <i class="fas fa-user"></i>
            </span>
            <input type="text" name="last_name" required />
            <label for="">Last Name<span style="color: red;">*</span></label>
          </div>

          <div class="input-box">
            <span class="icon">
            <i class="fas fa-user"></i>
            </span>
            <input type="text" name="first_name" required />
            <label for="">First Name<span style="color: red;">*</span></label>
          </div>

          <div class="input-box">
            <span class="icon">
            <i class="fas fa-user"></i>
            </span>
            <input type="text" name="middle_name" required />
            <label for="">Middle Name<span style="color: red;">*</span></label>
          </div>

          <!-- <div class="input-box date-input">
            <span class="icon">
              <i class="far fa-calendar-alt"></i>
            </span>
            <input type="text" id="datepicker" name="date_birth" required />
            <label for="datepicker">Date of Birth</label>
          </div> -->

          <div class="input-box">
            <span class="icon">
            <i class="fa-solid fa-address-book"></i>
            </span>
            <input type="text" name="contact_no" required />
            <label for="">Contact No (Viber Ready)<span style="color: red;">*</span></label>
          </div>

          <div class="input-box">
            <span class="icon">
            <i class="fa-solid fa-envelope"></i>
            </span>
            <input type="text" name="email_add" required />
            <label for="">Email Address<span style="color: red;">*</span></label>
          </div>

          <!-- <div class="input-box">
            <span class="icon">
              <!-- <i class="fa-solid fa-venus-mars"></i> -->
            <!-- </span>
            <div class="select-wrapper">
              <select name="gender" required >
                <option value="" disabled selected>Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="LGBTQIA+">LGBTQIA+</option>
                <option value="Prefer Not to Say">Prefer Not to Say</option>
              </select>
            </div> -->
            <!-- <label for="">Gender</label> -->
          <!-- </div> -->
          
<div class="input-box">
    <div class="select-wrapper">
        <select name="dealer" required>
            <option value="" disabled selected>Select Dealer</option>
            <?php foreach ($dealers as $dealer): ?>
                <option value="<?php echo $dealer['dealer_name']; ?>"><?php echo $dealer['dealer_name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <label for="">Dealer<span style="color: red;">*</span></label>
</div>

<!-- Replace the existing position select-box with this code -->
<div class="input-box">
    <div class="select-wrapper">
        <select name="position" required>
            <option value="" disabled selected>Select Position</option>
            <?php foreach ($positions as $position): ?>
                <option value="<?php echo $position['position_name']; ?>"><?php echo $position['position_name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <label for="">Position<span style="color: red;">*</span></label>
</div>



          <!-- Replace the existing profile picture input-box with this code -->
<div class="input-box">
<label for="profilePicture">Profile Picture</label>
<input type="file" id="profilePicture" name="profile_pic" accept="image/*" style="margin-top: 15px;">
</div>
          <button type="submit" name="submit" id="register-button" onclick="return showConfirmation();">REGISTER</button>
          <!-- <div class="toggle">
            <p>
              Already have an account? <a class="login-link" href="#">Login</a>
            </p>
          </div> -->
        </form>
      </div>
    </div>

    <script>
function showConfirmation() {
    // Check if all required fields are filled
    var empNo = document.getElementById('emp_no').value.trim();
    var lastName = document.getElementsByName('last_name')[0].value.trim();
    var firstName = document.getElementsByName('first_name')[0].value.trim();
    var middleName = document.getElementsByName('middle_name')[0].value.trim();
    var contactNo = document.getElementsByName('contact_no')[0].value.trim();
    var emailAddress = document.getElementsByName('email_add')[0].value.trim();
    var dealer = document.getElementsByName('dealer')[0].value.trim();
    var position = document.getElementsByName('position')[0].value.trim();

    // Check if any required field is empty
    if (empNo === '' || lastName === '' || firstName === '' || middleName === '' || contactNo === '' || emailAddress === '' || dealer === '' || position === '') {
        alert("Please fill out all required fields.");
        return false;
    }

    // If all required fields are filled, show confirmation dialog
    if (confirm("Are you sure all the information is correct?")) {
        // Submit the form
        document.getElementById('registration-form').submit();
    } else {
        return false;
    }
}
</script>


<script>
let empNoTimer; // Variable to hold the timer

function checkEmployeeNumber() {
    const empNoInput = document.getElementById('emp_no');

    // Clear any existing timer
    clearTimeout(empNoTimer);

    // Set a timer to delay the check after the user stops typing
    empNoTimer = setTimeout(function() {
        // Get the employee number input value
        const empNo = empNoInput.value;

        // Check if the input is not empty
        if (empNo.trim() !== '') {
            // Use AJAX to send a request to the server to check if the employee number exists
            const xhr = new XMLHttpRequest();
            xhr.open('POST', './checkempno.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Show a pop-up message based on the response
                    if (xhr.responseText === 'exists') {
                        alert('Username already exists!');
                        empNoInput.value = ''; // Clear the input field
                    }
                }
            };

            // Send the request with the employee number
            xhr.send('emp_no=' + empNo);
        }
    }, 1000); // Adjust the delay (in milliseconds) as needed
}
</script>


   <script>
  document.addEventListener("DOMContentLoaded", function () {
    let wrapper = document.querySelector(".wrapper");
    let loginLink = document.querySelector(".login-link");
    let signupLink = document.querySelector(".signup-link");
    let btn = document.querySelector(".btn");
    let closeBtn = document.querySelector(".close-btn");

    signupLink.addEventListener("click", () => {
      wrapper.classList.add("active");
    });

    loginLink.addEventListener("click", () => {
      wrapper.classList.remove("active");
    });

    btn.addEventListener("click", () => {
      wrapper.classList.add("open");
    });

    closeBtn.addEventListener("click", () => {
      wrapper.classList.remove("open");
    });

    // Add an event listener for each input box to check for content
    let inputBoxes = document.querySelectorAll(".input-box input");
    inputBoxes.forEach((inputBox) => {
      inputBox.addEventListener("input", () => {
        inputBox.parentNode.classList.toggle(
          "input-filled",
          inputBox.value.trim() !== ""
        );
      });

      // Trigger the input event once on page load to check initial state
      inputBox.dispatchEvent(new Event("input"));
    });
  });
</script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    var datepicker = new Pikaday({
        field: document.getElementById("datepicker"),
        format: "YYYY-MM-DD",
        yearRange: [1900, new Date().getFullYear()],
        showYearDropdown: true,
    });

    // Add an event listener to the form to format the date before submission
    document.getElementById("registration-form").addEventListener("submit", function () {
      // Get the selected date from the datepicker
      var selectedDate = datepicker.toString();

      // Update the value of the date input with the formatted date
      document.getElementById("datepicker").value = selectedDate;
    });
  });
</script>

<script>
        // Check if the user is already logged in using JavaScript
        document.addEventListener("DOMContentLoaded", function () {
            var isLoggedIn = <?php echo isset($_SESSION['emp_no']) ? 'true' : 'false'; ?>;

            if (isLoggedIn) {
                window.location.replace("dashboard");
            }
        });
    </script>

<script>
  function togglePassword(inputId) {
    const passwordInput = document.getElementById(inputId);
    const eyeIcon = document.getElementById('eye-icon');

    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      eyeIcon.classList.remove("fa-eye-slash");
      eyeIcon.classList.add("fa-eye");
    } else {
      passwordInput.type = "password";
      eyeIcon.classList.remove("fa-eye");
      eyeIcon.classList.add("fa-eye-slash");
    }
}

</script>


  </body>
</html>
