<?php
session_start();

if (!isset($_SESSION["LoginAdmin"]) || !$_SESSION["LoginAdmin"]) {
    header('location: ../login/login.php');
    exit;
}

require_once "../connection/connection.php";

/* =========================
   GET STREAM ID
========================= */
if (!isset($_GET['stream_id']) || empty($_GET['stream_id'])) {
    $_SESSION['error_message'] = "Invalid stream ID.";
    header("Location: add_stream.php");
    exit;
}

$stream_id = (int) $_GET['stream_id'];
$stream_name = '';
$message = '';

/* =========================
   FETCH STREAM DATA
========================= */
$stmt = $con->prepare("SELECT stream_name FROM streams WHERE stream_id = ?");
$stmt->bind_param("i", $stream_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $_SESSION['error_message'] = "Stream not found.";
    header("Location: add_stream.php");
    exit;
}

$stmt->bind_result($stream_name);
$stmt->fetch();
$stmt->close();

/* =========================
   UPDATE STREAM
========================= */
if (isset($_POST['btn_edit_stream'])) {

    $new_stream_name = trim($_POST['stream_name']);

    if (empty($new_stream_name)) {
        $message = "Stream name is required.";
    } else {

        // Check duplicate (exclude current stream)
        $check = $con->prepare(
            "SELECT stream_id FROM streams WHERE stream_name = ? AND stream_id != ?"
        );
        $check->bind_param("si", $new_stream_name, $stream_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "Stream name already exists.";
        } else {

            $update = $con->prepare(
                "UPDATE streams SET stream_name = ? WHERE stream_id = ?"
            );
            $update->bind_param("si", $new_stream_name, $stream_id);

            if ($update->execute()) {
                $_SESSION['success_message'] = "Stream updated successfully!";
                header("Location: add_stream.php");
                exit;
            } else {
                $message = "Error updating stream.";
            }
            $update->close();
        }
        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Stream</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>

<?php include('../Main/Main-sidebar.php'); ?>
<br><br><br>

<div class="container">
    <div class="card mx-auto" style="max-width: 500px;">
        <div class="card-header bg-primary text-white">
            Edit Stream
        </div>

        <div class="card-body">

            <?php if (!empty($message)): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="form-group">
                    <label>Stream Name</label>
                    <input type="text"
                           name="stream_name"
                           class="form-control"
                           value="<?php echo htmlspecialchars($stream_name); ?>"
                           required>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="add_stream.php" class="btn btn-secondary">
                        Cancel
                    </a>

                    <button type="submit" name="btn_edit_stream" class="btn btn-success">
                        Update Stream
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

</body>
</html>
