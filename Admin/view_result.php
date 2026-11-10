<?php
require_once "../connection/connection.php";

// Fetch classes
 
// Fetch classes and terms once for reuse
$class_result_junior = mysqli_query($con, "SELECT class_id, class_name FROM classes");
$class_result_senior = mysqli_query($con, "SELECT class_id, class_name FROM classes");
$class_result_primary = mysqli_query($con, "SELECT class_id, class_name FROM classes");

$term_result_junior = mysqli_query($con, "SELECT term_id, t_name FROM terms");
$term_result_senior = mysqli_query($con, "SELECT term_id, t_name FROM terms");
$term_result_primary = mysqli_query($con, "SELECT term_id, t_name FROM terms");

// Get selected values (different for each tab)
$selected_class_junior = $_GET['junior_class_id'] ?? '';
$selected_term_junior  = $_GET['junior_term_id'] ?? '';

$selected_class_senior = $_GET['senior_class_id'] ?? '';
$selected_term_senior  = $_GET['senior_term_id'] ?? '';

$selected_class_primary = $_GET['primary_class_id'] ?? '';
$selected_term_primary  = $_GET['primary_term_id'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Results</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        iframe { border: 1px solid #ccc; }
    </style>
</head>
<body>
<?php include('../Main/Main_header.php'); include('../Main/Main-sidebar.php'); ?>
<main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 w-100">
    <br> 

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" id="resultTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="junior-tab" data-toggle="tab" href="#junior" role="tab">Junior Results</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="senior-tab" data-toggle="tab" href="#senior" role="tab">Senior Results</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="primary-tab" data-toggle="tab" href="#primary" role="tab">Primary Results</a>
        </li>
    </ul>

    <!-- Tab content -->
    <div class="tab-content mt-3">
        <!-- Junior Tab -->
        <div class="tab-pane fade show active" id="junior" role="tabpanel">
            <form method="get" action="">
                <label>Select Class:</label>
                <select class="form-control" name="junior_class_id" required>
                    <option value="">-- Choose Class --</option>
                    <?php while ($row = mysqli_fetch_assoc($class_result_junior)) { ?>
                        <option value="<?php echo $row['class_id']; ?>" 
                            <?php if ($row['class_id'] == $selected_class_junior) echo 'selected'; ?>>
                            <?php echo $row['class_name']; ?>
                        </option>
                    <?php } ?>
                </select><br>

                <label>Select Term:</label>
                <select class="form-control" name="junior_term_id" required>
                    <option value="">-- Choose Term --</option>
                    <?php while ($row = mysqli_fetch_assoc($term_result_junior)) { ?>
                        <option value="<?php echo $row['term_id']; ?>" 
                            <?php if ($row['term_id'] == $selected_term_junior) echo 'selected'; ?>>
                            <?php echo $row['t_name']; ?>
                        </option>
                    <?php } ?>
                </select>

                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary w-50">View Junior Result</button>
                </div>
            </form>
            <?php if (!empty($selected_class_junior) && !empty($selected_term_junior)) { ?>
                <h4>Junior Results Preview</h4>
                <iframe src="juniors.php?class_id=<?php echo $selected_class_junior; ?>&term_id=<?php echo $selected_term_junior; ?>" 
                        width="100%" height="800px"></iframe>
            <?php } ?>
        </div>

        <!-- Senior Tab -->
        <div class="tab-pane fade" id="senior" role="tabpanel">
            <form method="get" action="">
                <label>Select Class:</label>
                <select class="form-control" name="senior_class_id" required>
                    <option value="">-- Choose Class --</option>
                    <?php while ($row = mysqli_fetch_assoc($class_result_senior)) { ?>
                        <option value="<?php echo $row['class_id']; ?>" 
                            <?php if ($row['class_id'] == $selected_class_senior) echo 'selected'; ?>>
                            <?php echo $row['class_name']; ?>
                        </option>
                    <?php } ?>
                </select><br>

                <label>Select Term:</label>
                <select class="form-control" name="senior_term_id" required>
                    <option value="">-- Choose Term --</option>
                    <?php while ($row = mysqli_fetch_assoc($term_result_senior)) { ?>
                        <option value="<?php echo $row['term_id']; ?>" 
                            <?php if ($row['term_id'] == $selected_term_senior) echo 'selected'; ?>>
                            <?php echo $row['t_name']; ?>
                        </option>
                    <?php } ?>
                </select>

                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-success w-50">View Senior Result</button>
                </div>
            </form>
            <?php if (!empty($selected_class_senior) && !empty($selected_term_senior)) { ?>
                <h4>Senior Results Preview</h4>
                <iframe src="view_senior.php?class_id=<?php echo $selected_class_senior; ?>&term_id=<?php echo $selected_term_senior; ?>" 
                        width="100%" height="800px"></iframe>
            <?php } ?>
        </div>

        <!-- Primary Tab -->
        <div class="tab-pane fade" id="primary" role="tabpanel">
            <form method="get" action="">
                <label>Select Class:</label>
                <select class="form-control" name="primary_class_id" required>
                    <option value="">-- Choose Class --</option>
                    <?php while ($row = mysqli_fetch_assoc($class_result_primary)) { ?>
                        <option value="<?php echo $row['class_id']; ?>" 
                            <?php if ($row['class_id'] == $selected_class_primary) echo 'selected'; ?>>
                            <?php echo $row['class_name']; ?>
                        </option>
                    <?php } ?>
                </select><br>

                <label>Select Term:</label>
                <select class="form-control" name="primary_term_id" required>
                    <option value="">-- Choose Term --</option>
                    <?php while ($row = mysqli_fetch_assoc($term_result_primary)) { ?>
                        <option value="<?php echo $row['term_id']; ?>" 
                            <?php if ($row['term_id'] == $selected_term_primary) echo 'selected'; ?>>
                            <?php echo $row['t_name']; ?>
                        </option>
                    <?php } ?>
                </select>

                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-info w-50">View Primary Result</button>
                </div>
            </form>
            <?php if (!empty($selected_class_primary) && !empty($selected_term_primary)) { ?>
                <h4>Primary Results Preview</h4>
                <iframe src="view_primary.php?class_id=<?php echo $selected_class_primary; ?>&term_id=<?php echo $selected_term_primary; ?>" 
                        width="100%" height="800px"></iframe>
            <?php } ?>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


