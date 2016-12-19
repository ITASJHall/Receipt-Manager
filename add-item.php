<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="css/site.css"/>
        <title>Receipt Manager</title>
    </head>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
  	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script> 
    <body>    	
<?php
include 'config.php';
include 'opendb.php'; 


if (isset($_POST['add'])){
	
	$result = mysqli_query($conn,"SELECT `name` FROM  `_items` WHERE `name` = '" . $_POST['item_new'] . "' LIMIT 1");	
	if($result){
		$row = $result->fetch_assoc();					
	}
	
	 if(empty($row)){  
		 $query = "INSERT INTO `_items` SET ";     
	     $query .= "`name`        = '" .  $conn->real_escape_string(htmlentities($_POST['item_new'])) . "', ";   
	     $query .= "`number_purchased`     = '0', ";
	     $query .= "`spent_total`     = '0.00', ";
	     $query .= "`cost_avg`     = '0.00', ";
	     $query .= "`frequency`     = '0', ";
	     $query .= "`timestamp_created` = NOW() ";
	     	 
		 if(mysqli_query($conn, $query)){	     	       
	     } else {
	        die(mysqli_error($conn));
	     }
     }	 
} elseif (isset($_GET['submit']) && isset($_POST['addItem'])) { 
	unset($_POST['addItem']);
	if(!empty($_POST)){ ?>
	 <script>
	 	var managed_items = <?php echo json_encode($_POST); ?>;
	 	window.parent.addItems(managed_items);
	 </script> 
<?php
	}
 }

	$result = mysqli_query($conn,"SELECT * FROM  `_items`");	
	if($result){
		$rows = array();
		while($row = $result->fetch_assoc()) {
			$rows[] = $row;
		}
		$entrys = $rows;
	}


?>
<div style="padding: 3px;">
	<form action="?add" method="post">
		<table border="0" width="100%">
			<tr class="tbl_header">
				<th>Name</th>
			</tr>
			<tr>
				<td><input name="item_new" placeholder="Enter new Item" required/><input type="submit" name="add" value="Add Item" /></td>
			</tr>
		</table>
	</form>

	<?php if(!empty($entrys)){ ?>
	<form action="?submit" method="post">
		<table border="0" width="100%">
			<tr class="tbl_header">
				<th>Item Name</th>
				<th>&nbsp;</th>
			</tr>
			<?php $row = true; ?>
			<?php foreach($entrys as $entry) { ?>
				<?php if($row) echo '<tr>'; ?>
					<td><input type="checkbox" name="<?=$entry['name'];?>" value="<?=$entry['name'];?>"><label><?=$entry['name'];?></label></td>
				<?php if(!$row) echo '</tr>'; ?>
				<?php $row = !$row; ?>
			<?php } ?>

		</table>
		<input type="submit" name="addItem" value="Add to List"  />
	</form>
</div>


<?php 
}
include 'closedb.php';
include 'footer.html';
?>