<?php
include "../connection/connection.php";

// Fetch dropdown data
$classes  = mysqli_query($con, "SELECT class_id, class_name FROM classes");
$subjects = mysqli_query($con, "SELECT subject_id, subject_name FROM subjects");
$terms    = mysqli_query($con, "SELECT term_id, t_name FROM terms");

// Get selected values
$class_id   = $_POST['class_id'] ?? '';
$subject_id = $_POST['subject_id'] ?? '';
$term_id    = $_POST['term_id'] ?? '';

// Example report data (replace with actual queries)
$report_data = [
    "class_name" => "Grade 10",
    "subject_name" => "Mathematics",
    "term_name" => "Term 1",
    "average_score" => 72,
    "top_student" => "John Banda",
    "performance_trend" => [65, 70, 75, 78, 80]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Report Generator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .header { background: linear-gradient(135deg, #3f4c6b, #606c88); color: white; padding: 2rem 0; margin-bottom: 2rem; border-radius: 0 0 10px 10px; }
        .card { border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 20px; border: none; }
        .stat-card { text-align: center; padding: 20px; }
        .stat-value { font-size: 24px; font-weight: bold; color: #4e73df; }
        .chart-container { height: 300px; margin-top: 20px; }
    </style>
</head>
<body>
   <?php 
// Fetch your data before this point
include "../Main/Main-sidebar.php"; 
?>

<div class="container-fluid">
    <div class="row">
        <!-- ================= SIDEBAR ================= -->
        

        <!-- ================= MAIN CONTENT ================= -->
        <main role="main" class="col-xl-10 col-lg-9 col-md-8 ms-auto px-md-4">
            <div class="container-fluid py-4">

                 

                <!-- ================= FORM ================= -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Select Report Criteria</h5>
                        <form method="POST" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Class</label>
                                <select class="form-select" name="class_id" required>
                                    <option value="">--Select Class--</option>
                                    <?php while($c=mysqli_fetch_assoc($classes)): ?>
                                        <option value="<?= $c['class_id'] ?>" <?= ($c['class_id']==$class_id)?'selected':'' ?>><?= $c['class_name'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Subject</label>
                                <select class="form-select" name="subject_id" required>
                                    <option value="">--Select Subject--</option>
                                    <?php while($s=mysqli_fetch_assoc($subjects)): ?>
                                        <option value="<?= $s['subject_id'] ?>" <?= ($s['subject_id']==$subject_id)?'selected':'' ?>><?= $s['subject_name'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Term</label>
                                <select class="form-select" name="term_id" required>
                                    <option value="">--Select Term--</option>
                                    <?php while($t=mysqli_fetch_assoc($terms)): ?>
                                        <option value="<?= $t['term_id'] ?>" <?= ($t['term_id']==$term_id)?'selected':'' ?>><?= $t['t_name'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary">Show Report</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- ================= REPORT & PDF ================= -->
                <?php if ($class_id && $subject_id && $term_id): ?>
                <div class="card mb-4">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Performance Report</h4>
                        <form method="POST" action="generate_summary.php" target="_blank">
                            <input type="hidden" name="class_id" value="<?= $class_id ?>">
                            <input type="hidden" name="subject_id" value="<?= $subject_id ?>">
                            <input type="hidden" name="term_id" value="<?= $term_id ?>">
                            <button type="submit" name="export_pdf" class="btn btn-danger">
                                <i class="fas fa-file-pdf me-1"></i>Download PDF
                            </button>
                        </form>
                    </div>
                </div>

                

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var ctx = document.getElementById('performanceChart').getContext('2d');
                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: <?= json_encode($report_data['grades']) ?>,
                                datasets: [
                                    {
                                        label: 'Male',
                                        data: <?= json_encode($report_data['male_counts']) ?>,
                                        backgroundColor: 'rgba(54, 162, 235, 0.7)'
                                    },
                                    {
                                        label: 'Female',
                                        data: <?= json_encode($report_data['female_counts']) ?>,
                                        backgroundColor: 'rgba(255, 99, 132, 0.7)'
                                    },
                                    {
                                        label: 'Total',
                                        data: <?= json_encode($report_data['total_counts']) ?>,
                                        type: 'line',
                                        borderColor: 'rgba(0,0,0,0.9)',
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
                                    legend: { position: 'top' }
                                },
                                scales: {
                                    y: { beginAtZero: true, title: { display: true, text: 'Number of Students' } },
                                    x: { title: { display: true, text: 'Grades' } }
                                }
                            }
                        });
                    });
                </script>
                <?php endif; ?>

            </div>
        </main>
    </div>
</div>

