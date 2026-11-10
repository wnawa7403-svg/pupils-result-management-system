
<?php
// subjects.php - View/Add/Edit/Delete subjects
session_start();
require_once "../connection/connection.php";

$message = '';
$subject_name = $abbreviation = '';

// -------------------- HANDLE INSERTION --------------------
if (isset($_POST['btn_add_subject'])) {

    $subject_name = trim($_POST['subject_name']);
    $abbreviation = strtoupper(trim($_POST['abbreviation']));

    if (empty($subject_name) || empty($abbreviation)) {

        $message = ['type' => 'danger', 'text' => 'All fields are required.'];

    } else {

        // Check if subject exists
        $check = $con->prepare(
            "SELECT subject_id FROM subjects WHERE subject_name = ? OR abbreviation = ?"
        );
        $check->bind_param("ss", $subject_name, $abbreviation);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {

            $message = ['type' => 'warning', 'text' => 'Subject or abbreviation already exists.'];

        } else {

            // Insert subject
            $stmt = $con->prepare(
                "INSERT INTO subjects (subject_name, abbreviation) VALUES (?, ?)"
            );
            $stmt->bind_param("ss", $subject_name, $abbreviation);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Subject added successfully!";
                header("Location: add_subjects.php");
                exit;
            } else {
                $message = ['type' => 'danger', 'text' => 'Error adding subject.'];
            }
        }
    }
}

 

?>
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage SUBJECTS</title>
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
                        <h5 class="modal-title" id="addClassModalLabel"><i class="fas fa-plus-circle mr-2"></i>Add Subject</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="add_subjects.php">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="subject_name"><i class="fas fa-chalkboard mr-2"></i>Subject Name</label>
                                <input type="text" class="form-control" id="subject_name" name="subject_name" required placeholder="Enter subject name">
                            </div>
                            <div class="form-group">
                              <label for="abbreviation"><i class="fas fa-chalkboard mr-2"></i>Abbreviation</label>
                                <input type="text" class="form-control" id="abbreviation" name="abbreviation" required placeholder="Abbreviation">   
                            </div>
                             
                            </div>
                         
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" name="btn_add_subject" class="btn btn-primary">Save Subject</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

     
<div class="col-lg-12">
    <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-list mr-2"></i>All Subject</span>
        </div>
        <div class="page-header d-flex justify-content-between align-items-center">
            <h4><i  ></i>Manage Subjects</h4>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addClassModal">
                <i class="fas fa-plus-circle mr-2"></i>Add New Subject
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="list" style="border: 2px solid #dee2e6;">
                    <thead>
                        <tr>
                            <th class="sortable" style="border: 1px solid #dee2e6;" >subject Name</th>
                            <th class="sortable" style="border: 1px solid #dee2e6;" >Abbreviation</th>
                            <th class="sortable" style="border: 1px solid #dee2e6;" >Date</th>
                             
                            <th class="sortable" style="border: 1px solid #dee2e6;" >Actions</th>
                        </tr>
                    </thead>
                    <tbody>
					<?php
													   $query = "SELECT * FROM subjects ORDER BY subject_name ASC";
								$result = mysqli_query($con, $query);

								if ($result && mysqli_num_rows($result) > 0) {
									while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td style='border: 1px solid #dee2e6;'><strong>" . ucwords($row['subject_name']) . "</strong></td>";
								echo "<td style='border: 1px solid #dee2e6;'>" . strtoupper($row['abbreviation']) . "</td>";
								echo "<td style='border: 1px solid #dee2e6;'>" . date('M d, Y', strtotime($row['date_added'])) . "</td>";

                                echo '<td style="border: 1px solid #dee2e6;">
                                        <div class="btn-group">
                                            <a class="btn btn-success btn-sm mr-1 action-btn" href="edit_subject.php?subject_id='.$row['subject_id'].'" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
									         <a class="btn btn-danger btn-sm mr-1 action-btn" href="delete_subject.php?subject_id='.$row['subject_id'].'" title="Edit">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
									         

                                            
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
<script src="../assets/js/streams.js"></script>
<script>
$ 
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
 
</script>
</body>
</html>