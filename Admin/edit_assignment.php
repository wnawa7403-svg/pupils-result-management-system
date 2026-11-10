<?php
session_start();

// Login check
if (!isset($_SESSION["LoginAdmin"]) || !$_SESSION["LoginAdmin"]) {
    header('location: ../login/login.php');
    exit;
}

require_once '../connection/connection.php';

// Validate ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = 'Invalid assignment ID.';
    header("Location: assign_teacher.php");
    exit;
}

$assignment_id = (int)$_GET['id'];

// ================= FETCH CURRENT ASSIGNMENT =================
$stmt = $con->prepare("
    SELECT 
        ts.id,
        ts.teacher_id,
        ts.subject_form_id,
        ts.academic_year
    FROM teacher_subjects ts
    WHERE ts.id = ?
");
$stmt->bind_param("i", $assignment_id);
$stmt->execute();
$result = $stmt->get_result();
$assignment = $result->fetch_assoc();
$stmt->close();

if (!$assignment) {
    $_SESSION['error_message'] = 'Assignment not found.';
    header("Location: assign_teacher.php");
    exit;
}

// ================= UPDATE ASSIGNMENT =================
if (isset($_POST['btn_update_assignment'])) {
    $teacher_id = (int)$_POST['teacher_id'];
    $subject_form_id = (int)$_POST['subject_form_id'];
    $academic_year = trim($_POST['academic_year']);

    if (!$teacher_id || !$subject_form_id || empty($academic_year)) {
        $_SESSION['error_message'] = 'All fields are required.';
    } else {
        $update = $con->prepare("
            UPDATE teacher_subjects 
            SET teacher_id = ?, subject_form_id = ?, academic_year = ?
            WHERE id = ?
        ");
        $update->bind_param("iisi", $teacher_id, $subject_form_id, $academic_year, $assignment_id);

        if ($update->execute()) {
            $_SESSION['success_message'] = 'Assignment updated successfully!';
            header("Location: assign_teacher.php");
            exit;
        } else {
            $_SESSION['error_message'] = 'Failed to update assignment.';
        }
        $update->close();
    }
}

// ================= DATA FOR DROPDOWNS =================
$teachers = $con->query("SELECT * FROM teachers_tb ORDER BY first_name, last_name");

$subject_forms = $con->query("
    SELECT 
        sf.id,
        f.class_name,
        s.subject_name
    FROM subject_forms sf
    JOIN classes f ON sf.class_id = f.class_id
    JOIN subjects s ON sf.subject_id = s.subject_id
    ORDER BY f.class_name, s.subject_name
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Teacher Assignment</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>

<?php include('../Main/Main-sidebar.php'); ?>
<br><br><br>

<main class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4">
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5><i class="fas fa-edit mr-2"></i>Edit Teacher Assignment</h5>
            </div>

            <div class="card-body">
                <form method="post">

                    <!-- Teacher -->
                    <div class="form-group">
                        <label>Select Teacher</label>
                        <select name="teacher_id" class="form-control" required>
                            <option value="">-- Select Teacher --</option>
                            <?php while($t = $teachers->fetch_assoc()): ?>
                                <option value="<?= $t['teacher_id'] ?>"
                                    <?= $assignment['teacher_id'] == $t['teacher_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t['first_name'].' '.$t['last_name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Subject + Form -->
                    <div class="form-group">
                        <label>Select Form & Subject</label>
                        <select name="subject_form_id" class="form-control" required>
                            <option value="">-- Select Form & Subject --</option>
                            <?php while($sf = $subject_forms->fetch_assoc()): ?>
                                <option value="<?= $sf['id'] ?>"
                                    <?= $assignment['subject_form_id'] == $sf['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($sf['class_name'].' - '.$sf['subject_name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Academic Year -->
                    <div class="form-group">
                        <label>Academic Year</label>
                        <input type="text" name="academic_year" class="form-control"
                               value="<?= htmlspecialchars($assignment['academic_year']) ?>" required>
                    </div>

                    <!-- Buttons -->
                    <div class="text-right">
                        <a href="assign_teacher.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" name="btn_update_assignment" class="btn btn-primary">
                            Update Assignment
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</main>

</body>
</html>
