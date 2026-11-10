<?php
session_start();
if (!isset($_SESSION["LoginAdmin"]) || !$_SESSION["LoginAdmin"]) {
    header('location: ../login/login.php');
    exit;
}

require_once "../connection/connection.php";

// Initialize variables
$test_id = $test_name = $term_id = $year = $test_status = '';
$error_message = '';
$success_message = '';

// Get test ID from URL
if (isset($_GET['test_id']) && is_numeric($_GET['test_id'])) {
    $test_id = $_GET['test_id'];

    // Fetch test data
    $query = "SELECT * FROM test WHERE test_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $test_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $test_name = $row['test_name'];
        $term_id = $row['term_id']; // Fix: added this line
        $year = $row['year'];
        $test_status = $row['test_status'];
    } else {
        $error_message = "Test record not found.";
        header("Location: test.php");
        exit;
    }
    mysqli_stmt_close($stmt);
} else if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    // Redirect if no test_id and not a form submission
    header("Location: test.php");
    exit;
}

// Handle form submission
if (isset($_POST['btn_update_test'])) {
    $test_id = $_POST['test_id'];
    $test_id = mysqli_real_escape_string($con, $_POST['test_id']);
    $test_name = mysqli_real_escape_string($con, $_POST['test_name']);
    $term_id = mysqli_real_escape_string($con, $_POST['term_id']);
    $year = mysqli_real_escape_string($con, $_POST['year']);
    $test_status = mysqli_real_escape_string($con, $_POST['test_status']);

  // Update query (fixed)
$query = "UPDATE test 
          SET test_id = ?, test_name = ?, term_id = ?, year = ?, test_status = ? 
          WHERE test_id = ?";

$stmt = mysqli_prepare($con, $query);

if ($stmt) {
    // Bind 6 parameters: test_id, test_name, term_id, year, test_status, test_id (for WHERE)
    mysqli_stmt_bind_param(
        $stmt,
        "isiisi",   // types: i=int, s=string (adjust if needed)
        $test_id,
        $test_name,
        $term_id,
        $year,
        $test_status,
        $test_id
    );

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        $success_message = "Test updated successfully!";
        header("refresh:2; url=test.php");
    } else {
        $error_message = "Error updating test: " . mysqli_error($con);
    }
    mysqli_stmt_close($stmt);
} else {
    $error_message = "Database error: " . mysqli_error($con);
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Test - School Management System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="alternate" href="atom.xml" type="application/atom+xml" title="Atom">
    <link rel="stylesheet" href="../Css/style.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #6f42c1;
            --success-color: #1cc88a;
            --danger-color: #e74a3b;
            --warning-color: #f6c23e;
            --light-bg: #f8f9fc;
        }
        
        body {
            background-color: var(--light-bg);
            font-family: 'Poppins', sans-serif;
            color: #5a5c69;
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .edit-container {
            max-width: 700px;
            margin: 0 auto 40px;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
            border-top: 4px solid var(--primary-color);
        }
        
        .form-label {
            font-weight: 600;
            color: #4a4a4a;
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .btn-outline-secondary {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
        }
        
        .alert {
            border-radius: 8px;
            padding: 15px 20px;
        }
        
        .alert-success {
            background-color: rgba(28, 200, 138, 0.15);
            border-color: var(--success-color);
            color: #0f6848;
        }
        
        .alert-danger {
            background-color: rgba(231, 74, 59, 0.15);
            border-color: var(--danger-color);
            color: #b23b34;
        }
        
        .card-icon {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .card-icon i {
            font-size: 24px;
            color: white;
        }
        
        .input-group-text {
            background-color: #eaecf4;
            border-radius: 8px 0 0 8px;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .status-active {
            background-color: rgba(28, 200, 138, 0.15);
            color: #0f6848;
        }
        
        .status-inactive {
            background-color: rgba(231, 74, 59, 0.15);
            color: #b23b34;
        }
        
        @media (max-width: 768px) {
            .edit-container {
                padding: 20px;
                margin: 0 15px 30px;
            }
        }
    </style>
</head>
<body>
 
   <?php include('../Main/Main-sidebar.php') ?>
    
     
    <div class="container">
        <div class="edit-container">
            <div class="card-icon">
                <i class="fas fa-pen"></i>
            </div>
            
            <h2 class="text-center mb-4">Update Test Information</h2>
            <p class="text-center text-muted mb-4">Modify the test details below and click update to save changes</p>

            <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <div><?php echo $error_message; ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <div><?php echo $success_message; ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="post" action="edit_test.php?test_id=<?php echo htmlspecialchars($test_id); ?>">
                <input type="hidden" name="test_id" value="<?php echo htmlspecialchars($test_id); ?>">
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="test_name" class="form-label">Test Name</label>
                        <input type="text" class="form-control" id="test_name" name="test_name"
                               value="<?php echo htmlspecialchars($test_name); ?>" required
                               pattern="[A-Za-z0-9\s]+" title="Only letters, numbers and spaces allowed">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="year" class="form-label">Year</label>
                        <input type="number" class="form-control" id="year" name="year"
                               value="<?php echo htmlspecialchars($year); ?>" required
                               min="2000" max="<?php echo date('Y') + 5; ?>">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="term_id" class="form-label">Term</label>
                        <select name="term_id" class="form-select" required>
                            <option value="">Select term</option>
                            <?php
                            $sql = "SELECT * FROM terms";
                            $result = $con->query($sql);
                            while ($row = $result->fetch_assoc()) {
                                $selected = ($row['term_id'] == $term_id) ? 'selected' : '';
                                echo '<option value="' . $row['term_id'] . '" ' . $selected . '>' . htmlspecialchars($row['t_name']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <label for="test_status" class="form-label">Status</label>
                        <select class="form-select" id="test_status" name="test_status" required>
                            <option value="Active" <?php echo ($test_status == 'Active') ? 'selected' : ''; ?>>Active</option>
                            <option value="Inactive" <?php echo ($test_status == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                        <div class="mt-2">
                            Current status: 
                            <span class="status-badge <?php echo ($test_status == 'Active') ? 'status-active' : 'status-inactive'; ?>">
                                <i class="fas fa-circle me-1" style="font-size: 8px;"></i>
                                <?php echo $test_status; ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <a href="test.php" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                    <button type="submit" name="btn_update_test" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Test
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Client-side validation
        document.querySelector('form').addEventListener('submit', function (e) {
            const yearInput = document.getElementById('year');
            const currentYear = new Date().getFullYear();

            if (yearInput.value < 2000 || yearInput.value > currentYear + 5) {
                alert('Please enter a valid year between 2000 and ' + (currentYear + 5));
                e.preventDefault();
                yearInput.focus();
            }
        });
        
        // Add real-time validation feedback
        const inputs = document.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.checkValidity()) {
                    this.classList.add('is-valid');
                    this.classList.remove('is-invalid');
                } else {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                }
            });
        });
    </script>
</body>
</html>