<?php
require_once "../connection/connection.php";
require_once "../fpdf186/fpdf.php";  

// Get class_id and term_id from GET
$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$term_id  = isset($_GET['term_id']) ? (int)$_GET['term_id'] : 0;

if ($class_id <= 0 || $term_id <= 0) {
    die("Error: Invalid class or term!");
}

// Fetch class name
$class_result = mysqli_query($con, "SELECT class_name FROM classes WHERE class_id = $class_id LIMIT 1");
$class_name = mysqli_fetch_assoc($class_result)['class_name'] ?? 'Unknown Class';

// Fetch term name
$term_result = mysqli_query($con, "SELECT t_name FROM terms WHERE term_id = $term_id LIMIT 1");
$term_name = mysqli_fetch_assoc($term_result)['t_name'] ?? 'Unknown Term';

// Fetch attendance for pupils in selected class and term
$query = "SELECT p.pupil_id, p.first_name, p.last_name, a.status, a.day, a.date
          FROM pupils_tb p
          LEFT JOIN attendance a
          ON p.pupil_id = a.pupil_id 
             AND a.class_id = ? 
             AND a.term_id = ?
          WHERE p.class_id = ?
          ORDER BY p.first_name, a.date";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "iii", $class_id, $term_id, $class_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,"Attendance Report -  $class_name,  $term_name",0,1,'C');
$pdf->Ln(5);

// Table header
$pdf->SetFont('Arial','B',12);
$pdf->Cell(30,10,'Pupil ID',1,0,'C');
$pdf->Cell(70,10,'Name',1,0,'C');
$pdf->Cell(30,10,'Status',1,0,'C');
$pdf->Cell(30,10,'Day',1,0,'C');
$pdf->Cell(30,10,'Date',1,1,'C');

// Table body
$pdf->SetFont('Arial','',12);
$presentCount = 0;
$absentCount = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $statusText = isset($row['status']) ? (($row['status'] == 1) ? ' Present' : ' Absent') : 'Not marked';
    if ($row['status'] == 1) $presentCount++;
    elseif ($row['status'] == 0) $absentCount++;

    $pdf->Cell(30,10,$row['pupil_id'],1,0,'C');
    $pdf->Cell(70,10,utf8_decode($row['first_name'].' '.$row['last_name']),1,0,'C');
    $pdf->Cell(30,10,$statusText,1,0,'C');
    $pdf->Cell(30,10,$row['day'] ?? '-',1,0,'C');
    $pdf->Cell(30,10,$row['date'] ?? '-',1,1,'C');
}

// Summary
$pdf->Ln(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,);


// Summary
$pdf->Ln(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,"Summary: $presentCount Present, $absentCount Absent",0,1,'L');

$pdf->Output("I","Attendance_Class_{$class_id}_Term_{$term_id}.pdf");
?>
