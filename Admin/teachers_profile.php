<?php
require_once "../connection/connection.php";

if (!isset($_GET['teacher_id'])) {
    die("No teacher selected");
}

$teacher_id = $_GET['teacher_id'];

$stmt = $con->prepare("SELECT * FROM teachers_tb WHERE teacher_id = ?");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();

if (!$teacher) {
    die("Teacher not found");
}

$image = !empty($teacher['profile_image']) ? $teacher['profile_image'] : "default.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Teacher Profile</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

<style>
body {
    background-color: #f4f6f9;
    font-family: 'Segoe UI', sans-serif;
}

.profile-container {
    max-width: 900px;
    margin: 40px auto;
}

.profile-card {
    background: #fff;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    padding: 30px;
}

.profile-header {
    text-align: center;
    border-bottom: 1px solid #eee;
    padding-bottom: 20px;
    margin-bottom: 25px;
}

.profile-img {
    width: 130px;
    height: 130px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #ddd;
}

.profile-name {
    font-size: 22px;
    font-weight: 600;
    margin-top: 10px;
}

.profile-role {
    color: #6c757d;
    font-size: 14px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #f1f1f1;
}

.info-label {
    font-weight: 600;
    color: #555;
}

.info-value {
    color: #333;
}

.actions {
    margin-top: 25px;
    text-align: right;
}
</style>
</head>

<body>
 
<?php include('../Main/Main-sidebar.php') ?>
<div class="profile-container">
    <div class="profile-card">

        <!-- Header -->
        <div class="profile-header">
            <img src="images/<?php echo $image; ?>" class="profile-img">
            <div class="profile-name">
                <?php echo $teacher['first_name']." ".$teacher['middle_name']." ".$teacher['last_name']; ?>
            </div>
            <div class="profile-role">
                <?php echo $teacher['role']; ?>
            </div>
        </div>

        <!-- Info Section -->
        <div class="info-row">
            <div class="info-label">Teacher ID</div>
            <div class="info-value"><?php echo $teacher['teacher_id']; ?></div>
        </div>

        <div class="info-row">
            <div class="info-label">Gender</div>
            <div class="info-value"><?php echo $teacher['gender']; ?></div>
        </div>

        <div class="info-row">
            <div class="info-label">Email</div>
            <div class="info-value"><?php echo $teacher['email']; ?></div>
        </div>

        <div class="info-row">
            <div class="info-label">TCZ Number</div>
            <div class="info-value"><?php echo $teacher['Teaching_number']; ?></div>
        </div>

        <div class="info-row">
            <div class="info-label">Role</div>
            <div class="info-value"><?php echo $teacher['role']; ?></div>
        </div>

        <!-- Actions -->
        <div class="actions">
            <a href="instructor.php" class="btn btn-light btn-sm">Back</a>
            <a href="edit_teacher.php?teacher_id=<?php echo $teacher_id; ?>" class="btn btn-dark btn-sm">Edit</a>
        </div>

    </div>
</div>

</body>
</html>