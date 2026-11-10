<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<H3>Classes</H3><br>

<div class="sub-main">
        
        <div class="card card-outline card-primary rounded-0 shadow" style="width:18rem;">
<div class="card-header">

                            <h4>search</h4>
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
                                            <table class="table table-hover ">
                        
                                                <?php
                                    $query = "SELECT c.class_id, c.class_name, t.last_name AS teacher_last_name 
                                    FROM classes c 
                                    JOIN teacher_tb t ON c.teacher_id = t.teacher_id";
                          $result = mysqli_query($con, $query);
                          
                          if ($result) {
                            echo '<table>';
                            echo "<tr>
                                  <th style='padding: 10px; border-bottom: 1px solid #ddd; text-align: center;'>class Name</th>
                                  <th style='padding: 10px; border-bottom: 1px solid #ddd; text-align: center;'>class Teacher</th>
                                  <th style='padding: 10px; border-bottom: 1px solid #ddd; text-align: center;'>Action</th>
                                  </tr>";
                            
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                              
                                echo '<td style="padding: 10px;  border-bottom: 1px solid #ddd;text-align : center">' . ucwords($row['class_name']). '</td>';
                                echo '<td style="padding: 10px;  border-bottom: 1px solid #ddd;text-align : center">' . ucwords($row['teacher_last_name']). '</td>';
                                echo '<td><a class="btn btn-secondary" href="class_profile.php?class_id=' . $row['class_id'] . '">View class</a></td>';

                                
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
</div>
</div>
</body>
</html>