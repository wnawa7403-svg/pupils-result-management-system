<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database credentials
 
	$con=mysqli_connect("localhost","root","","school");
	if(!$con)
	{
		echo "Connection is not Successfully";
	}
 
?>
