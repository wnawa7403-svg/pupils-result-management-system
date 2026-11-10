<?php
// subjects.php - View/Add/Edit/Delete subjects
session_start();
require_once "../connection/connection.php";

$message = '';
$stream_name = $abbreviation = '';

// -------------------- HANDLE INSERTION --------------------
if (isset($_POST['btn_add_subject'])) {
    $stream_name = trim($_POST['stream_name']);

    if (empty($stream_name)) {
        $message = ['type' => 'danger', 'text' => 'All fields are required.'];
    } else {
        // Check if subject exists
        $check = $con->prepare(
            "SELECT stream_id FROM streams WHERE stream_name = ? "
        );
        $check->bind_param("s", $stream_name);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = ['type' => 'warning', 'text' => 'Stream already exists.'];
        } else {
            // Insert subject
            $stmt = $con->prepare(
                "INSERT INTO streams (stream_name ) VALUES ( ?)"
            );
            $stmt->bind_param("s", $stream_name);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "stream added successfully!";
                header("Location: add_stream.php");
                exit;
            } else {
                $message = ['type' => 'danger', 'text' => 'Error adding streams.'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Streams</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../class/class.css">
    <link rel="stylesheet" href="../css/streams.css">
</head>
<body>
<?php include('../Main/Main-sidebar.php') ?>
<br><br><br>
<main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 w-100">
    <div class="container-fluid">
        <!-- Add Stream Modal -->
        <div class="modal fade" id="addClassModal" tabindex="-1" role="dialog" aria-labelledby="addClassModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addClassModalLabel"><i class="fas fa-plus-circle mr-2"></i>Add Streams</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="add_stream.php">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="stream_name"><i class="fas fa-chalkboard mr-2"></i>Stream Name</label>
                                <input type="text" class="form-control" id="stream_name" name="stream_name" required placeholder="Enter stream name">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" name="btn_add_subject" class="btn btn-primary">Save Stream</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card card-outline card-primary">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-list mr-2"></i>All Streams</span>
                </div>
                <div class="page-header d-flex justify-content-between align-items-center">
                    <h4><i></i>Manage Streams</h4>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addClassModal">
                        <i class="fas fa-plus-circle mr-2"></i>Add New Streams
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="list">
                            <thead>
                                <tr>
                                    <th class="sortable">Stream Name</th>
                                    <th class="sortable">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM streams ORDER BY stream_name ASC";
                                $result = mysqli_query($con, $query);

                                if ($result && mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        
                                        echo "<tr>";
                                        echo "<td><strong>" . ucwords($row['stream_name']) . "</strong></td>";
                                        echo '<td>
                                                <div class="btn-group">
                                                    <a class="btn btn-success btn-sm mr-1 action-btn" href="edit_stream.php?stream_id='.$row['stream_id'].'" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a class="btn btn-danger btn-sm mr-1 action-btn" href="delete_subject.php?stream_id='.$row['stream_id'].'" title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </div>
                                              </td>';
                                        echo "</tr>";
                                    }
                                } else {
                                    echo '<tr><td colspan="2" class="text-center">No streams found.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
 
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="../assets/js/streams.js"></script>
 
<?php if (isset($_SESSION['success_message'])): ?>
<script>
    swal({
        title: "Success!",
        text: "<?php echo addslashes($_SESSION['success_message']); ?>",
        icon: "success",
        button: "OK"
    });
</script>
<?php unset($_SESSION['success_message']); endif; ?>

<?php if (!empty($message) && $message['type'] === 'warning'): ?>
<script>
    swal({
        title: "Warning!",
        text: "<?php echo addslashes($message['text']); ?>",
        icon: "warning",
        button: "OK"
    });
</script>
<?php endif; ?>

<?php if (!empty($message) && $message['type'] === 'danger'): ?>
<script>
    swal({
        title: "Error!",
        text: "<?php echo addslashes($message['text']); ?>",
        icon: "error",
        button: "OK"
    });
</script>
<?php endif; ?>

</body>
</html>