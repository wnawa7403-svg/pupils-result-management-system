<?php
session_start();
require_once "../connection/connection.php";

// Check login
if (isset($_SESSION["LoginAdmin"])) {
    $pupil_id = $_GET['pupil_id'] ?? $_SESSION['pupil'];
} elseif (isset($_SESSION['pupil'])) {
    $pupil_id = $_SESSION['pupil'];
} else {
    echo "<script>alert('You are not authorized to view this page');</script>";
    header('Location: ../login/login.php');
    exit();
}

// Fetch pupil basic info
$query = "
    SELECT p.*, c.class_name 
    FROM pupils_tb p
    LEFT JOIN classes c ON p.class_id = c.class_id
    WHERE p.pupil_id = '$pupil_id'
";
$run = mysqli_query($con, $query);
$pupil = mysqli_fetch_assoc($run);

if (!$pupil) {
    die("<p class='text-danger'>❌ No pupil found with this ID.</p>");
}

$profile_image = $pupil["profile_image"] ?: "default.png";
$class_id = $pupil['class_id'];

// -------------------------
// REPORT CARD DATA (from your second file)
// -------------------------
$terms_result = $con->query("SELECT term_id, t_name FROM terms ORDER BY term_id ASC");
$terms = [];
while($t = $terms_result->fetch_assoc()) {
    $terms[$t['term_id']] = $t['t_name'];
}

// Fetch all marks for the pupil
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
        'marks' => $r['marks']
    ];
}

// Compute ranking per term
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

    // Calculate total marks
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pupil Profile & Report Card</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
    --primary-color: #2c3e50;
    --light-gray: #f5f6f7;
    --border-color: #dee2e6;
}

/* Page */
body {
    background: #f4f6f9;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Container */
.main-container {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    margin: 20px auto;
    max-width: 1200px;
}

/* Tabs */
.nav-tabs {
    background: #ffffff;
    border-bottom: 1px solid var(--border-color);
}

.nav-tabs .nav-link {
    color: #555;
    border: none;
    padding: 12px 20px;
    font-weight: 500;
}

.nav-tabs .nav-link.active {
    color: #000;
    border-bottom: 2px solid #000;
    background: transparent;
}

/* Profile */
.profile-card {
    background: #ffffff;
    border-radius: 8px;
    border: 1px solid var(--border-color);
}

.profile-header {
    background: #ffffff;
    color: #000;
    text-align: center;
    padding: 20px;
    border-bottom: 1px solid var(--border-color);
}

.profile-image {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    border: 1px solid var(--border-color);
    margin: 0 auto 15px;
}

/* Info Cards */
.info-card {
    background: #ffffff;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 15px;
    margin: 10px 0;
}

.info-card h5 {
    font-size: 14px;
    color: #666;
    margin-bottom: 5px;
}

.info-card p {
    margin: 0;
    font-weight: 500;
}

/* Report Header */
.report-header {
    background: #ffffff;
    color: #000;
    padding: 20px;
    text-align: center;
    border-bottom: 1px solid var(--border-color);
}

/* Cards */
.term-card {
    background: #ffffff;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    margin-bottom: 20px;
}

.term-header {
    background: #f8f9fa;
    color: #000;
    padding: 15px;
    border-bottom: 1px solid var(--border-color);
}

/* Table */
.table {
    background: #ffffff;
}

.table thead {
    background: #f1f1f1;
}

.table th {
    font-weight: 600;
}

/* Grades (minimal color) */
.grade-distinction,
.grade-merit,
.grade-credit,
.grade-satisfactory,
.grade-fail {
    color: #000;
    font-weight: 600;
}

/* Stats */
.stats-card {
    background: #ffffff;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 15px;
}

/* Remove sidebar arrows fix remains */
.sidebar .dropdown-toggle::after {
    display: none !important;
}
    </style>
</head>
<body>
<?php include('../Main/Main-sidebar.php') ?>
<br><br><br><br>

<main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 w-100">
    <div class="main-container">
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" id="pupilTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab">
                    <i class="fas fa-user-circle mr-2"></i>Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="report-tab" data-toggle="tab" href="#report" role="tab">
                    <i class="fas fa-chart-line mr-2"></i>Report Card
                </a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="pupilTabsContent">
            
            <!-- Profile Tab -->
            <div class="tab-pane fade show active" id="profile" role="tabpanel">
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-image">
                            <img src="<?php echo "images/$profile_image"; ?>" 
                                   
                                    class="img-fluid rounded-circle" 
                                    style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <h2><?php echo htmlspecialchars($pupil['first_name'] . " " . $pupil['other_name'] . " " . $pupil['last_name']); ?></h2>
                        <p class="lead">Pupil ID: <?php echo htmlspecialchars($pupil['pupil_id']); ?></p>
                    </div>
                    
                    <div class="p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <h5><i class="fas fa-calendar-day text-primary mr-2"></i>Date of Birth</h5>
                                    <p class="mb-0"><?php echo htmlspecialchars($pupil['DOB']); ?></p>
                                </div>
                                <div class="info-card">
                                    <h5><i class="fas fa-venus-mars text-primary mr-2"></i>Gender</h5>
                                    <p class="mb-0"><?php echo htmlspecialchars($pupil['gender']); ?></p>
                                </div>
                                <div class="info-card">
                                    <h5><i class="fas fa-calendar-plus text-primary mr-2"></i>Registration Date</h5>
                                    <p class="mb-0"><?php echo htmlspecialchars($pupil['RegDate']); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <h5><i class="fas fa-map-marker-alt text-primary mr-2"></i>Address</h5>
                                    <p class="mb-0"><?php echo htmlspecialchars($pupil['address']); ?></p>
                                </div>
                                <div class="info-card">
                                    <h5><i class="fas fa-graduation-cap text-primary mr-2"></i>Class</h5>
                                    <p class="mb-0"><?php echo htmlspecialchars($pupil['class_name']); ?></p>
                                </div>
                                <div class="info-card">
                                    <h5><i class="fas fa-calendar text-primary mr-2"></i>Academic Year</h5>
                                    <p class="mb-0"><?php echo htmlspecialchars($pupil['year']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Card Tab -->
            <div class="tab-pane fade" id="report" role="tabpanel">
                <div class="report-header">
                    <h1 class="display-4 mb-2"><i class="fas fa-award"></i> Academic Report Card</h1>
                    <h3 class="mb-3"><?php echo htmlspecialchars($pupil['first_name'].' '.$pupil['last_name']); ?></h3>
                    <p class="lead mb-1"><i class="fas fa-graduation-cap"></i> Class <?php echo htmlspecialchars($pupil['class_name']); ?></p>
                     
                </div>

                <?php
                $allTests = ['T1', 'T2', 'eot'];
                $term_counter = 0;

                foreach($terms as $term_id => $term_name) {
                    $term_counter++;
                    $has_data = isset($subjects[$term_id]);

                    echo "<div class='term-card'>";
                    echo "<div class='term-header'>";
                    echo "<h3 class='mb-0'><i class='fas fa-calendar-alt mr-2'></i>Term: $term_name</h3>";
                    echo "</div>";
                    echo "<div class='card-body p-4'>";

                    if($has_data && !empty($subjects[$term_id])) {
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

                            echo "<tr>";
                            echo "<td><strong><i class='fas fa-book-open mr-2'></i>$subject</strong></td>";
                            echo "<td class='text-center'>$T1</td>";
                            echo "<td class='text-center'>$T2</td>";
                            echo "<td class='text-center font-weight-bold'>$EOT</td>";
                            echo "<td class='text-center $grade_class'>$grade</td>";
                            echo "<td class='text-center $grade_class'>$grade_standard</td>";
                            echo "<td><small><i class='fas fa-comment-dots mr-1'></i>$remarks</small></td>";
                            echo "</tr>";
                        }

                        echo "</tbody></table></div>";

                        // Performance statistics
                        $aggregate = count($totals) > 0 ? array_sum($totals) : 0;
                        $average_mark = count($totals) > 0 ? round($aggregate / count($totals), 2) : 0;

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

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="stats-card">
                                    <h5><i class="fas fa-chart-line text-primary mr-2"></i>Academic Performance</h5>
                                    <p class="mb-1"><strong>Average Mark:</strong> <?php echo $average_mark; ?>%</p>
                                    <p class="mb-1"><strong>Total Subjects:</strong> <?php echo $subject_count; ?></p>
                                    <p class="mb-0"><strong>Rank:</strong> 
                                        <?php 
                                        if(isset($rankings[$term_id][$pupil_id])) {
                                            echo $rankings[$term_id][$pupil_id]['rank'] . " out of " . $rankings[$term_id]['totalStudents'];
                                        } else {
                                            echo "Not ranked";
                                        }
                                        ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stats-card">
                                    <h5><i class="fas fa-calendar-check text-success mr-2"></i>Attendance</h5>
                                    <p class="mb-1"><strong>Present:</strong> <?php echo $present; ?> days</p>
                                    <p class="mb-1"><strong>Absent:</strong> <?php echo $absent; ?> days</p>
                                    <p class="mb-0"><strong>Attendance Rate:</strong> <?php echo $attPercent; ?>%</p>
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

                    echo "</div></div>";
                }
                ?>
            </div>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Smooth tab transitions
    $(document).ready(function() {
        $('#pupilTabs a').on('click', function (e) {
            e.preventDefault();
            $(this).tab('show');
        });
        
        // Add loading animation
        $('.nav-link').on('click', function() {
            $('.tab-pane').addClass('loading');
            setTimeout(function() {
                $('.tab-pane').removeClass('loading');
            }, 500);
        });
    });
</script>
</body>
</html>