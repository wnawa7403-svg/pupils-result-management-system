<?php
require_once "../connection/connection.php";

// Fetch marks with LEFT JOIN to display all even if some data is missing
$query = "
SELECT 
    m.marks_id,
    m.subject_id,
    m.pupil_id,
    m.test_id,
    m.term_id,
    m.class_id,
    m.marks,
    m.remarks,
    
    s.subject_name,
    tst.test_name,
    trm.t_name AS term_name,
    p.first_name,
    p.last_name,
    c.class_name
FROM marks_tb m
LEFT JOIN pupils_tb p ON m.pupil_id = p.pupil_id
LEFT JOIN subjects s ON m.subject_id = s.subject_id
LEFT JOIN classes c ON m.class_id = c.class_id
LEFT JOIN terms trm ON m.term_id = trm.term_id
LEFT JOIN test tst ON m.test_id = tst.test_id
ORDER BY p.last_name, p.first_name, s.subject_name;
";

$result = mysqli_query($con, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Marks Table</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body >
    <br><br><br><br>
    <main class="col-xl-10     ml-sm-auto     mt-3">
 <?php include('../Main/Main_header.php') ?>
    <?php include('../Main/Main-sidebar.php') ?>
<div class="d-flex justify-content-between align-items-center   mb-3  ">
        
     <div class="col-md-10">
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class=" text-white font-weight-bold">
  
        <tr>
            <th>Pupils ID</th>
            <th>Pupil</th>
            <th>Class</th>
            <th>Subject</th>
           <th>Term</th>
           <th>Test </th>
            <th>Marks</th>
          
            <th>Remarks</th>
 
        </tr>
    </thead>
 
    <tbody>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['pupil_id']; ?></td>
            <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
            <td><?php echo htmlspecialchars($row['class_name']); ?></td>
            <td><?php echo htmlspecialchars($row['subject_name']); ?></td>

              <td><?php echo htmlspecialchars($row['term_name']); ?></td>
                <td><?php echo htmlspecialchars($row['test_name']); ?></td>
           <td><?php echo htmlspecialchars($row['marks']); ?></td>
          
            
            <td><?php echo htmlspecialchars($row['remarks']); ?></td>
             
        </tr>
    <?php } ?>
    </tbody>
</table>

</body>
</html>
