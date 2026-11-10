<?php
session_start();
if (!isset($_SESSION["LoginAdmin"])) {
    header('location:../login/login.php');
    exit;
}

require_once "../connection/connection.php";

// Get teacher_id from URL
if (!isset($_GET['teacher_id'])) {
    echo "Invalid request!";
    exit;
}
$teacher_id = $_GET['teacher_id'];

// Fetch teacher data securely
$stmt = $con->prepare("SELECT * FROM teachers_tb WHERE teacher_id=?");
$stmt->bind_param("s", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();

if (!$teacher) {
    echo "Teacher not found!";
    exit;
}

// Handle update
if (isset($_POST['update_teacher'])) {
    $teacher_id  = $_POST['teacher_id'] ?? $_GET['teacher_id'];
    $first_name  = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name   = $_POST['last_name'];
    $email       = $_POST['email'];
    $tcz_number  = $_POST['tcz_number'];
    $gender      = $_POST['gender'];
    $role        = $_POST['role'];

    // Fetch old email securely
    $stmt_old = $con->prepare("SELECT email FROM teachers_tb WHERE teacher_id=?");
    $stmt_old->bind_param("s", $teacher_id);
    $stmt_old->execute();
    $old_email = $stmt_old->get_result()->fetch_assoc()['email'];

    // Update teacher
    $stmt = $con->prepare("UPDATE teachers_tb 
        SET first_name=?, middle_name=?, last_name=?, email=?, Teaching_number=?, gender=?, role=? 
        WHERE teacher_id=?");
    $stmt->bind_param("ssssssss", $first_name, $middle_name, $last_name, $email, $tcz_number, $gender, $role, $teacher_id);
    $stmt->execute();

    // Update login table
    $stmt2 = $con->prepare("UPDATE login SET user_id=?, Role=? WHERE user_id=?");
    $stmt2->bind_param("sss", $email, $role, $old_email);
    $stmt2->execute();

    $_SESSION['success_message'] = "Teacher updated successfully!";
    header("Location: insert_admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Teacher - EduTrack</title>
<link rel="stylesheet" href="../css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root {
    --primary: #4361ee;
    --secondary: #3f37c9;
    --accent: #4895ef;
    --success: #4cc9f0;
    --warning: #f72585;
    --danger: #e63946;
    --dark: #2c3e50;
    --light: #f8f9fa;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f5f7fa;
    color: #333;
}

main[role="main"] {
    padding: 100px 20px 20px 270px;
    transition: all 0.3s;
    min-height: 100vh;
}

.card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    margin-bottom: 20px;
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    color: white;
    padding: 20px 25px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.card-header h4 {
    margin: 0;
    font-weight: 600;
    display: flex;
    align-items: center;
}

.card-header h4 i {
    margin-right: 10px;
}

.card-body {
    padding: 30px;
    background: white;
}

.form-group {
    margin-bottom: 20px;
}

label {
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 8px;
    display: block;
}

.form-control {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 12px 15px;
    transition: all 0.3s;
    height: 45px;
}

.form-control:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(72, 149, 239, 0.1);
}

.btn {
    border-radius: 8px;
    padding: 12px 25px;
    font-weight: 600;
    transition: all 0.3s;
    border: none;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    box-shadow: 0 4px 6px rgba(67, 97, 238, 0.2);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(67, 97, 238, 0.3);
}

.btn-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
    box-shadow: 0 4px 6px rgba(108, 117, 125, 0.2);
}

.btn-secondary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(108, 117, 125, 0.3);
}

/* Responsive adjustments */
@media (max-width: 1200px) {
    main[role="main"] {
        padding-left: 20px;
        padding-top: 120px;
    }
}

@media (max-width: 768px) {
    .card-body {
        padding: 20px;
    }
    
    .btn {
        width: 100%;
        margin-bottom: 10px;
    }
    
    main[role="main"] {
        padding: 100px 15px 15px 15px;
    }
}

/* Animation for form elements */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.form-group {
    animation: fadeIn 0.5s ease-out;
}

.form-group:nth-child(1) { animation-delay: 0.1s; }
.form-group:nth-child(2) { animation-delay: 0.2s; }
.form-group:nth-child(3) { animation-delay: 0.3s; }
.form-group:nth-child(4) { animation-delay: 0.4s; }
.form-group:nth-child(5) { animation-delay: 0.5s; }
.form-group:nth-child(6) { animation-delay: 0.6s; }
.form-group:nth-child(7) { animation-delay: 0.7s; }

/* Custom select styling */
select.form-control {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%234361ee' viewBox='0 0 16 16'%3E%3Cpath d='M8 12L2 6h12L8 12z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 15px center;
    background-size: 16px;
    padding-right: 45px;
}

/* Focus states for accessibility */
.form-control:focus,
.btn:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(72, 149, 239, 0.3);
}

/* Button group spacing */
.mt-4 .btn {
    margin-right: 10px;
}

/* Success message styling */
.alert-success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border: none;
    border-radius: 8px;
    color: #155724;
    border-left: 4px solid #28a745;
}

/* Error message styling */
.alert-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    border: none;
    border-radius: 8px;
    color: #721c24;
    border-left: 4px solid #dc3545;
}
    /* Fix Bootstrap dropdown arrows in sidebar */
.sidebar .dropdown-toggle::after {
    display: none !important;
}

.sidebar .dropdown-container {
    border-left: none !important;
}
</style>
</head>
<body>
 
<?php include('../Main/Main-sidebar.php'); ?>

<main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 w-100">
  <div class="container mt-5 mb-5">
        <div class="card card-outline rounded-0 w-100">
            <div class="card-header">
                <h4><i class="fas fa-user-edit me-2"></i>Edit Teacher</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="teacher_id" value="<?= htmlspecialchars($teacher['teacher_id']); ?>">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>First Name</label>
                            <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($teacher['first_name']); ?>" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Middle Name</label>
                            <input type="text" name="middle_name" class="form-control" value="<?= htmlspecialchars($teacher['middle_name']); ?>">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($teacher['last_name']); ?>" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($teacher['email']); ?>" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>TCZ Number</label>
                            <input type="text" name="tcz_number" class="form-control" value="<?= htmlspecialchars($teacher['Teaching_number']); ?>" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Gender</label>
                            <select name="gender" class="form-control" required>
                                <option value="Male" <?= $teacher['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?= $teacher['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Role</label>
                            <select name="role" class="form-control" required>
                               
                                <option value="Admin" <?= $teacher['role'] == 'Admin' ? 'selected' : ''; ?>>Admin</option>
                               
                            </select>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" name="update_teacher" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Teacher
                        </button>
                        <a href="instructor.php" class="btn btn-secondary text-white">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
  </div>
</main>

<script src="../Js/jquery.min.js"></script>
<script src="../Js/bootstrap.bundle.min.js"></script>
<script>
// Add some interactive form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input[required], select[required]');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    });
    
    form.addEventListener('submit', function(e) {
        let valid = true;
        inputs.forEach(input => {
            if (!input.value) {
                input.classList.add('is-invalid');
                valid = false;
            }
        });
        
        if (!valid) {
            e.preventDefault();
            // You could add a sweetalert notification here
        }
    });
});

// Add animation to form elements
const formGroups = document.querySelectorAll('.form-group');
formGroups.forEach((group, index) => {
    group.style.animationDelay = `${index * 0.1}s`;
});
</script>
</body>
</html>
