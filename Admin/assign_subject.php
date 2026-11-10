<?php
error_reporting(0);
require_once "../connection/connection.php";

if (strlen($_SESSION['alogin']) == "") {   
    header("Location: index.php"); 
    exit();
} else {
    
    if (isset($_POST['submit'])) {
        $class = $_POST['class'];
        $subject = $_POST['subject']; 
        $status = 1;
    
        // Correct the variable name to $sql1
        $sql1 = "INSERT INTO tblsubjectcombination (ClassId, SubjectId, status) VALUES (?, ?, ?)";
    
        // Prepare the statement using the correct variable name
        if ($stmt = $con->prepare($sql1)) {
            $stmt->bind_param("iii", $class, $subject, $status);
            $stmt->execute();
    
            if ($stmt->affected_rows > 0) {
                $message = "Subject has been added successfully.";
        echo "<script type='text/javascript'>alert('$message');window.location.href = 'assign_subject.php';</script>";
            } else {
                
                $message = "Something went wrong. Please try again";
                echo "<script type='text/javascript'>alert('$message');window.location.href = 'assign_subject.php';</script>";
            }
    
            $stmt->close();
        } else {
            
        }
    }
}
    ?>
    

<!-- ========== TOP NAVBAR ========== -->
 
<?php include('../Main/Main_header.php') ?>
<?php include('../Main/Main-sidebar.php') ?>
<main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 mb-2 w-100">
                <!-- /.row -->
                <div class="row breadcrumb-div">
                    <div class="col-md-6">
                        <ul class="breadcrumb">
                          
                        </ul>
                    </div>
                </div>
                <!-- /.row -->
            </div>
            <section class="section">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-8 col-md-offset-2">
                            
                                <div class="panel-body">
                                    <?php if ($msg) { ?>
                                        <div class="alert alert-success left-icon-alert" role="alert">
                                            <strong>Well done!</strong> <?php echo htmlentities($msg); ?>
                                        </div>
                                    <?php } elseif ($error) { ?>
                                        <div class="alert alert-danger left-icon-alert" role="alert">
                                            <strong>Oh snap!</strong> <?php echo htmlentities($error); ?>
                                        </div>
                                    <?php } ?>
                                    <form action="assign_subject.php" method="post">
    <div class="form-group">
        <label for="class" class="control-label">Class</label>
        <select name="class" class="form-control" id="class" required="required">
            <option value="">Select Class</option>
            <?php 
            $sql = "SELECT * FROM tblclasses";
            $result = $con->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) { ?>
                    <option value="<?php echo htmlentities($row['id']); ?>">
                        <?php echo htmlentities($row['ClassName']); ?>&nbsp; Section-<?php echo htmlentities($row['Section']); ?>
                    </option>
                <?php }
            } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="subject" class="control-label">Subject</label>
        <select name="subject" class="form-control" id="subject" required="required">
            <option value="">Select Subject</option>
            <?php 
            $sql = "SELECT * FROM tblsubjects";
            $result = $con->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) { ?>
                    <option value="<?php echo htmlentities($row['id']); ?>">
                        <?php echo htmlentities($row['SubjectName']); ?>
                    </option>
                <?php }
            } ?>
        </select>
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary" name="btn_add_sub">Add Subject</button>
    </div>
</form>

    


                                </div>
                            </div>
                        </div>
                        <!-- /.col-md-12 -->
                    </div>
                </div>
            </section>
        </div>
        <!-- /.content-container -->
    </div>
</div>
