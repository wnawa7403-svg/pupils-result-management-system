<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION["LoginAdmin"]) || !$_SESSION["LoginAdmin"]) {
    header('location: ../login/login.php');
    exit;
}

require_once '../connection/connection.php';

// Initialize variables
$message = [];
if(isset($_POST['btn_assign_teacher'])) {
    $teacher_id = (int)$_POST['teacher_id'];
    $subject_form_id = (int)$_POST['subject_form_id']; 
    $academic_year = $_POST['academic_year'] ?? date('Y') . '-' . (date('Y') + 1);

    // Validate
    if(empty($teacher_id) || empty($subject_form_id)) {
        $_SESSION['error_message'] = 'Please select all fields.';
    } else {
        // Get the stream of this subject_form
        $stream_result = $con->query("
            SELECT c.stream_id 
            FROM subject_forms sf 
            JOIN classes c ON sf.class_id = c.class_id 
            WHERE sf.id = $subject_form_id
        ");
        $stream_row = $stream_result->fetch_assoc();
        $stream_id = $stream_row['stream_id'];

        // Check if this subject is already assigned to any teacher in this stream
        $check = $con->prepare("
            SELECT ts.id
            FROM teacher_subjects ts
            JOIN subject_forms sf ON ts.subject_form_id = sf.id
            JOIN classes c ON sf.class_id = c.class_id
            WHERE sf.id = ? OR (sf.subject_id = (SELECT subject_id FROM subject_forms WHERE id = ?) AND c.stream_id = ?)
        ");
        $check->bind_param("iii", $subject_form_id, $subject_form_id, $stream_id);
        $check->execute();
        $check->store_result();

        if($check->num_rows > 0) {
            $_SESSION['error_message'] = 'This subject is already assigned to another teacher in this stream.';
        } else {
            // Insert assignment
            $stmt = $con->prepare("
                INSERT INTO teacher_subjects (teacher_id, subject_form_id, academic_year) 
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("iis", $teacher_id, $subject_form_id, $academic_year);
            
            if($stmt->execute()) {
                $_SESSION['success_message'] = 'Teacher assigned successfully!';
            } else {
                $_SESSION['error_message'] = 'Error assigning teacher: ' . $con->error;
            }
        }
        $check->close();
    }
    header("Location: assign_teacher.php");
    exit;
}


// ==================== GET ALL DATA ====================

// Get all teachers
$teachers = $con->query("
    SELECT * 
    FROM teachers_tb 
    ORDER BY first_name, last_name
");

// Get all subject + form combinations - FIXED table name
 $subject_forms_query = $con->query("
   SELECT 
    sf.id,
    f.class_name,
    sn.stream_name,
    s.subject_name,
    s.abbreviation
FROM subject_forms sf
JOIN classes f ON sf.class_id = f.class_id
JOIN streams sn ON f.stream_id = sn.stream_id
JOIN subjects s ON sf.subject_id = s.subject_id
ORDER BY sn.stream_name, f.class_name, s.subject_name;

");

// Get all assignments for DataTable - FIXED table names
 
$sql = "
    SELECT 
    ts.id AS assignment_id,
    t.first_name,
    t.last_name,
    s.subject_name,
    s.abbreviation,       -- subject abbreviation
    f.class_name,
    sn.stream_name,       -- stream name
    ts.academic_year
FROM teacher_subjects ts
JOIN teachers_tb t ON ts.teacher_id = t.teacher_id
JOIN subject_forms sf ON ts.subject_form_id = sf.id
JOIN subjects s ON sf.subject_id = s.subject_id
JOIN classes f ON sf.class_id = f.class_id
JOIN streams sn ON f.stream_id = sn.stream_id  -- get stream name
ORDER BY t.first_name, s.subject_name;

";

 

$all_assignments_result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Teachers to Subjects - EduTrack</title>
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* Your CSS styles remain the same */
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        #assignmentsTable {
            border: 2px solid #dee2e6 !important;
            border-collapse: collapse !important;
        }
        #assignmentsTable th {
            border: 1px solid #dee2e6 !important;
            background-color: #f8f9fa;
            font-weight: 600;
            padding: 12px 8px;
        }
        #assignmentsTable td {
            border: 1px solid #dee2e6 !important;
            padding: 10px 8px;
            vertical-align: middle;
        }
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
        .action-btn {
            padding: 4px 8px;
            font-size: 12px;
        }
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            #assignmentsTable th,
            #assignmentsTable td {
                padding: 8px 5px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>

<?php include('../Main/Main-sidebar.php') ?>
<br><br><br>

<main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 w-100">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header d-flex justify-content-between align-items-center">
            <h4><i class="fas fa-chalkboard-teacher mr-2"></i>Assign Teachers to Subjects</h4>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#assignTeacherModal">
                <i class="fas fa-plus-circle mr-2"></i>New Assignment
            </button>
        </div>

        <!-- Display Messages -->
        <?php if(isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['success_message']; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $_SESSION['error_message']; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <!-- Assign Teacher Modal -->
        <div class="modal fade" id="assignTeacherModal" tabindex="-1" role="dialog" aria-labelledby="assignTeacherModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="assignTeacherModalLabel">
                            <i class="fas fa-link mr-2"></i>New Teacher Assignment
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="assign_teacher.php" id="assignmentForm">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="teacher_id"><i class="fas fa-user-tie mr-2"></i>Select Teacher</label>
                                <select class="form-control" id="teacher_id" name="teacher_id" required>
                                    <option value="">-- Select Teacher --</option>
                                    <?php 
                                    // Reset pointer and fetch teachers again
                                    $teachers->data_seek(0);
                                    while($teacher = $teachers->fetch_assoc()): ?>
                                        <option value="<?= $teacher['teacher_id'] ?>">
                                            <?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="subject_form_id">
                                    <i class="fas fa-book-open mr-2"></i>Select Form & Subject
                                </label>
                                <select class="form-control" id="subject_form_id" name="subject_form_id" required>
                                    <option value="">-- Select Form & Subject --</option>
                                    <?php 
                                    if($subject_forms_query):
                                        while ($row = $subject_forms_query->fetch_assoc()): ?>
                                            <option value="<?= $row['id'] ?>">
                                                <?= htmlspecialchars($row['class_name'] . ' - ' . $row['stream_name'].' - '.$row['subject_name']) ?>
                                            </option>
                                        <?php endwhile;
                                    endif; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="academic_year">
                                    <i class="fas fa-calendar-alt mr-2"></i>Academic Year
                                </label>
                                <input type="text" class="form-control" id="academic_year" name="academic_year" 
                                       value="<?= date('Y') . '-' . (date('Y') + 1) ?>" required>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" name="btn_assign_teacher" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>Save Assignment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Assignments Table -->
        <div class="col-lg-12">
            <div class="card card-outline card-primary">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-list mr-2"></i>All Teacher Assignments</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="assignmentsTable">
                            <thead>
                                <tr class="text-center">
                                    <th width="2%" class="sortable">#</th>
                                    <th width="10%" class="sortable">Teacher</th>
                                    <th width="8%" class="sortable">Subject</th>
                                    <th width="8%" class="sortable">Form</th>
                                    <th width="10%" class="sortable">Academic Year</th>
                                    <th width="8%" class="sortable">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                if($all_assignments_result && $all_assignments_result->num_rows > 0):
                                    while($row = $all_assignments_result->fetch_assoc()):
                                ?>
                                    <tr class="text-center">
                                        <td><?= $i++; ?></td>
                                        <td>
                                            <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($row['subject_name']) ?>
                                            <?= $row['abbreviation'] ? ' (' . htmlspecialchars($row['abbreviation']) . ')' : '' ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($row['class_name'].'-'. $row['stream_name']) ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($row['academic_year']) ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="edit_assignment.php?id=<?= $row['assignment_id']; ?>"
                                                   class="btn btn-success btn-sm mr-1 action-btn" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-danger btn-sm action-btn deleteBtn" 
                                                        data-id="<?= $row['assignment_id'] ?>"
                                                        data-teacher="<?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?>"
                                                        data-subject="<?= htmlspecialchars($row['subject_name']) ?>"
                                                        data-form="<?= htmlspecialchars($row['class_name']) ?>"
                                                        title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            No teacher assignments found.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function(){
    // ==================== INITIALIZE DATATABLE ====================
    $('#assignmentsTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        responsive: true,
        pageLength: 10,
        order: [[0, 'asc']],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search assignments...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                previous: "<i class='fas fa-chevron-left'></i>",
                next: "<i class='fas fa-chevron-right'></i>"
            }
        }
    });
    
    // ==================== DELETE CONFIRMATION ====================
    $(document).on('click', '.deleteBtn', function(){
        var assignmentId = $(this).data('id');
        var teacherName = $(this).data('teacher');
        var subjectName = $(this).data('subject');
        var formName = $(this).data('form');
        
        Swal.fire({
            title: 'Are you sure?',
            html: `Remove <strong>${teacherName}</strong> from teaching <strong>${subjectName}</strong> in <strong>${formName}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            allowOutsideClick: false,
            allowEscapeKey: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Redirect to delete page
                window.location.href = 'delete_assignment.php?id=' + assignmentId;
            }
        });
    });
    
    // ==================== FORM VALIDATION ====================
    $('#assignmentForm').submit(function(e) {
        var teacherId = $('#teacher_id').val();
        var subjectFormId = $('#subject_form_id').val();
        var academicYear = $('#academic_year').val();
        
        if(!teacherId || !subjectFormId || !academicYear) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Please select all required fields.',
                confirmButtonColor: '#3085d6'
            });
            return false;
        }
        return true;
    });
    
    // ==================== MODAL ANIMATIONS ====================
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