
<?php
session_start();
if (!isset($_SESSION["LoginAdmin"]) || !$_SESSION["LoginAdmin"]) {
    header('location:../login/login.php');
    exit;
}

require_once "../connection/connection.php";

$success_message = '';
$error_message = '';
if (isset($_POST['btn_add_sub'])) {
    $dept_id    = $_POST["dept_id"];
    $teacher_id = $_POST["teacher_id"];

    if (!empty($dept_id) && !empty($teacher_id)) {

        // 🔍 Check for duplicate assignment
        $check = $con->prepare("SELECT 1 FROM department_teachers_tb WHERE dept_id = ? AND teacher_id = ?");
        $check->bind_param("ii", $dept_id, $teacher_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            // Already exists
            $error_message = "This teacher is already assigned to the selected department.";
        } else {
            // Insert new record
            $stmt = $con->prepare("INSERT INTO department_teachers_tb (dept_id, teacher_id) VALUES (?, ?)");
            if ($stmt === false) {
                die("Prepare failed: " . $con->error);
            }

            $stmt->bind_param("ii", $dept_id, $teacher_id);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Teacher assigned to department successfully!";
                header("Location: teacher_departments.php");
                exit;
            } else {
                $error_message = "Error adding teacher to department: " . $stmt->error;
            }
            $stmt->close();
        }
        $check->close();

    } else {
        $error_message = "Please select both a department and a teacher.";
    }
}

?>

   
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Classes </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../class/class.css">
    <style>
     
    </style>
</head>
<body>
 
<?php include('../Main/Main-sidebar.php') ?>
<br><br><br>
<main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 w-100">
    <div class="container-fluid">
        <!-- Page Header -->
        

        <!-- Add Class Modal -->
        <div class="modal fade" id="addClassModal" tabindex="-1" role="dialog" aria-labelledby="addClassModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addClassModalLabel"><i class="fas fa-plus-circle mr-2"></i>create Department</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="teacher_departments.php">
                        <div class="modal-body">
                            <div class="form-group">
                                
                                     <label for="dept_id"><i class="fas fa-building mr-2"></i>Select Department</label>
                                <select class="form-control" id="dept_id" name="dept_id" required>
                                    <option value="">-- Select Department --</option>
                                    <?php
                                    $query = "SELECT * FROM dept ORDER BY dept_name";
                                    $result = mysqli_query($con, $query);
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<option value="' . $row['dept_id'] . '">' . ucwords($row['dept_name']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="teacherSelect"><i class="fas fa-user-tie mr-2"></i>Select Hod</label>
                                <select class="form-control" id="teacherSelect" name="teacher_id" required>
                                    <option value="">-- Select teacher --</option>
                                    <?php
                                    $result = mysqli_query($con, "SELECT * FROM teachers_tb   ORDER BY last_name");
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<option value="' . $row['teacher_id'] . '">' . ucwords($row['last_name']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                             
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" name="btn_add_sub" class="btn btn-primary">Save Class</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

     
<div class="col-lg-12">
    <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-list mr-2"></i>All Departments</span>
        </div>
        <div class="page-header d-flex justify-content-between align-items-center">
            <h4><i  ></i>Manage Department</h4>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addClassModal">
                <i class="fas fa-plus-circle mr-2"></i>Assign Departments
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="list" style="border: 2px solid #dee2e6;">
                    <thead>
                        <tr>
                            <th class="sortable" style="border: 1px solid #dee2e6;">Department</th>
                            <th class="sortable" style="border: 1px solid #dee2e6;">Teacher</th>
                       
                            <th class="sortable" style="border: 1px solid #dee2e6;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                   
                            $query = "SELECT dt.dept_id, d.dept_name, t.first_name AS teacher_first_name, t.last_name AS teacher_last_name
                                      FROM department_teachers_tb dt
                                      JOIN dept d ON dt.dept_id = d.dept_id
                                      JOIN teachers_tb t ON dt.teacher_id = t.teacher_id
                                      ORDER BY d.dept_name";
                        $result = mysqli_query($con, $query);
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                               
                                echo "<tr>";
                                echo "<td style='border: 1px solid #dee2e6;'><strong>" . ucwords($row['dept_name']) . "</strong></td>";
                                echo "<td style='border: 1px solid #dee2e6;'>" . ucwords($row['teacher_first_name'] . " " . $row['teacher_last_name']) . "</td>";
                               
                       
                                echo '<td style="border: 1px solid #dee2e6;">
                                        <div class="btn-group">
                                            <a class="btn btn-success btn-sm mr-1 action-btn" href="edit_department.php?dept_id='.$row['dept_id'].'" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-danger btn-sm action-btn deleteBtn" data-id="'.$row['dept_id'].'" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                      </td>';
                                echo "</tr>";
                            }
                        } else {
                            echo '<tr><td colspan="5" class="text-center" style="border: 1px solid #dee2e6;">No classes found.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
/* Make all table borders visible */
#list {
    border: 2px solid #dee2e6 !important;
    border-collapse: collapse !important;
}

#list th {
    border: 1px solid #dee2e6 !important;
    background-color: #f8f9fa;
    font-weight: 600;
    padding: 12px 8px;
}

#list td {
    border: 1px solid #dee2e6 !important;
    padding: 10px 8px;
    vertical-align: middle;
}

#list tr {
    border-bottom: 1px solid #dee2e6 !important;
}

#list thead tr {
    border-bottom: 2px solid #dee2e6 !important;
}

#list tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

/* Sortable header styling */
th.sortable {
    position: relative;
    cursor: pointer;
    padding-right: 20px !important;
}

th.sortable::after {
    content: "↕";
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 12px;
    color: #6c757d;
    opacity: 0.7;
    transition: all 0.2s;
}

th.sortable:hover::after {
    opacity: 1;
    color: #495057;
}

th.sortable.asc::after {
    content: "↑";
    color: #007bff;
    opacity: 1;
    font-weight: bold;
}

th.sortable.desc::after {
    content: "↓";
    color: #007bff;
    opacity: 1;
    font-weight: bold;
}

/* Button group styling */
.btn-group .btn {
    margin-right: 2px;
    border-radius: 4px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

/* Action buttons */
.action-btn {
    padding: 4px 8px;
    font-size: 12px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sorting functionality
    const sortableHeaders = document.querySelectorAll('th.sortable');
    let currentSortColumn = null;
    let currentSortDirection = 'asc';
    const rows = Array.from(document.querySelectorAll('#list tbody tr'));
    
    sortableHeaders.forEach((header, index) => {
        header.addEventListener('click', function() {
            // Update sort direction
            if (currentSortColumn === index) {
                currentSortDirection = currentSortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                currentSortColumn = index;
                currentSortDirection = 'asc';
            }
            
            // Update visual indicators
            sortableHeaders.forEach(h => {
                h.classList.remove('asc', 'desc');
            });
            this.classList.add(currentSortDirection);
            
            // Sort rows
            const tbody = document.querySelector('#list tbody');
            rows.sort((a, b) => {
                const aText = a.children[index].textContent.trim().toLowerCase();
                const bText = b.children[index].textContent.trim().toLowerCase();
                
                // Special handling for dates (Date Added column)
                if (index === 3) {
                    const aDate = new Date(a.children[index].textContent.trim());
                    const bDate = new Date(b.children[index].textContent.trim());
                    return currentSortDirection === 'asc' ? aDate - bDate : bDate - aDate;
                }
                
                // String comparison for other columns
                if (currentSortDirection === 'asc') {
                    return aText.localeCompare(bText);
                } else {
                    return bText.localeCompare(aText);
                }
            });
            
            // Reorder rows
            rows.forEach(row => tbody.appendChild(row));
        });
    });
    
    // Delete button functionality
    document.querySelectorAll('.deleteBtn').forEach(btn => {
        btn.addEventListener('click', function() {
            const classId = this.getAttribute('data-id');
            if (confirm('Are you sure you want to delete this class? This action cannot be undone.')) {
                window.location.href = "delete_class.php?class_id=" + classId;
            }
        });
    });
});
</script>
</main>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<script>
$(document).ready(function(){
    // Initialize DataTable
    var table = $('#classTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        responsive: true,
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        dom: '<"row"<"col-md-6"B><"col-md-6"f>>rt<"row"<"col-md-6"l><"col-md-6"p>>',
        buttons: [
            {
                extend: 'copy',
                className: 'btn btn-sm btn-outline-primary',
                text: '<i class="fas fa-copy mr-1"></i>Copy'
            },
            {
                extend: 'excel',
                className: 'btn btn-sm btn-success',
                text: '<i class="fas fa-file-excel mr-1"></i>Excel'
            },
            {
                extend: 'pdf',
                className: 'btn btn-sm btn-danger',
                text: '<i class="fas fa-file-pdf mr-1"></i>PDF'
            },
            {
                extend: 'print',
                className: 'btn btn-sm btn-info',
                text: '<i class="fas fa-print mr-1"></i>Print'
            }
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search classes...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                previous: "<i class='fas fa-chevron-left'></i>",
                next: "<i class='fas fa-chevron-right'></i>"
            }
        }
    });
    
    // Delete confirmation
    $(document).on('click', '.deleteBtn', function(){
        var id = $(this).data('id');
        swal({
            title: "Are you sure?",
            text: "This class and all associated data will be permanently deleted!",
            icon: "warning",
            buttons: {
                cancel: {
                    text: "Cancel",
                    value: null,
                    visible: true,
                    className: "btn-secondary",
                    closeModal: true,
                },
                confirm: {
                    text: "Yes, delete it!",
                    value: true,
                    visible: true,
                    className: "btn-danger",
                    closeModal: false
                }
            },
            dangerMode: true
        }).then((willDelete) => {
            if (willDelete) {
                window.location.href = "delete_class.php?class_id=" + id;
            }
        });
    });

    // Show success/error messages from PHP
    <?php if(isset($_SESSION['success_message'])): ?>
        swal("Success!", "<?= $_SESSION['success_message']; ?>", "success");
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if(isset($_SESSION['error_message'])): ?>
        swal("Error!", "<?= $_SESSION['error_message']; ?>", "error");
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    
    // Enhance modal closing with animation
    $('.modal').on('show.bs.modal', function () {
        $(this).find('.modal-content').css({
            'transform': 'scale(0.9)',
            'transition': 'transform 0.3s ease'
        });
    });
    
    $('.modal').on('shown.bs.modal', function () {
        $(this).find('.modal-content').css('transform', 'scale(1)');
    });
});
</script>
</body>
</html>