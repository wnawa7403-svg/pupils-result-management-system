 
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
$query = "SELECT teacher_id FROM teachers_tb WHERE email = ? LIMIT 1";
$stmt = $con->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows == 0) {
    die("Teacher record not found.");
}

$teacher = $result->fetch_assoc();
$teacher_id = $teacher['teacher_id'];

// Fetch ONLY marks entered by this teacher
$query = "SELECT 
    m.marks_id,
    m.subject_id,
    m.pupil_id,
    m.test_id,
    m.term_id,
    m.class_id,
    m.marks,
    m.remarks,
    s.subject_name,
    tst.test_name,
    trm.t_name AS term_name,
    p.first_name,
    p.last_name,
    c.class_name
FROM marks_tb m
LEFT JOIN pupils_tb p ON m.pupil_id = p.pupil_id
LEFT JOIN subjects s ON m.subject_id = s.subject_id
LEFT JOIN classes c ON m.class_id = c.class_id
LEFT JOIN terms trm ON m.term_id = trm.term_id
LEFT JOIN test tst ON m.test_id = tst.test_id
WHERE s.teacher_id = ?   -- ✅ filter only subjects taught by logged-in teacher
ORDER BY c.class_name, p.last_name, p.first_name, s.subject_name";


$stmt2 = $con->prepare($query);
$stmt2->bind_param("i", $teacher_id);
$stmt2->execute();
$marks_result = $stmt2->get_result();

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Marks Table</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../css/style.css">
</head>

<body >
    <br><br><br><br>
    <main class="col-xl-10     ml-sm-auto     mt-3">
 
    <?php include('../Main/class_teacher_sidebar.php') ?>
<div class="d-flex justify-content-between align-items-center   mb-3  ">
        
     <div class="col-md-10">
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class=" text-white font-weight-bold">
  
        <tr>
            <th>Pupils ID</th>
            <th>Pupil</th>
            <th>Class</th>
            <th>Subject</th>
           <th>Term</th>
           <th>Test </th>
            <th>Marks</th>
          
            <th>Remarks</th>
 
        </tr>
    </thead>
 
    <tbody>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['pupil_id']; ?></td>
            <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
            <td><?php echo htmlspecialchars($row['class_name']); ?></td>
            <td><?php echo htmlspecialchars($row['subject_name']); ?></td>

              <td><?php echo htmlspecialchars($row['term_name']); ?></td>
                <td><?php echo htmlspecialchars($row['test_name']); ?></td>
           <td><?php echo htmlspecialchars($row['marks']); ?></td>
          
            
            <td><?php echo htmlspecialchars($row['remarks']); ?></td>
             
        </tr>
    <?php } ?>
    </tbody>
</table>

</body>
</html>
