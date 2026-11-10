
<?php
require_once "../connection/connection.php";

if(isset($_POST['attendance'])){

$class_id=$_POST['class_id'];
$term_id=$_POST['term_id'];
$date=$_POST['date'];

/* CHECK IF ATTENDANCE ALREADY EXISTS */

$check=mysqli_prepare($con,"
SELECT * FROM attendance 
WHERE class_id=? AND date=?
");

mysqli_stmt_bind_param($check,"is",$class_id,$date);
mysqli_stmt_execute($check);
$result=mysqli_stmt_get_result($check);

if(mysqli_num_rows($result)>0){

header("Location: attendance.php?duplicate=1");
exit;

}

/* INSERT ATTENDANCE */

foreach($_POST['attendance'] as $pupil_id=>$status){

$stmt=mysqli_prepare($con,"
INSERT INTO attendance
(pupil_id,class_id,term_id,status,date)
VALUES(?,?,?,?,?)
");

mysqli_stmt_bind_param($stmt,"iiiis",
$pupil_id,
$class_id,
$term_id,
$status,
$date
);

mysqli_stmt_execute($stmt);

}

header("Location: attendance.php?success=1");
exit;

}
?>
```
