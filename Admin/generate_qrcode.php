<?php
session_start();
require_once "../connection/connection.php";

// Ensure admin is logged in
if(!isset($_SESSION['LoginAdmin'])){
    header("Location: ../login/login.php");
    exit;
}

// Include PHP QR Code library
require 'phpqrcode/qrlib.php'; // Adjust path

// Folder to save QR codes
$folder = 'qrcodes/';
if(!is_dir($folder)) mkdir($folder, 0777, true);

// Fetch all students with class and stream
$query = "
SELECT p.pupil_id, p.first_name, p.last_name, 
c.class_name, s.stream_name
FROM pupils_tb p
LEFT JOIN classes c ON c.class_id=p.class_id
LEFT JOIN streams s ON s.stream_id=c.stream_id
ORDER BY c.class_name, s.stream_name, p.last_name
";
$result = mysqli_query($con, $query);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Generate Student QR Codes</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<style>
body{ background:#f4f6f9; font-family:Poppins; }
.card{ border:none; box-shadow:0 0 10px rgba(0,0,0,0.08); margin:15px; padding:15px; border-radius:10px; text-align:center; }
.card img{ width:150px; height:150px; margin-bottom:10px; }
.cards-container{ display:flex; flex-wrap:wrap; justify-content:center; }
@media print {
    body{ background:white; }
    .card{ page-break-inside: avoid; }
}
</style>
</head>
<body>

<div class="container mt-4">
<h2 class="text-center mb-4">Student QR Codes</h2>
<div class="cards-container">

<?php while($row=mysqli_fetch_assoc($result)) { 
    $pupil_id = $row['pupil_id'];
    $name = $row['first_name'].' '.$row['last_name'];
    $class_stream = $row['class_name'].' - '.$row['stream_name'];
    $filename = $folder.$pupil_id.'.png';

    // Generate QR code if it doesn't exist
    if(!file_exists($filename)){
        QRcode::png($pupil_id, $filename, QR_ECLEVEL_L, 4);
    }
?>
<div class="card">
    <h5><?php echo htmlspecialchars($name); ?></h5>
    <p><?php echo htmlspecialchars($class_stream); ?></p>
    <img src="<?php echo $filename; ?>" alt="QR Code">
</div>
<?php } ?>

</div>

<div class="text-center mt-4">
<button class="btn btn-primary" onclick="window.print()">Print All QR Codes</button>
</div>
</div>

</body>
</html>