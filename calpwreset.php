<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
 
// Connection
 
$host = "127.0.0.1:49484";
$db = 'cal';
$user = 'azure';
$password = '6#vWHD_$';

$link = new mysqli($host, $user, $password, $db);

// check connection 
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}     
 
 
// Define variables and initialize with empty values
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate new password
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Please enter the new password.";     
    } elseif(strlen(trim($_POST["new_password"])) < 6){
        $new_password_err = "Password must have atleast 6 characters.";
    } else{
        $new_password = trim($_POST["new_password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm the password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
        
    // Check input errors before updating the database
    if(empty($new_password_err) && empty($confirm_password_err)){
        // Prepare an update statement
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "si", $param_password, $param_id);
            
            // Set parameters
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Password updated successfully. Destroy the session, and redirect to login page
                $message = "Update successfull!";
                echo "<script type='text/javascript'>alert('$message');</script>";
                session_destroy();
                header("location: callogin.php");
                $message = "Update successfull!";
                echo "<script type='text/javascript'>alert('$message');</script>";
               
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>


<html>
<head>
	<title>cal</title>
<!---->
<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="cal.css">

</head>
<body>
<div class="jumbotron text-center">
    <h1>EQUINOX</h1>
  <p>Interactive Calendar!</p> 
  <p><?php echo "Wellcome ".$_SESSION["username"]." !"; ?>  </p> 
</div>
	
<div class="container">
  <div class="row">
	  <div class="col-sm-6">
   		<canvas id="canvas" width="400" height="400" 
			   style="background-color: transparent">
		</canvas>
		<script src="cal.js"></script> 
	</div>
    <div class="col-sm-6">
      <div>
					<style>
						input {width:90%;}
						.form-control {width:90%;}
					</style>
					<!-- Password Reset Form -->
					<h1>Password Reset</h1>
					
					<hr class="mb-3">
			        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
			            <div class="form-group <?php echo (!empty($new_password_err)) ? 'has-error' : ''; ?>">
			                <label>New Password</label>
			                <input type="password" name="new_password" class="form-control" value="<?php echo $new_password; ?>">
			                <span class="help-block"><?php echo $new_password_err; ?></span>
			            </div>
			            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
			                <label>Confirm Password</label>
			                <input type="password" name="confirm_password" class="form-control">
			                <span class="help-block"><?php echo $confirm_password_err; ?></span>
			            </div>
			            <div class="form-group">
			                <input type="submit" class="btn btn-primary" value="Submit">
			                <a class="btn btn-link" href="calwelcome.php">Cancel</a>
			            </div>
			        </form>
                <!-- Form -->
				</div>
		</div>
	</div>
</div>

<hr>

</body>

<!-- Footer -->
<footer class="container-fluid bg-4 text-center">

  <!-- Copyright -->
  <p>© 2020 Copyright:
    <a href="https://linkedin.com/in/gammoncu"> glorius </a>
  </p>
  <!-- Copyright -->
</footer>
<!-- Footer -->



