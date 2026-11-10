 <?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 
 
require_once "../Main/auth.php";
requireRole(["Teacher", "Class Teacher", "Hod"]); 
 
require_once "../connection/connection.php";

// Ensure teacher is logged in
if (!isset($_SESSION['LoginAdmin'])) {
    header('Location: ../login/login.php');
    exit;
}


$teacher_email = $_SESSION['LoginAdmin'];

// Get teacher_id
$query = "SELECT teacher_id FROM teachers_tb WHERE email='$teacher_email' LIMIT 1";
$result = mysqli_query($con, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Teacher record not found.");
}

$teacher = mysqli_fetch_assoc($result);
$teacher_id = $teacher['teacher_id'];

// Fetch selected values safely
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Marks - Teacher Portal</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

<?php include('../Main/Main-sidebar.php'); ?>
   <main role="main" class="col-xl-10 col-lg-9 col-md-8 px-md-4 w-100">
    <div class="container-fluid">
        <div class="row justify-content-center"><!-- Center row inside main -->

           
            <div class="col-lg-4 offset-lg-2">
                <div class="card selection-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-filter me-2"></i> Selection Criteria
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <!-- Test -->
                            <div class="form-group">
                                <label for="test_id"><i class="fas fa-file-alt me-1"></i> Test:</label>
                                <select class="form-select" name="test_id" required>
                                    <option value="">-- Select Test --</option>
                                    <?php
                                    $tests = mysqli_query($con, "SELECT * FROM test WHERE test_status='Active' ORDER BY test_name ASC");
                                    while ($row = mysqli_fetch_assoc($tests)) {
                                        $selected = ($row['test_id'] == $test_id) ? 'selected' : '';
                                        echo "<option value='{$row['test_id']}' $selected>{$row['test_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Year -->
                            <div class="form-group">
                                <label for="year"><i class="fas fa-calendar me-1"></i> Year:</label>
                                <select class="form-select" name="year" required>
                                    <option value="">-- Select Year --</option>
                                    <?php
                                    $years = mysqli_query($con, "SELECT DISTINCT year FROM pupils_tb ORDER BY year ASC");
                                    while ($row = mysqli_fetch_assoc($years)) {
                                        $selected = ($row['year'] == $year) ? 'selected' : '';
                                        echo "<option value='{$row['year']}' $selected>{$row['year']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Class -->
                            <div class="form-group">
                                <label for="class_id"><i class="fas fa-chalkboard me-1"></i> Class:</label>
                                <select class="form-select" name="class_id" required>
                                    <option value="">-- Select Class --</option>
                                    <?php
                                    $classes = mysqli_query($con, "
                                        SELECT DISTINCT c.class_id, c.class_name
                                        FROM classes c
                                        JOIN subjects s ON s.class_id = c.class_id
                                        WHERE s.teacher_id = $teacher_id
                                        ORDER BY c.class_name ASC
                                    ");
                                    while ($row = mysqli_fetch_assoc($classes)) {
                                        $selected = ($row['class_id'] == $class_id) ? 'selected' : '';
                                        echo "<option value='{$row['class_id']}' $selected>{$row['class_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Subject -->
                            <div class="form-group">
                                <label for="subject_id"><i class="fas fa-book me-1"></i> Subject:</label>
                                <select class="form-select" name="subject_id" required>
                                    <option value="">-- Select Subject --</option>
                                    <?php
                                    $subjects = mysqli_query($con, "
                                        SELECT s.subject_id, s.subject_name
                                        FROM subjects s
                                        WHERE s.teacher_id = $teacher_id
                                        ORDER BY s.subject_name ASC
                                    ");
                                    while ($row = mysqli_fetch_assoc($subjects)) {
                                        $selected = ($row['subject_id'] == $subject_id) ? 'selected' : '';
                                        echo "<option value='{$row['subject_id']}' $selected>{$row['subject_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Term -->
                            <div class="form-group">
                                <label for="term_id"><i class="fas fa-calendar-alt me-1"></i> Term:</label>
                                <select class="form-select" name="term_id" required>
                                    <option value="">-- Select Term --</option>
                                    <?php
                                    $terms = mysqli_query($con, "SELECT * FROM terms ORDER BY t_name ASC");
                                    while ($row = mysqli_fetch_assoc($terms)) {
                                        $selected = ($row['term_id'] == $term_id) ? 'selected' : '';
                                        echo "<option value='{$row['term_id']}' $selected>{$row['t_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i> Search Students
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right: Pupils Table -->
            <div class="col-lg-6">
                <?php if ($year && $class_id && $subject_id && $test_id && $term_id): ?>
                    <?php
                    $pupils_sql = "
                        SELECT p.pupil_id, p.first_name, p.last_name
                        FROM pupils_tb p
                        WHERE p.class_id = '$class_id'
                        ORDER BY p.last_name, p.first_name
                    ";
                    $pupils_res = mysqli_query($con, $pupils_sql);
                    if (!$pupils_res) {
                        die("Error fetching pupils: " . mysqli_error($con));
                    }
                    ?>
                    <form action="insertMark.php" method="POST">
                        <input type="hidden" name="user" value="<?php echo htmlspecialchars($teacher_email); ?>">
                        <input type="hidden" name="year" value="<?php echo htmlspecialchars($year); ?>">
                        <input type="hidden" name="test_id" value="<?php echo htmlspecialchars($test_id); ?>">
                        <input type="hidden" name="term_id" value="<?php echo htmlspecialchars($term_id); ?>">
                        <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class_id); ?>">
                        <input type="hidden" name="subject_id" value="<?php echo htmlspecialchars($subject_id); ?>">

                        <div class="table-responsive mt-3">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Pupil ID</th>
                                        <th>Pupil Name</th>
                                        <th>Marks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($pupil = mysqli_fetch_assoc($pupils_res)):
                                        $pupil_id = $pupil['pupil_id'];
                                        $fullName = $pupil['first_name'] . ' ' . $pupil['last_name'];
                                        $mark_res = mysqli_query($con, "
                                            SELECT marks 
                                            FROM marks_tb 
                                            WHERE pupil_id='$pupil_id' 
                                              AND class_id='$class_id' 
                                              AND subject_id='$subject_id' 
                                              AND test_id='$test_id' 
                                              AND term_id='$term_id' 
                                            LIMIT 1
                                        ");
                                        $existing_mark = ($mark_res && mysqli_num_rows($mark_res) > 0) ? mysqli_fetch_assoc($mark_res)['marks'] : '';
                                    ?>
                                    <tr>
                                        <td>
                                            <?php echo htmlspecialchars($pupil_id); ?>
                                            <input type="hidden" name="pupil_id[]" value="<?php echo htmlspecialchars($pupil_id); ?>">
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($fullName); ?>
                                            <input type="hidden" name="FullName[]" value="<?php echo htmlspecialchars($fullName); ?>">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" name="marks[]" 
                                                   value="<?php echo htmlspecialchars($existing_mark); ?>" 
                                                   min="0" max="100">
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
                        Please select Year, Class, Subject, Term, and Test first.
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</main>

</body>

               

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Input validation for marks
        document.addEventListener('DOMContentLoaded', function() {
            const markInputs = document.querySelectorAll('input[name="marks[]"]');
            
            markInputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.value) {
                        const mark = parseFloat(this.value);
                        if (isNaN(mark) || mark < 0 || mark > 100) {
                            this.classList.add('is-invalid');
                            this.value = '';
                        } else {
                            this.classList.remove('is-invalid');
                            // Round to 2 decimal places
                            this.value = Math.round(mark * 100) / 100;
                        }
                    }
                });
            });
            
            // Form validation before submission
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    let valid = true;
                    markInputs.forEach(input => {
                        if (input.value) {
                            const mark = parseFloat(input.value);
                            if (isNaN(mark) || mark < 0 || mark > 100) {
                                input.classList.add('is-invalid');
                                valid = false;
                            }
                        }
                    });
                    
                    if (!valid) {
                        e.preventDefault();
                        alert('Please enter valid marks between 0 and 100.');
                    }
                });
            }
        });
    </script>
</body>
</html>
</html>
