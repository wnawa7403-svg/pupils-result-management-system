<?php
session_start();
require_once '../connection/connection.php';

// ---------------- Session Check ----------------
if (!isset($_SESSION['LoginAdmin']) || empty($_SESSION['LoginAdmin'])) {
    $_SESSION['error_message'] = "Please login as Admin.";
    header('Location: ../login/login.php');
    exit;
}

// ---------------- Fetch Teachers by Department ----------------
$query = "
SELECT
    d.dept_id,
    d.dept_name,
    t.teacher_id,
    t.first_name,
    t.middle_name,
    t.last_name,
    t.email,
    t.Teaching_number,
    t.gender,
    t.role
FROM dept d
JOIN department_teachers_tb dt 
    ON d.dept_id = dt.dept_id
JOIN teachers_tb t 
    ON dt.teacher_id = t.teacher_id
ORDER BY d.dept_name, t.last_name
";

$result = mysqli_query($con, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($con));
}

// ---------------- Flash Messages ----------------
$success_message = $_SESSION['success_message'] ?? '';
$error_message   = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Department Teachers - Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/classes.css">
<style>
 
</style>
</head>
<body>
<?php include('../Main/Main-sidebar.php') ?>

<main class="col-xl-10 col-lg-9 col-md-8 px-md-4 w-100">
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1>Department Teachers</h1>
                    <p class="text-muted mb-0">View teachers assigned to each department</p>
                </div>
            </div>
        </div>

        <!-- Flash messages -->
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <!-- Teachers by Department -->
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php
            $current_dept = null;
            $dept_count = 0;
            $teacher_count = 0;
            
            while ($row = mysqli_fetch_assoc($result)):
                // New department header and table
                if ($current_dept !== $row['dept_id']):
                    // close previous table
                    if ($current_dept !== null) {
                        echo '</tbody></table>';
                        echo '</div>';
                    }
                    
                    $dept_count++;
                    echo '<div class="department-section mb-4">';
                    echo '<h3 class="department-header">' . htmlspecialchars($row['dept_name']) . '</h3>';
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-hover">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>Teacher Name</th>';
                    echo '<th>Email</th>';
                    echo '<th>Teaching Number</th>';
                    echo '<th>Gender</th>';
                    echo '<th>Role</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';
                    $current_dept = $row['dept_id'];
                endif;

                // Teacher full name
                $teacher_name = trim($row['first_name'] . ' ' . ($row['middle_name'] ? $row['middle_name'] . ' ' : '') . $row['last_name']);
                $teacher_count++;

                // Row output
                echo '<tr>';
                echo '<td class="teacher-name">' . htmlspecialchars($teacher_name) . '</td>';
                echo '<td>' . htmlspecialchars($row['email'] ?? '-') . '</td>';
                echo '<td>' . htmlspecialchars($row['Teaching_number'] ?? '-') . '</td>';
                echo '<td>' . htmlspecialchars($row['gender'] ?? '-') . '</td>';
                echo '<td>' . htmlspecialchars($row['role'] ?? '-') . '</td>';
                echo '</tr>';

            endwhile;

            // close last table
            echo '</tbody></table>';
            echo '</div>'; // close table-responsive
            echo '</div>'; // close department-section
            
            // Display summary
            echo '<div class="mt-4 pt-3 border-top">';
            echo '<p class="text-muted">';
            echo 'Showing ' . $teacher_count . ' teacher' . ($teacher_count != 1 ? 's' : '') . ' across ' . $dept_count . ' department' . ($dept_count != 1 ? 's' : '');
            echo '</p>';
            echo '</div>';
            
        else:
        ?>
         
                </svg>
                <h4>No Teachers Found</h4>
                <p>There are no teachers assigned to any department yet.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>