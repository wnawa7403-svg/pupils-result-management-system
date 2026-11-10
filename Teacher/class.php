<?php
session_start();
require_once '../connection/connection.php';

// Session check
if (!isset($_SESSION['LoginTeacher']) || empty($_SESSION['LoginTeacher'])) {
    die('Not logged in (LoginTeacher missing).');
}

$teacher_email = mysqli_real_escape_string($con, $_SESSION['LoginTeacher']);

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
</head>
<body>
    <?php include('../Main/Teacher-sidebar.php');?>
    <br><br><br>
    
    <main role="main" class="col-xl-10 col-lg-9 col-md-8 px-md-4 w-100">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-4 offset-lg-2">
                    <div class="card selection-card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-filter me-2"></i> Classes
                            </h5>
                        </div>
                        
                        <?php
                        // Prepare the query to include stream name and stream_id
                        $stmt = $con->prepare("
                            SELECT DISTINCT 
                                c.class_id, 
                                c.class_name, 
                                st.stream_id,
                                st.stream_name
                            FROM classes c
                            JOIN streams st ON st.stream_id = c.stream_id
                            JOIN subject_forms sf ON sf.class_id = c.class_id
                            JOIN teacher_subjects ts ON ts.subject_form_id = sf.id
                            WHERE ts.teacher_id = ?
                            ORDER BY c.class_name ASC
                        ");

                        $stmt->bind_param("i", $teacher_id);
                        $stmt->execute();
                        $class_result = $stmt->get_result();

                        if ($class_result && $class_result->num_rows > 0):
                        ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Class Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($class = $class_result->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <i class="fas fa-chalkboard text-primary me-2"></i>
                                            <?= htmlspecialchars($class['class_name'] . " - " . $class['stream_name']); ?>
                                        </td>
                                        <td>
                                            <a href="classList.php?class_id=<?= $class['class_id'] ?>&stream_id=<?= $class['stream_id'] ?>" 
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i> View Students
                                            </a>
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
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>