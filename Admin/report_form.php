<?php
require_once '../connection/connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Report Form Selection</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include('../Main/Main-sidebar.php'); ?>

<main class="container-fluid p-4">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow p-4">
        <h4 class="mb-4 text-center">Generate Report Forms</h4>

        <form action="../Admin/Senior_report_book.php" method="POST" target="_blank">

          <div class="row mb-3">
            <!-- CLASS -->
            <div class="col-md-4">
              <label class="form-label">Select Class</label>
              <select name="class_id" class="form-select" required>
                <option value="" selected disabled>-- Select Class --</option>
                <?php
                $classes = mysqli_query($con,"
                  SELECT c.class_id, c.class_name, s.stream_name
                  FROM classes c
                  JOIN streams s ON c.stream_id = s.stream_id
                  ORDER BY c.class_name ASC
                ");
                while($row = mysqli_fetch_assoc($classes)){
                  echo "<option value='{$row['class_id']}'>{$row['class_name']} - {$row['stream_name']}</option>";
                }
                ?>
              </select>
            </div>

            <!-- TERM -->
            <div class="col-md-4">
              <label class="form-label">Select Term</label>
              <select name="term_id" class="form-select" required>
                <option value="" selected disabled>-- Select Term --</option>
                <?php
                $terms = mysqli_query($con,"
                  SELECT term_id, t_name, year
                  FROM terms
                  WHERE status='active'
                  ORDER BY year DESC
                ");
                while($row = mysqli_fetch_assoc($terms)){
                  echo "<option value='{$row['term_id']}'>{$row['t_name']} - {$row['year']}</option>";
                }
                ?>
              </select>
            </div>

            <!-- YEAR -->
            <div class="col-md-4">
              <label class="form-label">Select Year</label>
              <select name="year" class="form-select" required>
                <option value="" selected disabled>-- Select Year --</option>
                <?php
                $years = mysqli_query($con,"
                  SELECT DISTINCT year
                  FROM pupils_tb
                  ORDER BY year DESC
                ");
                while($row = mysqli_fetch_assoc($years)){
                  echo "<option value='{$row['year']}'>{$row['year']}</option>";
                }
                ?>
              </select>
            </div>
          </div>

          <button type="submit" class="btn btn-primary w-100 mt-3">Generate Report Forms</button>

        </form>

      </div>
    </div>
  </div>
</main>

</body>
</html>