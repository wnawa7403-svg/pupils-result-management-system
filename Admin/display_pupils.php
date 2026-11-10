<?php
session_start();
if (!$_SESSION["LoginAdmin"]) {
    header('location:../login/login.php');
    exit;
}
require_once "../connection/connection.php";

// Handle Add Pupil

 
// Fetch pupils
$query = "SELECT p.pupil_id, p.first_name, p.last_name, p.other_name, p.DOB, p.gender, 
                p.RegDate, p.address, p.parents, p.year, p.parents_no, p.profile_image, 
                c.class_name
          FROM pupils_tb p
          LEFT JOIN classes c ON p.class_id = c.class_id
          ORDER BY p.pupil_id ASC";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Pupils</title>

<!-- Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- DataTables CSS & Buttons -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<!-- Custom CSS -->
<link rel="stylesheet" href="../Css/style.css">
<style>

</style>

</style>
</head>
<body>
 
<?php include('../Main/Main-sidebar.php') ?>

<main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 mb-2 w-100">
<div class=" card header text-center dashboard-header mt-4">
    <h2>Pupils List</h2>  </div>
<div class="card mt-3"> 

<!-- Pupils Table -->
 <div class="card">
<div class="table-responsive">
<table class="table table-hover" id="pupilsTable">
    <thead class= "text-black">
        <tr>
            <th>PUPIL ID</th>
            <th>FULL NAME</th>
            <th>CLASS</th>
            <th>GENDER</th>
            <th>ADDRESS</th>
            <th>PARENTS</th>
            <th>YEAR</th>
            <th>DATE OF BIRTH</th>
            
        </tr>
    </thead>
    <tbody>
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td class="text-center align-middle"><?= $row['pupil_id']; ?></td>
                <td class="align-middle"><?= ucwords($row['first_name'] . ' ' . $row['other_name'] . ' ' . $row['last_name']); ?></td>
                <td class="text-center align-middle"><?= ucwords($row['class_name']); ?></td>
                <td class="text-center align-middle"><?= ucwords($row['gender']); ?></td>
                <td class="align-middle"><?= ucwords($row['address']); ?></td>
                <td class="align-middle"><?= ucwords($row['parents']); ?></td>
                <td class="text-center align-middle"><?= $row['year']; ?></td>
                <td class="text-center align-middle"><?= !empty($row['DOB']) ? date('d-m-Y', strtotime($row['DOB'])) : 'N/A'; ?></td>
                 
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="9" class="text-center">No records found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
</div>
</div>

</div>
</div>
</div>

<!-- JS Scripts -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script>
$(document).ready(function() {
    // DataTables with buttons
    $('#pupilsTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        responsive: true,
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        dom: '<"d-flex justify-content-between align-items-center mb-3"Bfl>rt<"d-flex justify-content-between"ip>',
        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-secondary' },
            { extend: 'excel', className: 'btn btn-sm btn-success' },
            { extend: 'pdf', className: 'btn btn-sm btn-danger' },
            { extend: 'print', className: 'btn btn-sm btn-primary' }
        ]
    });

    // Search input
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("table tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Search button
    $("#searchButton").click(function() {
        $("#searchInput").keyup(); 
    });
});
</script>

     
   
</main>
</body>
</html>
