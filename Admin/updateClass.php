<?php
session_start();

// Check if the user is an admin
if (!isset($_SESSION["LoginAdmin"]) || !$_SESSION["LoginAdmin"]) {
    header('location:../login/login.php');
    exit;
}

require_once "../connection/connection.php";

// Get the class_id from the URL
$class_id = isset($_GET['class_id']) ? $_GET['class_id'] : die('ERROR: Record ID not found.');

if (isset($_POST['className']) && !empty($_POST['class_status']) && isset($_POST['teacher_id'])) {
    // Get data from POST request
    $className = $_POST["className"];
    $teacher_id = $_POST["teacher_id"];
    $class_status = $_POST["class_status"];

    // Use a prepared statement for security
    $update_query = "UPDATE classes SET className=?, teacher_id=?, class_status=? WHERE class_id=?";
    $stmt = mysqli_prepare($con, $update_query);

    // Bind parameters
    mysqli_stmt_bind_param($stmt, "sssi", $className, $teacher_id, $class_status, $class_id);

    // Execute the query
    if (mysqli_stmt_execute($stmt)) {
        // Now update the className in other related tables (e.g., students, assignments)
        $update_students_query = "UPDATE pupils_tb SET className=? WHERE className=?";
        $stmt_students = mysqli_prepare($con, $update_students_query);
        mysqli_stmt_bind_param($stmt_students, "ss", $className, $_POST['old_className']); // Assuming you pass the old className in POST
        mysqli_stmt_execute($stmt_students);

        // If you have other tables like assignments that reference className, do the same
        $update_assignments_query = "UPDATE testOne SET className=? WHERE className=?";
        $stmt_assignments = mysqli_prepare($con, $update_assignments_query);
        mysqli_stmt_bind_param($stmt_assignments, "ss", $className, $_POST['old_className']); // Assuming you pass the old className in POST
        mysqli_stmt_execute($stmt_assignments);

        echo "<script>alert('Class updated successfully, and related records have been updated.'); window.location.href='display_classes.php';</script>";
    } else {
        echo "<script>alert('Error updating class: " . mysqli_error($con) . "');</script>";
    }
} else {
    // Fetch the current class data to pre-fill the form
    $result = mysqli_query($con, "SELECT * FROM classes WHERE class_id=$class_id");
    while ($res = mysqli_fetch_array($result)) {
        $className = $res['className'];
        $teacher_id = $res['teacher_id'];
        $class_status = $res['class_status'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Class</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Edit Class</h2>

        <form action="updateClass.php?class_id=<?php echo $class_id; ?>" method="post">
            <div class="form-group">
                <label for="className">Class Name:</label>
                <input type="text" class="form-control" id="className" name="className" value="<?php echo $className; ?>" required>
            </div>

            <div class="form-group">
                <label for="teacher_id">Select Teacher:</label>
                <select name="teacher_id" class="form-control" required>
                    <option value="">Select Teacher</option>
                    <?php
                    // Fetch teachers from the database
                    $teacher_query = "SELECT teacher_id, last_name FROM teacher_tb";
                    $teacher_result = mysqli_query($con, $teacher_query);

                    while ($teacher = mysqli_fetch_assoc($teacher_result)) {
                        // Pre-select the teacher if it's the one assigned to the class
                        echo '<option value="' . $teacher['teacher_id'] . '" ' . ($teacher['teacher_id'] == $teacher_id ? 'selected' : '') . '>' . $teacher['last_name'] . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="class_status">Status:</label>
                <select name="class_status" class="form-control" required>
                    <option value="Active" <?php echo ($class_status == 'Active' ? 'selected' : ''); ?>>Active</option>
                    <option value="Inactive" <?php echo ($class_status == 'Inactive' ? 'selected' : ''); ?>>Inactive</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update Class</button>
            <a href="display_classes.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
