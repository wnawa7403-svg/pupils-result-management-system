<?php
session_start();
require_once "../connection/connection.php";

if (!isset($_SESSION['LoginTeacher'])) exit;

$teacher_email = $_SESSION['LoginTeacher'];
$stmt = $con->prepare("SELECT teacher_id FROM teachers_tb WHERE email=? LIMIT 1");
$stmt->bind_param("s", $teacher_email);
$stmt->execute();
$res = $stmt->get_result();
$teacher_id = $res->fetch_assoc()['teacher_id'];

$class_id = $_GET['class_id'] ?? 0;

$options = '<option value="">Select Subject</option>';

if ($class_id) {
    $stmt = $con->prepare("
        SELECT DISTINCT s.subject_id, s.subject_name
        FROM subjects s
        JOIN subject_forms sf ON sf.subject_id = s.subject_id
        JOIN teacher_subjects ts ON ts.subject_form_id = sf.id
        WHERE ts.teacher_id=? AND sf.class_id=?
        ORDER BY s.subject_name
    ");
    $stmt->bind_param("ii", $teacher_id, $class_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while($row=$res->fetch_assoc()){
        $options .= "<option value='{$row['subject_id']}'>{$row['subject_name']}</option>";
    }
}

echo $options;
