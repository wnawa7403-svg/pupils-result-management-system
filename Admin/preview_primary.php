<?php
require_once '../connection/connection.php'; // your DB connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Preview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<?php include('../Main/Main_header.php') ?>
<?php include('../Main/Main-sidebar.php') ?>

 <main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 mb-2 w-100">
    
    <form action="../Admin/preview_PR.php" method="POST" target="pdf_preview_frame">



        <!-- Class Selection -->
        <div class="mb-3">
            <label for="class_id" class="form-label">Select Class:</label>
            <select name="class_id" id="class_id" class="form-control" required>
                <option value="">-- Select Class --</option>
                <?php
                $classes = mysqli_query($con, "SELECT class_id, class_name FROM classes ORDER BY class_name ASC");
                while ($row = mysqli_fetch_assoc($classes)) {
                    echo "<option value='{$row['class_id']}'>{$row['class_name']}</option>";
                }
                ?>
            </select>
        </div>

        <!-- Term Selection -->
        <div class="mb-3">
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

        <div class="form-group mt-3">
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

        <button type="submit" class="btn btn-primary">Preview PDF</button>
    </form>

    <!-- PDF Preview -->
    <h3 class="mt-4">PDF Preview:</h3>
    <iframe name="pdf_preview_frame" style="width:100%; height:600px; border:1px solid #ccc;"></iframe>
</div>

</body>
</html>
