 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Classes</title>
   
</head>
<body>
            <?php
            session_start();
            require_once '../connection/connection.php';

            // Session check
            if (!isset($_SESSION['LoginClassTeacher']) || empty($_SESSION['LoginClassTeacher'])) {
                die('Not logged in (LoginClassTeacher missing).');
            }

            $teacher_email = mysqli_real_escape_string($con, $_SESSION['LoginClassTeacher']);

            // Get teacher ID
            $q = "SELECT teacher_id FROM teachers_tb WHERE email = '$teacher_email' LIMIT 1";
            $r = mysqli_query($con, $q);
            if (!$r || mysqli_num_rows($r) === 0) die("Teacher not found");
            $teacher_id = (int)mysqli_fetch_assoc($r)['teacher_id'];

            // Get selected class and subject IDs
            $class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
            $subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : 0;
            ?>

            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>View Classes</title>

                 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <link rel="stylesheet" href="../css/classes.css">
                <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- Font Awesome -->
            </head>
            <body>
            <?php include('../Main/class-teacher-sidebar.php'); ?>

            <main class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 w-100">
                <div class="d-flex justify-content-center mt-4">
                      <div style="width: 90%; max-width: 1000px;">
        <h1 class="mb-4">Classes</h1>

        <?php if (!$class_id && !$subject_id): ?>
            <!-- Show all classes -->
            <?php
            $class_query = "SELECT DISTINCT c.class_id, c.class_name 
                            FROM classes c
                            JOIN subjects s ON c.class_id = s.class_id
                            WHERE s.teacher_id = $teacher_id
                            ORDER BY c.class_name";
            $class_result = mysqli_query($con, $class_query);
            if (mysqli_num_rows($class_result) > 0):
            ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Class Name</th>
                            <th>Subjects</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($class = mysqli_fetch_assoc($class_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($class['class_name']); ?></td>
                            <td>
                                <?php
                                $sub_query = "SELECT subject_id, subject_name 
                                              FROM subjects 
                                              WHERE class_id = {$class['class_id']} AND teacher_id = $teacher_id";
                                $sub_result = mysqli_query($con, $sub_query);
                                while ($sub = mysqli_fetch_assoc($sub_result)):
                                ?>
                                <a href="?class_id=<?php echo $class['class_id']; ?>&subject_id=<?php echo $sub['subject_id']; ?>" 
                                   class="btn btn-secondary btn-sm mb-1">
                                    <?php echo htmlspecialchars($sub['subject_name']); ?>
                                </a>
                                <?php endwhile; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <p class="text-muted">No classes assigned to you.</p>
            <?php endif; ?>

        <?php elseif ($class_id && $subject_id): ?>
            <!-- Show pupils for selected class and subject -->
            <?php
            $class_name = mysqli_fetch_assoc(mysqli_query($con, "SELECT class_name FROM classes WHERE class_id=$class_id"))['class_name'];
            $subject_name = mysqli_fetch_assoc(mysqli_query($con, "SELECT subject_name FROM subjects WHERE subject_id=$subject_id"))['subject_name'];

            $students_query = "
                SELECT p.pupil_id, p.first_name, p.other_name, p.last_name, p.DOB, p.gender, p.RegDate,
                       m.marks, t.test_name, term.t_name AS term_name
                FROM pupils_tb p
                LEFT JOIN marks_tb m ON m.pupil_id = p.pupil_id AND m.subject_id=$subject_id AND m.class_id=$class_id
                LEFT JOIN test t ON t.test_id = m.test_id
                LEFT JOIN terms term ON term.term_id = t.term_id
                WHERE p.class_id = $class_id
                ORDER BY p.first_name, t.date_added
            ";
            $students_result = mysqli_query($con, $students_query);
            ?>

            <h3>Students in <?php echo htmlspecialchars($class_name); ?> - <?php echo htmlspecialchars($subject_name); ?></h3>
            <a href="view_classes.php" class="btn btn-sm btn-outline-secondary mb-3">
                <i class="fas fa-arrow-left me-1"></i> Back to Classes
            </a>

            <?php if (mysqli_num_rows($students_result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Gender</th>
                            <th>Test Name</th>
                            <th>Marks</th>
                            <th>Term</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($students_result)):
                            $fullName = trim($row['first_name'] .   ' ' . $row['last_name']);
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['pupil_id']); ?></td>
                            <td><?php echo htmlspecialchars($fullName); ?></td>
                     
                            <td><?php echo htmlspecialchars($row['gender']); ?></td>
                      
                          <td>  <?php echo htmlspecialchars($row['test_name'] ?? 'N/A'); ?></td>
                           <td> <?php echo htmlspecialchars($row['marks'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['term_name'] ?? ''); ?></td>

                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <p class="text-muted">No students found for this subject in this class.</p>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
