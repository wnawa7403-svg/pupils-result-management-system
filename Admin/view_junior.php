
<?php
require_once '../connection/connection.php'; // Make sure this defines $con as MySQLi connection
?>

 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</head>
<body class="p-4">
    <?php include('../Main/Main-sidebar.php') ?>
    
    <br><br><br><br>
  
    <main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 w-100">
<form action="junior_report_book.php" method="POST" >
    <div class="sub-main">
				<div class="text-center d-flex flex-wrap flex-md-nowrap pt-3 pb-2 mb-3 text-white admin-dashboard pl-3">
				
					<div class="d-flex item-align:center">
						 
						<button type="button" class="btn btn-primary ml-5" data-toggle="modal" data-target=".bd-example-modal-sm">GENERATE REPORT BOOK</button>
					</div>
				</div>
				<div class="col-md-2 pt-3 w-100">
  				    <!-- Large modal -->
					<div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
					   <div class="modal-dialog modal-sm">
						    <div class="modal-content">
							    <div class="modal-header bg-info text-white">
							        <h4 class="modal-title text-center">SELECT CLASS AND TERM</h4>
						        </div>
							    <div class="modal-body">
	                              <div class="form-group">
                                    <label for="class_id">Select Class:</label>
                                    <select class="form-control" id="class_id" name="class_id" required>
                                        <option value="">-- Select Class --</option>
                                        <?php
                                        $classes_query = "SELECT * FROM classes   ORDER BY class_name ASC";
                                        $classes_result = mysqli_query($con, $classes_query);
                                        while ($row = mysqli_fetch_assoc($classes_result)) {
                                            echo "<option value='{$row['class_id']}'>{$row['class_name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- Term Selection -->
                                <div class="form-group mt-3">
                                    <label for="term_id">Select Term:</label>
                                    <select class="form-control" id="term_id" name="term_id" required>
                                        <option value="">-- Select Term --</option>
                                        <?php
                                        $terms_query = "SELECT * FROM terms ORDER BY t_name ASC";
                                        $terms_result = mysqli_query($con, $terms_query);
                                        while ($row = mysqli_fetch_assoc($terms_result)) {
                                            echo "<option value='{$row['term_id']}'>{$row['t_name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                <label for="year">Select Year:</label>
                                <select class="form-control" name="year" required>
                                    <option value="">-- Select Year --</option>
                                    <?php
                                    $years = mysqli_query($con, "SELECT DISTINCT year FROM pupils_tb ORDER BY year ASC");
                                    while ($row = mysqli_fetch_assoc($years)) {
                                        $selected = ($row['year'] == $year) ? 'selected' : '';
                                        echo "<option value='{$row['year']}' $selected>{$row['year']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                                 
   
                        <div class="modal-footer">
                     
                    <button type="submit" class="btn btn-primary" name="btn_add_sub">print to pdf</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
    
        <!-- Form Column -->
          

	</div>
</div>
</div>
</div>
</div>
</div>

      </div>
		
</div>
</div>

	
       
 </div>
 
       
      
        </div>
 
       
        				
				
    </section>
                                
                   