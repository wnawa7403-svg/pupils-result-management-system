<?php
require_once "../connection/connection.php";

$class_id   = $_POST['class_id'];
$term_id    = $_POST['term_id'];
$date       = date("Y-m-d");
$day        = date("l");
$attendance = $_POST['attendance'];

$query = "INSERT INTO attendance (pupil_id, class_id, term_id, date, day, status) 
          VALUES (?, ?, ?, ?, ?, ?)
          ON DUPLICATE KEY UPDATE status=?, day=?";

$stmt = mysqli_prepare($con, $query);

if (!$stmt) {
    die("SQL Error: " . mysqli_error($con));
}

foreach ($attendance as $pupil_id => $status) {
    // ✅ here $status is already 1 (present) or 0 (absent)
    mysqli_stmt_bind_param($stmt, "iiissiis", 
        $pupil_id, 
        $class_id, 
        $term_id, 
        $date, 
        $day, 
        $status,   // insert
        $status,   // update
        $day       // update
    );
    mysqli_stmt_execute($stmt);
}

mysqli_stmt_close($stmt);
    header("refresh:2; url=attendance.php");
 
?>
