<?php
session_start();
if (!isset($_SESSION['LoginTeacher'])) {
    header('Location: ../Login/login.php');
    exit();
}

require_once "../connection/connection.php";

$user_id = $_SESSION['LoginTeacher']; // email

// Get teacher_id from email
$query = "SELECT teacher_id FROM teachers_tb WHERE email = ? LIMIT 1";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "s", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    die("Teacher record not found.");
}

$teacher = mysqli_fetch_assoc($result);
$teacher_id = $teacher['teacher_id'];

// Get dashboard stats for THIS teacher only
$sql = "
SELECT
    t.teacher_id,
    CONCAT(t.first_name, ' ', t.last_name) AS teacher_name,
    COUNT(DISTINCT ts.subject_form_id) AS total_subjects,
    COUNT(DISTINCT sf.class_id) AS total_classes,
    COUNT(DISTINCT c.stream_id) AS total_streams,
    COUNT(DISTINCT p.pupil_id) AS total_pupils
FROM teachers_tb t
LEFT JOIN teacher_subjects ts
    ON t.teacher_id = ts.teacher_id
LEFT JOIN subject_forms sf
    ON ts.subject_form_id = sf.id
LEFT JOIN classes c
    ON sf.class_id = c.class_id
LEFT JOIN streams s
    ON c.stream_id = s.stream_id
LEFT JOIN pupils_tb p
    ON c.class_id = p.class_id
WHERE t.teacher_id = ?  -- Filter by logged-in teacher
GROUP BY t.teacher_id
";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $teacher_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $teacher_name = $row['teacher_name'];
    $total_subjects = $row['total_subjects'] ?: 0;
    $total_classes = $row['total_classes'] ?: 0;
    $total_streams = $row['total_streams'] ?: 0;
    $total_pupils = $row['total_pupils'] ?: 0;
} else {
    // If no specific data found, set defaults
    $teacher_name = "Teacher";
    $total_subjects = 0;
    $total_classes = 0;
    $total_streams = 0;
    $total_pupils = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

 
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <style>
       body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f4f6f9;
        }
        .welcome {
            background: #4CAF50;
            color: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .menu {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 20px;
            margin-bottom: 30px;
        }
        .menu-item {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: all 0.3s;
        }
        .menu-item:hover {
            background: #e0e0e0;
            transform: translateY(-2px);
        }
        
        /* Info Box Styles */
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
            margin-bottom: 30px;
        }
        
        .col-12, .col-sm-6, .col-md-4 {
            position: relative;
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
        }
        
        @media (min-width: 576px) {
            .col-sm-6 {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }
        
        @media (min-width: 768px) {
            .col-md-4 {
                flex: 0 0 33.333333%;
                max-width: 33.333333%;
            }
        }
        
        .info-box {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
            border-radius: .25rem;
            background: #fff;
            display: flex;
            margin-bottom: 1rem;
            min-height: 80px;
            padding: .5rem;
            position: relative;
        }
        
        .info-box-icon {
            border-radius: .25rem;
            align-items: center;
            display: flex;
            font-size: 1.875rem;
            justify-content: center;
            text-align: center;
            width: 70px;
        }
        
        .info-box-icon.bg-info {
            background-color: #17a2b8 !important;
            color: white;
        }
        
        .info-box-icon.bg-primary {
            background-color: #007bff !important;
            color: white;
        }
        
        .info-box-icon.bg-success {
            background-color: #28a745 !important;
            color: white;
        }
        
        .info-box-icon.bg-warning {
            background-color: #ffc107 !important;
            color: #212529;
        }
        
        .info-box-content {
            flex: 1;
            padding: 5px 10px;
        }
        
        .info-box-text {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 14px;
            color: #6c757d;
        }
        
        .info-box-number {
            display: block;
            font-weight: 700;
            font-size: 24px;
        }
        
        .card {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
            margin-bottom: 1rem;
            background: #fff;
            border-radius: .25rem;
            border: 0 solid rgba(0,0,0,.125);
        }
        
        .card-body {
            flex: 1 1 auto;
            min-height: 1px;
            padding: 1.25rem;
        }
        
        hr {
            margin: 30px 0;
            border: 0;
            border-top: 1px solid #dee2e6;
        }
        
        ul {
            list-style: none;
            padding: 0;
        }
        
        ul li {
            margin-bottom: 10px;
        }
        
        ul li a {
            color: #007bff;
            text-decoration: none;
        }
        
        ul li a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <?php include('../Main/Teacher-sidebar.php')?>
    
    <<main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 w-100">
    <!-- Welcome Message -->
    <div class="welcome-message mb-4">
        <h1>Welcome, <?php echo htmlspecialchars($teacher_name); ?>!</h1>
        <p class="text-muted">You are logged in as Teacher.</p>
    </div>
    
    <!-- Stats Section -->
    <div class="row mb-4">
        <!-- Total Students -->
        <div class="col-12 col-sm-6 col-md-4 mb-3">
            <div class="info-box">
                <div class="info-box-icon bg-info">
                    <i class="fas fa-users"></i>
                </div>
                <div class="info-box-content">
                    <span class="info-box-text">Total Students</span>
                    <span class="info-box-number"><?php echo number_format($total_pupils); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Total Classes -->
        <div class="col-12 col-sm-6 col-md-4 mb-3">
            <div class="info-box">
                <div class="info-box-icon bg-primary">
                    <i class="fas fa-school"></i>
                </div>
                <div class="info-box-content">
                    <span class="info-box-text">Total Classes</span>
                    <span class="info-box-number"><?php echo number_format($total_classes); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Total Subjects -->
        <div class="col-12 col-sm-6 col-md-4 mb-3">
            <div class="info-box">
                <div class="info-box-icon bg-success">
                    <i class="fas fa-book"></i>
                </div>
                <div class="info-box-content">
                    <span class="info-box-text">Total Subjects</span>
                    <span class="info-box-number"><?php echo number_format($total_subjects); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Total Streams -->
        <div class="col-12 col-sm-6 col-md-4 mb-3">
            <div class="info-box">
                <div class="info-box-icon bg-warning">
                    <i class="fas fa-stream"></i>
                </div>
                <div class="info-box-content">
                    <span class="info-box-text">Total Streams</span>
                    <span class="info-box-number"><?php echo number_format($total_streams); ?></span>
                </div>
            </div>
        </div>
        
                
         
        
        <!-- Average Students per Class -->
        <div class="col-12 col-sm-6 col-md-4 mb-3">
            <div class="info-box">
                <div class="info-box-icon bg-danger">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="info-box-content">
                    <span class="info-box-text">Avg. Students/Class</span>
                    <span class="info-box-number">
                        <?php 
                            if ($total_classes > 0) {
                                echo number_format($total_pupils / $total_classes, 1);
                            } else {
                                echo '0';
                            }
                        ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    
     
    
    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6 col-md-3">
                            <a href="../Teacher/instructor.php" class="btn btn-outline-primary w-100">
                                <i class="fas fa-plus-circle me-1"></i> Teacher info
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="../Teacher/class.php" class="btn btn-outline-success w-100">
                                <i class="fas fa-tasks me-1"></i> View Class
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="../Teacher/display_pupils.php" class="btn btn-outline-info w-100">
                                <i class="fas fa-user-plus me-1"></i> Enter Results
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="#" class="btn btn-outline-warning w-100">
                                <i class="fas fa-chart-line me-1"></i> View Results
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
    /* Welcome Message */
    .welcome-message {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border-left: 4px solid #3498db;
    }
    
    .welcome-message h1 {
        margin: 0 0 10px 0;
        color: #333;
        font-size: 24px;
    }
    
    .welcome-message p {
        margin: 0;
        color: #666;
    }
    
    /* Info Box Styling */
    .info-box {
        display: flex;
        align-items: center;
        padding: 15px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        height: 100%;
    }
    
    .info-box-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        border-radius: 8px;
        color: white;
        font-size: 24px;
        margin-right: 15px;
    }
    
    .info-box-content {
        flex: 1;
    }
    
    .info-box-text {
        display: block;
        color: #666;
        font-size: 14px;
        margin-bottom: 5px;
    }
    
    .info-box-number {
        display: block;
        font-size: 24px;
        font-weight: bold;
        color: #333;
    }
    
    /* Color Classes */
    .bg-info { background-color: #17a2b8; }
    .bg-primary { background-color: #007bff; }
    .bg-success { background-color: #28a745; }
    .bg-warning { background-color: #ffc107; }
    .bg-purple { background-color: #6f42c1; }
    .bg-danger { background-color: #dc3545; }
    
    /* Dashboard Header */
    .dashboard-header {
        padding: 15px;
        background-color: #fff;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border-left: 4px solid #2c3e50;
    }
    
    .dashboard-header h2 {
        margin: 0;
        color: #333;
        font-size: 20px;
    }
    
    /* Dashboard Cards */
    .dashboard-card {
        background-color: #fff;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        text-align: center;
        transition: transform 0.3s ease;
        border-top: 4px solid #3498db;
    }
    
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .card-icon {
        font-size: 40px;
        margin-bottom: 15px;
    }
    
    .dashboard-card h3 {
        margin: 10px 0;
        color: #333;
        font-size: 18px;
        font-weight: 600;
    }
    
    .count {
        font-size: 32px;
        font-weight: bold;
        color: #2c3e50;
        margin: 15px 0;
    }
    
    .dashboard-card p {
        color: #666;
        margin-bottom: 15px;
        font-size: 14px;
    }
    
    .btn-view {
        display: inline-block;
        padding: 8px 20px;
        text-decoration: none;
        border-radius: 4px;
        margin-top: 10px;
        font-size: 14px;
        font-weight: 500;
    }
    
    /* Quick Actions */
    .card {
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border-radius: 8px;
    }
    
    .card-header {
        border-bottom: 1px solid #dee2e6;
        border-radius: 8px 8px 0 0 !important;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .info-box {
            padding: 10px;
        }
        
        .info-box-icon {
            width: 50px;
            height: 50px;
            font-size: 20px;
        }
        
        .info-box-number {
            font-size: 20px;
        }
        
        .dashboard-card {
            padding: 20px 15px;
        }
        
        .count {
            font-size: 28px;
        }
    }
</style>

    </main>

    <script>
        // Add active class to current page in sidebar
        document.addEventListener('DOMContentLoaded', function() {
            // Set dashboard as active
            const currentPage = window.location.pathname.split('/').pop();
            if (currentPage === 'Teacher-index.php') {
                const dashboardLink = document.querySelector('a[href="../Teacher/Teacher-index.php"]');
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
</body>
</html>

