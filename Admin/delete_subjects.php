<?php
session_start();
if (!isset($_SESSION["LoginAdmin"]) || !$_SESSION["LoginAdmin"]) {
    header('location:../login/login.php');
    exit;
}

require_once "../connection/connection.php";

function deleteSubjectSafely($con, $subject_id) {
    // Start transaction
    $con->begin_transaction();

    try {
        // 1. Delete all marks linked to this subject
        $stmt = $con->prepare("DELETE FROM marks_tb WHERE subject_id = ?");
        $stmt->bind_param("i", $subject_id);
        $stmt->execute();
        $stmt->close();

        // 2. Delete other child tables if needed
        // Example: delete from exams or other related tables
        // $stmt = $con->prepare("DELETE FROM exams WHERE subject_id = ?");
        // $stmt->bind_param("i", $subject_id);
        // $stmt->execute();
        // $stmt->close();

        // 3. Delete the subject itself
        $stmt = $con->prepare("DELETE FROM subjects WHERE subject_id = ?");
        $stmt->bind_param("i", $subject_id);
        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $con->commit();
        return true;

    } catch (Exception $e) {
        // Rollback on error
        $con->rollback();
        return $e->getMessage();
    }
}

// Usage example:
if (isset($_GET['subject_id'])) {
    $subject_id = intval($_GET['subject_id']);
    $result = deleteSubjectSafely($con, $subject_id);

    if ($result === true) {
        header("Location: add_subjects.php?msg=deleted");
        exit;
    } else {
        echo "Cannot delete this subject: " . $result;
        echo "<br>Redirecting back in 5 seconds...";
        header("refresh:5;url=add_subjects.php");
        exit;
    }
}
?>
