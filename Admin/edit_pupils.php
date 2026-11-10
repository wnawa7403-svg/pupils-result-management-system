<?php
session_start();

if (!isset($_SESSION["LoginAdmin"])) {
    echo "<script>alert('You Are Not Authorized For This Link'); window.location.href='pupils.php';</script>";
    exit();
}

require_once "../connection/connection.php";

if (!isset($_GET['pupil_id'])) {
    echo "<script>alert('Pupil ID not specified!'); window.location.href='pupils.php';</script>";
    exit();
}

$pupil_id = $_GET['pupil_id'];

if (isset($_POST['submit'])) {
    $first_name  = mysqli_real_escape_string($con, $_POST['first_name']);
    $last_name   = mysqli_real_escape_string($con, $_POST['last_name']);
    $other_name  = mysqli_real_escape_string($con, $_POST['other_name']);
    
    $DOB         = $_POST['DOB'];
    $gender      = $_POST['gender'];
    $address     = mysqli_real_escape_string($con, $_POST['address']);
    
    $year        = mysqli_real_escape_string($con, $_POST['year']);
    
    $class_id    = $_POST['class_id'];

    $query = "UPDATE pupils_tb 
              SET first_name='$first_name',
                  last_name='$last_name',
                  other_name='$other_name',
                 
                  DOB='$DOB',
                  gender='$gender',
                  address='$address',
                  
                  year='$year',
                 
                  class_id='$class_id'
              WHERE pupil_id='$pupil_id'";

    if (mysqli_query($con, $query)) {
       $_SESSION['success_message'] = "pupil updated successfully!";
    header("Location: pupils.php");
    exit;
    } else {
        echo "<script>alert('Error updating pupil data: " . mysqli_error($con) . "');</script>";
    }
}

$query = "SELECT * FROM pupils_tb WHERE pupil_id='$pupil_id'";
$run   = mysqli_query($con, $query);
$row   = mysqli_fetch_assoc($run);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Pupil</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<style>
    body { background-color: rgb(248, 251, 252); }
    .form-group { margin-bottom: 15px; }
    label { font-weight: 600; color: black; display: block; margin-bottom: 0.3rem; }
    .btn-primary { background-color: #007bff; border-color: #007bff; }
    .btn-primary:hover { background-color: #0056b3; border-color: #004085; }
</style>
</head>
<body>

<?php include('../Main/Main-sidebar.php'); ?>

<main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 mb-2 w-100">
    
    <div class="container mt-5 mb-5">
        <div class="card card-outline rounded-0 w-100">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">Edit Pupil Information</h3>
            </div>
            <div class="card-body">
                <form action="edit_pupils.php?pupil_id=<?php echo $pupil_id; ?>" method="post">
                    <div class="form-row">
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" name="first_name" class="form-control" value="<?php echo $row['first_name']; ?>" required>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" name="last_name" class="form-control" value="<?php echo $row['last_name']; ?>" required>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label>Other Name</label>
                                <input type="text" name="other_name" class="form-control" value="<?php echo $row['other_name']; ?>">
                            </div>
                        </div>

                        

                    <div class="form-row">
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label>Date of Birth</label>
                                <input type="date" name="DOB" class="form-control" value="<?php echo $row['DOB']; ?>" required>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label>Gender</label>
                                <select name="gender" class="form-control" required>
                                    <option value="Male" <?= $row['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?= $row['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label>Address</label>
                                <input type="text" name="address" class="form-control" value="<?php echo $row['address']; ?>" required>
                            </div>
                        </div>

                        
                    <div class="form-row">
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label>Year</label>
                                <input type="text" name="year" class="form-control" value="<?php echo $row['year']; ?>" required>
                            </div>
                        </div>

                    
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label>Class</label>
                                <select name="class_id" class="form-control" required>
                                    <option value="">Select Class</option>
                                    <?php
                                        $sql = "SELECT * FROM classes";
                                        $result = $con->query($sql);
                                        while ($class = $result->fetch_assoc()) {
                                            echo '<option value="' . $class['class_id'] . '" ' . 
                                                 ($row['class_id'] == $class['class_id'] ? 'selected' : '') . 
                                                 '>' . $class['class_name'] . '</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label>Registration Date</label>
                                <input type="text" class="form-control" value="<?php echo $row['RegDate']; ?>" readonly>
                            </div>
                        </div>
                    </div> <!-- row 3 -->

                    <div class="form-group mt-3">
                        <button type="submit" name="submit" class="btn btn-primary btn-block">Update Pupil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
          // Success messages from PHP
    <?php if(isset($_SESSION['success_message'])): ?>
        swal("Success!", "<?= $_SESSION['success_message']; ?>", "success");
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    </script>
</main>
</body>
</html>
