<?php
session_start();
require_once "../connection/connection.php";

/* ===================== AUTH CHECK ===================== */
if (!isset($_SESSION['LoginTeacher'])) {
    header("Location: ../login/login.php");
    exit;
}

$teacher_id = (int) $_SESSION['LoginTeacher'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Classes</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/fontawesome/css/all.min.css" rel="stylesheet">
</head>

<body>
<main class="container mt-4">

<?php
/* =====================================================
   IF CLASS IS SELECTED → SHOW ALL PUPILS
   ===================================================== */
if (isset($_GET['class_id'])):

    $class_id = (int) $_GET['class_id'];

    /* ---------- ACCESS CHECK ---------- */
    $access_stmt = $con->prepare("
        SELECT 1
        FROM subject_forms sf
        JOIN teacher_subjects ts ON ts.subject_form_id = sf.id
        WHERE ts.teacher_id = ?
          AND sf.class_id = ?
        LIMIT 1
    ");
    $access_stmt->bind_param("ii", $teacher_id, $class_id);
    $access_stmt->execute();

    if ($access_stmt->get_result()->num_rows === 0) {
        echo "<div class='alert alert-danger'>Access Denied</div>";
        exit;
    }

    /* ---------- CLASS + STREAM ---------- */
    $class_stmt = $con->prepare("
        SELECT c.class_name, st.stream_name
        FROM classes c
        JOIN streams st ON st.stream_id = c.stream_id
        WHERE c.class_id = ?
    ");
    $class_stmt->bind_param("i", $class_id);
    $class_stmt->execute();
    $class_info = $class_stmt->get_result()->fetch_assoc();

    /* ---------- ALL PUPILS IN CLASS ---------- */
    $pupil_stmt = $con->prepare("
        SELECT 
            pupil_id,
            first_name,
            other_name,
            last_name,
            DOB,
            gender,
            RegDate
        FROM pupils_tb
        WHERE class_id = ?
        ORDER BY last_name, first_name
    ");
    $pupil_stmt->bind_param("i", $class_id);
    $pupil_stmt->execute();
    $pupils = $pupil_stmt->get_result();
?>

<!-- ================= CLASS HEADER ================= -->
<div class="card shadow mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-users me-2"></i>
            Students in <?php echo htmlspecialchars($class_info['class_name']); ?>
            <span class="badge bg-secondary ms-2">
                Stream <?php echo htmlspecialchars($class_info['stream_name']); ?>
            </span>
        </h5>
        <a href="view_classes.php" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Classes
        </a>
    </div>

    <div class="card-body">
        <?php if ($pupils->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center">
                <thead class="table-light">
                    <tr>
                        <th>Pupil ID</th>
                        <th>Full Name</th>
                        <th>DOB</th>
                        <th>Gender</th>
                        <th>Reg Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($p = $pupils->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $p['pupil_id']; ?></td>
                        <td>
                            <?php
                                echo htmlspecialchars(
                                    trim(
                                        $p['first_name'] . " " .
                                        $p['other_name'] . " " .
                                        $p['last_name']
                                    )
                                );
                            ?>
                        </td>
                        <td><?php echo $p['DOB']; ?></td>
                        <td><?php echo $p['gender']; ?></td>
                        <td><?php echo $p['RegDate']; ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-user-slash fa-3x text-muted mb-2"></i>
                <p>No pupils found in this class.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
/* =====================================================
   ELSE → SHOW CLASSES FOR TEACHER
   ===================================================== */
else:

$class_stmt = $con->prepare("
    SELECT DISTINCT 
        c.class_id,
        c.class_name,
        st.stream_name
    FROM subject_forms sf
    JOIN teacher_subjects ts ON ts.subject_form_id = sf.id
    JOIN classes c ON c.class_id = sf.class_id
    JOIN streams st ON st.stream_id = c.stream_id
    WHERE ts.teacher_id = ?
    ORDER BY c.class_name ASC
");
$class_stmt->bind_param("i", $teacher_id);
$class_stmt->execute();
$class_result = $class_stmt->get_result();
?>

<div class="card shadow">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-chalkboard me-2"></i> My Classes
        </h5>
    </div>

    <div class="card-body">
        <?php if ($class_result->num_rows > 0): ?>
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Class</th>
                    <th>Stream</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($c = $class_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($c['class_name']); ?></td>
                    <td><?php echo htmlspecialchars($c['stream_name']); ?></td>
                    <td>
                        <a href="view_classes.php?class_id=<?php echo $c['class_id']; ?>"
                           class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> View Students
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-inbox fa-3x text-muted mb-2"></i>
                <p>No classes assigned.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php endif; ?>

</main>
</body>
</html>
