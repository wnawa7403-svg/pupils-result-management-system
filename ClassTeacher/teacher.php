<?php 
session_start();
if (!isset($_SESSION['LoginClassTeacher'])) {
    header('Location: ../Login/login.php');
    exit();
}
$teacher_id = $_SESSION['LoginClassTeacher'];

require_once "../connection/connection.php";

$user_id = $_SESSION['LoginClassTeacher'];

// Fetch teacher info
$query = "SELECT teacher_id, first_name, middle_name, last_name, email, Teaching_number, gender, date 
          FROM teachers_tb 
          WHERE email = ? 
          LIMIT 1";
$stmt = $con->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Teacher record not found.");
}
$teacher = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-light">

<?php include('../Main/class-teacher-sidebar.php'); ?>

<main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 w-100">

    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3>Welcome, <?php echo $teacher['first_name']; ?>!</h3>
            </div>
            <div class="card-body">
                <h4>My Profile</h4>
                <table class="table table-striped">
                    <tr>
                        <th>Teacher ID</th>
                        <td><?php echo $teacher['teacher_id']; ?></td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td><?php echo $teacher['first_name'] . " " . $teacher['middle_name'] . " " . $teacher['last_name']; ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?php echo $teacher['email']; ?></td>
                    </tr>
                    <tr>
                        <th>TCZ Number</th>
                        <td><?php echo $teacher['Teaching_number']; ?></td>
                    </tr>
                    <tr>
                        <th>Gender</th>
                        <td><?php echo $teacher['gender']; ?></td>
                    </tr>
                    <tr>
                        <th>Date Added</th>
                        <td><?php echo $teacher['date']; ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

</main>
</body>
</html>
