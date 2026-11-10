<?php

 
// Check if logged in
if (!isset($_SESSION["LoginAdmin"])) {
    header("Location: /login/login");
    exit();
}

// Get session data
$username = isset($_SESSION['LoginAdmin']) ? $_SESSION['LoginAdmin'] : 'User';
$role = $_SESSION['Role'] ?? 'User';

// Database connection
require_once "../connection/connection.php";

// Get counts from database
$total_students = $con->query("SELECT * FROM pupils_tb")->num_rows ?? 0;
$total_classes = $con->query("SELECT * FROM classes")->num_rows ?? 0;
$total_subjects = $con->query("SELECT * FROM subjects")->num_rows ?? 0;
$total_teachers = $con->query("SELECT * FROM teachers_tb")->num_rows ?? 0;
?>
<!DOCTYPE html>
<html>
<head>

    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- DataTables CSS & Buttons -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
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
    <!-- Font Awesome for icons -->
</head>
<body>
    
     
    <div class="dashboard-wrapper">
        <!-- Include Sidebar -->
        <?php include('../Main/Main-sidebar.php'); ?>
        
        <main class="main-content col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 w-100">
            <div class="welcome ">
                <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
                <p>You are logged in as <?php echo htmlspecialchars($role); ?></p>
            </div>
            
            <?php if($role == 'Admin'): ?>
                <!-- Stats Section -->
                <div class="row">
                    <!-- Total Students -->
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="info-box">
                            <div class="info-box-icon bg-info">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Students</span>
                                <span class="info-box-number"><?php echo number_format($total_students); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Total Classes -->
                    <div class="col-12 col-sm-6 col-md-4">
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
                    <div class="col-12 col-sm-6 col-md-4">
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
                    
                    <!-- Total Teachers -->
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="info-box">
                            <div class="info-box-icon bg-warning">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Teachers</span>
                                <span class="info-box-number"><?php echo number_format($total_teachers); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Active Exams -->
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="info-box">
                            <div class="info-box-icon bg-purple">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="info-box-content">
                                <span class="info-box-text">Active Exams</span>
                                <span class="info-box-number">
                                    <?php 
                                        $active_exams = $con->query("SELECT * FROM test WHERE test_status = 'active'")->num_rows ?? 0;
                                        echo number_format($active_exams);
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Departments -->
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="info-box">
                            <div class="info-box-icon bg-danger">
                                <i class="fas fa-sitemap"></i>
                            </div>
                            <div class="info-box-content">
                                <span class="info-box-text">Departments</span>
                                <span class="info-box-number">
                                    <?php 
                                        $total_departments = $con->query("SELECT * FROM dept")->num_rows ?? 0;
                                        echo number_format($total_departments);
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <h2 class="section-title">Quick Actions</h2>
                <div class="menu">
                    <a href="index.php?page=instructor" class="menu-item">
                        <i class="fas fa-user-plus"></i>
                        <span>Add Teacher</span>
                    </a>
                    <a href="index.php?page=student_list" class="menu-item">
                        <i class="fas fa-users"></i>
                        <span>Student List</span>
                    </a>
                    <a href="index.php?page=class" class="menu-item">
                        <i class="fas fa-school"></i>
                        <span>Manage Classes</span>
                    </a>
                    <a href="index.php?page=departments" class="menu-item">
                        <i class="fas fa-sitemap"></i>
                        <span>Departments</span>
                    </a>
                    <a href="index.php?page=add_subjects" class="menu-item">
                        <i class="fas fa-book"></i>
                        <span>Subjects</span>
                    </a>
                    <a href="index.php?page=exam" class="menu-item">
                        <i class="fas fa-pen-alt"></i>
                        <span>Exams</span>
                    </a>
                    <a href="index.php?page=attendance" class="menu-item">
                        <i class="fas fa-calendar-check"></i>
                        <span>Attendance</span>
                    </a>
                    <a href="index.php?page=reports" class="menu-item">
                        <i class="fas fa-file-pdf"></i>
                        <span>Reports</span>
                    </a>
                    <a href="index.php?page=manage_accounts" class="menu-item">
                        <i class="fas fa-users-cog"></i>
                        <span>Manage Accounts</span>
                    </a>
                    
                </div>
                
            <?php else: ?>
                <!-- For non-admin users -->
                <div class="card">
                    <div class="card-body">
                        <h3>Welcome <?php echo htmlspecialchars($username); ?>!</h3>
                        <p>You are logged in as <?php echo htmlspecialchars($role); ?>.</p>
                        
                        <div class="menu">
                            <a href="index.php?page=attendance" class="menu-item">
                                <i class="fas fa-calendar-check"></i>
                                <span>Take Attendance</span>
                            </a>
                            <a href="index.php?page=marks" class="menu-item">
                                <i class="fas fa-edit"></i>
                                <span>Enter Marks</span>
                            </a>
                            <a href="index.php?page=reports" class="menu-item">
                                <i class="fas fa-chart-line"></i>
                                <span>View Reports</span>
                            </a>
                            <a href="index.php?page=logout" class="menu-item logout">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Debug Section (Remove in production) -->
            <div class="card" style="margin-top: 30px; background: #f8f9fa;">
                <div class="card-body">
                    <h5>Debug Information</h5>
                    <p><strong>Session ID:</strong> <?php echo session_id(); ?></p>
                    <p><strong>Role:</strong> <?php echo htmlspecialchars($role); ?></p>
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
                     
                </div>
            </div>
        </main>
    </div>
    
    <script>
        // Mobile sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobileMenuButton');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-content');
            
            if (mobileMenuBtn && sidebar) {
                mobileMenuBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    if (sidebar.classList.contains('active')) {
                        mainContent.style.marginLeft = '250px';
                    } else {
                        mainContent.style.marginLeft = '0';
                    }
                });
            }
            
            // Add animation to info boxes
            const infoBoxes = document.querySelectorAll('.info-box');
            infoBoxes.forEach(box => {
                box.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                    this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
                });
                
                box.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 2px 10px rgba(0,0,0,0.08)';
                });
            });
        });
    </script>
</body>
</html>
</body>
</html>