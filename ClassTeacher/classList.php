<?php 
session_start();
if (!isset($_SESSION['LoginClassTeacher'])) {
    header('Location: ../Login/login.php');
    exit();
}
$teacher_id = $_SESSION['LoginClassTeacher'];



require_once "../connection/connection.php";

?>
  
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
     
    <title>Add New Class</title>
     
   <link rel="stylesheet" href="../css/style.css">
    
</head>
<body>

<?php include('../Main/class-teacher-sidebar.php') ?>
<br><br><br><br>
<script>
        const toggleBtn = document.querySelector(".show-btn");
        const sidebar = document.querySelector(".sidebar");
        // undefined
        toggleBtn.addEventListener("click",function(){
            sidebar.classList.toggle("show-sidebar");
        });
    </script>

<main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 mb-2 main-wrapper w-100">
    

 
		        <div class="sub-main">
				
                <div class="card card-outline card-primary rounded-0 shadow w-100">
	                            
                <div class="modal-header  text-black">
                                        <h1><b> Classes</b></h1>
                                        </div>
                                    
                                        
                                        <div class="card-body">
                                      
                                        </div>
                                            <div class="container-fluid">
                                            <div class="container-fluid">
                                                <table class=" display table table-hover table-bordered ">
                                                    <colgroup>
                                                        <col width="5%">
                                                        <col width="35%">
                                                        <col width="35%">
                                                        <col width="25%">
                                                    </colgroup>
                                                                                      
                                                    </div>
                                                    
                                           <tr style='padding: 10px; border-bottom: 1px solid #ddd; text-align: center;'>
                                            <th style='padding: 10px; border-bottom: 1px solid #ddd; text-align: center;'> No #</th>
                                          <th style='padding: 10px; border-bottom: 1px solid #ddd; text-align: center;'>class Name</th>
                                          <th style='padding: 10px; border-bottom: 1px solid #ddd; text-align: center;'>class Teacher</th>
                                           <th style='padding: 10px; border-bottom: 1px solid #ddd; text-align: center;'>Date Added</th>
                                          <th style='padding: 10px; border-bottom: 1px solid #ddd; text-align: center;'>Action</th>
                                          <th style='padding: 10px; border-bottom: 1px solid #ddd; text-align: center;'>Action</th>
                                          <th style='padding: 10px; border-bottom: 1px solid #ddd; text-align: center;'>Action</th>
                                          </tr>
                                
                                            <?php           
                                          $query = "SELECT c.class_id, c.className,c.date_added, t.last_name AS teacher_last_name 
                                            FROM classes c 
                                            JOIN teacher_tb t ON c.teacher_id = t.teacher_id";
                                  $result = mysqli_query($con, $query);
                                  
                                  if ($result) {
                                     
                                    while ($row = mysqli_fetch_assoc($result)) {
                                       echo "<tr>";
                                       echo '<td style="padding: 10px;  border-bottom: 1px solid #ddd;text-align : center">' . ucwords($row['class_id']). '</td>';
                                        echo '<td style="padding: 10px;  border-bottom: 1px solid #ddd;text-align : center">' . ucwords($row['className']). '</td>';
                                        echo '<td style="padding: 10px;  border-bottom: 1px solid #ddd;text-align : center">' . ucwords($row['teacher_last_name']). '</td>';
                                        echo '<td style="padding: 10px;  border-bottom: 1px solid #ddd;text-align : center">' . ucwords($row['date_added']). '</td>';
                                        echo '<td><a  class="btn btn-info"href="class_profile.php?class_id=' . $row['class_id'] . '">View</a></td>';
                                        echo '<td><a  class="btn btn-info"href="updateClass.php?class_id=' . $row['class_id'] . '">Edit</a></td>';
                                        echo '<td><a class="btn btn-danger" href="deleteClass.php?class_id=' . $row['class_id'] . '">delete</a></td>';
 
                                       
                                        echo '</tr>';
                                    }
                                    echo '</table>';
                                }                                    
                                  else {
                                      echo "Error: " . mysqli_error($con);
                                  }
                                  
                                                      

?>
                                                    
                
                                        </tbody>
                                 </div>
                         </div>
		</div>
                               
 			
				
		</main>
		<script type="text/javascript" src="../bootstrap/js/jquery.min.js"></script>
		<script type="text/javascript" src="../bootstrap/js/bootstrap.min.js"></script>
 


  


</body>
</html>