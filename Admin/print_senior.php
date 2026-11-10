<?php
require_once '../connection/connection.php'; // Make sure this defines $con as MySQLi connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Book Selection</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
</head>
<body>
    <?php include('../Main/Main_header.php') ?>
    <?php include('../Main/Main-sidebar.php') ?>
    
    <br><br><br><br>
  
    <main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 w-100">
        <div class="container mt-3">
            <div class="row">
                <!-- Left Side: Selection Form -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white text-center">
                            <h5 class="mb-0">Select Class, Term & Exam</h5>
                        </div>
                        <div class="card-body">
                            <form action="senior_report_book.php" method="POST">
                                
                                <!-- Class Selection -->
                                <div class="form-group">
                                    <label for="class_id">Select Class:</label>
                                    <select class="form-control" id="class_id" name="class_id" required>
                                        <option value="">-- Select Class --</option>
                                        <?php
                                        $classes_query = "SELECT * FROM classes ORDER BY class_name ASC";
                                        $classes_result = mysqli_query($con, $classes_query);
                                        while ($row = mysqli_fetch_assoc($classes_result)) {
                                            echo "<option value='{$row['class_id']}'>{$row['class_name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- Term Selection -->
                                <div class="form-group mt-3">
                                    <label for="term_id">Select Term:</label>
                                    <select class="form-control" id="term_id" name="term_id" required>
                                        <option value="">-- Select Term --</option>
                                        <?php
                                        $terms_query = "SELECT * FROM terms ORDER BY t_name ASC";
                                        $terms_result = mysqli_query($con, $terms_query);
                                        while ($row = mysqli_fetch_assoc($terms_result)) {
                                            echo "<option value='{$row['term_id']}'>{$row['t_name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                 

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary w-50">
                                        Generate All PDF
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Pupils Table (optional later) -->
                <div class="col-md-8">
                    <!-- You can load pupils list dynamically once class is selected -->
                </div>
            </div>
        </div>
    </main>

    <script src="../bootstrap/js/jquery.min.js"></script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
