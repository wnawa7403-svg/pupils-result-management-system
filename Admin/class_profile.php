<?php  
	session_start();
    if ($_SESSION["Admin"])
	{
		$class_id=$_GET['class_id'] ?? $_SESSION['class_id'];
	}
	else if(!$_SESSION["Admin"] && $_SESSION['Admin']){
		$Admin=$_SESSION['Admin'];
	
    } else { ?>
        <script> alert("You Are Not Authorized Person For This Link");</script>
    <?php
        header('location:../login/login.php');
    }
    
	require_once "../connection/connection.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
 
</head>


<body>
<?php include('../Main/Main_header.php') ?>
<?php include('../Main/Main-sidebar.php') ?>
 <h4> </h4>

<main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 mb-2 w-100">

<div class="sub-main">
  
        <div class="modal-header  text-black">
        <h3> CLASS MANAGEMENT </h3>
    </div>
 
    </div>
    </div><br><br>
        <!-- Tab content -->
        <div id="Inquiries" class="tabcontent">
        
		<div class="sub-main">
				
            <div class="card card-outline card-primary rounded-0 shadow">
   
		<h3 class="card-title">List of pupils</h3>
	<div class="card-body">
		<div class="container-fluid">
        <div class="container-fluid">
			<table class="">
            <colgroup>
					<col width="5%">
					<col width="35%">
					<col width="35%">
					<col width="25%">
				</colgroup>
                    </div>
                    <table class="table table-hover responsive ">
                        <thead>
		<div class="modal-header  text-black">
    <h1>CLASS DETAILS</h1>
    </div>
    <div class="m-4">
             <?php
                      if ($class_id) {
                        $query = "SELECT * FROM classes WHERE class_id='$class_id'";
                    } else {
                        $query = "SELECT * FROM classes WHERE className='$className'";
                    }
                    
                    $run = mysqli_query($con, $query);
                    
                    if (!$run) {
                        // Handle the error, e.g., display an error message or log the error.
                        echo "Query failed: " . mysqli_error($con);
                    } else {
                        while ($row = mysqli_fetch_array($run)) {
                            // Process the fetched row
                            
                           
                            if (isset($row['className'])) {
                            
                                echo '<label style="font-weight: bold;"></label> <span   style="font-weight: bold; font-size: 50px">' . ucwords($row['className']) . '</span><br>';

                             }
                            if (isset($row['teacher_id'])) {
                               
                                echo '<label class="label"style="font-weight: bold;"></label> <span class="pupil-id"  style="font-weight: bold; font-size: 20px">' . $row['teacher_id'] . '</span><br>';
                            }
                            if (isset($row['class_status'])) {
                               
                                echo '<label class="label"></label> <span class="pupil-id"  style="font-weight: bold; font-size: 20px">' . $row['class_status'] . '</span><br>';
                            }
                        }         
                    }
                  
   ?>
<div class="modal-header  text-black">
     
    </div>
<div class="modal-header  text-black">
    <h4>CLASS LIST</h4>
    </div>

                                           
                                                        <?php
                                                    try {
                                            // Get the selected class ID from the URL parameter
                                            $class_id = isset($_GET['class_id']) ? $_GET['class_id'] : '';

                                            // Prepare the SQL query
                                            $sql = "SELECT pupils_tb.*, classes.className 
                                            FROM pupils_tb JOIN classes ON pupils_tb.className = classes.className 
                                            WHERE classes.class_id = '$class_id'";
                                            $result = mysqli_query($con, $sql);

                                            if ($result === false) {
                                                throw new Exception("Query failed: " . mysqli_error($con));
                                            }

                                            if (mysqli_num_rows($result) > 0) {?>
                                              
		 
			<table class="">
            <colgroup>
					<col width="5%">
					<col width="35%">
					<col width="35%">
					<col width="25%">
				</colgroup>
                    </div>
                    <table class="table table-hover ">
                        <thead>
                    <tr>
                    <th style="padding: 10px;  border-bottom: 1px solid #ddd;text-align : center">ID</th>
                    <th style="padding: 10px;  border-bottom: 1px solid #ddd;text-align : center">FULL NAME</th>
                        <th style="padding: 10px;  border-bottom: 1px solid #ddd;text-align : center">CLASS NAME</th>
                        
                        <th style="padding: 10px;  border-bottom: 1px solid #ddd;text-align : center">GENDER</th>
                        <th style="padding: 10px;  border-bottom: 1px solid #ddd;text-align : center">ADDRESS</th>
                        <th style="padding: 10px;  border-bottom: 1px solid #ddd;text-align : center">PARENTS</th>
                        
                        <th style="padding: 10px;  border-bottom: 1px solid #ddd;text-align : center">CONTACTS</th>
                        <th style="padding: 10px;  border-bottom: 1px solid #ddd;text-align : center">YEAR</th>
                        <th style="padding: 10px;  border-bottom: 1px solid #ddd;text-align : center">DATE OF BIRTH</th>
                         
                        <th style="padding: 10px;  border-bottom: 1px solid #ddd;text-align : center">ACTION</th>
                    </tr>
                    </thead>

                                           <?php while ($row = mysqli_fetch_assoc($result)) {
                                                echo "<tr>";
                                                
                                                echo '<td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center;">' . $row['pupils_Id'] . '</td>';
                                                echo '<td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center;">' . ucwords($row['FullName']) . '</td>';
                                                echo '<td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center;">' . ucwords($row['className']) . '</td>';
                                                
                                                echo '<td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center;">' . ucwords($row['Gender']) . '</td>';
                                                echo '<td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center;">' . ucwords($row['address']) . '</td>';
                                                echo '<td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center;">' . ucwords($row['parents']) . '</td>';
                                                echo '<td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center;">' . ucwords($row['parents_no']) . '</td>';
                                                echo '<td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center;">' . ucwords($row['year']) . '</td>';
                                        
                                                // Format the date field if needed
                                                if (!empty($row['RegDate'])) {
                                                    $date = new DateTime($row['RegDate']); // assuming 'RegDate' is stored in 'YYYY-MM-DD' format
                                                    echo '<td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center;">' . $date->format('d-m-Y') . '</td>';
                                                } else {
                                                    echo '<td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center;">N/A</td>';
                                                }
                                                echo '<td><a  class="btn btn-info"href="pupils_profile.php?pupils_Id=' . $row['pupils_Id'] . '">view</a></td>';
                                                echo '<td><a  class="btn btn-info"href="class_profile.php?pupils_Id=' . $row['pupils_Id'] . '">Edit</a></td>';
                                                echo '<td><a class="btn btn-danger" href="insert_results.php?pupils_Id=' . $row['pupils_Id'] . '">delete</a></td>';
                                                echo '</tr>';
                                            }
                                        
                                            echo '</tbody>';
                                            echo '</table>';
                                        } else {
                                            echo "No records found.";
                                        }
                                    } catch (Exception $e) {
                                        echo "Error: " . $e->getMessage();
                                    }


?>


      </div>
      </div>
				

      </div>
      </div>
      </div><br><br>
</div>
			

    <script>
        function openCity(evt, cityName) {
        // Declare all variables
        var i, tabcontent, tablinks;

        // Get all elements with class="tabcontent" and hide them
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }

        // Get all elements with class="tablinks" and remove the class "active"
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }

        // Show the current tab, and add an "active" class to the button that opened the tab
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.className += " active";
        }
    </script>
									
				
		</main>
		<script type="text/javascript" src="../bootstrap/js/jquery.min.js"></script>
		<script type="text/javascript" src="../bootstrap/js/bootstrap.min.js"></script>
 


  

                                      
</body>
</html>
























