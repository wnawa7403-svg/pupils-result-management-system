<?php 
session_start();
if (!isset($_SESSION['LoginClassTeacher'])) {
    header('Location: ../Login/login.php');
    exit();
}
$teacher_id = $_SESSION['LoginClassTeacher'];

require_once "../connection/connection.php";

$teacher_email = $_SESSION['LoginClassTeacher'];

// Get teacher_id
$query = "SELECT teacher_id FROM teachers_tb WHERE email=? LIMIT 1";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "s", $teacher_email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$teacher = mysqli_fetch_assoc($result);
$teacher_id = $teacher['teacher_id'];

// Fetch only classes assigned to this teacher
$class_query = "
    SELECT DISTINCT c.class_id, c.class_name 
    FROM classes c
    JOIN subjects s ON s.class_id = c.class_id
    WHERE s.teacher_id = ?
    ORDER BY c.class_name ASC
";
$class_stmt = mysqli_prepare($con, $class_query);
mysqli_stmt_bind_param($class_stmt, "i", $teacher_id);
mysqli_stmt_execute($class_stmt);
$class_result = mysqli_stmt_get_result($class_stmt);

// Fetch terms
$term_result = mysqli_query($con, "SELECT term_id, t_name FROM terms");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Daily Attendance</title>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="../css/style.css">
    
</head>
<body>
  <?php  include('../Main/class_teacher_sidebar.php');
   ?>
    <br><br>
 <main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 w-100">
  
    
         <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
        <h2>Daily Attendance Entry</h2>
</div>
<div class="card-body">
<!-- Selection Form -->
<form method="GET" action="attendance.php">
    <label>Class:</label>
    <select class="form-control" name="class_id" required>
        <option value="">--Select Class--</option>
        <?php while ($row = mysqli_fetch_assoc($class_result)) { ?>
            <option value="<?php echo $row['class_id']; ?>">
                <?php echo $row['class_name']; ?>
            </option>
        <?php } ?>
    </select>
<br>
    <label>Term:</label>
    <select class="form-control" name="term_id" required>
        <option value="">--Select Term--</option>
        <?php
        mysqli_data_seek($term_result, 0);
        while ($row = mysqli_fetch_assoc($term_result)) { ?>
            <option value="<?php echo $row['term_id']; ?>">
                <?php echo $row['t_name']; ?>
            </option>
        <?php } ?>
    </select>
<br>
    <div class="form-group ">
                        <button type="submit" name="submit" class="btn btn-primary ">Search</Search></button>
                    </div>
										
</form>

<hr>
   </div>
                </div>
            </div>
 
  <div class="col-md-8">
<?php
if (isset($_GET['class_id']) && isset($_GET['term_id'])):
    $class_id = $_GET['class_id'];
    $term_id = $_GET['term_id'];

    // Fetch pupils in the selected class
    $stmt = mysqli_prepare($con, "SELECT pupil_id, first_name, last_name FROM pupils_tb WHERE class_id=? ORDER BY last_name, first_name");
    mysqli_stmt_bind_param($stmt, "i", $class_id);
    mysqli_stmt_execute($stmt);
    $students = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($students) > 0):
?>
    <form method="POST" action="insert_attendance.php">
        <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
        <input type="hidden" name="term_id" value="<?php echo $term_id; ?>">
  <div class="card-header bg-primary text-white">
        <h3>Mark Attendance (<?php echo date("l, Y-m-d"); ?>)</h3>
    </div>
    
        <table class="table table-bordered table-striped border="1" cellpadding="5">
            <tr>
                <th>Pupil ID</th>
                <th>Name</th>
                <th >Status</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($students)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['pupil_id']); ?></td>
                <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                
<td class="attendance-btn text-center align-middle">
     
    <input type="radio" id="present_<?php echo $row['pupil_id']; ?>" 
           name="attendance[<?php echo $row['pupil_id']; ?>]" value="1" required>
    <label class="present" for="present_<?php echo $row['pupil_id']; ?>">✅ Present</label>

    <!-- Absent = 0 -->
    <input type="radio" id="absent_<?php echo $row['pupil_id']; ?>" 
           name="attendance[<?php echo $row['pupil_id']; ?>]" value="0">
    <label class="absent" for="absent_<?php echo $row['pupil_id']; ?>">❌ Absent</label>
</td>

                </td>
            </tr>
            <?php } ?>
        </table>
        <br>
         
                    <div class="form-group ">
                        <button type="submit" name="submit" class="btn btn-primary ">Save Attendance</button>
                    </div>
											
         
             
            </div>
    </form>
<?php
    else:
        echo "<p>No students found in this class.</p>";
    endif;
endif;
?>
</body>
</html>
