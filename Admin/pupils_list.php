<?php
session_start();
require_once "../connection/connection.php";

// Ensure only admin can access
if (!isset($_SESSION["LoginAdmin"])) {
    header('Location: ../login/login.php');
    exit();
}

// Fetch all classes
$classes_result = $con->query("SELECT class_id, class_name FROM classes ORDER BY class_name ASC");

// Fetch all terms
$terms_result = $con->query("SELECT term_id, t_name FROM terms ORDER BY term_id ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Select Student</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
   <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --success-color: #1cc88a;
            --light-bg: #f8f9fc;
            --dark-color: #2c3e50;
            --text-dark: #212529;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fb;
            color: var(--text-dark);
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 25px 0;
            margin-bottom: 30px;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s;
            margin-bottom: 25px;
            
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: 12px 12px 0 0 !important;
            padding: 15px 20px;
            font-weight: 600;
        }
        
        .form-control, .form-select {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .btn-success {
            background: linear-gradient(to right, var(--success-color), #17a673);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .marks-table {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .marks-table th {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 15px;
            border: none;
        }
        
        .marks-table td {
            padding: 15px;
            vertical-align: middle;
        }
        
        .student-photo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
            border: 2px solid #e3e6f0;
        }
        
        .selection-card {
            height: 100%;
        }
        
        .selection-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 20px;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #4a4a4a;
        }
        
        .alert-info {
            background-color: rgba(72, 149, 239, 0.15);
            border-color: var(--accent-color);
            color: #0c63e4;
            border-radius: 8px;
        }
        
        .badge-count {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 500;
        }
        
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 1.8rem;
            }
            
            .selection-icon {
                font-size: 2.5rem;
            }
        }
        main {
  display: flex;
  justify-content: center;
  align-items: flex-start; /* or center for full vertical centering */
}

    </style>
</head>
<body>
 <?php include('../Main/main-sidebar.php'); ?>
   <main role="main" class="col-xl-10 col-lg-9 col-md-8 px-md-4 w-100">
    <div class="container-fluid">
        <div class="row justify-content-center"><!-- Center row inside main -->
 
            <div class="col-lg-4 offset-lg-2">
                <div class="card selection-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-filter me-2"></i> Selection Criteria
                        </h5>
                    </div>
    <div class="card-body">
    <form method="get" action="individual_results.php">
        <div class="form-group">
            <label for="class_id">Select Class</label>
            <select name="class_id" id="class_id" class="form-select" required>
                <option value="">-- Select Class --</option>
                <?php while($class = $classes_result->fetch_assoc()): ?>
                    <option value="<?php echo $class['class_id']; ?>"><?php echo $class['class_name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="pupil_id"> Student </label>
            <select name="pupil_id" id="pupil_id" class="form-select" required>
                <option value="">-- Select Student --</option>
                <!-- Students will be loaded dynamically via AJAX -->
            </select>
        </div>

                                <div class="form-group">
                                <label for="year"><i class="fas fa-calendar me-1"></i> Year:</label>
                                <select class="form-select" name="year" required>
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

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i> Search Students
                                </button>
                            </div>
    </form>
</div>
   </div>
                </div>
            </div>
</div>
                </div>
            </div>
<!-- Include full jQuery (not slim) for AJAX -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script>
$(document).ready(function(){
    $('#class_id').change(function(){
        var classId = $(this).val();
        if(classId){
            $.ajax({
                url: 'fetch_students.php',
                type: 'GET',
                data: { class_id: classId },
                success: function(data){
                    $('#pupil_id').html(data);
                },
                error: function(){
                    alert('Error fetching students. Check fetch_students.php');
                }
            });
        } else {
            $('#pupil_id').html('<option value="">-- Select Student --</option>');
        }
    });
});
</script>
</body>
</html>
