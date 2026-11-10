<?php
session_start();
require_once '../connection/connection.php';

// ---------------------
// Session Check
// ---------------------
if (!isset($_SESSION['LoginTeacher']) || empty($_SESSION['LoginTeacher'])) {
    die('Not logged in.');
}

$teacher_email = mysqli_real_escape_string($con, $_SESSION['LoginTeacher']);

// ---------------------
// Get Teacher Info
// ---------------------
$q = "SELECT teacher_id, first_name, last_name FROM teachers_tb WHERE email = '$teacher_email' LIMIT 1";
$r = mysqli_query($con, $q);
if (!$r || mysqli_num_rows($r) === 0) die("Teacher not found");
$teacher_data = mysqli_fetch_assoc($r);
$teacher_id = (int)$teacher_data['teacher_id'];
$teacher_name = htmlspecialchars($teacher_data['first_name'] . ' ' . $teacher_data['last_name']);

// ---------------------
// Get class & stream from GET
// ---------------------
$class_id  = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
$stream_id = isset($_GET['stream_id']) ? intval($_GET['stream_id']) : 0;
if (!$class_id || !$stream_id) die("Class or Stream not selected.");

// ---------------------
// Fetch Students + Subjects + Marks
// ---------------------
$sql = "
SELECT 
    p.pupil_id,
    p.first_name,
    p.last_name,
    p.gender,
    sub.subject_name,
    sub.abbreviation,
    t.test_name,
    t.assessment_code,
    tr.t_name,
    tr.year,
    tr.term_number,
    m.marks,
    sub.subject_id,
    t.test_id,
    tr.term_id,
    tr.term_number
FROM pupils_tb p
JOIN classes c ON c.class_id = p.class_id
JOIN streams s ON s.stream_id = c.stream_id
JOIN subject_forms sf ON sf.class_id = p.class_id
JOIN teacher_subjects ts ON ts.subject_form_id = sf.id AND ts.teacher_id = ?
JOIN subjects sub ON sub.subject_id = sf.subject_id
LEFT JOIN marks_tb m ON m.pupil_id = p.pupil_id AND m.subject_id = sub.subject_id
LEFT JOIN test t ON t.test_id = m.test_id
LEFT JOIN terms tr ON tr.term_id = m.term_id
WHERE p.class_id = ? AND c.stream_id = ?
ORDER BY  sub.abbreviation
";

$stmt = $con->prepare($sql);
$stmt->bind_param("iii", $teacher_id, $class_id, $stream_id);
$stmt->execute();
$result = $stmt->get_result();

// ---------------------
// Get unique subjects, terms/tests for filters
// ---------------------
$allSubjects = [];
$allTermTests = [];
while ($row = $result->fetch_assoc()) {
    $allSubjects[$row['subject_id']] = $row['subject_name'];

    $key = $row['term_id'].'|'.$row['test_id'];
    if ($row['term_id'] && !isset($allTermTests[$key])) {
        $allTermTests[$key] = [
            'term_number' => $row['term_number'],
            'year' => $row['year'],
            'assessment_code' => $row['assessment_code'],
            'test_name' => $row['test_name'],
            'subject_id' => $row['subject_id']
        ];
    }

    // Save row for table rendering
    $students[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Class Students - <?= $teacher_name ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
/* Marks color badges */
.marks-cell { display:inline-block; padding:2px 8px; border-radius:4px; text-align:center; min-width:50px; font-weight:500; }
.marks-cell.high { background:#28a745;color:white; }
.marks-cell.medium { background:#ffc107;color:black; }
.marks-cell.low { background:#dc3545;color:white; }
.marks-cell.na { background:#6c757d;color:white; }

/* Sidebar adjustments */
.sidebar { height:100vh; position:fixed; top:0; left:0; width:250px; overflow-y:auto; background:#f8f9fa; }
.main-content { margin-left:250px; padding:20px; }

/* Responsive */
@media (max-width: 992px) {
    .sidebar { position:relative; width:100%; height:auto; }
    .main-content { margin-left:0; padding:10px; }
}

/* Table responsive */
.table-responsive { max-height:70vh; overflow:auto; }
</style>
</head>
<body>
 
        <?php include('../Main/Teacher-sidebar.php'); ?>
     
    <!-- Main Content -->
    <main class="main-content flex-grow-1">
        <div class="container-fluid mt-4">

            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
               
                <div>Teacher: <strong><?= $teacher_name ?></strong></div>
            </div>

            <!-- Filters -->
            <div class="card mb-3">
                <div class="card-header d-flex flex-wrap gap-2 align-items-center">
                    <h5 class="mb-0 flex-grow-1">Filters</h5>
                    <select id="filterSubject" class="form-select form-select-sm w-auto">
                        <option value="">All Subjects</option>
                        <?php foreach ($allSubjects ?? [] as $id => $name): ?>
                            <option value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <select id="filterTermTest" class="form-select form-select-sm w-auto">
                        <option value="">All Terms & Tests</option>
                        <?php foreach ($allTermTests ?? [] as $key => $tt): ?>
                            <option value="<?= $key ?>"><?= "Term {$tt['term_number']} - {$tt['year']} | {$tt['test_name']}" ?></option>
                        <?php endforeach; ?>
                    </select>

                    <input type="text" id="searchInput" class="form-control form-control-sm w-auto" placeholder="Search pupil...">
                    <button class="btn btn-sm btn-outline-secondary" onclick="resetFilters()">Reset</button>
                </div>
            </div>

            <!-- Students Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle" id="marksTable">
                    <thead class="table-light">
                        <tr>
                             
                            <th>Pupil Name</th>
                            <th>Subject</th>
                            <th>Term & Test</th>
                            <th>Marks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($students)):
                            $no = 1;
                            foreach ($students as $row):
                                $marks = $row['marks'] ?? null;
                                $marksClass = 'na';
                                $marksDisplay = 'N/A';
                                if ($marks !== null) {
                                    $marks = floatval($marks);
                                    if ($marks >= 75) $marksClass='high';
                                    elseif ($marks >=50) $marksClass='medium';
                                    else $marksClass='low';
                                    $marksDisplay = number_format($marks);
                                }
                        ?>
                        <tr 
                            data-subject="<?= $row['subject_id'] ?>" 
                            data-termtest="<?= $row['term_id'].'|'.$row['test_id'] ?>"
                            data-name="<?= htmlspecialchars($row['first_name'].' '.$row['last_name']) ?>"
                        >
                           
                            <td><?= htmlspecialchars($row['first_name'].' '.$row['last_name']) ?></td>
                            <td><?= htmlspecialchars($row['abbreviation']) ?></td>
                            <td>
                              
                                    <?= htmlspecialchars($row['t_name'] ?? '') ?> - <?= htmlspecialchars($row['year'] ?? '') ?>
                                   <br>
                                    <small><?= htmlspecialchars($row['test_name'] ?? '') ?></small>
                                </td>
                                                            </td>
                            <td><span class="marks-cell <?= $marksClass ?>"><?= $marksDisplay ?></span></td>
                        </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="5" class="text-center py-4">No students found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Simple filters
const subjectFilter = document.getElementById('filterSubject');
const termTestFilter = document.getElementById('filterTermTest');
const searchInput = document.getElementById('searchInput');
const rows = document.querySelectorAll('#marksTable tbody tr');

function filterTable() {
    const subject = subjectFilter.value;
    const termTest = termTestFilter.value;
    const search = searchInput.value.toUpperCase();

    rows.forEach(row => {
        const matchesSubject = !subject || row.dataset.subject === subject;
        const matchesTermTest = !termTest || row.dataset.termtest === termTest;
        const matchesSearch = !search || row.dataset.name.toUpperCase().includes(search);

        row.style.display = (matchesSubject && matchesTermTest && matchesSearch) ? '' : 'none';
    });
}

function resetFilters() {
    subjectFilter.value = '';
    termTestFilter.value = '';
    searchInput.value = '';
    filterTable();
}

subjectFilter.addEventListener('change', filterTable);
termTestFilter.addEventListener('change', filterTable);
searchInput.addEventListener('keyup', filterTable);

</script>
</body>
</html>>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const subjectFilter = document.getElementById('filterSubject');
const termTestFilter = document.getElementById('filterTermTest');
const searchInput = document.getElementById('searchInput');
const rows = document.querySelectorAll('#marksTable tbody tr');

function filterTable() {
    const subject = subjectFilter.value;
    const termTest = termTestFilter.value;
    const search = searchInput.value.toUpperCase();

    rows.forEach(row => {
        const matchSubject = !subject || row.dataset.subject === subject;
        const matchTermTest = !termTest || row.dataset.termtest === termTest;
        const matchSearch = !search || row.dataset.name.toUpperCase().includes(search);
        row.style.display = (matchSubject && matchTermTest && matchSearch) ? '' : 'none';
    });
}

subjectFilter.addEventListener('change', filterTable);
termTestFilter.addEventListener('change', filterTable);
searchInput.addEventListener('keyup', filterTable);

function resetFilters() {
    subjectFilter.value = '';
    termTestFilter.value = '';
    searchInput.value = '';
    filterTable();
}

// Initialize filter
filterTable();
</script>
</body>
</html>
