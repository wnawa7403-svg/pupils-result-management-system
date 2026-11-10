<?php  
session_start();
if (!$_SESSION["LoginAdmin"]) {
    header('location:../login/login.php');
}
require_once "../connection/connection.php";

// Add Term
if (isset($_POST['btn_term'])) {
    $term_number = mysqli_real_escape_string($con, $_POST["term_number"]);
    $t_name = mysqli_real_escape_string($con, $_POST["t_name"]);
    $year = mysqli_real_escape_string($con, $_POST["year"]);
    $status = mysqli_real_escape_string($con, $_POST["status"]);

    $query = "INSERT INTO terms (term_number, t_name, year, status) 
              VALUES ('$term_number', '$t_name', '$year', '$status')";
    $result = mysqli_query($con, $query);

    if ($result) {
        $msg = "Term added successfully";
    } else {
        $msg = "Error: " . mysqli_error($con);
    }
}

// Delete Term
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    mysqli_query($con, "DELETE FROM terms WHERE term_id = $id");
    echo "<script>
            Swal.fire({icon:'success', title:'Deleted!', text:'Term deleted successfully', timer:2000, showConfirmButton:false})
            .then(()=>{ window.location.href='terms.php'; });
          </script>";
    exit;
}

// Fetch terms for table
$terms = mysqli_query($con, "SELECT * FROM terms ORDER BY year DESC, term_number ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Terms</title>
<link rel="stylesheet" href="../Css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
.table-container { margin-top: 20px; background: white; padding: 20px; border-radius: 10px; }
table { width: 100%; border-collapse: collapse; }
table th, table td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
table th { background: #1a365d; color: white; }
.btn { padding: 6px 12px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; }
.btn-primary { background: #1a365d; color: white; }
.btn-success { background: #28a745; color: white; }
.btn-warning { background: #ffc107; color: #333; }
.btn-danger { background: #dc3545; color: white; }
.modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); }
.modal-content { background-color: #fefefe; margin: 10% auto; padding: 20px; border-radius: 10px; width: 400px; }
.close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
.close:hover { color: red; }
.form-group { margin-bottom: 15px; }
.form-control { width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc; }
</style>
</head>
<body>

<?php include('../Main/Main-sidebar.php'); ?>

<main class="main-content">
    <div class="content-card">
        <h2>Manage Terms</h2>
        <button class="btn btn-primary" id="openModal"><i class="fas fa-plus"></i> Add Term</button>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Term Number</th>
                        <th>Term Name</th>
                        <th>Year</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($terms)) { ?>
                        <tr>
                            <td><?= $row['term_id'] ?></td>
                            <td><?= $row['term_number'] ?></td>
                            <td><?= $row['t_name'] ?></td>
                            <td><?= $row['year'] ?></td>
                            <td><?= $row['status'] ?></td>
                            <td>
                                <button class="btn btn-warning editBtn" 
                                    data-id="<?= $row['term_id'] ?>"
                                    data-number="<?= $row['term_number'] ?>"
                                    data-name="<?= $row['t_name'] ?>"
                                    data-year="<?= $row['year'] ?>"
                                    data-status="<?= $row['status'] ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger deleteBtn" data-id="<?= $row['term_id'] ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Add/Edit Modal -->
<div id="termModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3 id="modalTitle">Add New Term</h3>
        <form method="post" action="terms.php" id="termForm">
            <input type="hidden" name="term_id" id="term_id">
            <div class="form-group">
                <label>Term Number</label>
                <select name="term_number" id="term_number" class="form-control" required>
                    <option value="">Select</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
            </div>
            <div class="form-group">
                <label>Term Name</label>
                <select name="t_name" id="t_name" class="form-control" required>
                    <option value="">Select</option>
                    <option value="Term 1">Term 1</option>
                    <option value="Term 2">Term 2</option>
                    <option value="Term 4">Term 3</option>
                </select>
            </div>
            <div class="form-group">
                <label>Year</label>
                <input type="number" name="year" id="year" class="form-control" min="2000" max="<?= date('Y')+5 ?>" value="<?= date('Y') ?>" required>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="">Select</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div><br>
            <button type="submit" name="btn_term" class="btn btn-success" id="submitBtn">Submit</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Modal logic
var modal = document.getElementById("termModal");
var openBtn = document.getElementById("openModal");
var span = document.getElementsByClassName("close")[0];
var modalTitle = document.getElementById("modalTitle");
var form = document.getElementById("termForm");

openBtn.onclick = function() {
    modal.style.display = "block";
    modalTitle.textContent = "Add New Term";
    form.reset();
    document.getElementById("term_id").value = "";
}
span.onclick = function() { modal.style.display = "none"; }
window.onclick = function(event) { if (event.target == modal) { modal.style.display = "none"; } }

// Edit Term
document.querySelectorAll('.editBtn').forEach(btn=>{
    btn.onclick = function(){
        modal.style.display = "block";
        modalTitle.textContent = "Edit Term";
        document.getElementById("term_id").value = this.dataset.id;
        document.getElementById("term_number").value = this.dataset.number;
        document.getElementById("t_name").value = this.dataset.name;
        document.getElementById("year").value = this.dataset.year;
        document.getElementById("status").value = this.dataset.status;
    }
});

// Delete Term
document.querySelectorAll('.deleteBtn').forEach(btn=>{
    btn.onclick = function(){
        let id = this.dataset.id;
        Swal.fire({
            title: 'Are you sure?',
            text: "This will delete the term permanently!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result)=>{
            if(result.isConfirmed){
                window.location.href = "terms.php?delete_id="+id;
            }
        });
    }
});

// Success alert after adding term
<?php if(isset($msg)){ ?>
Swal.fire({icon:'success', title:'Done!', text:'<?= $msg ?>', timer:2000, showConfirmButton:false});
<?php } ?>
</script>

</body>
</html>
<?php mysqli_close($con); ?>