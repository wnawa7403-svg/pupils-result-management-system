<?php
session_start();
require_once "../connection/connection.php";
header('Content-Type: application/json');

$teacher_email = $_SESSION['LoginTeacher'] ?? '';
if(!$teacher_email) exit(json_encode(['status'=>'Error','message'=>'Not logged in','icon'=>'error']));

// Teacher ID
$stmt = $con->prepare("SELECT teacher_id FROM teachers_tb WHERE email=? LIMIT 1");
$stmt->bind_param("s", $teacher_email);
$stmt->execute();
$res = $stmt->get_result();
$teacher_id = $res->fetch_assoc()['teacher_id'] ?? 0;

$pupil_ids = $_POST['pupil_id'] ?? [];
$marks_vals = $_POST['marks'] ?? [];
$class_id = (int) ($_POST['class_id'] ?? 0);
$subject_id = (int) ($_POST['subject_id'] ?? 0);
$term_id = (int) ($_POST['term_id'] ?? 0);
$test_id = (int) ($_POST['test_id'] ?? 0);

if(!$class_id || !$subject_id || !$term_id || !$test_id) 
    exit(json_encode(['status'=>'Error','message'=>'Invalid submission','icon'=>'error']));

// Get year
$stmt = $con->prepare("SELECT year FROM terms WHERE term_id=? LIMIT 1");
$stmt->bind_param("i", $term_id);
$stmt->execute();
$res = $stmt->get_result();
$year = $res->fetch_assoc()['year'] ?? 0;

// Prepare statements
$stmt_check = $con->prepare("SELECT marks_id FROM marks_tb WHERE pupil_id=? AND class_id=? AND subject_id=? AND test_id=? AND term_id=? LIMIT 1");
$stmt_update = $con->prepare("UPDATE marks_tb SET marks=?, remarks=? WHERE marks_id=?");
$stmt_insert = $con->prepare("INSERT INTO marks_tb (pupil_id,class_id,subject_id,test_id,term_id,marks,remarks,entered_by) VALUES (?,?,?,?,?,?,?,?)");

$saved = 0;

for($i=0;$i<count($pupil_ids);$i++){
    $pupil_id = (int) $pupil_ids[$i];
    $marks = strtoupper(trim($marks_vals[$i]));
    if($marks==='') continue;

    if($marks==='AB') $remarks='ABSENT';
    elseif($marks==='NE') $remarks='NOT EXAMINED';
    elseif(is_numeric($marks)){
        $m=floatval($marks);
        if($m<0 || $m>100) $remarks='INVALID';
        elseif($m<30) $remarks='FAILED';
        elseif($m<=40) $remarks='PASS';
        elseif($m<=50) $remarks='CREDIT';
        elseif($m<=60) $remarks='GOOD';
        elseif($m<=80) $remarks='VERY GOOD';
        else $remarks='EXCELLENT';
        $marks=$m;
    } else $remarks='INVALID';

    // Check existing
    $stmt_check->bind_param("iiiii",$pupil_id,$class_id,$subject_id,$test_id,$term_id);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();

    if($row=$res_check->fetch_assoc()){
        $stmt_update->bind_param("ssi",$marks,$remarks,$row['marks_id']);
        $stmt_update->execute();
    } else {
        $stmt_insert->bind_param("iiiiissi",$pupil_id,$class_id,$subject_id,$test_id,$term_id,$marks,$remarks,$teacher_id);
        $stmt_insert->execute();
    }
    $saved++;
}

echo json_encode(['status'=>'Success','message'=>"Saved marks for $saved student(s)","icon"=>"success"]);
?>