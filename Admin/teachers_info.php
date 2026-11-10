
<?php 
    ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    session_start();
 if (!$_SESSION["LoginAdmin"]) 
    { header('location:../login/login.php');
         exit; } 
         require_once "../connection/connection.php"; // ----------------------------- // Fetch all teachers // ----------------------------- 
  



// -----------------------------
// Fetch all teachers
// -----------------------------
$teachers = $con->query("SELECT * FROM teachers_tb ORDER BY teacher_id ASC");
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Teachers </title>
<link rel="stylesheet" href="../css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
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
</style>
</head>
<body>
 
<?php include('../Main/Main-sidebar.php'); ?>

<main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 w-100">
    <div class="container-fluid py-4">
       
        <!-- Teachers Table Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>All Teachers Records</span>
                 
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
                                 
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($teachers && $teachers->num_rows > 0): ?>
                            <?php while ($row = $teachers->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['teacher_id']; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                             
                                            <?= $row['first_name']; ?>
                                        </div>
                                    </td>
                                    <td><?= $row['middle_name']; ?></td>
                                    <td><?= $row['last_name']; ?></td>
                                    <td>
                                         
                                            <?= $row['gender']; ?>
                                        </span>
                                    </td>
                                    <td><?= $row['email']; ?></td>
                                    <td><?= $row['Teaching_number']; ?></td>
                                    <td>
                                        <?= $row['role']; ?>
                                    </td>
                                     
                                     
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-users fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">No teachers found.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
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
$(document).ready(function(){
    // Initialize DataTable with enhanced features
    var table = $('#teachersTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        responsive: true,
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"p>>',
        buttons: [
            {
                extend: 'copy',
                className: 'btn btn-sm btn-secondary',
                text: '<i class="fas fa-copy me-1"></i>Copy'
            },
            {
                extend: 'excel',
                className: 'btn btn-sm btn-success',
                text: '<i class="fas fa-file-excel me-1"></i>Excel'
            },
            {
                extend: 'pdf',
                className: 'btn btn-sm btn-danger',
                text: '<i class="fas fa-file-pdf me-1"></i>PDF'
            },
            {
                extend: 'print',
                className: 'btn btn-sm btn-primary',
                text: '<i class="fas fa-print me-1"></i>Print'
            }
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search teachers...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                previous: '<i class="fas fa-chevron-left"></i>',
                next: '<i class="fas fa-chevron-right"></i>'
            }
        }
    });

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
                window.location.href = "delete_teacher.php?id=" + teacherId;
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
});
</script>
</body>
</html>