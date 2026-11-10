<?php
session_start();
require_once "../connection/connection.php";

// -------------------------
// Check login
// -------------------------
if (!isset($_SESSION["LoginAdmin"]) && !isset($_SESSION["pupil"])) {
    header('Location: ../login/login.php');
    exit();
}

 
$pupil_id = $_GET['pupil_id'] ?? $_SESSION['pupil'] ?? null;
if (!$pupil_id) die("<p class='text-danger'>Invalid pupil selected.</p>");

// Fetch pupil info safely
$stmt = $con->prepare("
    SELECT p.*, c.class_name 
    FROM pupils_tb p 
    LEFT JOIN classes c ON p.class_id=c.class_id 
    WHERE p.pupil_id=?
");
$stmt->bind_param("i", $pupil_id);
$stmt->execute();
$pupil = $stmt->get_result()->fetch_assoc();
if (!$pupil) die("<p class='text-danger'>Pupil not found.</p>");

$class_id = $pupil['class_id'];

// -------------------------
// Fetch all terms for the year
// -------------------------
$terms_result = $con->query("SELECT term_id, t_name FROM terms ORDER BY term_id ASC");
$terms = [];
while($t = $terms_result->fetch_assoc()) {
    $terms[$t['term_id']] = $t['t_name'];
}

// -------------------------
// Fetch all marks for the pupil
// -------------------------
$stmt = $con->prepare("
    SELECT s.subject_name, tt.test_name, m.marks, m.term_id
    FROM marks_tb m
    JOIN subjects s ON m.subject_id = s.subject_id
    JOIN test tt ON m.test_id = tt.test_id
    WHERE m.pupil_id=? AND m.class_id=?
");
$stmt->bind_param("ii", $pupil_id, $class_id);
$stmt->execute();
$marks_result = $stmt->get_result();

$subjects = [];
while($r = $marks_result->fetch_assoc()){
    $subjects[$r['term_id']][$r['subject_name']][$r['test_name']] = [
        'marks' => $r['marks'],
        'grade' => $r['grade'],
        'Grade_Standard' => $r['Grade_Standard'],
        'remarks' => $r['remarks']
    ];
}

// -------------------------
// Compute ranking per term
// -------------------------
$rankings = [];
foreach($terms as $term_id => $term_name){
    $studentTotals = [];

    $stmt_all = $con->prepare("
        SELECT m.pupil_id, s.subject_name, tt.test_name, m.marks
        FROM marks_tb m
        JOIN subjects s ON m.subject_id = s.subject_id
        JOIN test tt ON m.test_id = tt.test_id
        WHERE m.class_id=? AND m.term_id=?
    ");
    $stmt_all->bind_param("ii", $class_id, $term_id);
    $stmt_all->execute();
    $all_marks = $stmt_all->get_result();

    while($row = $all_marks->fetch_assoc()){
        $pid = $row['pupil_id'];
        $subject = strtolower($row['subject_name']);
        $test = strtolower($row['test_name']);
        $marks = is_numeric($row['marks']) ? floatval($row['marks']) : 0;

        if(!isset($studentTotals[$pid])) $studentTotals[$pid] = ['totals'=>[], 'english'=>null];

        if($test === 'eot' && $marks >= 40){
            if($subject === 'english') $studentTotals[$pid]['english'] = $marks;
            else $studentTotals[$pid]['totals'][$subject] = $marks;
        }
    }

    // Calculate total marks (best 5 subjects + English)
    $studentTotalMarks = [];
    foreach($studentTotals as $pid => $data){
        arsort($data['totals']);
        $best5 = array_slice($data['totals'], 0, 5);
        $total = array_sum($best5);
        if($data['english'] !== null) $total += $data['english'];
        $studentTotalMarks[$pid] = $total;
    }

    // Assign rankings
    arsort($studentTotalMarks);
    $rankings[$term_id] = [];
    $rank = 1; $lastTotal = null; $skip = 0;
    foreach($studentTotalMarks as $pid => $total){
        if($lastTotal === null) $rankings[$term_id][$pid] = ['rank'=>$rank, 'total'=>$total];
        elseif($total == $lastTotal){ 
            $rankings[$term_id][$pid] = ['rank'=>$rank, 'total'=>$total]; 
            $skip++; 
        }
        else { 
            $rank += $skip + 1; 
            $skip = 0; 
            $rankings[$term_id][$pid] = ['rank'=>$rank, 'total'=>$total]; 
        }
        $lastTotal = $total;
    }
    $rankings[$term_id]['totalStudents'] = count($studentTotalMarks);
}
?>

 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Full Year Report Card - <?php echo $pupil['first_name'].' '.$pupil['last_name']; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --dark-color: #2c3e50;
            --distinction: #27ae60;
            --merit: #3498db;
            --credit: #f39c12;
            --satisfactory: #9b59b6;
            --fail: #e74c3c;
        }
        
        body {
            
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .report-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin: 0 auto;
            overflow: hidden;
            max-width: 1200px;
        }
        
        .report-header {
            background: linear-gradient(135deg, var(--dark-color), var(--primary-color));
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }
        
        .student-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 5px solid rgba(255,255,255,0.3);
            margin: 0 auto 20px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
        }
        
        .term-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            border: none;
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .term-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        
        .term-header {
            background: linear-gradient(135deg, var(--primary-color), #2980b9);
            color: white;
            padding: 20px;
            border-bottom: 3px solid rgba(255,255,255,0.2);
        }
        
        .grade-distinction { color: var(--distinction); font-weight: bold; background-color: rgba(39, 174, 96, 0.1); }
        .grade-merit { color: var(--merit); font-weight: bold; background-color: rgba(52, 152, 219, 0.1); }
        .grade-credit { color: var(--credit); font-weight: bold; background-color: rgba(243, 156, 18, 0.1); }
        .grade-satisfactory { color: var(--satisfactory); font-weight: bold; background-color: rgba(155, 89, 182, 0.1); }
        .grade-fail { color: var(--fail); font-weight: bold; background-color: rgba(231, 76, 60, 0.1); }
        
        .stats-card {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 12px;
            padding: 20px;
            margin: 10px 0;
            border-left: 4px solid var(--primary-color);
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateX(5px);
        }
        
        .progress {
            height: 12px;
            border-radius: 6px;
            background-color: #e9ecef;
        }
        
        .progress-bar {
            border-radius: 6px;
        }
        
        .comment-bubble {
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            border-radius: 15px;
            padding: 20px;
            margin: 15px 0;
            border-left: 4px solid var(--primary-color);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .print-btn {
            background: linear-gradient(135deg, var(--success-color), #27ae60);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            color: white;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
        }
        
        .print-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
        }
        
        @media print {
            body { background: white; padding: 0; }
            .report-container { box-shadow: none; margin: 0; border-radius: 0; }
            .print-btn { display: none; }
            .term-card { break-inside: avoid; }
        }
        
        .subject-row {
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .subject-row:hover {
            background-color: #f8f9fa;
            border-left-color: var(--primary-color);
            transform: scale(1.01);
        }
        
        .rank-badge {
            background: linear-gradient(135deg, #ffd700, #ffed4e);
            color: #000;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
        }
        
        .grade-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 0.85em;
        }
        
        .table th {
            background: linear-gradient(135deg, #34495e, #2c3e50);
            color: white;
            border: none;
            font-weight: 600;
        }
        
        .performance-meter {
            height: 120px;
            background: conic-gradient(
                var(--success-color) 0% 25%,
                var(--merit) 25% 50%,
                var(--warning-color) 50% 75%,
                var(--danger-color) 75% 100%
            );
            border-radius: 50%;
            width: 120px;
            margin: 0 auto;
            position: relative;
        }
        
        .performance-meter::after {
            content: '';
            position: absolute;
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            top: 20px;
            left: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="report-container">
            <!-- Header Section -->
            <div class="report-header">
                <div class="row align-items-center">
                    <div class="col-md-2 text-center">
                        <div class="student-avatar">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h1 class="display-4 mb-2"><i class="fas fa-award"></i> Academic Report Card</h1>
                        <h3 class="mb-3"><?php echo htmlspecialchars($pupil['first_name'].' '.$pupil['last_name']); ?></h3>
                        <p class="lead mb-1"><i class="fas fa-graduation-cap"></i> Class <?php echo htmlspecialchars($pupil['class_name']); ?></p>
                        
                        <p class="mb-0"><i class="fas fa-chalkboard-teacher"></i> Class Teacher: <?php echo htmlspecialchars($pupil['class_teacher'] ?? 'Not Assigned'); ?></p>
                    </div>
                    <div class="col-md-2 text-center">
                        <button onclick="window.print()" class="print-btn mb-3">
                            <i class="fas fa-print"></i> Print Report
                        </button>
                        <div class="text-white">
                            <small>Generated on: <?php echo date('F j, Y'); ?></small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="p-4">
           <?php
// Grading function
function getGradeDetails($marks) {
    if ($marks === "AB") return [0, 'ABSENT', 'Absent'];
    if ($marks === "NE") return [0, 'Not Examined', 'Not Examined'];
    if (!is_numeric($marks)) return [0, 'Invalid', 'Invalid input'];

    $mark = floatval($marks);

    if ($mark >= 75) return [1, 'Distinction', 'Excellent'];
    if ($mark >= 65) return [2, 'Merit', 'Good'];
    if ($mark >= 55) return [3, 'Credit', 'Average'];
    if ($mark >= 40) return [4, 'Satisfactory', 'Pass'];
    return [5, 'Fail', 'Unsatisfactory'];
}

$allTests = ['T1', 'T2', 'eot'];
$term_counter = 0;
$annual_stats = ['total_subjects' => 0, 'terms_with_data' => 0];

foreach($terms as $term_id => $term_name) {
    $term_counter++;
    $has_data = isset($subjects[$term_id]);

    echo "<div class='term-card'>";
    echo "<div class='term-header'>";
    echo "<h3 class='mb-0'><i class='fas fa-calendar-alt mr-2'></i>Term: $term_name</h3>";
    echo "</div>";
    echo "<div class='card-body p-4'>";

    if($has_data && !empty($subjects[$term_id])) {
        $annual_stats['terms_with_data']++;

        echo "<div class='table-responsive'>";
        echo "<table class='table table-hover table-bordered'>";
        echo "<thead class='thead-dark'>";
        echo "<tr>
                <th><i class='fas fa-book'></i> Subject</th>";
        foreach($allTests as $test) {
            echo "<th class='text-center'><i class='fas fa-pencil-alt'></i> $test</th>";
        }
        echo "<th class='text-center'><i class='fas fa-star'></i> Grade</th>";
        echo "<th class='text-center'><i class='fas fa-trophy'></i> Grade Standard</th>";
        echo "<th class='text-center'><i class='fas fa-comment'></i> Remarks</th>";
        echo "</tr></thead><tbody>";

        $totals = [];
        $subject_count = 0;

        foreach($subjects[$term_id] as $subject => $tests) {
            $subject_count++;
            $annual_stats['total_subjects']++;

            $T1 = $tests['T1']['marks'] ?? '-';
            $T2 = $tests['T2']['marks'] ?? '-';
            $EOT = $tests['eot']['marks'] ?? '-';

            // Calculate grade dynamically
            [$grade, $grade_standard, $remarks] = getGradeDetails($EOT);

            // Grade class for styling
            $grade_class = 'text-muted';
            switch($grade_standard) {
                case 'Distinction': $grade_class = 'grade-distinction'; break;
                case 'Merit': $grade_class = 'grade-merit'; break;
                case 'Credit': $grade_class = 'grade-credit'; break;
                case 'Satisfactory': $grade_class = 'grade-satisfactory'; break;
                case 'Fail': $grade_class = 'grade-fail'; break;
            }

            if(is_numeric($EOT) && $EOT > 0) $totals[$subject] = floatval($EOT);

            echo "<tr class='subject-row'>";
            echo "<td><strong><i class='fas fa-book-open mr-2'></i>$subject</strong></td>";
            echo "<td class='text-center'>$T1</td>";
            echo "<td class='text-center'>$T2</td>";
            echo "<td class='text-center font-weight-bold'>$EOT</td>";
            echo "<td class='text-center $grade_class'><span class='grade-badge $grade_class'>$grade</span></td>";
            echo "<td class='text-center $grade_class'>$grade_standard</td>";
            echo "<td><small><i class='fas fa-comment-dots mr-1'></i>$remarks</small></td>";
            echo "</tr>";
        }

        echo "</tbody></table></div>";

        // Performance statistics
        $aggregate = count($totals) > 0 ? array_sum($totals) : 0;
        $average_mark = count($totals) > 0 ? round($aggregate / count($totals), 2) : 0;
        $gpa = $average_mark > 0 ? round(($average_mark / 100) * 4, 2) : 0;

        if($average_mark >= 75) {
            $statement = 'Excellent'; $statement_class = 'success';
        } elseif($average_mark >= 60) {
            $statement = 'Good'; $statement_class = 'primary';
        } elseif($average_mark >= 40) {
            $statement = 'Satisfactory'; $statement_class = 'warning';
        } else {
            $statement = 'Needs Improvement'; $statement_class = 'danger';
        }

        // Attendance
        $att_stmt = $con->prepare("SELECT SUM(CASE WHEN status=1 THEN 1 ELSE 0 END) as present_days,
                                          SUM(CASE WHEN status=0 THEN 1 ELSE 0 END) as absent_days
                                   FROM attendance WHERE pupil_id=? AND class_id=? AND term_id=?");
        $att_stmt->bind_param("iii", $pupil_id, $class_id, $term_id);
        $att_stmt->execute();
        $att = $att_stmt->get_result()->fetch_assoc();

        $present = $att['present_days'] ?? 0;
        $absent = $att['absent_days'] ?? 0;
        $totalDays = $present + $absent;
        $attPercent = ($totalDays > 0) ? round(($present / $totalDays) * 100, 2) : 0;

        ?>

        <!-- Academic and Attendance Stats -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="stats-card">
                    <h5><i class="fas fa-chart-line text-primary mr-2"></i>Academic Performance</h5>
                    <div class="row text-center">
                        <div class="col-4"><small>Average Mark</small><br><strong><?php echo $average_mark; ?>%</strong></div>
                        <div class="col-4"><small>GPA</small><br><strong><?php echo $gpa; ?>/4.0</strong></div>
                        <div class="col-4"><small>Status</small><br><span class="badge badge-<?php echo $statement_class; ?>"><?php echo $statement; ?></span></div>
                        <div class="col-12 mt-2"><small>Total Subjects</small><br><strong><?php echo $subject_count; ?> Subjects</strong></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stats-card">
                    <h5><i class="fas fa-calendar-check text-success mr-2"></i>Attendance Record</h5>
                    <div class="progress mb-3">
                        <div class="progress-bar bg-success" style="width: <?php echo $attPercent; ?>%"></div>
                    </div>
                    <div class="row text-center">
                        <div class="col-4"><small>Present</small><br><strong><?php echo $present; ?> days</strong></div>
                        <div class="col-4"><small>Absent</small><br><strong><?php echo $absent; ?> days</strong></div>
                        <div class="col-4"><small>Percentage</small><br><strong><?php echo $attPercent; ?>%</strong></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teacher Comments -->
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="comment-bubble">
                    <h6><i class="fas fa-user-tie text-primary mr-2"></i>Headteacher's Comments</h6>
                    <p class="mb-0 lead">
                        <?php
                        if($average_mark >= 75) echo "🎉 Outstanding performance! Consistently excellent work across all subjects.";
                        elseif($average_mark >= 60) echo "👍 Very good effort. Showing strong understanding of the curriculum.";
                        elseif($average_mark >= 40) echo "📚 Satisfactory progress. Focus on improving weaker areas.";
                        else echo "💡 Needs more dedication. Regular practice and attendance will help improve results.";
                        ?>
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="comment-bubble">
                    <h6><i class="fas fa-chalkboard-teacher text-success mr-2"></i>Class Teacher's Comments</h6>
                    <p class="mb-0 lead">
                        <?php
                        if($attPercent >= 90) echo "⭐ Excellent attendance! Perfect record this term.";
                        elseif($attPercent >= 75) echo "✅ Good attendance. Maintain this consistency.";
                        elseif($attPercent >= 50) echo "ℹ️ Regular attendance needed. Try to reduce absences.";
                        else echo "⚠️ Poor attendance affecting performance. Immediate improvement required.";
                        ?>
                    </p>
                </div>
            </div>
        </div>

        <?php
    } else {
        echo "<div class='text-center py-5'>";
        echo "<i class='fas fa-clipboard-list fa-4x text-muted mb-3'></i>";
        echo "<h4 class='text-muted'>No Academic Data Available</h4>";
        echo "<p class='text-muted'>Marks and attendance records for $term_name will appear here once entered by teachers.</p>";
        echo "</div>";
    }

    echo "</div></div>"; // Close card-body & term-card
}
?>
