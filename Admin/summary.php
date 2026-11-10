<?php
include "../connection/connection.php"; // adjust connection

// Fetch dropdown data
$classes  = mysqli_query($con, "SELECT class_id, class_name FROM classes");
$subjects = mysqli_query($con, "SELECT subject_id, subject_name FROM subjects");
$terms    = mysqli_query($con, "SELECT term_id, t_name FROM terms");

// Get selected values
$class_id   = isset($_POST['class_id']) ? $_POST['class_id'] : '';
$subject_id = isset($_POST['subject_id']) ? $_POST['subject_id'] : '';
$term_id    = isset($_POST['term_id']) ? $_POST['term_id'] : '';
?>
 
<?php if ($class_id && $subject_id && $term_id): 

    // --- SUMMARY ---
    $present = mysqli_fetch_assoc(mysqli_query($con, "
        SELECT SUM(CASE WHEN p.gender='Male' THEN 1 ELSE 0 END) AS Male,
               SUM(CASE WHEN p.gender='Female' THEN 1 ELSE 0 END) AS Female,
               COUNT(*) AS Total
        FROM marks_tb m
        INNER JOIN pupils_tb p ON m.pupil_id=p.pupil_id
        WHERE m.class_id='$class_id' AND m.subject_id='$subject_id' AND m.term_id='$term_id'
    "));
    $passed = mysqli_fetch_assoc(mysqli_query($con, "
        SELECT SUM(CASE WHEN p.gender='Male' THEN 1 ELSE 0 END) AS Male,
               SUM(CASE WHEN p.gender='Female' THEN 1 ELSE 0 END) AS Female,
               COUNT(*) AS Total
        FROM marks_tb m
        INNER JOIN pupils_tb p ON m.pupil_id=p.pupil_id
        WHERE m.class_id='$class_id' AND m.subject_id='$subject_id' AND m.term_id='$term_id' AND CAST(m.marks AS UNSIGNED) >= 40
    "));
    $failed = mysqli_fetch_assoc(mysqli_query($con, "
        SELECT SUM(CASE WHEN p.gender='Male' THEN 1 ELSE 0 END) AS Male,
               SUM(CASE WHEN p.gender='Female' THEN 1 ELSE 0 END) AS Female,
               COUNT(*) AS Total
        FROM marks_tb m
        INNER JOIN pupils_tb p ON m.pupil_id=p.pupil_id
        WHERE m.class_id='$class_id' AND m.subject_id='$subject_id' AND m.term_id='$term_id' AND CAST(m.marks AS UNSIGNED) < 40
    "));
    $absent = mysqli_fetch_assoc(mysqli_query($con, "
        SELECT SUM(CASE WHEN p.gender='Male' THEN 1 ELSE 0 END) AS Male,
               SUM(CASE WHEN p.gender='Female' THEN 1 ELSE 0 END) AS Female,
               COUNT(*) AS Total
        FROM marks_tb m
        INNER JOIN pupils_tb p ON m.pupil_id=p.pupil_id
        WHERE m.class_id='$class_id' AND m.subject_id='$subject_id' AND m.term_id='$term_id' AND (marks IS NULL OR marks = '')
    "));
?>

 <?php
    // --- GRADE ANALYSIS ---
    $sql_grades = "
    WITH grade_list AS (
        SELECT 'Distinction - 1' AS Grade
        UNION ALL SELECT 'Merit - 2'
        UNION ALL SELECT 'Credit - 3'
        UNION ALL SELECT 'Pass - 4'
        UNION ALL SELECT 'Fail'
        UNION ALL SELECT 'Absent'
    ),
    grade_data AS (
        SELECT CASE 
                   WHEN CAST(m.marks AS UNSIGNED) BETWEEN 75 AND 100 THEN 'Distinction - 1'
                   WHEN CAST(m.marks AS UNSIGNED) BETWEEN 60 AND 74 THEN 'Merit - 2'
                   WHEN CAST(m.marks AS UNSIGNED) BETWEEN 50 AND 59 THEN 'Credit - 3'
                   WHEN CAST(m.marks AS UNSIGNED) BETWEEN 40 AND 49 THEN 'Pass - 4'
                   WHEN CAST(m.marks AS UNSIGNED) < 40 THEN 'Fail'
                   WHEN m.marks IS NULL OR m.marks='' THEN 'Absent'
               END AS Grade,
               p.gender
        FROM marks_tb m
        INNER JOIN pupils_tb p ON m.pupil_id=p.pupil_id
        WHERE m.class_id='$class_id' AND m.subject_id='$subject_id' AND m.term_id='$term_id'
    ),
    grade_summary AS (
        SELECT g.Grade,
               COALESCE(SUM(CASE WHEN gd.gender='Male' THEN 1 ELSE 0 END),0) AS Male,
               COALESCE(SUM(CASE WHEN gd.gender='Female' THEN 1 ELSE 0 END),0) AS Female,
               COALESCE(COUNT(gd.Grade),0) AS Total
        FROM grade_list g
        LEFT JOIN grade_data gd ON g.Grade=gd.Grade
        GROUP BY g.Grade
    )
    SELECT * FROM grade_summary
    UNION ALL
    SELECT 'Total', SUM(Male), SUM(Female), SUM(Total) FROM grade_summary;
    ";
    $grades_result = mysqli_query($con, $sql_grades);
?>
 
 
<?php
    // --- QUANTITATIVE & QUALITATIVE ---
    $sql_scores = "
        SELECT MAX(CAST(marks AS UNSIGNED)) AS max_score,
               MIN(CAST(marks AS UNSIGNED)) AS min_score,
               AVG(CAST(marks AS UNSIGNED)) AS mean_score,
               SUM(CASE WHEN CAST(marks AS UNSIGNED) >=40 THEN 1 ELSE 0 END) AS passed_count,
               SUM(CASE WHEN CAST(marks AS UNSIGNED) <40 THEN 1 ELSE 0 END) AS failed_count,
               COUNT(*) AS total_count
        FROM marks_tb
        WHERE class_id='$class_id' AND subject_id='$subject_id' AND term_id='$term_id' AND marks IS NOT NULL AND marks != ''
    ";
    $scores = mysqli_fetch_assoc(mysqli_query($con,$sql_scores));

    $total = $scores['total_count'] ?: 1;
    $pass_percent = round(($scores['passed_count']/$total)*100);
    $fail_percent = round(($scores['failed_count']/$total)*100);
    $max_score  = $scores['max_score'];
    $min_score  = $scores['min_score'];
    $mean_score = round($scores['mean_score']);
?>

 
 

<?php endif; ?>
<?php
// --- GRADE ANALYSIS ---
$sql_grades = "
WITH grade_list AS (
    SELECT 'Distinction - 1' AS Grade
    UNION ALL SELECT 'Merit - 2'
    UNION ALL SELECT 'Credit - 3'
    UNION ALL SELECT 'Pass - 4'
    UNION ALL SELECT 'Fail'
    UNION ALL SELECT 'Absent'
),
grade_data AS (
    SELECT CASE 
               WHEN CAST(m.marks AS UNSIGNED) BETWEEN 75 AND 100 THEN 'Distinction - 1'
               WHEN CAST(m.marks AS UNSIGNED) BETWEEN 60 AND 74 THEN 'Merit - 2'
               WHEN CAST(m.marks AS UNSIGNED) BETWEEN 50 AND 59 THEN 'Credit - 3'
               WHEN CAST(m.marks AS UNSIGNED) BETWEEN 40 AND 49 THEN 'Pass - 4'
               WHEN CAST(m.marks AS UNSIGNED) < 40 THEN 'Fail'
               WHEN m.marks IS NULL OR m.marks='' THEN 'Absent'
           END AS Grade,
           p.gender
    FROM marks_tb m
    INNER JOIN pupils_tb p ON m.pupil_id=p.pupil_id
    WHERE m.class_id='$class_id' AND m.subject_id='$subject_id' AND m.term_id='$term_id'
),
grade_summary AS (
    SELECT g.Grade,
           COALESCE(SUM(CASE WHEN gd.gender='Male' THEN 1 ELSE 0 END),0) AS Male,
           COALESCE(SUM(CASE WHEN gd.gender='Female' THEN 1 ELSE 0 END),0) AS Female,
           COALESCE(COUNT(gd.Grade),0) AS Total
    FROM grade_list g
    LEFT JOIN grade_data gd ON g.Grade=gd.Grade
    GROUP BY g.Grade
)
SELECT * FROM grade_summary
UNION ALL
SELECT 'Total', SUM(Male), SUM(Female), SUM(Total) FROM grade_summary;
";

$grades_result = mysqli_query($con, $sql_grades);
?>
 
  
<!DOCTYPE html>
<html>
<head>
    <title>Full Report</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4895ef;
            --success: #4cc9f0;
            --warning: #f72585;
            --danger: #e63946;
            --dark: #2c3e50;
            --light: #f8f9fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        main[role="main"] {
            padding: 100px 20px 20px 270px;
            transition: all 0.3s;
            min-height: 100vh;
        }
        
        .report-header {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        
        .report-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }
        
        .filter-form {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        
        .filter-form label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 5px;
        }
        
        .filter-form select, .filter-form button {
            border-radius: 6px;
            padding: 8px 12px;
            border: 1px solid #ddd;
        }
        
        .filter-form button {
            background: var(--primary);
            color: white;
            border: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .filter-form button:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }
        
        .report-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            overflow: hidden;
        }
        
        .report-card-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 15px 20px;
            font-weight: 600;
            font-size: 18px;
        }
        
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        
        .report-table th {
            background-color: #f8f9fa;
            color: var(--dark);
            font-weight: 600;
            padding: 12px 15px;
            text-align: center;
            border-bottom: 2px solid #dee2e6;
        }
        
        .report-table td {
            padding: 10px 15px;
            text-align: center;
            border-bottom: 1px solid #dee2e6;
        }
        
        .report-table tr:last-child td {
            border-bottom: none;
        }
        
        .total-row {
            background-color: #e9f7ff !important;
            font-weight: 600;
        }
        
        .badge {
            padding: 6px 10px;
            border-radius: 4px;
            font-weight: 500;
        }
        
        .chart-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .chart-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 15px;
            text-align: center;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 20px;
            text-align: center;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 14px;
            color: #6c757d;
        }
        
        @media (max-width: 1200px) {
            main[role="main"] {
                padding-left: 20px;
                padding-top: 120px;
            }
        }
        
        @media (max-width: 768px) {
            main[role="main"] {
                padding: 100px 15px 15px 15px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .filter-form .form-group {
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>
    
    <?php include "../Main/Main-sidebar.php"; ?>
    
    <main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 w-100">
        <div class="container-fluid py-4">
            <!-- Report Header -->
            <div class="report-header">
                <h1 class="report-title"><i class="fas fa-chart-bar me-2"></i>Summary & Grade Report</h1>
                <p class="text-muted mb-0">Comprehensive analysis of student performance</p>
            </div>
            
            <!-- Filter Form -->
            <div class="filter-form">
                <form method="POST" class="row align-items-end">
                    <div class="col-md-3 form-group">
                        <label>Class:</label>
                        <select name="class_id" class="form-control" required>
                            <option value="">-- Select Class --</option>
                            <?php while ($row = mysqli_fetch_assoc($classes)): ?>
                                <option value="<?= $row['class_id'] ?>" <?= ($row['class_id'] == $class_id) ? 'selected' : '' ?>>
                                    <?= $row['class_name'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3 form-group">
                        <label>Subject:</label>
                        <select name="subject_id" class="form-control" required>
                            <option value="">-- Select Subject --</option>
                            <?php while ($row = mysqli_fetch_assoc($subjects)): ?>
                                <option value="<?= $row['subject_id'] ?>" <?= ($row['subject_id'] == $subject_id) ? 'selected' : '' ?>>
                                    <?= $row['subject_name'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3 form-group">
                        <label>Term:</label>
                        <select name="term_id" class="form-control" required>
                            <option value="">-- Select Term --</option>
                            <?php while ($row = mysqli_fetch_assoc($terms)): ?>
                                <option value="<?= $row['term_id'] ?>" <?= ($row['term_id'] == $term_id) ? 'selected' : '' ?>>
                                    <?= $row['t_name'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3 form-group">
                        <button type="submit" name="analyze" class="btn btn-primary w-100">
                            <i class="fas fa-chart-line me-2"></i>Generate Report
                        </button>
                    </div>
                </form>
            </div>
            
            <?php if ($class_id && $subject_id && $term_id): ?>
            
            <!-- Summary Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?= $present['Total'] ?></div>
                    <div class="stat-label">Total Students</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= $passed['Total'] ?></div>
                    <div class="stat-label">Passed</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= $failed['Total'] ?></div>
                    <div class="stat-label">Failed</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= $absent['Total'] ?></div>
                    <div class="stat-label">Absent</div>
                </div>
            </div>
            
            <!-- Student Summary Report -->
            <div class="report-card">
                <div class="report-card-header">
                    Student Summary
                </div>
                <div class="card-body p-0">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Male</th>
                                <th>Female</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Entered</td>
                                <td><?= $present['Male'] ?></td>
                                <td><?= $present['Female'] ?></td>
                                <td><?= $present['Total'] ?></td>
                            </tr>
                            <tr>
                                <td>Present</td>
                                <td><?= $present['Male'] ?></td>
                                <td><?= $present['Female'] ?></td>
                                <td><?= $present['Total'] ?></td>
                            </tr>
                            <tr>
                                <td>Passed</td>
                                <td><?= $passed['Male'] ?></td>
                                <td><?= $passed['Female'] ?></td>
                                <td><?= $passed['Total'] ?></td>
                            </tr>
                            <tr>
                                <td>Failed</td>
                                <td><?= $failed['Male'] ?></td>
                                <td><?= $failed['Female'] ?></td>
                                <td><?= $failed['Total'] ?></td>
                            </tr>
                            <tr>
                                <td>Absent</td>
                                <td><?= $absent['Male'] ?></td>
                                <td><?= $absent['Female'] ?></td>
                                <td><?= $absent['Total'] ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
<?php
    // --- GRADE ANALYSIS ---
    $sql_grades = "
    WITH grade_list AS (
        SELECT 'Distinction - 1' AS Grade
        UNION ALL SELECT 'Merit - 2'
        UNION ALL SELECT 'Credit - 3'
        UNION ALL SELECT 'Pass - 4'
        UNION ALL SELECT 'Fail'
        UNION ALL SELECT 'Absent'
    ),
    grade_data AS (
        SELECT CASE 
                   WHEN CAST(m.marks AS UNSIGNED) BETWEEN 75 AND 100 THEN 'Distinction - 1'
                   WHEN CAST(m.marks AS UNSIGNED) BETWEEN 60 AND 74 THEN 'Merit - 2'
                   WHEN CAST(m.marks AS UNSIGNED) BETWEEN 50 AND 59 THEN 'Credit - 3'
                   WHEN CAST(m.marks AS UNSIGNED) BETWEEN 40 AND 49 THEN 'Pass - 4'
                   WHEN CAST(m.marks AS UNSIGNED) < 40 THEN 'Fail'
                   WHEN m.marks IS NULL OR m.marks='' THEN 'Absent'
               END AS Grade,
               p.gender
        FROM marks_tb m
        INNER JOIN pupils_tb p ON m.pupil_id=p.pupil_id
        WHERE m.class_id='$class_id' AND m.subject_id='$subject_id' AND m.term_id='$term_id'
    ),
    grade_summary AS (
        SELECT g.Grade,
               COALESCE(SUM(CASE WHEN gd.gender='Male' THEN 1 ELSE 0 END),0) AS Male,
               COALESCE(SUM(CASE WHEN gd.gender='Female' THEN 1 ELSE 0 END),0) AS Female,
               COALESCE(COUNT(gd.Grade),0) AS Total
        FROM grade_list g
        LEFT JOIN grade_data gd ON g.Grade=gd.Grade
        GROUP BY g.Grade
    )
    SELECT * FROM grade_summary
    UNION ALL
    SELECT 'Total', SUM(Male), SUM(Female), SUM(Total) FROM grade_summary;
    ";
    $grades_result = mysqli_query($con, $sql_grades);
?>
 
            <!-- Grade Summary -->
            <div class="report-card">
                <div class="report-card-header">
                    Grade Summary
                </div>
                <div class="card-body p-0">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Grade</th>
                                <th>Male</th>
                                <th>Female</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php mysqli_data_seek($grades_result, 0); ?>
                            <?php while ($row = mysqli_fetch_assoc($grades_result)): ?>
                                <tr class="<?= ($row['Grade'] === 'Total') ? 'total-row' : '' ?>">
                                    <td><?= $row['Grade'] ?></td>
                                    <td><?= $row['Male'] ?></td>
                                    <td><?= $row['Female'] ?></td>
                                    <td><?= $row['Total'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Quantitative & Qualitative Analysis -->
            <div class="row">
                <div class="col-md-6">
                    <div class="report-card">
                        <div class="report-card-header">
                            Quantitative Analysis
                        </div>
                        <div class="card-body p-0">
                            <table class="report-table">
                                <thead>
                                    <tr>
                                        <th>Analysis</th>
                                        <th>Pass %</th>
                                        <th>Fail %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Quantitative</td>
                                        <td><?= $pass_percent ?>%</td>
                                        <td><?= $fail_percent ?>%</td>
                                    </tr>
                                    <tr>
                                        <td>Qualitative</td>
                                        <td><?= $pass_percent ?>%</td>
                                        <td><?= $fail_percent ?>%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="report-card">
                        <div class="report-card-header">
                            Score Analysis
                        </div>
                        <div class="card-body p-0">
                            <table class="report-table">
                                <thead>
                                    <tr>
                                        <th>Max Score</th>
                                        <th>Mean Score</th>
                                        <th>Min Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?= $max_score ?></td>
                                        <td><?= $mean_score ?></td>
                                        <td><?= $min_score ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Grade Analysis Chart -->
            <div class="chart-container">
               
                <canvas id="gradeChart" height="350"></canvas>
            </div>
            
            <?php endif; ?>
        </div>
    </main>
    
    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
    <?php if ($class_id && $subject_id && $term_id): ?>
    // Prepare chart data
    <?php
    mysqli_data_seek($grades_result, 0);
    $grades = [];
    $males = [];
    $females = [];
    $totals = [];
    
    while ($row = mysqli_fetch_assoc($grades_result)) {
        if ($row['Grade'] !== 'Total') {
            $grades[] = $row['Grade'];
            $males[] = (int)$row['Male'];
            $females[] = (int)$row['Female'];
            $totals[] = (int)$row['Total'];
        }
    }
    ?>
    
    // Initialize chart
    const ctx = document.getElementById('gradeChart').getContext('2d');
    const gradeChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($grades) ?>,
            datasets: [
                {
                    label: 'Male',
                    data: <?= json_encode($males) ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Female',
                    data: <?= json_encode($females) ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.7)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Total',
                    data: <?= json_encode($totals) ?>,
                    type: 'line',
                    borderColor: 'rgba(0, 0, 0, 0.9)',
                    borderWidth: 2,
                    fill: false,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Grade Analysis by Gender',
                    font: { size: 18 }
                },
                tooltip: { mode: 'index', intersect: false },
                legend: { position: 'top' }
            },
            scales: {
                x: { 
                    stacked: false,
                    title: {
                        display: true,
                        text: 'Grades'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: { 
                        display: true, 
                        text: 'Number of Students' 
                    }
                }
            }
        }
    });
    <?php endif; ?>
    </script>
</body>
</html>