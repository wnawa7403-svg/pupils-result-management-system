<?php
session_start();
require_once "../connection/connection.php";

// Ensure Admin is logged in
if (!isset($_SESSION['LoginAdmin']) || !$_SESSION['LoginAdmin']) {
    header('Location: ../login/login.php');
    exit;
}

if (isset($_POST['submit_marks'])) {

    $user        = $_POST['user'] ?? '';
    $year        = intval($_POST['year'] ?? 0);
    $test_id     = intval($_POST['test_id'] ?? 0);
    $term_id     = intval($_POST['term_id'] ?? 0);
    $class_id    = intval($_POST['class_id'] ?? 0);
    $subject_id  = intval($_POST['subject_id'] ?? 0);

    $pupil_ids   = $_POST['pupil_id'] ?? [];
    $marks_vals  = $_POST['marks'] ?? [];

    if (!$year || !$test_id || !$term_id || !$class_id || !$subject_id) {
        die("Invalid form submission. Missing required fields.");
    }

    for ($i = 0; $i < count($pupil_ids); $i++) {
        $pupil_id = intval($pupil_ids[$i]);
        $marks    = strtoupper(trim($marks_vals[$i]));
        $remarks  = "";

        // Determine remarks
        if ($marks === "AB") {
            $remarks = "ABSENT";
        } elseif ($marks === "NE") {
            $remarks = "Not Examined";
        } elseif (is_numeric($marks)) {
            $marks_val = floatval($marks);
            if ($marks_val < 30) $remarks = "Failed";
            elseif ($marks_val <= 40) $remarks = "Pass";
            elseif ($marks_val <= 50) $remarks = "Credit";
            elseif ($marks_val <= 60) $remarks = "Good";
            elseif ($marks_val <= 80) $remarks = "Very Good";
            else $remarks = "Excellent";
        } else {
            $remarks = "Invalid input";
        }

        // Check if mark already exists
        $check = mysqli_query($con, "SELECT marks_id FROM marks_tb WHERE pupil_id='$pupil_id' AND class_id='$class_id' AND subject_id='$subject_id' AND test_id='$test_id' AND term_id='$term_id' LIMIT 1");

        if ($check && mysqli_num_rows($check) > 0) {
            // Update existing mark
            $row = mysqli_fetch_assoc($check);
            $marks_id = $row['marks_id'];
            mysqli_query($con, "UPDATE marks_tb SET marks='$marks', remarks='$remarks' WHERE marks_id='$marks_id'");
        } else {
            // Insert new mark
            mysqli_query($con, "INSERT INTO marks_tb (pupil_id, class_id, subject_id, test_id, term_id, marks, remarks) VALUES ('$pupil_id','$class_id','$subject_id','$test_id','$term_id','$marks','$remarks')");
        }
    }
echo '
<div style="
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10000;
">
    <div style="
        background: white;
        padding: 30px;
        border-radius: 15px;
        text-align: center;
        box-shadow: 0 5px 25px rgba(0,0,0,0.2);
        animation: fadeIn 0.3s;
    ">
        <div style="
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #4CAF50;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: scaleIn 0.3s;
        ">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none">
                <path d="M5 13L9 17L19 7" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h3 style="margin: 0 0 10px; color: #2E7D32;">Success!</h3>
        <p style="margin: 0; color: #555;">Marks saved successfully.</p>
        <p style="font-size: 14px; color: #888; margin-top: 10px;">Redirecting...</p>
    </div>
</div>
<script>
    setTimeout(function() {
        window.location.href = "exam.php";
    }, 2000);
</script>
<style>
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes scaleIn {
        from { transform: scale(0); }
        to { transform: scale(1); }
    }
</style>';
exit();
}
?>
