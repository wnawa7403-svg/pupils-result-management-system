<?php
require_once '../connection/connection.php';
require_once '../fpdf186/fpdf.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Error: This script must be accessed via POST method.");
}

$class_id = isset($_POST['class_id']) ? intval($_POST['class_id']) : 0;
$term_id  = isset($_POST['term_id']) ? intval($_POST['term_id']) : 0;

if ($class_id <= 0 || $term_id <= 0) die("Invalid class or term.");

// Fetch class name
// Fetch class name AND stream name
$stmt_class = $con->prepare("
    SELECT c.class_name, s.stream_name 
    FROM classes c
    JOIN streams s ON c.stream_id = s.stream_id
    WHERE c.class_id = ?
");
$stmt_class->bind_param("i", $class_id);
$stmt_class->execute();
$stmt_class->bind_result($class_name, $stream_name);
$stmt_class->fetch();
$stmt_class->close();

// Fetch term name
$stmt_term = $con->prepare("SELECT t_name FROM terms WHERE term_id=?");
$stmt_term->bind_param("i", $term_id);
$stmt_term->execute();
$stmt_term->bind_result($term_name);
$stmt_term->fetch();
$stmt_term->close();

// Get all pupils
$stmt_students = $con->prepare("SELECT pupil_id, first_name, last_name FROM pupils_tb WHERE class_id=? ORDER BY last_name ASC");
$stmt_students->bind_param("i", $class_id);
$stmt_students->execute();
$students_result = $stmt_students->get_result();

if ($students_result->num_rows === 0) die("No students found.");

// -----------------------------
// Precompute POINTS for ranking (not total marks)
// -----------------------------
$studentPoints = [];

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

    if (!isset($studentPoints[$pid])) {
        $studentPoints[$pid] = [
            'totals' => [],
            'english' => null,
        ];
    }

    if ($test === 'eot' && is_numeric($marks)) {
        // Calculate points for ALL subjects including English
        if ($marks >= 75) $points = 1;
        elseif ($marks >= 70) $points = 2;
        elseif ($marks >= 65) $points = 3;
        elseif ($marks >= 60) $points = 4;
        elseif ($marks >= 55) $points = 5;
        elseif ($marks >= 50) $points = 6;
        elseif ($marks >= 45) $points = 7;
        elseif ($marks >= 40) $points = 8;
        else $points = 9;

        if ($subject === 'english') {
            $studentPoints[$pid]['english'] = $points;
        } else {
            $studentPoints[$pid]['totals'][$subject] = $points;
        }
    }
}

// Compute total POINTS for each student
$studentTotalPoints = [];
foreach ($studentPoints as $pid => $data) {
    // Sort by lowest points (best subjects first)
    asort($data['totals']);
    
    // Take best 5 subjects
    $best5 = array_slice($data['totals'], 0, 5);
    $totalPoints = array_sum($best5);

    // Add English points (always include English)
    if ($data['english'] !== null) {
        $totalPoints += $data['english'];
    }

    $studentTotalPoints[$pid] = $totalPoints;
}

// Rank students based on POINTS (lower points = better rank)
asort($studentTotalPoints); // Sort by lowest points first
$rankings = [];
$rank = 1;
$lastPoints = null;
$skip = 0;

foreach ($studentTotalPoints as $pid => $points) {
    if ($lastPoints === null) {
        $rankings[$pid] = $rank;
    } elseif ($points == $lastPoints) {
        $rankings[$pid] = $rank;
        $skip++;
    } else {
        $rank += 1 + $skip;
        $skip = 0;
        $rankings[$pid] = $rank;
    }
    $lastPoints = $points;
}

// -----------------------------
// Generate report cards
// -----------------------------
$pdf = new FPDF();

while ($student = $students_result->fetch_assoc()) {
    $pdf->AddPage();

    // Header section
    $pdf->Rect(0, 0, 210, 28, 'D');
    
    // Logos
    $pdf->Image('../Admin/images/R.jpeg', 8, 4, 18);
    $pdf->Image('../Admin/images/R.jpeg', 184, 4, 18);

    // Header text
    $pdf->SetFont('Courier','B',12);
    $pdf->SetY(6);
    $pdf->Cell(0,5,'MINISTRY OF GENERAL EDUCATION',0,1,'C');
    $pdf->SetFont('Courier','B',10);
    $pdf->Cell(0,5,'MILEMU SECONDARY SCHOOL',0,1,'C');
    $pdf->SetFont('Courier','',8);
    $pdf->Cell(0,4,'P.O. BOX 124-001 | Copperbelt Province | Ndola Zambia',0,1,'C');

    $pdf->Ln(15);

    // Student Info Box
    $pdf->SetLineWidth(0.2);
    $pdf->Rect(8, $pdf->GetY(), 194, 16, 'D');
    $pdf->SetY($pdf->GetY() + 2);

    // Student Info
    $pdf->SetFont('Courier','B',10);
    $pdf->Cell(20,4,'NAME: ',0,0);
    $pdf->SetFont('Courier','',10);
    $pdf->Cell(65,4,$student['first_name'].' '.$student['last_name'],0,0);

    $pdf->SetFont('Courier','B',10);
    $pdf->Cell(15,4,'CLASS: ',0,0);
    $pdf->SetFont('Courier','',10);
     $pdf->Cell(25,4, $class_name . ' ' . $stream_name, 0, 0);
    

    $pdf->SetFont('Courier','B',10);
    $pdf->Cell(15,4,'TERM: ',0,0);
    $pdf->SetFont('Courier','',10);
    $pdf->Cell(20,4,$term_name,0,1);
    $pdf->Ln(10);

    // Performance Summary Box
    $pdf->Rect(10, $pdf->GetY(), 190, 20, 'D');
    $pdf->SetY($pdf->GetY() + 2);

    // Fetch marks
    $sql_marks = "SELECT s.subject_name, tt.test_name, m.marks, m.remarks
                  FROM marks_tb m
                  JOIN subjects s ON m.subject_id = s.subject_id
                  JOIN test tt ON m.test_id = tt.test_id
                  WHERE m.pupil_id=? AND m.class_id=? AND m.term_id=?
                  ORDER BY 
                    CASE 
                        WHEN LOWER(s.subject_name) = 'english' THEN 1
                        WHEN LOWER(s.subject_name) = 'mathematics' THEN 2
                        ELSE 3
                    END,
                    s.subject_name ASC, 
                    tt.test_name ASC";

    $stmt_marks = $con->prepare($sql_marks);
    $subjects = [];
    $englishMark = null;
    $mathematicsMark = null;

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

    // Calculate points and performance
    $totals = [];
    $englishPoints = null;

    foreach ($subjects as $subject => $tests) {
        $eot = $tests['eot']['marks'] ?? null;

        if (is_numeric($eot)) {
            // Determine points for ALL marks (including below 40)
            if ($eot >= 75) $points = 1;
            elseif ($eot >= 70) $points = 2;
            elseif ($eot >= 65) $points = 3;
            elseif ($eot >= 60) $points = 4;
            elseif ($eot >= 55) $points = 5;
            elseif ($eot >= 50) $points = 6;
            elseif ($eot >= 45) $points = 7;
            elseif ($eot >= 40) $points = 8;
            else $points = 9; // Points for marks below 40

            if (strtolower($subject) === 'english') {
                $englishPoints = $points; // Always store English points
            } else {
                $totals[$subject] = $points;
            }
        }
    }

    // Pick best 5 subjects (excluding English for now)
    asort($totals);
    $best5 = array_slice($totals, 0, 5);
    $totalPoints = array_sum($best5);
    $count = count($best5);

    // Add English points (ALWAYS add English points regardless of marks)
    if ($englishPoints !== null) {
        $totalPoints += $englishPoints;
        $count++;
    }

    // Average points
    $averagePoints = ($count > 0) ? round($totalPoints / $count, 2) : 0;

    // Get position from precomputed rankings based on points
    $currentRank = $rankings[$student['pupil_id']] ?? '-';
    $totalStudents = count($studentTotalPoints);

    // Determine STANDARD based on average points
    if ($averagePoints <= 1) $standard = "Distinction";
    elseif ($averagePoints <= 4) $standard = "Merit";
    elseif ($averagePoints <= 6) $standard = "Credit";
    elseif ($averagePoints <= 8) $standard = "Satisfactory";
    else $standard = "Unsatisfactory";

    // Performance metrics
    $pdf->SetFont('Courier','B',9);
    $pdf->Cell(20,4,'Statement:',0,0);
    $pdf->SetFont('Courier','',9);
    $pdf->Cell(12,4,'Pass',0,0);
    
    $pdf->SetFont('Courier','B',9);
$pdf->Cell(30,4,'Total Points:',0,0); // increased from 20 to 30
$pdf->SetFont('Courier','',9);
$pdf->Cell(12,4,$totalPoints,0,0);
    
    $pdf->SetFont('Courier','B',9);
    $pdf->Cell(18,4,'Average:',0,0);
    $pdf->SetFont('Courier','',9);
    $pdf->Cell(10,4,$averagePoints,0,0);
    
    $pdf->SetFont('Courier','B',9);
    $pdf->Cell(18,4,'Position:',0,0);
    $pdf->SetFont('Courier','',9);
    $pdf->Cell(12,4,$currentRank.' / '.$totalStudents,0,1);
    
    $pdf->SetFont('Courier','B',9);
    $pdf->Cell(18,4,'Standard:',0,0);
    $pdf->SetFont('Courier','',9);
    $pdf->Cell(20,4,$standard,0,0);
    
    $pdf->Ln(15);

    // Marks Table
    $pdf->SetFont('Courier','B',10);
    
    // Adjust column widths to fit everything
    $pdf->Cell(36,5,'SUBJECT',1,0,'C');
    $pdf->Cell(20,5,'TEST 1',1,0,'C');
    $pdf->Cell(20,5,'TEST 2',1,0,'C');
    $pdf->Cell(20,5,'EOT',1,0,'C');
    $pdf->Cell(20,5,'GRADE',1,0,'C');
    $pdf->Cell(36,5,'STANDARD',1,0,'C');
    $pdf->Cell(36,5,'COMMENTS',1,1,'C');
    
    $pdf->SetFont('Courier','',8);

    $rowCount = 0;
    $maxSubjectsPerPage = 9;
    
    foreach ($subjects as $subject => $tests) {
        if ($rowCount >= $maxSubjectsPerPage) break;

        $T1  = $tests['T1']['marks'] ?? '-';
        $T2  = $tests['T2']['marks'] ?? '-';
        $eot = $tests['eot']['marks'] ?? '-';
        $comment = $tests['eot']['remarks'] ?? '-';

        // Alternate row colors
        if ($rowCount % 2 == 0) {
            $pdf->SetFillColor(245, 245, 245);
        } else {
            $pdf->SetFillColor(255, 255, 255);
        }

        // Determine grade and standard
        if (is_numeric($eot)) {
            if ($eot >= 75) {
                $grade = "1";
                $standard = "Distinction";
            } elseif ($eot >= 70) {
                $grade = "2";
                $standard = "Distinction";
            } elseif ($eot >= 65) {
                $grade = "3";
                $standard = "Merit";
            } elseif ($eot >= 60) {
                $grade = "4";
                $standard = "Merit";
            } elseif ($eot >= 55) {
                $grade = "5";
                $standard = "Credit";
            } elseif ($eot >= 50) {
                $grade = "6";
                $standard = "Credit";
            } elseif ($eot >= 45) {
                $grade = "7";
                $standard = "Satisfactory";
            } elseif ($eot >= 40) {
                $grade = "8";
                $standard = "Satisfactory";
            } else {
                $grade = "9";
                $standard = "Unsatisfactory";
            }
        } elseif (strtoupper($eot) === "NE") {
            $grade = "NE";
            $standard = "-";
        } elseif (strtoupper($eot) === "AB") {
            $grade = "AB";
            $standard = "-";
        } else {
            $grade = "-";
            $standard = "-";
        }
        
        $pdf->SetFont('Courier','',10);
        $rowHeight = 9;

        $pdf->Cell(36,$rowHeight,$subject,1,0,'',true);
        $pdf->Cell(20,$rowHeight,$T1,1,0,'C',true);
        $pdf->Cell(20,$rowHeight,$T2,1,0,'C',true);
        $pdf->Cell(20,$rowHeight,$eot,1,0,'C',true);
        $pdf->Cell(20,$rowHeight,$grade,1,0,'C',true);
        $pdf->Cell(36,$rowHeight,$standard,1,0,'C',true);
        $pdf->Cell(36,$rowHeight,$comment,1,1,'C',true);
        
        $rowCount++;
    }

    // Get attendance directly from the database for a given student
$student_id = $student['pupil_id'];

$attendance_query = "
    SELECT 
        SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS present_days,
        SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) AS absent_days
    FROM attendance
    WHERE pupil_id = '$student_id'
      AND class_id = '$class_id'
      AND term_id = '$term_id'
";

$attendance_result = mysqli_query($con, $attendance_query);

if($attendance_result) {
    $attendance = mysqli_fetch_assoc($attendance_result);
    $present = $attendance['present_days'] ?? 0;
    $absent = $attendance['absent_days'] ?? 0;
} else {
    $present = 0;
    $absent = 0;
}

// Output in PDF
$pdf->Ln(3);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(0,5,"ATTENDANCE: Present $present days | Absent $absent days",0,1,'L');
$pdf->Ln(2);
    
    // Head Teacher Comment
    $pdf->SetFont('Courier','B',10);
    $pdf->Cell(40,4,'Head Teacher Comment: ',0,1);
    $pdf->SetFont('Courier','',8);
    
    if ($averagePoints <= 2) $commentText = "Excellent performance. Keep up the outstanding work!";
    elseif ($averagePoints <= 4) $commentText = "Good work. Continue striving for excellence.";
    elseif ($averagePoints <= 6) $commentText = "Satisfactory performance. More effort needed to improve.";
    else $commentText = "Needs significant improvement. Must work much harder.";
    
    $pdf->MultiCell(0,3,$commentText);
    $pdf->Ln(1);

    // Class Teacher Comment
    $pdf->SetFont('Courier','B',10);
    $pdf->Cell(40,4,'Class Teacher Comment: ',0,1);
    $pdf->SetFont('Courier','',8);
    
    if ($present == 0) $teacherComment = "Very poor attendance. Urgent improvement required.";
    elseif ($absent > ($present/2)) $teacherComment = "Attendance below expectations. Regular attendance is crucial for success.";
    else $teacherComment = "Good attendance record. Maintain this consistency.";
    
    $pdf->MultiCell(0,3,$teacherComment);
    $pdf->Ln(2);

    // Signatures
    $pdf->SetFont('Courier','',8);
    $pdf->Cell(95,4,'Class Teacher Signature: ___________________',0,0,'L');
    $pdf->Cell(95,4,'Headteacher Signature: ___________________',0,1,'L');
    
    $pdf->Ln(6);
    
    // Date of Issue
    $pdf->Cell(0,4,'Date of Issue: '.date('d-m-Y'),0,1,'R');

    // Grading System at bottom
    $pdf->SetY(-106);
    $pdf->SetFont('Courier','B',9);
    $pdf->Cell(90,4,'GRADING SYSTEM',0,1,'C');
    
    // Table Header
    $pdf->SetFont('Courier','B',7);
    $pdf->Cell(25,4,'Marks (%)',1,0,'C');
    $pdf->Cell(20,4,'Grade',1,0,'C');
    $pdf->Cell(45,4,'Standard',1,1,'C');

    // Table Content
    $pdf->SetFont('Courier','',10);
    $grades = [
        ['75-100','1','Distinction'],
        ['70-74','2','Distinction'],
        ['65-69','3','Merit'],
        ['60-64','4','Merit'],
        ['55-59','5','Credit'],
        ['50-54','6','Credit'],
        ['45-49','7','Satisfactory'],
        ['40-44','8','Satisfactory'],
        ['0-39','9','Unsatisfactory'],
    ];

    $rowHeight = 8;

    foreach ($grades as $row) {
        $pdf->Cell(25,$rowHeight,$row[0],1,0,'C');
        $pdf->Cell(20,$rowHeight,$row[1],1,0,'C');
        $pdf->Cell(45,$rowHeight,$row[2],1,1,'C');
    }

    if ($stmt_marks !== null) $stmt_marks->close();
}

$pdf->Output('I', "ReportCards_{$class_name}_{$term_name}.pdf");
?>