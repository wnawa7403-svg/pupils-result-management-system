<?php
require_once '../connection/connection.php';
require_once '../fpdf186/fpdf.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

// -----------------------------
// 1. Get class and term
// -----------------------------
$class_id = isset($_POST['class_id']) ? intval($_POST['class_id']) : (isset($_GET['class_id']) ? intval($_GET['class_id']) : 0);
$term_id  = isset($_POST['term_id']) ? intval($_POST['term_id']) : (isset($_GET['term_id']) ? intval($_GET['term_id']) : 0);

if ($class_id <= 0 || $term_id <= 0) die("Invalid class or term.");

// -----------------------------
// 2. Fetch class and term names
// -----------------------------
function fetchSingleValue($con, $sql, $param) {
    $stmt = $con->prepare($sql);
    if (!$stmt) die("Prepare failed: " . $con->error);
    $stmt->bind_param("i", $param);
    $stmt->execute();
    $stmt->bind_result($value);
    $stmt->fetch();
    $stmt->close();
    return $value;
}

$class_name = fetchSingleValue($con, "SELECT class_name FROM classes WHERE class_id=?", $class_id);
$term_name  = fetchSingleValue($con, "SELECT t_name FROM terms WHERE term_id=?", $term_id);

// -----------------------------
// 3. Get all pupils
// -----------------------------
$stmt_students = $con->prepare("SELECT pupil_id, first_name, last_name FROM pupils_tb WHERE class_id=? ORDER BY last_name ASC");
if (!$stmt_students) die("Prepare failed: " . $con->error);
$stmt_students->bind_param("i", $class_id);
$stmt_students->execute();
$students_result = $stmt_students->get_result();
if ($students_result->num_rows === 0) die("No students found.");
$stmt_students->close(); // We can close here if we fetched results

// -----------------------------
// 1. Precompute total marks for all students (for ranking)
// -----------------------------
$studentTotals = [];

$sql_all = "SELECT m.pupil_id, s.subject_name, tt.test_name, m.marks
            FROM marks_tb m
            JOIN subjects s ON m.subject_id = s.subject_id
            JOIN test tt ON m.test_id = tt.test_id
            WHERE m.class_id=? AND m.term_id=?
            ORDER BY m.pupil_id, s.subject_name ASC, tt.test_name ASC";

$stmt_all = $con->prepare($sql_all);
$stmt_all->bind_param("ii", $class_id, $term_id);
$stmt_all->execute();
$result_all = $stmt_all->get_result();

while ($row = $result_all->fetch_assoc()) {
    $pid     = $row['pupil_id'];
    $subject = strtolower($row['subject_name']);
    $test    = strtolower($row['test_name']);
    $marks   = $row['marks'];

    if (!isset($studentTotals[$pid])) {
        $studentTotals[$pid] = [
            'totals' => [],
            'english' => null,
        ];
    }

    if ($test === 'eot') {
        if (is_numeric($marks) && $marks >= 40) {
            if ($subject === 'english') {
                $studentTotals[$pid]['english'] = $marks;
            } else {
                $studentTotals[$pid]['totals'][$subject] = $marks;
            }
        }
    }
}

// Compute total marks for each student
$studentTotalMarks = [];
foreach ($studentTotals as $pid => $data) {
    arsort($data['totals']);
    $best5 = array_slice($data['totals'], 0, 5);
    $total = array_sum($best5);

    if ($data['english'] !== null && $data['english'] >= 40) {
        $total += $data['english'];
    }

    $studentTotalMarks[$pid] = $total;
}

// Rank students by total marks
arsort($studentTotalMarks);
$rankings = [];
$rank = 1;
$lastTotal = null;
$skip = 0;

foreach ($studentTotalMarks as $pid => $total) {
    if ($lastTotal === null) {
        $rankings[$pid] = $rank;
    } elseif ($total == $lastTotal) {
        $rankings[$pid] = $rank; // tie
        $skip++;
    } else {
        $rank += 1 + $skip;
        $skip = 0;
        $rankings[$pid] = $rank;
    }
    $lastTotal = $total;
}

// Best student
reset($studentTotalMarks);
$bestStudentId   = key($studentTotalMarks);
$bestTotalMarks  = current($studentTotalMarks);

$stmt_best = $con->prepare("SELECT first_name, last_name FROM pupils_tb WHERE pupil_id=?");
$stmt_best->bind_param("i", $bestStudentId);
$stmt_best->execute();
$stmt_best->bind_result($bestFirst, $bestLast);
$stmt_best->fetch();
$stmt_best->close();
$bestStudentName = $bestFirst . " " . $bestLast;

// -----------------------------
// 2. Generate report cards
// -----------------------------
$pdf = new FPDF();

while ($student = $students_result->fetch_assoc()) {
    $pdf->AddPage();

 // Header with two logos
$pdf->Image('../Admin/images/R.jpeg', 10, 8, 25);   // Left logo (x=10, y=8, width=25mm)
$pdf->Image('../Admin/images/R.jpeg', 175, 8, 25); 

$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,'MINISTRY OF GENERAL EDUCATION',0,1,'C');
$pdf->Cell(0,10,'MILEMU SECONDARY SCHOOL',0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,6,'P.O. BOX 124-001',0,1,'C');
$pdf->Cell(0,6,'Copperbelt Province',0,1,'C');
$pdf->Cell(0,6,'Ndola Zambia',0,1,'C');
$pdf->Ln(5);

    // Student Info on the same line
$pdf->SetFont('Arial','B',12);
$pdf->Cell(30,8,'NAME:'); 
$pdf->SetFont('Arial','',12);
$pdf->Cell(60,8,$student['first_name'].' '.$student['last_name'],0,0); // Keep on same line

$pdf->SetFont('Arial','B',12);
$pdf->Cell(25,8,'CLASS:');
$pdf->SetFont('Arial','',12);
$pdf->Cell(40,8,$class_name,0,0);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(20,8,'TERM:');
$pdf->SetFont('Arial','',12);
$pdf->Cell(30,8,$term_name,0,1); // Line break after TERM
$pdf->Ln(5);


    // Fetch marks for this student
$sql_marks = "SELECT s.subject_name, tt.test_name, m.marks, m.remarks
              FROM marks_tb m
              JOIN subjects s ON m.subject_id = s.subject_id
              JOIN test tt ON m.test_id = tt.test_id
              WHERE m.pupil_id=? AND m.class_id=? AND m.term_id=?
              ORDER BY s.subject_name ASC, tt.test_name ASC";

$stmt_marks = $con->prepare($sql_marks);
$subjects = [];
$englishMark = null;
$englishPoints = null;

if ($stmt_marks) {
    $stmt_marks->bind_param("iii", $student['pupil_id'], $class_id, $term_id);
    $stmt_marks->execute();
    $marks_result = $stmt_marks->get_result();

    while ($row = $marks_result->fetch_assoc()) {
        $subjects[$row['subject_name']][$row['test_name']] = [
            'marks' => $row['marks'],
            'remarks' => $row['remarks']
        ];
    }
}

// --- Function: Convert marks to ECZ points ---
function marksToPoints($marks) {
    if (!is_numeric($marks)) return 9; // treat AB/NE as fail
    if ($marks >= 75) return 1;
    if ($marks >= 70) return 2;
    if ($marks >= 60) return 3;
    if ($marks >= 50) return 4;
    if ($marks >= 45) return 5;
    if ($marks >= 40) return 6;
    if ($marks >= 35) return 7;
    if ($marks >= 30) return 8;
    return 9;
}

// --- Table Header ---
$pdf->SetFont('Arial','B',10);
$pdf->Cell(50,8,'SUBJECT',1,0,'C');
$pdf->Cell(25,8,'TEST ONE',1,0,'C');
$pdf->Cell(25,8,'TEST TWO',1,0,'C');
$pdf->Cell(25,8,'EOT',1,0,'C');
$pdf->Cell(25,8,'GRADE',1,0,'C');
$pdf->Cell(30,8,'COMMENTS',1,1,'C');

$pdf->SetFont('Arial','',12);

$totals = []; // store ECZ points (for Best 5)

// --- Loop subjects ---
foreach ($subjects as $subject => $tests) {
    $T1  = $tests['T1']['marks'] ?? '-';
    $T2  = $tests['T2']['marks'] ?? '-';
    $eot = $tests['eot']['marks'] ?? '-';
    $comment = $tests['eot']['remarks'] ?? '-';

    // Grade conversion
    if (is_numeric($eot)) {
        if ($eot >= 80) $grade = 'A';
        elseif ($eot >= 60) $grade = 'B';
        elseif ($eot >= 40) $grade = 'C';
        else $grade = 'F';
    } else {
        $grade = '-';
    }

    // Points conversion
    $points = marksToPoints($eot);

    // Collect for Best 5 + English rule
    if (is_numeric($eot) && $eot >= 40) {
        if (strtolower($subject) === 'english') {
            $englishMark = $eot;
            $englishPoints = $points;
        } else {
            $totals[$subject] = $points;
        }
    }

    $pdf->Cell(50,8,$subject,1,0,'C');
    $pdf->Cell(25,8,$T1,1,0,'C');
    $pdf->Cell(25,8,$T2,1,0,'C');
    $pdf->Cell(25,8,$eot,1,0,'C');
    $pdf->Cell(25,8,$grade,1,0,'C');
    $pdf->Cell(30,8,$comment,1,1,'C');
}

// --- Best 5 + English ---
arsort($totals); // sort highest to lowest
$best5 = array_slice($totals, 0, 5);

$totalPoints = array_sum($best5);
$count = count($best5);

if ($englishMark !== null && $englishMark >= 40) {
    $totalPoints += $englishPoints;
    $count += 1;
}

// Aggregate = sum of ECZ points (lower = better)
$aggregate = ($count > 0) ? $totalPoints : 0;

// Optional GPA (international)
$gpa = ($count > 0) ? round(4 - (($aggregate / ($count*9)) * 4), 2) : 0;

// --- Certificate Result ---
if ($englishMark === null || $englishMark < 40 || $count === 0) {
    $certificateText = 'Fail';
} else {
    $certificateText = 'Pass';
}

// --- Totals, Aggregate, GPA, Position ---
$pdf->Ln(5);
$pdf->Cell(0,8,"AGGREGATE POINTS: $aggregate | GPA: $gpa",0,1,'R');

$currentRank = $rankings[$student['pupil_id']] ?? '-';
$totalStudents = count($studentTotalMarks);
$pdf->Cell(0,8,"POSITION: $currentRank / $totalStudents",0,1,'R');
$pdf->Ln(5);

$pdf->SetFont('Arial','B',10);
$pdf->Cell(30,8,'Statement:');
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,$certificateText,0,1);
$pdf->Ln(5);

// --- Date + signatures ---
$pdf->Ln(10);
$pdf->Cell(0,8,'Date Printed: '.date('d-m-Y'),0,1,'R');
$pdf->Ln(10);
$pdf->Cell(0,8,'Class Teacher Comment: _________________________________________________',0,1,'L');
$pdf->Cell(0,8,'Headteacher Comment: ___________________________________________________',0,1,'L');
$pdf->Cell(0,8,'Class Teacher Signature: _________________ Headteacher Signature:__________________',0,1,'L');

if ($stmt_marks !== null) $stmt_marks->close();
}

// -----------------------------
$pdf->Output('I', "ReportCards_{$class_name}_{$term_name}.pdf");
?>
