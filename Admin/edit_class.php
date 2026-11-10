<?php
session_start();
if (!isset($_SESSION["LoginAdmin"]) || !$_SESSION["LoginAdmin"]) {
    header('location:../login/login.php');
    exit;
}

require_once "../connection/connection.php";

if (!isset($_GET['class_id'])) {
    echo "<script>alert('Class ID not specified!'); window.location.href='class.php';</script>";
    exit();
}

$class_id = $_GET['class_id'];

$success_message = '';
$error_message = '';

// Handle form submission
if (isset($_POST['btn_edit_class'])) {
    $class_name   = $_POST["class_name"];
    $teacher_id   = $_POST["teacher_id"];
    $level = $_POST["level"];

    $stmt = $con->prepare("UPDATE classes SET class_name=?, teacher_id=?, level=? WHERE class_id=?");
    if ($stmt === false) die("Prepare failed: " . $con->error);

    $stmt->bind_param("sisi", $class_name, $teacher_id, $level, $class_id);

    if ($stmt->execute()) {
         $_SESSION['success_message'] = " class Updated successfully!";
    header("Location: class.php");
    exit;
    } else {
        $error_message = "Error updating class: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch class data
$stmt = $con->prepare("SELECT * FROM classes WHERE class_id=?");
$stmt->bind_param("i", $class_id);
$stmt->execute();
$result = $stmt->get_result();
$class_row = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Class</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
 
<?php include('../Main/Main-sidebar.php'); ?>

<main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 w-100">
    
    <div class="container mt-5 mb-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3>Edit Class</h3>
            </div>
            <div class="card-body">
            
                <form action="edit_class.php?class_id=<?php echo $class_id; ?>" method="post">
                    <div class="form-group">
                        <label>Class Name:</label>
                        <input type="text" class="form-control" name="class_name" value="<?php echo htmlspecialchars($class_row['class_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Select Teacher:</label>
                        <select class="form-control" name="teacher_id" required>
                            <?php
                            $query = "SELECT * FROM teachers_tb where role='Class teacher'";
                            $result = mysqli_query($con, $query);
                            while ($teacher = mysqli_fetch_assoc($result)) {
                                $selected = ($class_row['teacher_id'] == $teacher['teacher_id']) ? 'selected' : '';
                                echo '<option value="' . $teacher['teacher_id'] . '" ' . $selected . '>' . ucwords($teacher['last_name']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Level:</label>
                        <select class="form-control" name="level" required>
                            <option value="Junior" <?php if ($class_row['level'] == 'Junior') echo 'selected'; ?>>Junior</option>
                            <option value="Senior" <?php if ($class_row['level'] == 'Senior') echo 'selected'; ?>>Senior</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="reset" class="btn btn-outline-secondary">Reset</button>
                        <button type="submit" class="btn btn-primary" name="btn_edit_class">Update Class</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
</body>
</html>
