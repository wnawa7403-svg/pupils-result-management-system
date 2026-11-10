<?php
session_start();
require_once "../connection/connection.php";

// Ensure teacher is logged in
if (!isset($_SESSION['LoginClassTeacher'])) {
    header('Location: ../login/login.php');
    exit;
}

$user_id = $_SESSION['LoginClassTeacher'];

// Get teacher_id from login
$query = "SELECT teacher_id FROM teachers_tb WHERE email = '$user_id' LIMIT 1";
$result = mysqli_query($con, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Teacher record not found.");
}

$teacher = mysqli_fetch_assoc($result);
$teacher_id = $teacher['teacher_id'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View subjects</title>

   <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    
<?php include('../Main/class_teacher_sidebar.php'); ?>
<main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 w-100">
    
<br> 
<div class="dashboard-header">
         
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h2>subjects</h2>
</div>
    <?php
    $sql_students = "SELECT DISTINCT p.pupil_id, p.first_name, p.last_name, c.class_name
                 FROM pupils_tb p
                 JOIN classes c ON p.class_id = c.class_id
                 JOIN subjects s ON s.class_id = c.class_id
                 WHERE s.teacher_id = $teacher_id
                 ORDER BY c.class_name, p.last_name, p.first_name";

$students_result = mysqli_query($con, $sql_students);

echo "<h3>Students in Your Classes</h3>";

if (mysqli_num_rows($students_result) > 0) {
    echo "<table class='table table-bordered'>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student Name</th>
                    <th>Class</th>
                </tr>
            </thead>
            <tbody>";
    $count = 1;
    while ($student = mysqli_fetch_assoc($students_result)) {
        echo "<tr>
                <td>{$count}</td>
                <td>{$student['first_name']} {$student['last_name']}</td>
                <td>{$student['class_name']}</td>
              </tr>";
        $count++;
    }
    echo "</tbody></table>";
} else {
    echo "<p>No students assigned to your classes yet.</p>";
}
?>
</main>
</body>
</html>
 
