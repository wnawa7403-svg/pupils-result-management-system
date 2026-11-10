<?php  
session_start();
if (!$_SESSION["LoginAdmin"])
{
	header('location:../login/login.php');
}
	require_once "../connection/connection.php";
?>
	<?php 
	if (isset($_GET['test_id'])) {
		$test_id=$_GET['test_id'];
		$query1="delete from test where test_id='$test_id'";
		$run1=mysqli_query($con,$query1);
		if ($run1) {
			header('Location: test.php');
		}
		else{
			echo "Class not deleted. Frist of all delete record  from the child table then you can delete from here ";
			header('Refresh: 5; url=test.php');
		}
	}
	?>
  