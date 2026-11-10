<?php
session_start();

// Login check
if (!isset($_SESSION["LoginAdmin"]) || !$_SESSION["LoginAdmin"]) {
    header("Location: ../login/login.php");
    exit;
}

require_once '../connection/connection.php';

// Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "Invalid assignment ID.";
    header("Location: assign_teacher.php");
    exit;
}

$assignment_id = (int)$_GET['id'];

// Check if assignment exists
$check = $con->prepare("
    SELECT ts.id, t.first_name, t.last_name, s.subject_name, f.class_name
    FROM teacher_subjects ts
    JOIN teachers_tb t ON ts.teacher_id = t.teacher_id
    JOIN subject_forms sf ON ts.subject_form_id = sf.id
    JOIN subjects s ON sf.subject_id = s.subject_id
    JOIN classes f ON sf.class_id = f.class_id
    WHERE ts.id = ?
");
$check->bind_param("i", $assignment_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Assignment not found.";
    header("Location: assign_teacher.php");
    exit;
}

$assignment = $result->fetch_assoc();
$check->close();

// Delete assignment
$delete = $con->prepare("DELETE FROM teacher_subjects WHERE id = ?");
$delete->bind_param("i", $assignment_id);

if ($delete->execute()) {
    $_SESSION['success_message'] =
        "Removed {$assignment['first_name']} {$assignment['last_name']} from " .
        "{$assignment['subject_name']} ({$assignment['class_name']}).";
} else {
    $_SESSION['error_message'] = "Failed to delete assignment.";
}

$delete->close();

// Redirect
header("Location: assign_teacher.php");
exit;
