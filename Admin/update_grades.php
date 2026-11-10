<?php
session_start();
if (!$_SESSION["Admin"]) {
    header('location:../login/login.php');
}
require_once "../connection/connection.php";

// Handle update form submission
if (isset($_POST['update'])) {
    $marks_id = $_POST['marks_id'];
    $marks = $_POST['marks'];
    $remarks = $_POST['remarks'];

    $updateQuery = "UPDATE marks SET marks=?, remarks=? WHERE marks_id=?";
    $stmt = mysqli_prepare($con, $updateQuery);
    mysqli_stmt_bind_param($stmt, "ssi", $marks, $remarks, $marks_id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Marks updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating: " . mysqli_error($con) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Update Marks</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h3>Marks List</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Pupil</th>
                <th>Subject</th>
                <th>Test</th>
                <th>Marks</th>
                <th>Remarks</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
<?php
$query = "SELECT m.marks_id, m.marks, m.remarks, s.subject_name, 
          t.test_name, p.first_name,p.last_name
          FROM marks m
          JOIN pupils_tb p ON m.pupils_Id = p.pupil_id
          JOIN subjects s ON m.subject_id = s.subject_id
          JOIN test t ON m.test_id = t.test_id
          ORDER BY m.marks_id ASC";
$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_assoc($result)) {
    ?>
    <tr>
        <td><?= htmlspecialchars($row['first_name'].' '.$row['last_name']); ?></td>
        <td><?= htmlspecialchars($row['subject_name']); ?></td>
        <td><?= htmlspecialchars($row['test_name']); ?></td>
        <td>
            <form method="post">
                <input type="hidden" name="marks_id" value="<?= $row['marks_id'] ?>">
                <input type="text" name="marks" value="<?= $row['marks'] ?>" class="form-control" required>
        </td>
        <td>
                <input type="text" name="remarks" value="<?= $row['remarks'] ?>" class="form-control">
        </td>
        <td>
                <button type="submit" name="update" class="btn btn-sm btn-primary">Update</button>
            </form>
        </td>
    </tr>
<?php
}
?>
        </tbody>
    </table>
</div>
</body>
</html>
