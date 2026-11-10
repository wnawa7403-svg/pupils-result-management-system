<?php
session_start();
require_once "../connection/connection.php";

// Ensure Admin/Teacher is logged in
if (!isset($_SESSION['LoginClassTeacher']) || !$_SESSION['LoginClassTeacher']) {
    header('Location: ../login/login.php');
    exit;
}

/***********************
 * SAVE MARKS SECTION
 ***********************/
if (isset($_POST['submit_marks'])) {

    $year        = intval($_POST['year']);
    $test_id     = intval($_POST['test_id']);
    $term_id     = intval($_POST['term_id']);
    $class_id    = intval($_POST['class_id']);
    $subject_id  = intval($_POST['subject_id']);

    $pupil_ids   = $_POST['pupil_id'] ?? [];
    $marks_vals  = $_POST['marks'] ?? [];

    for ($i = 0; $i < count($pupil_ids); $i++) {

        $pupil_id = intval($pupil_ids[$i]);
        $marks    = strtoupper(trim($marks_vals[$i]));
        $remarks  = "";

        // Determine remarks
        if ($marks === "AB") {
            $remarks = "ABSENT";
        } elseif ($marks === "NE") {
            $remarks = "Not Examined";
        } elseif (is_numeric($marks)) {
            $marks_val = floatval($marks);
            if ($marks_val < 30) $remarks = "Failed";
            elseif ($marks_val <= 40) $remarks = "Pass";
            elseif ($marks_val <= 50) $remarks = "Credit";
            elseif ($marks_val <= 60) $remarks = "Good";
            elseif ($marks_val <= 80) $remarks = "Very Good";
            else $remarks = "Excellent";
        } else {
            $remarks = "Invalid input";
        }

        // Check if mark exists
        $check = mysqli_query($con, "
            SELECT marks_id FROM marks_tb 
            WHERE pupil_id='$pupil_id' AND class_id='$class_id' 
            AND subject_id='$subject_id' AND test_id='$test_id' 
            AND term_id='$term_id' LIMIT 1
        ");

        if (mysqli_num_rows($check) > 0) {
            // UPDATE
            $row = mysqli_fetch_assoc($check);
            $marks_id = $row['marks_id'];
            mysqli_query($con, "
                UPDATE marks_tb 
                SET marks='$marks', remarks='$remarks' 
                WHERE marks_id='$marks_id'
            ");
        } else {
            // INSERT
            mysqli_query($con, "
                INSERT INTO marks_tb (pupil_id, class_id, subject_id, test_id, term_id, marks, remarks)
                VALUES ('$pupil_id','$class_id','$subject_id','$test_id','$term_id','$marks','$remarks')
            ");
        }
    }

    // Set session success message
    $_SESSION['marks_success'] = true;
    
    // Redirect back to same page with parameters
    header("Location: exam.php?year=$year&class_id=$class_id&subject_id=$subject_id&test_id=$test_id&term_id=$term_id");
    exit();
}

/******************************
 * LOAD MARK ENTRY PAGE
 ******************************/

$teacher_email = $_SESSION['LoginClassTeacher'];

// Get teacher_id
$getTeacher = mysqli_query($con, "SELECT teacher_id FROM teachers_tb WHERE email='$teacher_email' LIMIT 1");
if (!$getTeacher || mysqli_num_rows($getTeacher) == 0) {
    die("Teacher record not found.");
}

$teacher = mysqli_fetch_assoc($getTeacher);
$teacher_id = $teacher['teacher_id'];

// Get selection values
$year       = $_POST['year'] ?? $_GET['year'] ?? '';
$class_id   = $_POST['class_id'] ?? $_GET['class_id'] ?? '';
$subject_id = $_POST['subject_id'] ?? $_GET['subject_id'] ?? '';
$test_id    = $_POST['test_id'] ?? $_GET['test_id'] ?? '';
$term_id    = $_POST['term_id'] ?? $_GET['term_id'] ?? '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Enter Marks</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --success-color: #1cc88a;
            --light-bg: #f8f9fc;
            --dark-color: #2c3e50;
            --text-dark: #212529;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fb;
            color: var(--text-dark);
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 25px 0;
            margin-bottom: 30px;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s;
            margin-bottom: 25px;
            
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: 12px 12px 0 0 !important;
            padding: 15px 20px;
            font-weight: 600;
        }
        
        .form-control, .form-select {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .btn-success {
            background: linear-gradient(to right, var(--success-color), #17a673);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .marks-table {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .marks-table th {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 15px;
            border: none;
        }
        
        .marks-table td {
            padding: 15px;
            vertical-align: middle;
        }
        
        .student-photo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
            border: 2px solid #e3e6f0;
        }
        
        .selection-card {
            height: 100%;
        }
        
        .selection-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 20px;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #4a4a4a;
        }
        
        .alert-info {
            background-color: rgba(72, 149, 239, 0.15);
            border-color: var(--accent-color);
            color: #0c63e4;
            border-radius: 8px;
        }
        
        .badge-count {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 500;
        }
        
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 1.8rem;
            }
            
            .selection-icon {
                font-size: 2.5rem;
            }
        }
        main {
  display: flex;
  justify-content: center;
  align-items: flex-start; /* or center for full vertical centering */
}

    </style>
</head>

<body>

<?php include('../Main/class-teacher-sidebar.php'); ?>

<main class="col-xl-10 col-lg-9 col-md-8 px-md-4 w-100">

<div class="container-fluid">

<!-- SweetAlert2 Success Message -->
<?php if (isset($_SESSION['marks_success'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: "success",
        title: "Marks Submitted!",
        html: "All student marks have been saved successfully.<br><br>",
        confirmButtonColor: "#3085d6",
        confirmButtonText: "OK",
        timer: 3000,
        timerProgressBar: true
    }).then(() => {
        window.location.href = "teacher-index.php?year=<?= $year ?>&class_id=<?= $class_id ?>&subject_id=<?= $subject_id ?>&test_id=<?= $test_id ?>&term_id=<?= $term_id ?>";
    });

    // Backup redirect (after 4 sec)
    setTimeout(function() {
        window.location.href = "teacher-index.php?year=<?= $year ?>&class_id=<?= $class_id ?>&subject_id=<?= $subject_id ?>&test_id=<?= $test_id ?>&term_id=<?= $term_id ?>";
    }, 4000);
});
</script>
<?php unset($_SESSION['marks_success']); ?>
<?php endif; ?>


<div class="row justify-content-center">

<!-- Selection Card -->
<div class="col-lg-4 offset-lg-2">
    <div class="card">
        <div class="card-header"><h5>Select Criteria</h5></div>
        <div class="card-body">
            <form method="POST">

                <!-- Test -->
                <select class="form-select mb-3" name="test_id" required>
                    <option value="">Select Test</option>
                    <?php
                    $q = mysqli_query($con, "SELECT * FROM test WHERE test_status='Active'");
                    while ($r = mysqli_fetch_assoc($q)) {
                        $sel = ($r['test_id'] == $test_id) ? 'selected' : '';
                        echo "<option value='{$r['test_id']}' $sel>{$r['test_name']}</option>";
                    }
                    ?>
                </select>

                <!-- Year -->
                <select class="form-select mb-3" name="year" required>
                    <option value="">Select Year</option>
                    <?php
                    $yrs = mysqli_query($con, "SELECT DISTINCT year FROM pupils_tb");
                    while ($r = mysqli_fetch_assoc($yrs)) {
                        $sel = ($r['year'] == $year) ? 'selected' : '';
                        echo "<option value='{$r['year']}' $sel>{$r['year']}</option>";
                    }
                    ?>
                </select>

                <!-- Class -->
                <select class="form-select mb-3" name="class_id" required>
                    <option value="">Select Class</option>
                    <?php
                    $cls = mysqli_query($con, "
                        SELECT DISTINCT c.class_id, c.class_name
                        FROM classes c
                        JOIN subjects s ON s.class_id=c.class_id
                        WHERE s.teacher_id='$teacher_id'
                    ");
                    while ($r = mysqli_fetch_assoc($cls)) {
                        $sel = ($r['class_id'] == $class_id) ? 'selected' : '';
                        echo "<option value='{$r['class_id']}' $sel>{$r['class_name']}</option>";
                    }
                    ?>
                </select>

                <!-- Subject -->
                <select class="form-select mb-3" name="subject_id" required>
                    <option value="">Select Subject</option>
                    <?php
                    $sub = mysqli_query($con, "SELECT subject_id, subject_name FROM subjects WHERE teacher_id='$teacher_id'");
                    while ($r = mysqli_fetch_assoc($sub)) {
                        $sel = ($r['subject_id'] == $subject_id) ? 'selected' : '';
                        echo "<option value='{$r['subject_id']}' $sel>{$r['subject_name']}</option>";
                    }
                    ?>
                </select>

                <!-- Term -->
                <select class="form-select mb-3" name="term_id" required>
                    <option value="">Select Term</option>
                    <?php
                    $trs = mysqli_query($con, "SELECT * FROM terms");
                    while ($r = mysqli_fetch_assoc($trs)) {
                        $sel = ($r['term_id'] == $term_id) ? 'selected' : '';
                        echo "<option value='{$r['term_id']}' $sel>{$r['t_name']}</option>";
                    }
                    ?>
                </select>

                <button type="submit" class="btn btn-primary w-100">Search Students</button>

            </form>
        </div>
    </div>
</div>

<!-- Pupils Table -->
<div class="col-lg-6">

<?php if ($year && $class_id && $subject_id && $test_id && $term_id): ?>

<form action="" method="POST">

    <!-- Hidden fields -->
    <input type="hidden" name="year" value="<?= $year ?>">
    <input type="hidden" name="class_id" value="<?= $class_id ?>">
    <input type="hidden" name="subject_id" value="<?= $subject_id ?>">
    <input type="hidden" name="test_id" value="<?= $test_id ?>">
    <input type="hidden" name="term_id" value="<?= $term_id ?>">

    <div class="table-responsive mt-3">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Pupil ID</th>
                    <th>Name</th>
                    <th>Marks</th>
                </tr>
            </thead>
            <tbody>

            <?php
            $pupils = mysqli_query($con, "SELECT * FROM pupils_tb WHERE class_id='$class_id'");
            while ($p = mysqli_fetch_assoc($pupils)):
                $pid = $p['pupil_id'];

                // Get existing mark
                $m = mysqli_query($con, "
                    SELECT marks FROM marks_tb 
                    WHERE pupil_id='$pid' AND class_id='$class_id'
                    AND subject_id='$subject_id' AND test_id='$test_id'
                    AND term_id='$term_id' LIMIT 1
                ");
                $existing = mysqli_num_rows($m) ? mysqli_fetch_assoc($m)['marks'] : '';
            ?>

                <tr>
                    <td><?= $pid ?>
                        <input type="hidden" name="pupil_id[]" value="<?= $pid ?>">
                    </td>
                    <td><?= $p['first_name'] . " " . $p['last_name'] ?></td>
                    <td>
                        <input type="text" class="form-control"
                               name="marks[]" value="<?= $existing ?>"
                               placeholder="0-100 or AB or NE">
                    </td>
                </tr>

            <?php endwhile; ?>

            </tbody>
        </table>

        <div class="text-center mt-3">
            <button type="submit" name="submit_marks" class="btn btn-success w-50">
                Submit Marks
            </button>
        </div>

    </div>

</form>

<?php else: ?>

<div class="alert alert-info mt-3">
    Please select criteria to load pupils.
</div>

<?php endif; ?>

</div>

</div>
</div>
 <!-- JS Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).ready(function(){
        // DataTables with buttons
        var table = $('#pupilsTable').DataTable({
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
            ]
        });
        
        // Apply filters
        $('#classFilter, #genderFilter').on('change', function() {
            table.column(2).search($('#classFilter').val()).column(3).search($('#genderFilter').val()).draw();
        });

        // Delete pupil confirmation
        $('.deleteBtn').click(function() {
            var pupilId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "This pupil record will be deleted permanently!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "pupils.php?delete=" + pupilId;
                }
            });
        });

        // Show SweetAlert messages
        <?php if(isset($_SESSION['success_message'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?= $_SESSION['success_message']; ?>',
                timer: 3000
            });
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        <?php if(isset($_SESSION['error_message'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '<?= $_SESSION['error_message']; ?>'
            });
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
    });
    </script>

</main>

</body>
</html>