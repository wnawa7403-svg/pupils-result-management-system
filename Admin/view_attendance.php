<?php
require_once "../connection/connection.php";

// Get filters from GET
$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$term_id  = isset($_GET['term_id']) ? (int)$_GET['term_id'] : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Attendance Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<?php include('../Main/main_header.php'); ?>
<?php include('../Main/main-sidebar.php'); ?>
<main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 w-100">
  
<div class="container mt-4">
    <h2>Attendance Register</h2>

    <!-- Filter Form -->
    <form method="get" action="view_attendance.php" class="row g-3 mb-4">
        <div class="col-md-6">
            <label>Select Class:</label>
            <select name="class_id" class="form-select" required>
                <option value="">-- Select Class --</option>
                <?php
                $classes = mysqli_query($con, "SELECT class_id, class_name FROM classes");
                while($c = mysqli_fetch_assoc($classes)){
                    $selected = ($c['class_id'] == $class_id) ? 'selected' : '';
                    echo "<option value='{$c['class_id']}' $selected>{$c['class_name']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="col-md-6">
            <label>Select Term:</label>
            <select name="term_id" class="form-select" required>
                <option value="">-- Select Term --</option>
                <?php
                $terms = mysqli_query($con, "SELECT term_id, t_name FROM terms");
                while($t = mysqli_fetch_assoc($terms)){
                    $selected = ($t['term_id'] == $term_id) ? 'selected' : '';
                    echo "<option value='{$t['term_id']}' $selected>{$t['t_name']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="col-12 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">View Register</button>
        </div>
    </form>

<?php if ($class_id > 0 && $term_id > 0): ?>

    <?php
    // Fetch attendance records for selected class and term
    $query = "SELECT p.pupil_id, p.first_name, p.last_name, a.status, a.day, a.date
FROM pupils_tb p
LEFT JOIN attendance a
ON p.pupil_id = a.pupil_id 
   AND a.class_id = ? 
   AND a.term_id = ?
WHERE p.class_id = ?   -- only pupils in this class
ORDER BY p.first_name, a.date";


    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) die("SQL Prepare Error: " . mysqli_error($con));

  $stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "iii", $class_id, $term_id, $class_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    ?>

    <!-- Download PDF -->
    <div class="mb-3 text-end">
        <a href="attendance_pdf.php?class_id=<?php echo $class_id; ?>&term_id=<?php echo $term_id; ?>" target="_blank" class="btn btn-success">
            📄 Download PDF
        </a>
    </div>

    <!-- Attendance Table -->
    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>Pupil ID</th>
                <th>Name</th>
                <th>Status</th>
                <th>Day</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $presentCount = 0;
        $absentCount = 0;

        while ($row = mysqli_fetch_assoc($result)) {
            $statusText = "<span class='text-muted'>Not marked</span>";
            if ($row['status'] !== null) {
                if ($row['status'] == 1) {
                    $statusText = "<span class='text-success'>✅ Present</span>";
                    $presentCount++;
                } else {
                    $statusText = "<span class='text-danger'>❌ Absent</span>";
                    $absentCount++;
                }
            }
            ?>
            <tr>
                <td><?php echo htmlspecialchars($row['pupil_id']); ?></td>
                <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                <td><?php echo $statusText; ?></td>
                <td><?php echo htmlspecialchars($row['day'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($row['date'] ?? '-'); ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <!-- Summary -->
    <div class="mt-3">
        <strong>Summary:</strong> <?php echo $presentCount; ?> Present, <?php echo $absentCount; ?> Absent
    </div>

<?php else: ?>
    <div class="alert alert-info">Please select class and term to view the register.</div>
<?php endif; ?>

</div>
</body>
</html>
