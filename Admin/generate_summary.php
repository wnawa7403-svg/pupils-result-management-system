<?php
require("../fpdf186/fpdf.php");
include "../connection/connection.php";

if (isset($_POST['export_pdf'])) {
    $class_id   = $_POST['class_id'];
    $subject_id = $_POST['subject_id'];
    $term_id    = $_POST['term_id'];

    // ===== Fetch Data =====
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

    // ===== Grade Summary =====
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

    // ===== Scores =====
    $scores = mysqli_fetch_assoc(mysqli_query($con,"SELECT MAX(CAST(marks AS UNSIGNED)) AS max_score,
               MIN(CAST(marks AS UNSIGNED)) AS min_score,
               AVG(CAST(marks AS UNSIGNED)) AS mean_score,
               SUM(CASE WHEN CAST(marks AS UNSIGNED) >=40 THEN 1 ELSE 0 END) AS passed_count,
               SUM(CASE WHEN CAST(marks AS UNSIGNED) <40 THEN 1 ELSE 0 END) AS failed_count,
               COUNT(*) AS total_count
        FROM marks_tb
        WHERE class_id='$class_id' AND subject_id='$subject_id' AND term_id='$term_id' AND marks IS NOT NULL AND marks != ''
    "));

    $total = $scores['total_count'] ?: 1;
    $pass_percent = round(($scores['passed_count']/$total)*100);
    $fail_percent = round(($scores['failed_count']/$total)*100);

    // ===== Prepare Chart Data for QuickChart.io =====
    mysqli_data_seek($grades_result, 0);
    $grades = $males = $females = $totals = [];
    while ($row = mysqli_fetch_assoc($grades_result)) {
        if ($row['Grade'] !== 'Total') {
            $grades[] = $row['Grade'];
            $males[] = (int)$row['Male'];
            $females[] = (int)$row['Female'];
            $totals[] = (int)$row['Total'];
        }
    }

    $chartConfig = [
        "type" => "bar",
        "data" => [
            "labels" => $grades,
            "datasets" => [
                ["label"=>"Male","data"=>$males,"backgroundColor"=>"rgba(54, 162, 235, 0.7)"],
                ["label"=>"Female","data"=>$females,"backgroundColor"=>"rgba(255, 99, 132, 0.7)"],
                ["label"=>"Total","data"=>$totals,"type"=>"line","borderColor"=>"rgba(0,0,0,0.9)","borderWidth"=>2,"fill"=>false,"tension"=>0.3]
            ]
        ],
        "options"=>[
            "plugins"=>["title"=>["display"=>true,"text"=>"Grade Analysis by Gender","font"=>["size"=>18]]],
            "scales"=>[
                "x"=>["title"=>["display"=>true,"text"=>"Grades"]],
                "y"=>["beginAtZero"=>true,"title"=>["display"=>true,"text"=>"Number of Students"]]
            ]
        ]
    ];

    $chartUrl  = "https://quickchart.io/chart?c=" . urlencode(json_encode($chartConfig));
    $chartFile = "chart.png";
    file_put_contents($chartFile, file_get_contents($chartUrl));

    // ===== Generate PDF =====
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont("Arial","B",16);
    $pdf->Cell(190,10,"Full Performance Report",0,1,"C");
    $pdf->Ln(5);

    // --- Insert Chart ---
    if(file_exists($chartFile)){
        $pdf->Image($chartFile,10,30,190,90);
        $pdf->Ln(95);
    }

    // --- Summary Table ---
    $pdf->SetFont("Arial","B",12);
    $pdf->Cell(0,10,"Student Summary",0,1);
    $pdf->SetFont("Arial","",10);
    $pdf->Cell(48,8,"Category",1);
    $pdf->Cell(48,8,"Male",1);
    $pdf->Cell(48,8,"Female",1);
    $pdf->Cell(48,8,"Total",1,1);

    $rows = [
        ["Present",$present['Male'],$present['Female'],$present['Total']],
        ["Passed",$passed['Male'],$passed['Female'],$passed['Total']],
        ["Failed",$failed['Male'],$failed['Female'],$failed['Total']],
        ["Absent",$absent['Male'],$absent['Female'],$absent['Total']]
    ];
    foreach($rows as $r){
        $pdf->Cell(48,8,$r[0],1);
        $pdf->Cell(48,8,$r[1],1);
        $pdf->Cell(48,8,$r[2],1);
        $pdf->Cell(48,8,$r[3],1,1);
    }

    $pdf->Ln(5);

    // --- Grade Summary ---
    $pdf->SetFont("Arial","B",12);
    $pdf->Cell(0,10,"Grade Summary",0,1);
    $pdf->SetFont("Arial","",10);
    $pdf->Cell(48,8,"Grade",1);
    $pdf->Cell(48,8,"Male",1);
    $pdf->Cell(48,8,"Female",1);
    $pdf->Cell(48,8,"Total",1,1);

    mysqli_data_seek($grades_result,0);
    while($g = mysqli_fetch_assoc($grades_result)){
        $pdf->Cell(48,8,$g['Grade'],1);
        $pdf->Cell(48,8,$g['Male'],1);
        $pdf->Cell(48,8,$g['Female'],1);
        $pdf->Cell(48,8,$g['Total'],1,1);
    }

    // --- Quantitative & Qualitative ---
    $pdf->Ln(5);
    $pdf->SetFont("Arial","B",12);
    $pdf->Cell(0,10,"Quantitative & Qualitative Analysis",0,1);
    $pdf->SetFont("Arial","",10);
    $pdf->Cell(48,8,"Type",1);
    $pdf->Cell(48,8,"Pass %",1);
    $pdf->Cell(48,8,"Fail %",1,1);
    $pdf->Cell(48,8,"Quantitative",1);
    $pdf->Cell(48,8,"$pass_percent %",1);
    $pdf->Cell(48,8,"$fail_percent %",1,1);
    $pdf->Cell(48,8,"Qualitative",1);
    $pdf->Cell(48,8,"$pass_percent %",1);
    $pdf->Cell(48,8,"$fail_percent %",1,1);

    // --- Score Analysis ---
    $pdf->Ln(5);
    $pdf->SetFont("Arial","B",12);
    $pdf->Cell(0,10,"Score Analysis",0,1);
    $pdf->SetFont("Arial","",10);
    $pdf->Cell(63,8,"Max Score",1);
    $pdf->Cell(63,8,"Mean Score",1);
    $pdf->Cell(63,8,"Min Score",1,1);
    $pdf->Cell(63,8,$scores['max_score'],1);
    $pdf->Cell(63,8,round($scores['mean_score']),1);
    $pdf->Cell(63,8,$scores['min_score'],1,1);

    $pdf->Output("D","report.pdf");
    exit;
}
?>
