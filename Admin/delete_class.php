<?php  
session_start();
if (!$_SESSION["LoginAdmin"]) {
    header('location:../login/login.php');
    exit;
}

require_once "../connection/connection.php";

if (isset($_GET['class_id'])) {
    $class_id = intval($_GET['class_id']); // sanitize input

    // Delete the test by test_id
    $query1 = "DELETE FROM classes WHERE class_id = '$class_id'";
    $run1 = mysqli_query($con, $query1);

    if ($run1) {
        header('Location: class.php');
        exit;
    } else {
        echo "class not deleted. First delete dependent records from child tables, then try again.";
        header('Refresh: 5; url=class.php');
        exit;
    }
} else {
    echo "No class provided.";
    header('Refresh: 3; url=class.php');
    exit;
}
?>
