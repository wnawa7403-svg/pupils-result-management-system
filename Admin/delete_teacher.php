<?php  
session_start();
if (!$_SESSION["LoginAdmin"])
{
	header('location:../login/login.php');
}
	require_once "../connection/connection.php";
?>
	<?php 
	if (isset($_GET['teacher_id'])) {
		$eacher_id=$_GET['teacher_id'];
		$query1="delete from teachers_tb where teacher_id='$eacher_id'";
		$run1=mysqli_query($con,$query1);
		if ($run1) {
			header('Location: teacher_info.php');
		}
		else{
			echo "teacher not deleted. Frist of all delete record  from the child table then you can delete from here ";
			header('Refresh: 5; url=display_pupils.php');
		}
	}
	?>
  