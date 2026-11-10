<?php
require_once '../connection/connection.php'; // your DB connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Preview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/preview_results.css">
</head>
<body class="p-4">
 
<?php include('../Main/Main-sidebar.php') ?>
<main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 w-100">
    <div class="container-fluid py-4">
        
    <div class="row justify-content-center">
        <div class="col-md-8">

            <form action="../Admin/preview_JR.php" method="POST" target="pdf_preview_frame">

                  <div class="row">
                            <div class="col-md-4 mb-3">
                    <label for="class_id" class="form-label">Select Class:</label>
                    <select name="class_id" id="class_id" class="form-control" required>
                        <option value="">-- Select Class --</option>
                        <?php
                        $classes = mysqli_query($con, "SELECT class_id, class_name FROM  classes ORDER BY class_name ASC");
                        while ($row = mysqli_fetch_assoc($classes)) {
                            echo "<option value='{$row['class_id']}'>{$row['class_name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                  
                            <div class="col-md-4 mb-3">
                    <label for="term_id" class="form-label">Select Term:</label>
                    <select name="term_id" id="term_id" class="form-control" required>
                        <option value="">-- Select Term --</option>
                        <?php
                        $terms = mysqli_query($con, "SELECT term_id, t_name FROM terms ORDER BY t_name ASC");
                        while ($row = mysqli_fetch_assoc($terms)) {
                            echo "<option value='{$row['term_id']}'>{$row['t_name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Year Selection -->
               <div class="col-md-4 mb-3">
                    <label for="year">Select Year:</label>
                    <select class="form-control" name="year" required>
                        <option value="">-- Select Year --</option>
                        <?php
                        $years = mysqli_query($con, "SELECT DISTINCT year FROM pupils_tb ORDER BY year ASC");
                        while ($row = mysqli_fetch_assoc($years)) {
                            $selected = ($row['year'] == $year) ? 'selected' : '';
                            echo "<option value='{$row['year']}' $selected>{$row['year']}</option>";
                        }
                        ?>
                    </select>
                </div>     

                <button type="submit" class="btn btn-primary w-100">Preview PDF</button>
            </form>

           <!-- Add this after your iframe -->
<div id="loading" style="display: none; text-align: center; padding: 20px;">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <p class="mt-2">Generating PDF Preview...</p>
</div>

<script>
document.querySelector('form').addEventListener('submit', function() {
    document.getElementById('loading').style.display = 'block';
});

window.addEventListener('load', function() {
    document.getElementById('loading').style.display = 'none';
});
</script>

            <iframe name="pdf_preview_frame" style="width: 100%; height: 600px; border: 1px solid #ccc; margin-top: 20px;"></iframe>
        </div>
    </div>
</main>
