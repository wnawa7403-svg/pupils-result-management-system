<?php
session_start();
require_once '../connection/connection.php';

// Session check
if (!isset($_SESSION['LoginHod']) || empty($_SESSION['LoginHod'])) {
    die('Not logged in (LoginHod missing).');
}

$teacher_email = mysqli_real_escape_string($con, $_SESSION['LoginHod']);

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
     <?php  include('../Main/Hod_sidebar.php');?>
     <br><br><br>
    <div class="d-flex justify-content-center">
    <div class="card shadow-sm" style="max-width: 900px; width: 100%;">
        <div class="card-header">
            <h2 class="h5 mb-0"><i class="fas fa-list me-2"></i>Your Classes</h2>
        </div>
        <div class="card-body">
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
                            <th>Action</th>
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
                                <a href="view_classes.php?class_id=<?php echo $class['class_id']; ?>" class="btn btn-primary btn-sm">
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>