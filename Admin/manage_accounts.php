<!---------------- Session starts form here ----------------------->
<?php  
	session_start();
	if (!$_SESSION["LoginAdmin"])
	{
		header('location:../login/login.php');
	}
		require_once "../connection/connection.php";
	?>
<!---------------- Session Ends form here ------------------------>

<?php
	$message = "";
	$account = "";
if (isset($_POST['submit'])) {
	$account = $_POST['account'];
	$user_id = $_POST['user_id'];
	$que="update login set account='$account' where user_id = '$user_id'";
	$run=mysqli_query($con,$que);
	if ($run) {
		$message =  $account == "Activate" ? "Account Activated Successfully" : "Account Deactivated Successfully";
	}	
	else{
		$message = "Account Not Activated  Successfully";
	}
}
?>

<!doctype html>
<html lang="en">
	<head>
		<title>Admin - Manage Accounts</title>
		<!-- Bootstrap CSS -->
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
		<!-- Font Awesome -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
		<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
		<link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
		
		<style>
			:root {
				--primary: #4361ee;
				--secondary: #3f37c9;
				--success: #4cc9f0;
				--light: #f8f9fa;
				--dark: #212529;
				--danger: #e63946;
				--warning: #fca311;
				--info: #4895ef;
			}
			
			* {
				font-family: 'Poppins', sans-serif;
			}
			
			body {
				background-color: #f5f7fb;
				color: #333;
			}
			
			.sub-main {
				background: white;
				border-radius: 12px;
				box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
				padding: 25px;
				margin-top: 20px;
			}
			
			.modal-header {
				border-bottom: none;
				padding: 0 0 20px 0;
			}
			
			.modal-header h1 {
				color: var(--primary);
				font-weight: 700;
				font-size: 2.2rem;
				position: relative;
				display: inline-block;
			}
			
			.modal-header h1:after {
				content: '';
				position: absolute;
				width: 60%;
				height: 4px;
				background: linear-gradient(to right, var(--primary), var(--success));
				bottom: -10px;
				left: 0;
				border-radius: 2px;
			}
			
			.form-group {
				margin-bottom: 1.5rem;
			}
			
			.form-control {
				border-radius: 8px;
				border: 1px solid #e1e5ee;
				padding: 12px 15px;
				font-size: 0.95rem;
				transition: all 0.3s ease;
			}
			
			.form-control:focus {
				border-color: var(--primary);
				box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.15);
			}
			
			label {
				font-weight: 500;
				margin-bottom: 8px;
				color: #495057;
			}
			
			.btn-primary {
				background-color: var(--primary);
				border-color: var(--primary);
				border-radius: 8px;
				padding: 12px 30px;
				font-weight: 500;
				transition: all 0.3s ease;
				box-shadow: 0 4px 6px rgba(67, 97, 238, 0.2);
			}
			
			.btn-primary:hover {
				background-color: var(--secondary);
				border-color: var(--secondary);
				transform: translateY(-2px);
				box-shadow: 0 6px 8px rgba(67, 97, 238, 0.3);
			}
			
			.alert {
				border-radius: 8px;
				border: none;
				padding: 15px 20px;
				font-weight: 500;
			}
			
			.alert-success {
				background-color: rgba(76, 201, 240, 0.15);
				color: #0a9396;
			}
			
			.alert-danger {
				background-color: rgba(230, 57, 70, 0.15);
				color: #ae2012;
			}
			
			.py-2.pl-3 {
				padding: 12px 15px !important;
				border-radius: 8px;
			}
			
			/* Card styling for form */
			.form-container {
				background: white;
				border-radius: 12px;
				box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
				padding: 30px;
				margin-top: 20px;
			}
			
			/* Responsive adjustments */
			@media (max-width: 768px) {
				.modal-header h1 {
					font-size: 1.8rem;
				}
				
				.btn-primary {
					width: 100%;
				}
			}
			
			/* Animation for form elements */
			.form-group {
				animation: fadeIn 0.5s ease forwards;
				opacity: 0;
			}
			
			.form-group:nth-child(1) { animation-delay: 0.1s; }
			.form-group:nth-child(2) { animation-delay: 0.2s; }
			.form-group:nth-child(3) { animation-delay: 0.3s; }
			
			@keyframes fadeIn {
				from { opacity: 0; transform: translateY(10px); }
				to { opacity: 1; transform: translateY(0); }
			}
			
			/* Status indicator */
			.status-indicator {
				display: inline-block;
				width: 10px;
				height: 10px;
				border-radius: 50%;
				margin-right: 8px;
			}
			
			.status-active {
				background-color: #4CAF50;
			}
			
			.status-inactive {
				background-color: #F44336;
			}
            .main-wrapper {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 40px 20px;
}

/* Center card nicely */
.sub-main {
    width: 100%;
    max-width: 900px;
}

		</style>
	</head>
	<body>
		<body>

<?php include('../Main/Main-sidebar.php')?>

<main role="main" class="main-wrapper">

    <div class="sub-main">

        <div class="modal-header text-center">
            <h1><b>Manage Accounts</b></h1>
        </div>

        <?php
        if(($account == "Activate" or $account == "Deactivate") and $message==true)
            $bg_color = "alert-success";
        else if($message==true)
            $bg_color = "alert-danger";
        ?>

        <?php if($message): ?>
        <div class="alert <?php echo $bg_color; ?> text-center">
            <?php echo $message ?>
        </div>
        <?php endif; ?>

        <form action="manage_accounts.php" method="post">

            <div class="row justify-content-center">
                <div class="col-md-6 mb-3">
                    <label>User ID</label>
                    <input type="text" name="user_id" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Account Status</label>
                    <select name="account" class="form-control" required>
                        <option value="">Select Status</option>
                        <option value="Activate">Activate</option>
                        <option value="Deactivate">Deactivate</option>
                    </select>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" name="submit" class="btn btn-primary px-5">
                    <i class="fa fa-refresh"></i> Update Account
                </button>
            </div>

        </form>

    </div>

</main>

</body>

	 
	</body>
</html>