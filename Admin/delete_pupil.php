<?php  
session_start();
if (!$_SESSION["LoginAdmin"])
{
	header('location:../login/login.php');
}
	require_once "../connection/connection.php";
?>
	<?php 
	if (isset($_GET['pupils_Id'])) {
		$pupils_Id=$_GET['pupils_Id'];
		$query1="delete from pupils_tb where pupils_Id='$pupils_Id'";
		$run1=mysqli_query($con,$query1);
		if ($run1) {
			header('Location: display_pupils.php');
		}
		else{
			echo "Class not deleted. Frist of all delete record  from the child table then you can delete from here ";
			header('Refresh: 5; url=display_pupils.php');
		}
	}
	?>
  