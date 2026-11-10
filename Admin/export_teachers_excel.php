<?php
require '../connection/connection.php';

// Set headers so browser downloads as Excel-compatible CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="Teachers_List.csv"');

// Open output stream
$output = fopen("php://output", "w");

// Column headers
fputcsv($output, [
    'Man Number', 
    'First Name', 
    'Middle Name', 
    'Last Name', 
    'Email', 
    'Teaching Number', 
    'Gender'
]);

// Query teachers table
$query = "SELECT teacher_id, first_name, middle_name, last_name, email, teaching_number, gender 
          FROM teachers_tb 
          ORDER BY teacher_id ASC";

$result = mysqli_query($con, $query);

// Output each row as CSV
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row['teacher_id'],
            $row['first_name'],
            $row['middle_name'],
            $row['last_name'],
            $row['email'],
            $row['teaching_number'],
            $row['gender']
        ]);
    }
}

fclose($output);
exit;
?>
