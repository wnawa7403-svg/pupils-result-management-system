<?php
session_start();
require_once "../connection/connection.php";

$success = '';
$error = '';

if (
    isset($_POST['btn_assign_forms']) &&
    isset($_POST['subject_id']) &&
    !empty($_POST['class_ids'])
) {
    $subject_id = (int) $_POST['subject_id'];
    $class_ids  = $_POST['class_ids'];

    foreach ($class_ids as $class_id) {
        $class_id = (int) $class_id;

        // Get the stream_id of this class
        $stream_result = $con->query("SELECT stream_id FROM classes WHERE class_id = $class_id");
        $stream_row = $stream_result->fetch_assoc();
        $stream_id = $stream_row['stream_id'];

        // Check if this subject is already assigned in this stream
        $check = $con->prepare("
            SELECT sf.id
            FROM subject_forms sf
            JOIN classes c ON sf.class_id = c.class_id
            WHERE sf.subject_id = ? AND c.stream_id = ?
            LIMIT 1
        ");
        $check->bind_param("ii", $subject_id, $stream_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows == 0) {
            // Insert if not already assigned in this stream
            $stmt = $con->prepare("INSERT INTO subject_forms (subject_id, class_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $subject_id, $class_id);
            $stmt->execute();
            $stmt->close();
            $success = "Subject assigned to classes successfully!";
        } else {
            $error = "This subject is already assigned in stream ID $stream_id.";
        }

        $check->close();
    }
}
 
/* =========================
   FETCH ASSIGNED SUBJECTS
========================= */
$sql = "
  SELECT 
        sf.id,
        c.class_name,
        sn.stream_name,
        s.subject_name,
        s.abbreviation
    FROM subject_forms sf
    JOIN classes c ON sf.class_id = c.class_id
    JOIN streams sn ON c.stream_id = sn.stream_id
    JOIN subjects s ON sf.subject_id = s.subject_id
    ORDER BY sn.stream_name, c.class_name, s.subject_name
";
$assignments = $con->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Subjects</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include('../Main/Main-sidebar.php'); ?>
    <main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 w-100">
    
    <div class="container mt-5 mb-5">
<div class="container mt-4">

    <h3 class="mb-3">Assign Subject to Classes</h3>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- ASSIGN FORM -->
    <form method="post" class="card p-3 mb-4">
        <div class="form-group">
            <label>Select Subject</label>
            <select name="subject_id" class="form-control" required>
                <option value="">-- Select Subject --</option>
                <?php
                $subjects = $con->query("SELECT * FROM subjects ORDER BY subject_name");
                while ($s = $subjects->fetch_assoc()) {
                    echo "<option value='{$s['subject_id']}'>" . htmlspecialchars($s['subject_name']) . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
    <label>Select Classes (Multiple)</label>
    <select name="class_ids[]" class="form-control" multiple required>
        <?php
        // Fetch all classes with streams
        $classes = $con->query("
            SELECT c.class_id, c.class_name, s.stream_name
            FROM classes c
            JOIN streams s ON c.stream_id = s.stream_id
            ORDER BY c.class_name
        ");

        while ($c = $classes->fetch_assoc()) {
            // Display Class Name - Stream Name
            $display = $c['class_name'] . " => " . $c['stream_name'];
            echo "<option value='{$c['class_id']}'>" . htmlspecialchars($display) . "</option>";
        }
        ?>
    </select>
    <small class="text-muted">
        Hold <b>Ctrl</b> (Windows) or <b>Cmd</b> (Mac) to select multiple
    </small>
</div>
        <button type="submit" name="btn_assign_forms" class="btn btn-primary">
            Assign Subject
        </button>
    </form>

    <!-- ASSIGNED LIST -->
    <h4>Assigned Subjects</h4>
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Class</th>
                <th>Subject</th>
                <th>Abbreviation</th>
                <th width="80">Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($assignments->num_rows > 0): ?>
            <?php while ($row = $assignments->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['class_name'] . "=>" . $row['stream_name']) ?></td>
                    <td><?= htmlspecialchars($row['subject_name']) ?></td>
                    <td><?= htmlspecialchars($row['abbreviation']) ?></td>
                    <td class="text-center">
                        <a href="delete_subject_form.php?id=<?= $row['id'] ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Remove this subject from class?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4" class="text-center">No assignments found</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

</div>
</body>
</html>
