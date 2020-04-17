<?php
// Include config file
//$_SERVER['DOCUMENT_ROOT'] gives root
//__DIR__ gives current dir
//echo get_include_path();
//require_once(__DIR__."\con.php");
//include("./con.php");

//require_once('con.php');
//!require_once "D:\home\site\wwwroot\cal\con.php";
//!require_once 'D:\home\site\wwwroot\cal\con.php';
 
$host = "127.0.0.1:49484";
$db = 'cal';
$user = 'azure';
$password = '6#vWHD_$';

$con = new mysqli($host, $user, $password, $db);

// check connection 
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}     
 
// Define variables and initialize with empty values
$username = $password = $email = $confirm_password = "";
$username_err = $password_err = $email_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";
        
		// Set parameters
        $param_username = trim($_POST["username"]);

        if($stmt = mysqli_prepare($con, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
 
// Validate email______
 
 if(empty(trim($_POST["username"]))){
          $email_err = "Please enter an email adress";
}else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE email = ?";
        
		// Set parameters
        $param_email = trim($_POST["email"]);

        if($stmt = mysqli_prepare($con, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "This email is already taken.";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
 //____________________________________________________
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
         
        if($stmt = mysqli_prepare($con, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sss", $param_username,$param_email, $param_password);
            
            // Set parameters
            $param_username = $username;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                $message = "Register successfull!";
                echo "<script type='text/javascript'>alert('$message');</script>";
                header("location: callogin.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    echo $username." registered";
    mysqli_close($con);
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
  <h1>My Daily Calendar</h1>
  <p>Resize this responsive page to see the effect!</p> 
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
					<!-- Form -->
					<h1>Registration</h1>
					
					<hr class="mb-3">
					<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
			                <label>Username</label>
			                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
			                <span class="help-block"><?php echo $username_err; ?></span>
			            </div>    
                        <!--email start-->
			            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
			                <label>email</label>
			                <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
			                <span class="help-block"><?php echo $email_err; ?></span>
			            </div>    
                        <!--email end -->
			            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
			                <label>Password</label>
			                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
			                <span class="help-block"><?php echo $password_err; ?></span>
			            </div>
			            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
			                <label>Confirm Password</label>
			                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
			                <span class="help-block"><?php echo $confirm_password_err; ?></span>
			            </div>
			            <div class="form-group">
			                <input type="submit" class="btn btn-primary" value="Submit">
			                <input type="reset" class="btn btn-default" value="Reset">
			            </div>
			            <p>Already have an account? <a href="callogin.php">Login here</a>.</p>
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



