<?php
session_start();
require_once "../connection/connection.php";

if (!isset($_SESSION['LoginAdmin'])) {
    header("Location: ../login/login.php");
    exit;
}

/* CLASS + STREAM + STUDENT COUNT */
$class_query = "
SELECT 
c.class_id,
CONCAT(c.class_name,' - ',s.stream_name) AS class_stream,
COUNT(p.pupil_id) AS total_students
FROM classes c
LEFT JOIN streams s ON s.stream_id=c.stream_id
LEFT JOIN pupils_tb p ON p.class_id=c.class_id
GROUP BY c.class_id
ORDER BY c.class_name ASC
";

$class_result = mysqli_query($con,$class_query);

/* TERMS */
$term_result = mysqli_query($con,"SELECT term_id,t_name FROM terms");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>QR Attendance System</title>

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- QR Scanner -->
<script src="https://unpkg.com/html5-qrcode"></script>

<style>
body { background:#f4f6f9; font-family:Poppins; }
.card { border:none; box-shadow:0 0 10px rgba(0,0,0,0.08); border-radius:10px; margin-bottom:20px; }
.card-header { background:#4e73df; color:white; font-weight:600; }
.present { color:green; font-weight:bold; }
.absent { color:red; font-weight:bold; }
.table-responsive{ overflow-x:auto; }
.status-box{ display:flex; gap:20px; flex-wrap:wrap; }
@media (max-width:768px){
    main{ margin-left:0 !important; width:100% !important; padding:15px !important; }
    .status-box{ flex-direction:column; gap:5px; }
}
#reader{ width:100%; max-width:400px; margin:0 auto 15px auto; }
</style>
</head>
<body>

<?php include('../Main/Main-sidebar.php'); ?>

<main style="margin-left:250px;width:calc(100% - 250px);" class="p-4">
<div class="container-fluid">
<div class="row g-3">

<!-- CLASS SELECTION -->
<div class="col-lg-4 col-md-12">
<div class="card">
<div class="card-header">Select Class</div>
<div class="card-body">
<form method="GET">
<label>Class</label>
<select name="class_id" class="form-control" required>
<option value="">Select Class</option>
<?php while($row=mysqli_fetch_assoc($class_result)){ ?>
<option value="<?php echo $row['class_id']; ?>" <?php if(isset($_GET['class_id']) && $_GET['class_id']==$row['class_id']) echo "selected"; ?>>
<?php echo $row['class_stream']; ?> (<?php echo $row['total_students']; ?> Pupils)
</option>
<?php } ?>
</select><br>
<label>Term</label>
<select name="term_id" class="form-control" required>
<option value="">Select Term</option>
<?php while($row=mysqli_fetch_assoc($term_result)){ ?>
<option value="<?php echo $row['term_id']; ?>" <?php if(isset($_GET['term_id']) && $_GET['term_id']==$row['term_id']) echo "selected"; ?>>
<?php echo $row['t_name']; ?>
</option>
<?php } ?>
</select><br>
<button class="btn btn-primary w-100">Search Students</button>
</form>
</div>
</div>
</div>

<!-- STUDENTS TABLE & QR SCANNER -->
<div class="col-lg-8 col-md-12">

<?php
if(isset($_GET['class_id']) && isset($_GET['term_id'])){
$class_id = $_GET['class_id'];
$term_id = $_GET['term_id'];

$stmt = mysqli_prepare($con,"
SELECT pupil_id, first_name, last_name 
FROM pupils_tb 
WHERE class_id=?
ORDER BY last_name
");
mysqli_stmt_bind_param($stmt,"i",$class_id);
mysqli_stmt_execute($stmt);
$students = mysqli_stmt_get_result($stmt);

$total = mysqli_num_rows($students);
if($total>0){
?>

<div class="card">
<div class="card-header">Mark Attendance</div>
<div class="card-body">

<!-- QR Scanner -->
<div id="reader"></div>
<div id="status" class="mb-3 text-center">Scan student QR code to mark present</div>

<!-- Mark All Present Button -->
<button class="btn btn-success mb-3 w-100 w-md-auto" onclick="markAllPresent()">Mark All Present</button>

<form method="POST" action="insert_attendance.php" id="attendanceForm">
<input type="hidden" name="class_id" value="<?php echo $class_id ?>">
<input type="hidden" name="term_id" value="<?php echo $term_id ?>">
<input type="hidden" name="date" value="<?php echo date('Y-m-d') ?>">

<div class="table-responsive">
<table class="table table-bordered">
<thead>
<tr>
<th>ID</th>
<th>Name</th>
<th>Status</th>
</tr>
</thead>
<tbody>
<?php while($row=mysqli_fetch_assoc($students)){ ?>
<tr>
<td><?php echo $row['pupil_id']; ?></td>
<td><?php echo $row['first_name']." ".$row['last_name']; ?></td>
<td>
<div class="status-box">
<label>
<input type="radio" name="attendance[<?php echo $row['pupil_id'] ?>]" value="1" class="present-radio"> <span class="present">Present</span>
</label>
<label>
<input type="radio" name="attendance[<?php echo $row['pupil_id'] ?>]" value="0"> <span class="absent">Absent</span>
</label>
</div>
</td>
</tr>
<?php } ?>
</tbody>
</table>
</div>

<button type="button" onclick="confirmSave()" class="btn btn-primary w-100">Save Attendance</button>
</form>
</div>
</div>

<?php }else{ echo "<div class='alert alert-warning'>No students found</div>"; } } ?>
</div>

</div>
</div>
</main>

<script>
// MARK ALL PRESENT
function markAllPresent(){
document.querySelectorAll(".present-radio").forEach(el=>{ el.checked=true; });
}

// CONFIRM SAVE
function confirmSave(){
Swal.fire({
title:'Save Attendance?',
icon:'question',
showCancelButton:true,
confirmButtonText:'Yes Save'
}).then((result)=>{
if(result.isConfirmed){
document.getElementById("attendanceForm").submit();
}
});
}

// QR Code Scanner
const scannedStudents = new Set();
function onScanSuccess(decodedText, decodedResult){
if(scannedStudents.has(decodedText)) return;
scannedStudents.add(decodedText);
const radio = document.querySelector(`input[name="attendance[${decodedText}]"][value="1"]`);
if(radio){
radio.checked = true;
document.getElementById('status').innerText = `Scanned: ${decodedText} - Marked Present`;
}
}

let html5QrcodeScanner = new Html5Qrcode("reader");
html5QrcodeScanner.start(
{ facingMode: "environment" },
{ fps: 10, qrbox: 250 },
onScanSuccess
);
</script>

<?php if(isset($_GET['success'])){ ?>
<script>
Swal.fire({ icon:'success', title:'Attendance Saved Successfully' });
</script>
<?php } ?>

<?php if(isset($_GET['duplicate'])){ ?>
<script>
Swal.fire({ icon:'warning', title:'Attendance Already Taken Today' });
</script>
<?php } ?>

</body>
</html>