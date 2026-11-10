<?php
session_start();
require_once "../connection/connection.php";

// Check if teacher is logged in
if (!isset($_SESSION['LoginTeacher'])) {
    header('Location: ../Login/login.php');
    exit();
}

$user_id = $_SESSION['LoginTeacher']; // email

// Fetch current teacher data
$query = "SELECT * FROM teachers_tb WHERE email = ? LIMIT 1";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "s", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    die("Teacher record not found.");
}

$teacher = mysqli_fetch_assoc($result);
$teacher_id = $teacher['teacher_id'];

// Handle form submission
$success_message = "";
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input
    $first_name = mysqli_real_escape_string($con, $_POST['first_name']);
    $middle_name = mysqli_real_escape_string($con, $_POST['middle_name']);
    $last_name = mysqli_real_escape_string($con, $_POST['last_name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $Teaching_number = mysqli_real_escape_string($con, $_POST['Teaching_number']);
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        // Check if email already exists (excluding current teacher)
        $check_email_query = "SELECT teacher_id FROM teachers_tb WHERE email = ? AND teacher_id != ?";
        $check_stmt = mysqli_prepare($con, $check_email_query);
        mysqli_stmt_bind_param($check_stmt, "si", $email, $teacher_id);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);
        
        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $error_message = "Email already exists. Please use a different email.";
        } else {
            // Update query
            $update_query = "UPDATE teachers_tb SET 
                            first_name = ?, 
                            middle_name = ?, 
                            last_name = ?, 
                            email = ?, 
                            Teaching_number = ?, 
                            gender = ? 
                            WHERE teacher_id = ?";
            
            $update_stmt = mysqli_prepare($con, $update_query);
            mysqli_stmt_bind_param($update_stmt, "ssssssi", 
                $first_name, 
                $middle_name, 
                $last_name, 
                $email, 
                $Teaching_number, 
                $gender, 
                $teacher_id
            );
            
            if (mysqli_stmt_execute($update_stmt)) {
                $success_message = "Profile updated successfully!";
                
                // Update session email if email was changed
                if ($email != $user_id) {
                    $_SESSION['LoginTeacher'] = $email;
                }
                
                // Refresh teacher data
                $query = "SELECT * FROM teachers_tb WHERE teacher_id = ? LIMIT 1";
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "i", $teacher_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $teacher = mysqli_fetch_assoc($result);
            } else {
                $error_message = "Error updating profile: " . mysqli_error($con);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/profile.css">
</head>
<body>
<?php include('../Main/Teacher-sidebar.php'); ?>
    
    
    <main class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 w-100">
        <div class="profile-container">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="profile-breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="Teacher-index.php"><i class="fas fa-home me-1"></i> Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="instructor.php"><i class="fas fa-user me-1"></i> Profile</a></li>
                    <li class="breadcrumb-item active"><i class="fas fa-edit me-1"></i> Edit Profile</li>
                </ol>
            </nav>
            
            <!-- Header -->
            
            <!-- Messages -->
            <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo htmlspecialchars($success_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <!-- Edit Form -->
            <div class="profile-content">
                <form action="" method="POST" id="editProfileForm">
                    <!-- Personal Information Section -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-user-edit me-2"></i> Personal Information
                        </h3>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="first_name" class="form-label required">First Name</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="first_name" 
                                       name="first_name" 
                                       value="<?php echo htmlspecialchars($teacher['first_name']); ?>" 
                                       required>
                                <div class="info-hint">Your given name</div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="middle_name" class="form-label">Middle Name</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="middle_name" 
                                       name="middle_name" 
                                       value="<?php echo htmlspecialchars($teacher['middle_name'] ?? ''); ?>">
                                <div class="info-hint">Optional</div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="last_name" class="form-label required">Last Name</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="last_name" 
                                       name="last_name" 
                                       value="<?php echo htmlspecialchars($teacher['last_name']); ?>" 
                                       required>
                                <div class="info-hint">Your family name</div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label required">Gender</label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male" <?php echo ($teacher['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo ($teacher['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                 
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="Teaching_number" class="form-label required">Teaching Certificate Number</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    <input type="text" 
                                           class="form-control" 
                                           id="Teaching_number" 
                                           name="Teaching_number" 
                                           value="<?php echo htmlspecialchars($teacher['Teaching_number']); ?>" 
                                           required>
                                </div>
                                <div class="info-hint">Your official teaching certificate number</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Information Section -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-envelope me-2"></i> Contact Information
                        </h3>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="email" class="form-label required">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-at"></i></span>
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           name="email" 
                                           value="<?php echo htmlspecialchars($teacher['email']); ?>" 
                                           required
                                           pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                                </div>
                                <div class="info-hint">This will also be your login username</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Read-only Information -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-info-circle me-2"></i> System Information
                        </h3>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Teacher ID</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-fingerprint"></i></span>
                                    <input type="text" 
                                           class="form-control bg-light" 
                                           value="<?php echo htmlspecialchars($teacher['teacher_id']); ?>" 
                                           readonly>
                                </div>
                                <div class="info-hint">Your unique system identifier</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Registration Date</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="text" 
                                           class="form-control bg-light" 
                                           value="<?php 
                                                $date = new DateTime($teacher['date']);
                                                echo $date->format('F j, Y');
                                           ?>" 
                                           readonly>
                                </div>
                                <div class="info-hint">Date you joined the system</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between align-items-center mt-5 pt-4 border-top">
                        <div>
                            <a href="Teacher-index.php" class="btn btn-outline-secondary btn-action">
                                <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                            </a>
                        </div>
                        <div class="d-flex">
                            <button type="reset" class="btn btn-outline-secondary btn-action">
                                <i class="fas fa-undo me-2"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary btn-action">
                                <i class="fas fa-save me-2"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        document.getElementById('editProfileForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const firstName = document.getElementById('first_name').value;
            const lastName = document.getElementById('last_name').value;
            const teachingNumber = document.getElementById('Teaching_number').value;
            const gender = document.getElementById('gender').value;
            
            // Basic validation
            if (!firstName.trim() || !lastName.trim() || !email.trim() || !teachingNumber.trim() || !gender) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }
            
            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return false;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Saving...';
            submitBtn.disabled = true;
            
            return true;
        });
        
        // Reset form confirmation
        document.querySelector('button[type="reset"]').addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to reset all changes?')) {
                e.preventDefault();
            }
        });
        
        // Character counters
        document.getElementById('first_name').addEventListener('input', function() {
            const maxLength = 50;
            const currentLength = this.value.length;
            if (currentLength > maxLength) {
                this.value = this.value.substring(0, maxLength);
            }
        });
        
        // Toast notification for success
        <?php if ($success_message): ?>
            setTimeout(() => {
                const toast = document.createElement('div');
                toast.className = 'position-fixed top-0 end-0 p-3';
                toast.style.zIndex = '11';
                toast.innerHTML = `
                    <div class="toast show" role="alert">
                        <div class="toast-header bg-success text-white">
                            <strong class="me-auto"><i class="fas fa-check-circle me-2"></i> Success</strong>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                        </div>
                        <div class="toast-body">
                            <?php echo addslashes($success_message); ?>
                        </div>
                    </div>
                `;
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    toast.remove();
                }, 3000);
            }, 500);
        <?php endif; ?>
    </script>
</body>
</html>