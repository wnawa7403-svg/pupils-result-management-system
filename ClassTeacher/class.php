<?php
session_start();
require_once '../connection/connection.php';

// Session check
if (!isset($_SESSION['LoginClassTeacher']) || empty($_SESSION['LoginClassTeacher'])) {
    die('Not logged in (LoginTeacher missing).');
}

$teacher_email = mysqli_real_escape_string($con, $_SESSION['LoginClassTeacher']);

// Get teacher ID
$q = "SELECT teacher_id, first_name, last_name FROM teachers_tb WHERE email = '$teacher_email' LIMIT 1";
$r = mysqli_query($con, $q);
if (!$r || mysqli_num_rows($r) === 0) die("Teacher not found");
$teacher_data = mysqli_fetch_assoc($r);
$teacher_id = (int)$teacher_data['teacher_id'];
$teacher_name = htmlspecialchars($teacher_data['first_name'] . ' ' . $teacher_data['last_name']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Management - Teacher Portal</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/classes.css">
    <style>
        .sidebar .dropdown-toggle::after {
    display: none !important;
}

    </style>
</head>
<body>
     <?php  include('../Main/Teacher-sidebar.php');?>
     <br><br><br>
    <main role="main" class="col-xl-10 col-lg-9 col-md-8 px-md-4 w-100">
    <div class="container-fluid">
        <div class="row justify-content-center"><!-- Center row inside main -->
            <div class="col-lg-4 offset-lg-2">
                <div class="card selection-card">
                    <div class="card-header">
                        
                          <h5 class="mb-0">
                            <i class="fas fa-filter me-2"></i> Classes
                        </h5>
</div>
         <?php
// Fetch classes
$class_query = "SELECT DISTINCT c.class_id, c.class_name 
                FROM classes c
                JOIN subjects s ON c.class_id = s.class_id
                WHERE s.teacher_id = $teacher_id
                ORDER BY c.class_name";
$class_result = mysqli_query($con, $class_query);

if (mysqli_num_rows($class_result) > 0):
?>
<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Class Name</th>
                <th>Subjects</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($class = mysqli_fetch_assoc($class_result)): ?>
            <tr>
                <td>
                    <i class="fas fa-chalkboard text-primary me-2"></i>
                    <?php echo htmlspecialchars($class['class_name']); ?>
                </td>
                <td>
                    <?php
                    // Fetch subjects for this class assigned to the teacher
                    $class_id = $class['class_id'];
                    $subject_query = "SELECT subject_id, subject_name 
                                      FROM subjects 
                                      WHERE class_id = $class_id AND teacher_id = $teacher_id";
                    $subject_result = mysqli_query($con, $subject_query);

                    while ($subject = mysqli_fetch_assoc($subject_result)):
                    ?>
                        <a href="view_classes.php?class_id=<?php echo $class_id; ?>&subject_id=<?php echo $subject['subject_id']; ?>" 
                           class="btn btn-secondary btn-sm mb-1">
                            <?php echo htmlspecialchars($subject['subject_name']); ?>
                        </a>
                    <?php endwhile; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
<div class="text-center py-4">
    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
    <h4>No Classes Assigned</h4>
    <p class="text-muted">You haven't been assigned to any classes yet.</p>
</div>
<?php endif; ?>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>