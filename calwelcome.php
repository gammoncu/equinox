<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: callogin.php");
    exit;
}

// connection
$con = new mysqli("127.0.0.1:49484", 'azure', '6#vWHD_$', 'cal');
 
// Define variables and initialize with empty values
$task = $start = $end = "";
$task_err = $start_err = $end_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
 // Check if task is empty
    if(empty(trim($_POST["task"]))){
        $task_err = "Please enter task.";
    } else{
        $task = trim($_POST["task"]);
    }

 // Check if start is empty
    if(empty($_POST["begindate"])){
        $start_err = "Please enter begin time.";
    } else{
        $start = $_POST["begindate"];
    }

 // Check if task is empty
    if(empty($_POST["enddate"])){
        $end_err = "Please enter end time.";
    } else{
        $end = $_POST["enddate"];
    }

// Check input errors before inserting in database
if(empty($task_err) && empty($start_err) && empty($end_err) ){
        
        // Prepare an insert statement
        
        $sql = "INSERT INTO tasks (tasks, begindate, enddate, username) 
        VALUES (?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($con, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssss", $param_task,$param_start, $param_end, $user);
            
            // Set parameters
            $param_task = $task;
            $param_start = $start;
            $param_end = $end;
            $user = $_SESSION["username"];
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: calwelcome.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
}}

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
  <p><?php echo "Wellcome ".$_SESSION["username"]." !"; ?></p> 
</div>
	
<div class="container">
  <div class="row">
	  <div class="col-sm-6">
   		<canvas id="canvas" width="500" height="500" 
			   style="background-color: transparent">
		</canvas>
		<script src="cal.js"></script> 
	</div>
    <div class="col-sm-6">
      <div>
					<!-- Form for adding task -->
					<h1>Save a Task</h1>

					<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group <?php echo (!empty($task_err)) ? 'has-error' : ''; ?>">
                        <label>Task</label>
                        <input type="text" name="task" class="form-control" value="<?php echo $task; ?>">
                        <span class="help-block"><?php echo $task_err; ?></span>
                    </div>    
                    <div class="form-group <?php echo (!empty($start_err)) ? 'has-error' : ''; ?>">
                        <label>begin</label>
                        <input type="datetime-local"  value="<?php date_default_timezone_set("Europe/Helsinki");
echo date('Y-m-d').'T'.date('h:i');?>" name="begindate" class="form-control">
                        <span class="help-block"><?php echo $start_err; ?></span>
                    </div>
                    <div class="form-group <?php echo (!empty($end_err)) ? 'has-error' : ''; ?>">
                        <label>end</label>
                        <input type="datetime-local"  value="<?php date_default_timezone_set("Europe/Helsinki");
echo date('Y-m-d').'T'.date('h:i');?>" name="enddate" class="form-control">
                        <span class="help-block"><?php echo $end_err; ?></span>
                     </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="save task">
                        <a href="calpwreset.php" class="btn btn-warning">Reset Your Password</a>
                        <a href="callogout.php" class="btn btn-danger">Sign Out of Your Account</a>
                    </div>
                    </form>
					<!-- Form end -->
                    

                    
                    
				</div>
                <!-- Saved Tasks -->
                    <div>
                    <?php     
                    $con = new mysqli("127.0.0.1:49484", 'azure', '6#vWHD_$', 'cal');
                    //$query = "SELECT tasks, begindate, enddate FROM tasks LIMIT 0,25";
                    $user = $_SESSION["username"];
                    $query = "SELECT * FROM tasks 
                    WHERE cast(begindate as Date) = CURRENT_DATE() 
                    HAVING  username = '$user'";
                
                    $tulos = $con->query($query);
                    if (!$tulos) {
                    echo "err: $query<br>".$con->error;
                                }
                    echo "<div class ='box' id = 'b2'><table class='table'>
                        <thead><tr>
                        <th scope='col'>Task</th>
                        <th scope='col'>Start</th>
                        <th scope='col'>End</th>
                        <th scope='col'></th>
                        </tr></thead>";  
                    while($rivi = $tulos->fetch_assoc()){
                        $task = $rivi['tasks'];
                        $tid = $rivi['id'];
                        $s = $rivi['begindate'];
                        $sdate = new DateTime($s);
                        $st = $sdate->format('H:i');
                        //$s = $s->format;
                        $e = $rivi['enddate'];
                        $edate = new DateTime($e);
                        $et = $edate->format('H:i');
                        

                       echo "<tr>
                       <th scope='row' >$task</th> 
                       <th><span class='cst' >$st</span></th>
                       <th><span class='cet' >$et</span></th>
                       <th><span class='b' >
                       <a href='calwelcome.php?delete=$tid'>Delete</a>
                       </span></th>
                       </tr>";
                       }        
                    echo "</table>
                    <p>fin</p>
                    </div>";
                    
                    if(isset($_GET['delete']) and is_numeric($_GET['delete'])){
                    $id = $_GET['delete'];    
                    $query = "DELETE FROM tasks WHERE id = '$id'";
                    $tulos = $con->query($query);
                    if ($tulos === TRUE) {
                      echo "Task $id Deleted";
                      } 
                    else {
                      echo "Virhe: " . $query . "<br>" . $con->error;
                      }
                    }
                      
                  ?>
                    </div>
                    
                    <!-- Saved tasks end -->
                    <!-- test -->
          
                    <!-- test -->


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



