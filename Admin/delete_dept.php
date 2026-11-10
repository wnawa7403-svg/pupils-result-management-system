<?php
session_start();
if (!isset($_SESSION["LoginAdmin"]) || !$_SESSION["LoginAdmin"]) {
    header('location:../login/login.php');
    exit;
}

require_once "../connection/connection.php";

$dept_id = $_GET['dept_id'] ?? '';
if (!$dept_id) {
    die("Department ID is required.");
}

// Optional: check if the dept exists
$check = mysqli_query($con, "SELECT * FROM dept WHERE dept_id = '$dept_id'");
if (mysqli_num_rows($check) == 0) {
    die("Department not found.");
}

// Delete department
$delete_stmt = mysqli_prepare($con, "DELETE FROM dept WHERE dept_id = ?");
mysqli_stmt_bind_param($delete_stmt, "i", $dept_id);
if (mysqli_stmt_execute($delete_stmt)) {
    mysqli_stmt_close($delete_stmt);
    // Redirect back to department list with success message
    header("Location: departments.php?msg=deleted");
    exit;
} else {
    $error = mysqli_stmt_error($delete_stmt);
    mysqli_stmt_close($delete_stmt);
    die("Error deleting department: " . $error);
}
?>
