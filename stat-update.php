<?php
if(empty($_POST['receipt'])){
	return;
	
} else {
include 'config.php';
include 'opendb.php'; 	

	if(mysqli_query($conn, $query)){
		
		$receipt = json_decode($_POST['receipt']);
	     	
     	$receipt_id = $receipt['id'];
		
		foreach($items_id as $id){
			$query = "UPDATE `_items_purchased` SET `receipt_id` = '" . $receipt_id . "', `time_stamp_purchased` = '" . $date . "' WHERE `id` = '" . $id . "';";
			
			if(mysqli_query($conn, $query)){
			 } else {
			 	echo mysqli_error($conn);
			 }
		}
		
		$receipt_update = mysqli_query($conn,"SELECT COUNT(*) as `total`, SUM(`savings`) as `savings` FROM  `_items_purchased` WHERE `receipt_id` = '" . $receipt_id . "';");		
		
		if($receipt_update) {		
			$update = $receipt_update->fetch_assoc();		
			
			if(!empty($update)){
				$query = "UPDATE `_receipts` SET `savings_total` = IF(`savings_total` IS NULL,'" . $update['savings'] . "', `savings_total`), `num_items` = '" . $update['total'] . "' WHERE `id` = '" . $receipt_id . "';";
				
				if(mysqli_query($conn, $query)){					
				} else {
					echo mysqli_error($conn);
				}
			}
		}
		
				
		$item_update = mysqli_query($conn,"SELECT `item_id` FROM  `_items_purchased` WHERE `receipt_id` = '" . $receipt_id . "';");	
		
		if($item_update){
			while($item_purchased = $item_update->fetch_assoc()) {
				
				$item_stats = array();				
					
				$item_count_query = mysqli_query($conn,"SELECT SUM(`amount`) as `total` FROM  `_items_purchased` WHERE `item_id` = '" . $item_purchased['item_id'] . "';");	
				
				$item_cost_query = mysqli_query($conn,"SELECT SUM(`cost_per_unit`) as `cost` FROM  `_items_purchased` WHERE `item_id` = '" . $item_purchased['item_id'] . "';");
				
				$item_avgcost_query = mysqli_query($conn,"SELECT AVG(`cost_per_unit` / `amount`) as `avg_cost` FROM  `_items_purchased` WHERE `item_id` = '" . $item_purchased['item_id'] . "';");
					
				$item_time_query = mysqli_query($conn,"SELECT IFNULL(TIMESTAMPDIFF(DAY, MIN(`time_stamp_purchased`), MAX(`time_stamp_purchased`)) / NULLIF(COUNT(*) - 1,0),0) as `frequency` FROM  `_items_purchased` WHERE `item_id` = '" . $item_purchased['item_id'] . "';");
				
				$item_stats['total'] = $item_count_query->fetch_assoc()['total'];
				$item_stats['cost'] = $item_cost_query->fetch_assoc()['cost'];
				$item_stats['avgcost'] = $item_avgcost_query->fetch_assoc()['avg_cost'];
				$item_stats['time'] = $item_time_query->fetch_assoc()['frequency'];
				
				$query = "UPDATE `_items` SET 
				`number_purchased` = '" . $item_stats['total'] . "',
				`spent_total` = '" . $item_stats['cost'] . "',
				`cost_avg` = '" . $item_stats['avgcost'] . "',
				`frequency` = '" . $item_stats['time'] . "'
				 WHERE `id` = '" . $item_purchased['item_id'] . "';";
			
				if(mysqli_query($conn, $query)){
					
				} else {
					echo mysqli_error($conn);
				}
				
				
			}
		}
			
	        
     }  else {
	 	echo mysqli_error($conn);
	 }  
} 