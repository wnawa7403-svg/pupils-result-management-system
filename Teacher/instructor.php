<?php
session_start();
require_once "../connection/connection.php";

// Make sure teacher is logged in
if (!isset($_SESSION['LoginTeacher'])) {
    header('Location: ../login/login.php');
    exit;
}

$user_id = $_SESSION['LoginTeacher'];

// Fetch teacher info
$query = "SELECT teacher_id, first_name, middle_name, last_name, email, Teaching_number, gender, date 
          FROM teachers_tb 
          WHERE email = ? 
          LIMIT 1";
$stmt = $con->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Teacher record not found.");
}
$teacher = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/profile.css">
</head>
<body class="bg-light">
 
<?php include('../Main/Teacher-sidebar.php'); ?>

<main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 w-100">

    <div class="container mt-4">
          <nav aria-label="breadcrumb" class="profile-breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="Teacher-index.php"><i class="fas fa-home me-1"></i> Dashboard</a></li>
                    
                        <li class="breadcrumb-item active"><i class="fas fa-user me-1"></i>Profile</li>
                </ol>
            </nav>
            
    <div class="card profile-card shadow-lg border-0">
        <!-- Profile Header -->
        <div class="profile-header position-relative">
            <!-- Cover Image -->
            <div class="cover-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 150px; border-radius: 10px 10px 0 0;"></div>
            
            <!-- Profile Info -->
            <div class="profile-info position-relative px-4 pb-4">
                <!-- Avatar -->
                <<div class="position-absolute" style="top: -50px;">
    <div class="d-flex align-items-center justify-content-center" 
         style="width: 100px; height: 100px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 50%; border: 5px solid white;">
        <span class="avatar-initials text-white fw-bold" style="font-size: 36px;">
            <?php
                echo strtoupper(
                    substr($teacher['first_name'], 0, 1) .
                    substr($teacher['last_name'], 0, 1)
                );
            ?>
        </span>
    </div>
</div>

<!-- Welcome Message -->
<div class="welcome-section pt-5">
    <h2 class="fw-bold mb-1 text-black" style="color: #000;">
        Welcome, 
        <?php 
            echo htmlspecialchars($teacher['first_name']) . ' ' . htmlspecialchars($teacher['last_name']); 
        ?>!
    </h2>
    <p class="text-muted mb-3">Teacher Profile Dashboard</p>
 
                    <!-- Status Badge -->
                    <span class="badge bg-success fs-6 px-3 py-2">
                        <i class="fas fa-check-circle me-1"></i> Active Teacher
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Profile Body -->
        <div class="card-body p-4">
            <div class="row">
                <!-- Left Column - Personal Info -->
                <div class="col-lg-6">
                    <div class="profile-section mb-4">
                        <h4 class="section-title mb-3">
                            <i class="fas fa-user-circle me-2 text-primary"></i> Personal Information
                        </h4>
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label text-muted">Teacher ID</div>
                                <div class="info-value fw-bold">
                                    <i class="fas fa-id-card me-2 text-secondary"></i>
                                    <?php echo htmlspecialchars($teacher['teacher_id']); ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label text-muted">Full Name</div>
                                <div class="info-value fw-bold">
                                    <i class="fas fa-user me-2 text-secondary"></i>
                                    <?php 
                                        $full_name = trim($teacher['first_name'] . ' ' . 
                                        ($teacher['middle_name'] ? $teacher['middle_name'] . ' ' : '') . 
                                        $teacher['last_name']);
                                        echo htmlspecialchars($full_name);
                                    ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label text-muted">Email Address</div>
                                <div class="info-value">
                                    <i class="fas fa-envelope me-2 text-secondary"></i>
                                    <?php echo htmlspecialchars($teacher['email']); ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label text-muted">TCZ Number</div>
                                <div class="info-value">
                                    <i class="fas fa-certificate me-2 text-secondary"></i>
                                    <?php echo htmlspecialchars($teacher['Teaching_number']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column - Additional Info -->
                <div class="col-lg-6">
                    <div class="profile-section mb-4">
                        <h4 class="section-title mb-3">
                            <i class="fas fa-info-circle me-2 text-primary"></i> Additional Information
                        </h4>
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label text-muted">Gender</div>
                                <div class="info-value">
                                    <i class="fas fa-venus-mars me-2 text-secondary"></i>
                                    <span class="badge bg-light text-dark p-2">
                                        <?php echo htmlspecialchars($teacher['gender']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label text-muted">Registration Date</div>
                                <div class="info-value">
                                    <i class="fas fa-calendar-alt me-2 text-secondary"></i>
                                    <?php 
                                        $date = new DateTime($teacher['date']);
                                        echo $date->format('F j, Y');
                                    ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label text-muted">Account Status</div>
                                <div class="info-value">
                                    <i class="fas fa-shield-alt me-2 text-secondary"></i>
                                    <span class="badge bg-success p-2">Verified</span>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label text-muted">Member Since</div>
                                <div class="info-value">
                                    <i class="fas fa-clock me-2 text-secondary"></i>
                                    <?php 
                                        $register_date = new DateTime($teacher['date']);
                                        $today = new DateTime();
                                        $interval = $today->diff($register_date);
                                        echo $interval->y . ' years, ' . $interval->m . ' months';
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
             
            <!-- Action Buttons -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex justify-content-center gap-3">
                        <a href="edit_profile.php" class="btn btn-primary btn-sm px-4">
                            <i class="fas fa-edit me-2"></i> Edit Profile
                        </a>
                         
                         
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

 
</main>
</body>
</html>
