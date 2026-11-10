<?php
 
require_once "../connection/connection.php";

if (isset($_POST['submit'])) {
    $user_id = trim($_POST['email']); // User ID (email)
    $new_password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($new_password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.location='reset_password.php';</script>";
        exit();
    }

    // Check if user exists
    $stmt = $con->prepare("SELECT user_id FROM login WHERE user_id=? LIMIT 1");
    $stmt->bind_param("s",$user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result && $result->num_rows === 1) {
        // Hash the new password
        $password_hashed = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password in database
        $stmt2 = $con->prepare("UPDATE login SET Password=? WHERE user_id=?");
        $stmt2->bind_param("ss",$password_hashed,$user_id);
        if ($stmt2->execute()) {
            echo "<script>alert('Password reset successfully! You can now log in.'); window.location='reset_password.php';</script>";
        } else {
            echo "<script>alert('Error updating password: ".$stmt2->error."');</script>";
        }
        $stmt2->close();
    } else {
        echo "<script>alert('User ID not found!'); window.location='reset_password.php';</script>";
        exit();
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
                padding: 40px;
                padding-right : 30px;
                margin-top: 20px;
                margin: 29px auto; /* Centers horizontally */
  				  width: 80%; /* or any fixed width like 600px */
                position: relative;
               
            }

			
			.modal-header {
				border-bottom: none;
				padding: 0 0 20px 0;
			}
			
			.modal-header h1 {
				color: var(--primary);
				font-weight: 700;
                text-align : center;
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
				margin-top: 25px;
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
            /* Fix Bootstrap dropdown arrows in sidebar */
.sidebar .dropdown-toggle::after {
    display: none !important;
}

.sidebar .dropdown-container {
    border-left: none !important;
}
		</style>
	</head>
	<body>
		 <br><br><br>
    <?php  include('../Main/Main-sidebar.php'); ?>
    
		<main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 mb-2 w-100">
			<div class="sub-main">
				<div class="modal-header text-black text-center">
					<h1><b>RESET USER PASSWORD</b></h1>
				</div>
				<div class="row">
					<div class="container">
						<div class="row justify-content-center">
							<div class="col-md-10">
							
									<form action="reset_password.php" method="post">
										<div class="row">
											<div class="col-md-12">
												<div class="form-group">
													 
                                                        <input type="text" name="email" placeholder="Enter User ID" class="form-control" required>
												</div>
											</div>
											<div class="col-md-12">
												<div class="form-group">
													 
													 <input type="password" id="password" name="password" placeholder="Enter New Password" class="form-control" required>
												</div>
											</div>
                                            <div class="col-md-12">
												<div class="form-group">
												 
                                                 <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm New Password" class="form-control" required>
												</div>
											</div>
										</div>
                                            <div class="col text-center mb-3">
                                                                <input type="checkbox" onclick="togglePassword()"> Show Password
                                                            </div>

                                                    </div>
                                                </div>

                                                <script>
                                                    function togglePassword() {
                                                        let pwd = document.getElementById("password");
                                                        let cpwd = document.getElementById("confirm_password");

                                                        if (pwd.type === "password" && cpwd.type === "password") {
                                                            pwd.type = "text";
                                                            cpwd.type = "text";
                                                        } else {
                                                            pwd.type = "password";
                                                            cpwd.type = "password";
                                                        }
                                                    }
                                                </script>

										<div class="row mt-3">
											<div class="col-md-12">
												<div class="form-group text-center">
													<button type="submit" name="submit" value="Change" class="btn btn-primary px-5 py-2">
														<i class="fa fa-refresh me-2"></i> Update Account Status
													</button>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
	
		</main>
	</body>
</html>