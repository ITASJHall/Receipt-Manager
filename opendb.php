<?php
// This is an example opendb.php
$conn = new mysqli($dbhost, $dbuser, $dbpass); 
if ($conn->connect_error){
	die('Error connecting to mysql' . $conn->connect_error);
}                     
mysqli_select_db($conn,$dbname);
	   
?> 