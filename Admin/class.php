<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["LoginAdmin"]) || !$_SESSION["LoginAdmin"]) {
    header('location: ../login/login.php');
    exit;
}

require_once "../connection/connection.php";

if (isset($_POST['btn_add_sub'])) {
    $class_name = trim($_POST["class_name"]);
    $stream_id = (int) $_POST["stream_id"];
    $level = $_POST["level"];

    // Basic validation
    if (empty($class_name) || empty($stream_id) || empty($level)) {
        $_SESSION['error_message'] = "Please fill in all fields.";
        header("Location: class.php");
        exit;
    }

    // Optional: check for duplicate class in the same stream
    $check = $con->prepare("SELECT class_id FROM classes WHERE class_name = ? AND stream_id = ?");
    $check->bind_param("si", $class_name, $stream_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $_SESSION['error_message'] = "This class already exists in the selected stream.";
    } else {
        $stmt = $con->prepare("INSERT INTO classes (class_name, stream_id, level) VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sis", $class_name, $stream_id, $level);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Class added successfully!";
            } else {
                $_SESSION['error_message'] = "Error adding class: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['error_message'] = "Prepare failed: " . $con->error;
        }
    }

    $check->close();
    header("Location: class.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Classes - EduTrack</title>
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
                        <h5 class="modal-title" id="addClassModalLabel"><i class="fas fa-plus-circle mr-2"></i>Add New Class</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="class.php">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="className"><i class="fas fa-chalkboard mr-2"></i>Class Name</label>
                                <input type="text" class="form-control" id="className" name="class_name" required placeholder="Enter class name">
                            </div>
                            <div class="form-group">
                                <label for="teacherSelect"><i class="fas fa-user-tie mr-2"></i>Select stream</label>
                                <select class="form-control" id="select_stream" name="stream_id" required>
                                    <option value="">-- Select Stream --</option>
                                    <?php
                                    $result = mysqli_query($con, "SELECT * FROM streams  ORDER BY stream_name");
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<option value="' . $row['stream_id'] . '">' . ucwords($row['stream_name']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="Level"><i class="fas fa-check-circle mr-2"></i>Level</label>
                                <select class="form-control" id="level" name="level" required>
                                    <option value="">-- Select level --</option>
                                    <option value="Primary">primary</option>
                                    <option value="Junior">Junior</option>
                                    <option value="Senior">Senior</option>
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
            <span><i class="fas fa-list mr-2"></i>All Classes</span>
        </div>
        <div class="page-header d-flex justify-content-between align-items-center">
            <h4><i  ></i>Manage Classes</h4>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addClassModal">
                <i class="fas fa-plus-circle mr-2"></i>Add New Class
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="list" style="border: 2px solid #dee2e6;">
                    <thead>
                        <tr>
                            <th class="sortable" style="border: 1px solid #dee2e6;" >Class Name</th>
                            <th class="sortable" style="border: 1px solid #dee2e6;" >Stream</th>
                            <th class="sortable" style="border: 1px solid #dee2e6;" >Level</th>
                             
                            <th class="sortable" style="border: 1px solid #dee2e6;" >Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                       $query = "SELECT c.class_id, c.class_name, c.date_added, c.level, 
                                t.stream_name AS stream_name
                        FROM classes c 
                        JOIN streams t ON c.stream_id = t.stream_id
                        ORDER BY c.date_added DESC";

                        $result = mysqli_query($con, $query);
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $statusClass = ($row['level'] == 'Active') ? 'status-active' : 'status-inactive';
                                echo "<tr>";
                                echo "<td style='border: 1px solid #dee2e6;'><strong>" . ucwords($row['class_name']) . "</strong></td>";
                                echo "<td style='border: 1px solid #dee2e6;'>" . ucwords($row['stream_name']) . "</td>";
                                echo "<td style='border: 1px solid #dee2e6;'>" . ucwords($row['level']) . "</td>";
                       
                                echo '<td style="border: 1px solid #dee2e6;">
                                        <div class="btn-group">
                                            <a class="btn btn-success btn-sm mr-1 action-btn" href="edit_class.php?class_id='.$row['class_id'].'" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-danger btn-sm action-btn deleteBtn" data-id="'.$row['class_id'].'" title="Delete">
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