<?php
include 'config.php';
include 'opendb.php'; 

if(!empty($_POST['search'])){

	$result = mysqli_query($conn,"SELECT `name` FROM  `_items` WHERE `name` LIKE '%" . $_POST['search'] . "%'");	
	if($result){
		$rows = array();
		while($row = $result->fetch_assoc()) {
			$rows[] = $row;
		}
		$entrys = $rows;
	}
	if(!empty($entrys)){		
		foreach($entrys as $entry){
			echo "<option value=" . $entry['name'] . ">";
		}
	} else {
		http_response_code(204);		
	}
}

include 'closedb.php';
?>
