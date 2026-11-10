<?php
session_start();
if (!isset($_SESSION["LoginAdmin"]) || !$_SESSION["LoginAdmin"]) {
    header('location:../login/login.php');
    exit;
}

require_once "../connection/connection.php";

// Check if subject_id is set (not class_id)
if (!isset($_GET['subject_id'])) {
    echo "<script>alert('Subject ID not specified!'); window.location.href='add_subjects.php';</script>";
    exit();
}

$subject_id = $_GET['subject_id'];

$success_message = '';
$error_message = '';

// Handle form submission
if (isset($_POST['btn_edit_subject'])) {
    $subject_name   = trim($_POST["subject_name"]);
    $abbreviation   = strtoupper(trim($_POST["abbreviation"]));
    
    // Validation
    if(empty($subject_name) || empty($abbreviation)) {
        $error_message = "All fields are required!";
    } elseif(strlen($subject_name) > 100) {
        $error_message = "Subject name must not exceed 100 characters!";
    } elseif(strlen($abbreviation) > 10) {
        $error_message = "Abbreviation must not exceed 10 characters!";
    } elseif(!preg_match('/^[A-Z0-9]+$/', $abbreviation)) {
        $error_message = "Abbreviation should contain only uppercase letters and numbers!";
    } else {
        // Check if subject name or abbreviation already exists (excluding current subject)
        $check = $con->prepare("SELECT subject_id FROM subjects WHERE (subject_name=? OR abbreviation=?) AND subject_id != ?");
        $check->bind_param("ssi", $subject_name, $abbreviation, $subject_id);
        $check->execute();
        $check->store_result();
        
        if($check->num_rows > 0){
            $error_message = "Subject name or abbreviation already exists!";
        } else {
            // Update subject
            $stmt = $con->prepare("UPDATE subjects SET subject_name=?, abbreviation=?, date_updated=NOW() WHERE subject_id=?");
            if ($stmt === false) {
                die("Prepare failed: " . $con->error);
            }
            
            $stmt->bind_param("ssi", $subject_name, $abbreviation, $subject_id);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Subject updated successfully!";
                header("Location: add_subjects.php");
                exit;
            } else {
                $error_message = "Error updating subject: " . $stmt->error;
            }
            $stmt->close();
        }
        $check->close();
    }
}

// Fetch subject data
$stmt = $con->prepare("SELECT * FROM subjects WHERE subject_id=?");
$stmt->bind_param("i", $subject_id);
$stmt->execute();
$result = $stmt->get_result();
$subject_row = $result->fetch_assoc();
$stmt->close();

// Check if subject exists
if (!$subject_row) {
    echo "<script>alert('Subject not found!'); window.location.href='add_subjects.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Subject</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>   
<?php include('../Main/Main-sidebar.php'); ?>

<main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 w-100">
    
    <div class="container mt-5 mb-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3>Edit Subject</h3>
            </div>
            <div class="card-body">
                
                <?php if(!empty($error_message)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <form action="edit_subject.php?subject_id=<?php echo $subject_id; ?>" method="post">
                    <div class="form-group">
                        <label>Subject Name:</label>
                        <input type="text" class="form-control" name="subject_name" 
                               value="<?php echo htmlspecialchars($subject_row['subject_name']); ?>" 
                               required maxlength="100">
                    </div>

                    <div class="form-group">
                        <label>Abbreviation:</label>
                        <input type="text" class="form-control text-uppercase" name="abbreviation" 
                               value="<?php echo htmlspecialchars($subject_row['abbreviation']); ?>" 
                               required maxlength="10" pattern="[A-Z0-9]+">
                        <small class="form-text text-muted">Uppercase letters and numbers only</small>
                    </div>

                    <div class="form-actions">
                        <button type="reset" class="btn btn-outline-secondary">Reset</button>
                        <button type="submit" class="btn btn-primary" name="btn_edit_subject">Update Subject</button>
                        <a href="add_subjects.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
</body>
</html>