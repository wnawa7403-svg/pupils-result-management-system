<?php
require '../connection/connection.php';
require '../fpdf186/fpdf.php';

$query = "SELECT teacher_id, first_name, middle_name, last_name, email, teaching_number, gender 
          FROM teachers_tb ORDER BY teacher_id ASC";
$result = mysqli_query($con, $query);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,'Teachers List',0,1,'C');
$pdf->Ln(5);

// Table headers
$pdf->SetFont('Arial','B',10);
$pdf->Cell(25,10,'Man No',1,0,'C');
$pdf->Cell(30,10,'First Name',1,0,'C');
$pdf->Cell(30,10,'Middle Name',1,0,'C');
$pdf->Cell(30,10,'Last Name',1,0,'C');
$pdf->Cell(30,10,'Teach No',1,0,'C');
$pdf->Cell(20,10,'Gender',1,1,'C');

$pdf->SetFont('Arial','',10);
while ($row = mysqli_fetch_assoc($result)) {
    $pdf->Cell(25,8,$row['teacher_id'],1,0,'C');
    $pdf->Cell(30,8,$row['first_name'],1,0,'C');
    $pdf->Cell(30,8,$row['middle_name'],1,0,'C');
    $pdf->Cell(30,8,$row['last_name'],1,0,'C');
    $pdf->Cell(30,8,$row['teaching_number'],1,0,'C');
    $pdf->Cell(20,8,$row['gender'],1,1,'C');
}

$pdf->Output('D','Teachers_List.pdf');
?>
