<?php
session_start();
if (!isset($_SESSION["LoginAdmin"])) {
    header('location:../login/login.php');
    exit;
}

require_once "../connection/connection.php";

// -----------------------------
// Handle Add Teacher
// -----------------------------
if (isset($_POST['btn_save'])) {
    $first_name  = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name   = trim($_POST['last_name']);
    $email       = trim($_POST['email']);
    $tcz_number  = trim($_POST['tcz_number']);
    $gender      = $_POST['gender'];
    $role        = $_POST['role'];

    // ✅ Hash the password with bcrypt
    $password    = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $teacher_id  = date("Y") . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Invalid email format!";
        header("Location: instructor.php");
        exit;
    }

    // Check if email already exists
    $check = $con->prepare("SELECT 1 FROM teachers_tb WHERE email = ? LIMIT 1");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        $_SESSION['error_message'] = "Email already exists!";
        $check->close();
        header("Location: instructor.php");
        exit;
    }
    $check->close();

    // Insert into teachers table
    $stmt = $con->prepare("INSERT INTO teachers_tb (teacher_id, first_name, middle_name, last_name, email, Teaching_number, gender, role) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $teacher_id, $first_name, $middle_name, $last_name, $email, $tcz_number, $gender, $role);
    $stmt->execute();
    $stmt->close();

    // Insert into login table with hashed password (bcrypt ~60 chars)
    $stmt2 = $con->prepare("INSERT INTO login (user_id, Password, Role, account, failed_attempts) 
                            VALUES (?, ?, ?, 'Activate', 0)");
    $stmt2->bind_param("sss", $email, $password, $role);
    $stmt2->execute();
    $stmt2->close();

    $_SESSION['success_message'] = "Teacher added successfully!";
    header("Location: insert_admin.php");
    exit;
}

// -----------------------------
// Handle Delete Teacher
// -----------------------------
if (isset($_GET['delete'])) {
    $teacher_id = $_GET['delete'];

    // Get teacher email first
    $stmt = $con->prepare("SELECT email FROM teachers_tb WHERE teacher_id=?");
    $stmt->bind_param("s", $teacher_id);
    $stmt->execute();
    $stmt->bind_result($teacher_email);
    $stmt->fetch();
    $stmt->close();

    if ($teacher_email) {
        // Delete teacher record
        $stmt = $con->prepare("DELETE FROM teachers_tb WHERE teacher_id=?");
        $stmt->bind_param("s", $teacher_id);
        if ($stmt->execute()) {
            $stmt->close();

            // Delete login by email (hashed password stays secure)
            $stmt2 = $con->prepare("DELETE FROM login WHERE user_id=?");
            $stmt2->bind_param("s", $teacher_email);
            $stmt2->execute();
            $stmt2->close();

            $_SESSION['success_message'] = "Teacher deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Error deleting teacher: " . $stmt->error;
        }
    } else {
        $_SESSION['error_message'] = "Teacher not found!";
    }

    header("Location: insert_admin.php");
    exit;
}
 
$admins = $con->query("SELECT * FROM teachers_tb WHERE role = 'Admin' ORDER BY teacher_id ASC");
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Teachers</title>
<link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
    background-color: #f8f9fa;
    color: #333;
}

.page-header {
    background: white;
    border-radius: 10px;
    padding: 20px;
    margin: 20px 0;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}

.page-title {
    font-size: 1.8rem;
    font-weight: 600;
    color: var(--dark);
    margin: 0;
}

.card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.card-header {
    background: white;
    border-bottom: 1px solid #eee;
    font-weight: 600;
    padding: 15px 20px;
    border-radius: 10px 10px 0 0 !important;
}

.btn-primary {
    background-color: var(--primary);
    border-color: var(--primary);
}

.btn-primary:hover {
    background-color: #3a5fc9;
    border-color: #3a5fc9;
}

.table th {
    background-color: var(--light);
    color: var(--dark);
    font-weight: 600;
}

.action-buttons .btn {
    padding: 4px 8px;
    margin-right: 5px;
}

.dataTables_filter input {
    border-radius: 4px;
    border: 1px solid #ced4da;
    padding: 5px 10px;
}

.dt-buttons .btn {
    margin-right: 5px;
    margin-bottom: 5px;
}

.badge-success {
    background-color: var(--success);
}

.modal-header {
    background-color: var(--primary);
    color: white;
}

.profile-img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #eee;
}

@media (max-width: 768px) {
    .page-title {
        font-size: 1.5rem;
    }
    
    .dt-buttons {
        text-align: center;
        margin-bottom: 10px;
    }
    
    .dt-buttons .btn {
        width: 100%;
        margin-bottom: 5px;
    }
}

/* Custom styling for DataTables */
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate {
    color: #6c757d;
    padding: 10px 0;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 0.3em 0.8em;
    margin-left: 2px;
    border: 1px solid transparent;
    border-radius: 4px;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: var(--primary);
    color: white !important;
    border: 1px solid var(--primary);
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: var(--secondary);
    color: white !important;
    border: 1px solid var(--secondary);
}

/* SweetAlert customization */
.swal2-popup {
    border-radius: 10px;
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
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-header d-flex justify-content-between align-items-center">
            <h1 class="page-title"><i class="fas fa-chalkboard-teacher me-2"></i>Manage Admin</h1>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addTeacherModal">
                <i class="fas fa-plus me-2"></i>Add Admin
            </button>
        </div>

        <!-- Teachers Table Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>All Admin Records</span>
                 
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="teachersTable" class="table table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Last Name</th>
                                <th>Gender</th>
                                <th>Email</th>
                                <th>TCZ Number</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
        <?php if ($admins && $admins->num_rows > 0): ?>
            <?php while ($row = $admins->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['teacher_id']; ?></td>
                    <td><?= $row['first_name']; ?></td>
                    <td><?= $row['middle_name']; ?></td>
                    <td><?= $row['last_name']; ?></td>
                    <td><?= $row['gender']; ?></td>
                    <td><?= $row['email']; ?></td>
                    <td><?= $row['Teaching_number']; ?></td>
                    <td><?= $row['role']; ?></td>
                    <td>
                        <a class="btn btn-success btn-sm" href="edit-admin.php?teacher_id=<?= $row['teacher_id']; ?>"><i class="fas fa-edit"></i></a>
                        <button class="btn btn-danger btn-sm deleteBtn" data-id="<?= $row['teacher_id']; ?>"><i class="fas fa-trash-alt"></i></button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="9" class="text-center">No Admin found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>


    <!-- Add Teacher Modal -->
    <div class="modal fade" id="addTeacherModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Add Admin</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="addTeacherForm">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>First Name</label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Middle Name</label>
                                <input type="text" name="middle_name" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Last Name</label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>
                                 <div class="col-md-4 mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" required 
                                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                    title="Please enter a valid email address">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label>TCZ Number</label>
                                <input type="text" name="tcz_number" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Gender</label>
                                <select name="gender" class="form-control" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Role</label>
                                <select name="role" class="form-control" required>
                                    <option value="Admin">Admin</option>
                                </select>
                            </div>
                            <input type="hidden" name="password" value="teacher123*">
                        </div>
                        <div class="modal-footer mt-3">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" name="btn_save" class="btn btn-primary">Save Admin</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- JS Libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>


    // Search input
    $('#searchInput').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Delete Teacher
    $('.deleteBtn').click(function() {
        var teacherId = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This teacher will be deleted permanently!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "instructor.php?id=" + teacherId;
            }
        });
    });

    // Success messages from PHP
    <?php if(isset($_SESSION['success_message'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '<?= $_SESSION['success_message']; ?>',
            timer: 3000
        });
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    // Form validation
    $('#addTeacherForm').on('submit', function(e) {
        var valid = true;
        $(this).find('input[required], select[required]').each(function() {
            if ($(this).val() === '') {
                valid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!valid) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Please fill all required fields.'
            });
        }
    });
</script>
</body>
</html>
 