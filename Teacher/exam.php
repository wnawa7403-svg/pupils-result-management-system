<?php
session_start();
require_once "../connection/connection.php";

if (!isset($_SESSION['LoginTeacher'])) {
    header("Location: ../login/login.php");
    exit;
}

$teacher_email = $_SESSION['LoginTeacher'];

/* ================= FETCH TEACHER ================= */
$stmt = $con->prepare("SELECT teacher_id FROM teachers_tb WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $teacher_email);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) die("Teacher not found");
$teacher_id = $res->fetch_assoc()['teacher_id'];

/* ================= POST VALUES ================= */
$test_id    = $_POST['test_id'] ?? '';
$class_id   = $_POST['class_id'] ?? '';
$subject_id = $_POST['subject_id'] ?? '';
$term_id    = $_POST['term_id'] ?? '';

/* ================= GET YEAR FROM TERM ================= */
$term_year = null;
if ($term_id) {
    $stmt = $con->prepare("SELECT year FROM terms WHERE term_id = ? LIMIT 1");
    $stmt->bind_param("i", $term_id);
    $stmt->execute();
    $term_res = $stmt->get_result();
    if ($row = $term_res->fetch_assoc()) $term_year = (int)$row['year'];
}

/* ================= DROPDOWNS ================= */
/* TESTS */
$test_options = '';
$tests = mysqli_query($con, "SELECT * FROM test WHERE test_status='active' ORDER BY test_name");
while ($row = mysqli_fetch_assoc($tests)) {
    $selected = ($row['test_id'] == $test_id) ? 'selected' : '';
    $test_options .= "<option value='{$row['test_id']}' $selected>{$row['test_name']} ({$row['assessment_code']})</option>";
}

/* CLASSES */
$class_options = '';
$stmt = $con->prepare("
    SELECT DISTINCT c.class_id, c.class_name, s.stream_name
    FROM classes c
    JOIN streams s ON s.stream_id = c.stream_id
    JOIN subject_forms sf ON sf.class_id = c.class_id
    JOIN teacher_subjects ts ON ts.subject_form_id = sf.id
    WHERE ts.teacher_id = ?
    ORDER BY c.class_name
");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $selected = ($row['class_id'] == $class_id) ? 'selected' : '';
    $class_options .= "<option value='{$row['class_id']}' $selected>{$row['class_name']} - {$row['stream_name']}</option>";
}

/* SUBJECTS */
$subject_options = '<option value="">Select Subject</option>';
if ($class_id) {
    $stmt = $con->prepare("
        SELECT DISTINCT s.subject_id, s.subject_name
        FROM subjects s
        JOIN subject_forms sf ON sf.subject_id = s.subject_id
        JOIN teacher_subjects ts ON ts.subject_form_id = sf.id
        WHERE ts.teacher_id = ? AND sf.class_id = ?
        ORDER BY s.subject_name
    ");
    $stmt->bind_param("ii", $teacher_id, $class_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $selected = ($row['subject_id'] == $subject_id) ? 'selected' : '';
        $subject_options .= "<option value='{$row['subject_id']}' $selected>{$row['subject_name']}</option>";
    }
}

/* TERMS */
$term_options = '';
$terms = mysqli_query($con, "SELECT * FROM terms WHERE status='active' ORDER BY year DESC, t_name");
while ($row = mysqli_fetch_assoc($terms)) {
    $selected = ($row['term_id'] == $term_id) ? 'selected' : '';
    $term_options .= "<option value='{$row['term_id']}' $selected>{$row['t_name']} - {$row['year']}</option>";
}

/* ================= LOAD PUPILS & MARKS ================= */
$pupils_array = [];
$marks_map = [];
$show_marks_form = false;

if ($test_id && $term_id && $class_id && $subject_id) {
    $show_marks_form = true;

    // FETCH PUPILS WHO BELONG TO THE CLASS AND SUBJECT
    $stmt = $con->prepare("
        SELECT DISTINCT p.pupil_id, p.first_name, p.last_name
        FROM pupils_tb p
        JOIN subject_forms sf ON sf.class_id = p.class_id
        JOIN teacher_subjects ts ON ts.subject_form_id = sf.id
        WHERE p.class_id = ? 
          AND sf.subject_id = ? 
          AND ts.teacher_id = ?
        ORDER BY p.last_name, p.first_name
    ");
    $stmt->bind_param("iii", $class_id, $subject_id, $teacher_id);
    $stmt->execute();
    $pupils = $stmt->get_result();

    $pupil_ids = [];
    while ($p = $pupils->fetch_assoc()) {
        $pupils_array[$p['pupil_id']] = $p;
        $pupil_ids[] = $p['pupil_id'];
    }

    // FETCH EXISTING MARKS
    if (!empty($pupil_ids)) {
        $pupil_ids_str = implode(',', array_map('intval', $pupil_ids));
        $sql = "SELECT pupil_id, marks FROM marks_tb 
                WHERE pupil_id IN ($pupil_ids_str) 
                  AND class_id=$class_id 
                  AND subject_id=$subject_id 
                  AND test_id=$test_id 
                  AND term_id=$term_id";
        $res = mysqli_query($con, $sql);
        while ($m = mysqli_fetch_assoc($res)) {
            $marks_map[$m['pupil_id']] = $m['marks'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Enter Marks - Teacher Portal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
body{font-family:'Poppins',sans-serif;background:#f8f9fa;}
.card{border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.05);}
.marks-table input{width:100%;border-radius:6px;border:1px solid #e9ecef;padding:6px;}
</style>
</head>
<body>
<?php include('../Main/Teacher-sidebar.php'); ?>
<main class="main-content p-3">

<div class="row">
<!-- LEFT PANEL -->
<div class="col-lg-4">
<div class="card p-3 mb-3">
<h5>Assessment Selection</h5>
<form method="POST" id="selectionForm">
<label>Test</label>
<select name="test_id" class="form-select" required>
<option value="">Select Test</option>
<?= $test_options ?>
</select>
<label>Term</label>
<select name="term_id" class="form-select" required>
<option value="">Select Term</option>
<?= $term_options ?>
</select>
<label>Class</label>
<select name="class_id" id="class_id" class="form-select" required>
<option value="">Select Class</option>
<?= $class_options ?>
</select>
<label>Subject</label>
<select name="subject_id" id="subject_id" class="form-select" required>
<?= $subject_options ?>
</select>
<button type="submit" class="btn btn-primary mt-2 w-100">Load Students</button>
</form>
</div>
</div>

<!-- RIGHT PANEL -->
<div class="col-lg-8">
<?php if ($show_marks_form && !empty($pupils_array)): ?>
<div class="card p-3">
<h5>Enter Marks (<?= count($pupils_array) ?> Students)</h5>
<form method="POST" action="insertMark.php" id="marksForm">
<input type="hidden" name="test_id" value="<?= htmlspecialchars($test_id) ?>">
<input type="hidden" name="term_id" value="<?= htmlspecialchars($term_id) ?>">
<input type="hidden" name="class_id" value="<?= htmlspecialchars($class_id) ?>">
<input type="hidden" name="subject_id" value="<?= htmlspecialchars($subject_id) ?>">
<div class="table-responsive">
<table class="table marks-table">
<thead><tr><th>Student Name</th><th>Marks (0–100)</th></tr></thead>
<tbody>
<?php foreach ($pupils_array as $id=>$p): ?>
<tr>
<td><?= htmlspecialchars($p['first_name'].' '.$p['last_name']) ?>
<input type="hidden" name="pupil_id[]" value="<?= $id ?>"></td>
<td><input type="number" name="marks[]" min="0" max="100" step="0.01" value="<?= htmlspecialchars($marks_map[$id]??'') ?>" placeholder="NE"></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<button type="submit" class="btn btn-success w-100 mt-2">Save Marks</button>
</form>
</div>
<?php else: ?>
<div class="alert alert-info">Select assessment details to load students.</div>
<?php endif; ?>
</div>
</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function(){

    const classDropdown = document.getElementById("class_id");
    const subjectDropdown = document.getElementById("subject_id");
    const form = document.getElementById("marksForm");

    // Load subjects
    classDropdown.addEventListener("change", function(){
        const classId = this.value;
        subjectDropdown.innerHTML = '<option>Loading...</option>';

        if(!classId){
            subjectDropdown.innerHTML = '<option value="">Select Subject</option>';
            return;
        }

        fetch("get_subjects.php?class_id=" + classId)
        .then(res => res.text())
        .then(data => {
            subjectDropdown.innerHTML = data;
        })
        .catch(err => {
            subjectDropdown.innerHTML = '<option value="">Error loading subjects</option>';
        });
    });

    // Submit form with AJAX
    form.addEventListener("submit", function(e){
        e.preventDefault();

        let formData = new FormData(this);

        fetch("insertMark.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);

            Swal.fire({
                title: data.status,
                text: data.message,
                icon: data.icon,
                confirmButtonColor: "#3085d6"
            });
        })
        .catch(error => {
            console.error("Error:", error);
            Swal.fire("Error", "Something went wrong!", "error");
        });
    });

});
</script>

</body>
</html>
