<?php
session_start();
require_once "../connection/connection.php";

// Ensure teacher is logged in
if (!isset($_SESSION['LoginTeacher'])) {
    header('Location: ../login/login.php');
    exit;
}

$user_id = $_SESSION['LoginTeacher'];

// Get the teacher_id from login_user_id
$query = "SELECT teacher_id FROM teachers_tb WHERE  email = '$user_id' LIMIT 1";
$result = mysqli_query($con, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Teacher record not found.");
}
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
 
<?php include('../Main/Teacher_sidebar.php'); ?>
<main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 w-100">
    
<br> 
<div class="dashboard-header">
         
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h2>subjects</h2>
</div>
    <?php
$teacher = mysqli_fetch_assoc($result);
$teacher_id = $teacher['teacher_id'];

// Fetch subjects assigned to this teacher
$sql_subjects = "SELECT s.subject_id, s.subject_name, c.class_name, s.date_added
                 FROM subjects s
                 JOIN classes c ON s.class_id = c.class_id
                 WHERE s.teacher_id = $teacher_id
                 ORDER BY s.subject_name ASC";

$subjects_result = mysqli_query($con, $sql_subjects);

if (!$subjects_result) {
    die("Query error: " . mysqli_error($con));
}

// Display subjects
if (mysqli_num_rows($subjects_result) > 0) {
    echo "<table class='table table-bordered'>";
    echo "<thead><tr>
            <th>#</th>
            <th>Subject Name</th>
            <th>Class Name</th>
            <th>Date Added</th>
          </tr></thead><tbody>";
    $count = 1;
    while ($row = mysqli_fetch_assoc($subjects_result)) {
        echo "<tr>
                <td>{$count}</td>
                <td>{$row['subject_name']}</td>
                <td>{$row['class_name']}</td>
                <td>{$row['date_added']}</td>
              </tr>";
        $count++;
    }
    echo "</tbody></table>";
} else {
    echo "<p>No subjects assigned to you yet.</p>";
}
?>

</body>
</html>
