<?php 
session_start();
if (!isset($_SESSION['LoginClassTeacher'])) {
    header('Location: ../Login/login.php');
    exit();
}
$teacher_id = $_SESSION['LoginClassTeacher'];


require_once "../connection/connection.php";
 
 
$user_id = $_SESSION['LoginClassTeacher'];

// Get the teacher_id from login_user_id
$query = "SELECT teacher_id FROM teachers_tb WHERE email = '$user_id' LIMIT 1";
$result = mysqli_query($con, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Teacher record not found.");
}

$teacher = mysqli_fetch_assoc($result);
$teacher_id = $teacher['teacher_id'];

/** 🔹 Total subjects assigned */
$sql_subjects_count = "SELECT COUNT(*) AS total_subjects 
                       FROM subjects 
                       WHERE teacher_id = $teacher_id";
$res_subjects = mysqli_query($con, $sql_subjects_count);
$total_subjects = mysqli_fetch_assoc($res_subjects)['total_subjects'];

/** 🔹 Total classes assigned */
$sql_classes_count = "SELECT COUNT(DISTINCT class_id) AS total_classes 
                      FROM subjects 
                      WHERE teacher_id = $teacher_id";
$res_classes = mysqli_query($con, $sql_classes_count);
$total_classes = mysqli_fetch_assoc($res_classes)['total_classes'];

/** 🔹 Total pupils in those classes */
$sql_pupils_count = "SELECT COUNT(DISTINCT p.pupil_id) AS total_pupils
                     FROM pupils_tb p
                     JOIN classes c ON p.class_id = c.class_id
                     JOIN subjects s ON c.class_id = s.class_id
                     WHERE s.teacher_id = $teacher_id";
$res_pupils = mysqli_query($con, $sql_pupils_count);
$total_pupils = mysqli_fetch_assoc($res_pupils)['total_pupils'];

 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="../css/index.css">
</head>
<body>
     
    <?php include('../Main/class-teacher-sidebar.php')?>
    
    <main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 w-100">
        <div class="dashboard-header text-center">
            <h1><i class="  me-2"></i>Class Teacher Dashboard</h1>
        </div>
             
        </div>
    
        <div class="dashboard-container">
           
            <div class="dashboard-card classes">
                <div class="card-icon">
                    <i class="fas fa-school"></i>
                </div>
                <h3>Classes</h3>
                <div class="count"><?php echo $total_classes; ?></div>
                <a href="../ClassTeacher/class.php" class="btn-view">Manage Classes</a>
            </div>
            <!-- Pupils -->
            <div class="dashboard-card pupils">
                <div class="card-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h3>Pupils</h3>
                <div class="count"><?php echo $total_pupils; ?></div>
               
                <a href="../ClassTeacher/display_pupils.php" class="btn-view">Manage Pupils</a>
            </div>
 
            
            <div class="dashboard-card tests">
                <div class="card-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <h3>Tests</h3>
                <div class="count"><?php echo $total_subjects; ?></div>
                
                <a href="../ClassTeacher/test.php" class="btn-view">Manage Tests</a>
            </div>
        </div>

    <script>
        // Add active class to current page in sidebar
        document.addEventListener('DOMContentLoaded', function() {
            // Set dashboard as active
            const currentPage = window.location.pathname.split('/').pop();
            if (currentPage === 'admin-index.php') {
                const dashboardLink = document.querySelector('a[href="../Admin/admin-index.php"]');
                if (dashboardLink) {
                    dashboardLink.classList.add('active');
                    
                    // Also activate the parent if it's in a dropdown
                    let parent = dashboardLink.closest('.dropdown-container');
                    if (parent) {
                        parent.classList.add('show');
                        const dropdownBtn = parent.previousElementSibling;
                        if (dropdownBtn) {
                            dropdownBtn.classList.add('active');
                        }
                    }
                }
            }
            
            // Add animation to cards on scroll
            const cards = document.querySelectorAll('.dashboard-card');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animationPlayState = 'running';
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });
            
            cards.forEach(card => {
                card.style.animationPlayState = 'paused';
                observer.observe(card);
            });
        });
    </script>
    </main>
</body>
</html>