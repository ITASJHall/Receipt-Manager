<?php

include 'config.php';
include 'opendb.php';

if (isset($_GET['delete']) && isset($_POST['delete_item'])) {

	if(!empty($_POST['delete_item'])){
		$result = mysqli_query($conn, "SELECT * FROM  `_items_purchased` WHERE `id` = " . $_POST['delete_item'] . " LIMIT 1");

		$item = $result->fetch_assoc();

		if(!empty($item)){
			//Ignore empty values and build query
			$query = "UPDATE `_receipts` SET ";
			$query .= !empty($item['amount']) ? "`num_items`   = (`num_items`-'" . $item['amount'] . "'), " : '';
			$query .= "`time_stamp_updated` = NOW() ";
			$query .= " WHERE `_receipts`.`id` = " . $_POST['item_receipt'] . ";";

			if (mysqli_query($conn, $query)) {
				$item_query = "DELETE FROM `_items_purchased` ";
				$item_query .= "WHERE `id` = " . $item['id'] . ";";
				if (mysqli_query($conn, $item_query)) {
					echo "Success!";
					die;
				} else {
					echo mysqli_error($conn);
					http_response_code(202);
					die;
				}
			} else {
				echo mysqli_error($conn);
				http_response_code(202);
				die;
			}
		} else {
			var_dump($item);
			http_response_code(202);
			die;

		}

	} else {
		echo $_POST['delete_item'];
		http_response_code(202);
		die;
	}

}


if (isset($_POST['edit'])) {

	$id = $_GET['ID'];
	$timestampurchased = date('Y-m-d', strtotime(str_replace('-', '/', htmlentities($_POST['time_stamp_purchased']))));

	$item_query = "UPDATE `_items_purchased` SET ";
	$item_query .= !empty($_POST['price']) ?				   "`cost_per_unit` = '" . 		  $conn -> real_escape_string(htmlentities($_POST['price'])) . "', " : '';
	$item_query .= !empty($_POST['category']) ?				   "`category` = '" . 		  $conn -> real_escape_string(htmlentities($_POST['category'])) . "', " : '';
	$item_query .= !empty($_POST['type']) ?				   "`type` = '" . 		  $conn -> real_escape_string(htmlentities($_POST['type'])) . "', " : '';
	$item_query .= !empty($_POST['size']) ?				   "`size` = '" . 				  $conn -> real_escape_string(htmlentities($_POST['size'])) . "', " : '';
	$item_query .= !empty($_POST['size_unit']) ?			   "`size_unit` = '" . 			  $conn -> real_escape_string(htmlentities($_POST['size_unit'])) . "', " : '';
	$item_query .= !empty($_POST['amount']) ? 		  	   "`amount` = '" . 			  $conn -> real_escape_string(htmlentities($_POST['amount'])) . "', " : '';
	$item_query .= !empty($_POST['savings']) ? 			   "`savings` = '" . 			  $conn -> real_escape_string(htmlentities($_POST['savings'])) . "', " : '';
	$item_query .= !empty($_POST['brand']) ? 			   "`brand` = '" . 			      $conn -> real_escape_string(htmlentities($_POST['brand'])) . "', " : '';
	$item_query .= !empty($_POST['time_stamp_purchased']) ? "`time_stamp_purchased` = '" . $conn -> real_escape_string($timestampurchased) . "', " : '';
	$item_query .= "`time_stamp_updated` = NOW() ";
	$item_query .= " WHERE `id` = " . $id . ";";

	if (mysqli_query($conn, $item_query)) {
		$successes[] = "Receipt Item-" . $id . " Updated Successfully";
	} else {
		$errors[] = "Error updating Item-" . $id . ": " . mysqli_error($conn);
	}

} elseif (isset($_POST['delete'])){
    $id = $_GET['ID'];

    $result = mysqli_query($conn, "SELECT * FROM  `_items_purchased` WHERE `id` = " . $id . " LIMIT 1");

    $item = $result->fetch_assoc();

    if(!empty($item)){
        //Ignore empty values and build query
        $query = "UPDATE `_receipts` SET ";
        $query .= !empty($item['amount']) ? "`num_items`   = (`num_items`-'" . $item['amount'] . "'), " : '';
        $query .= "`time_stamp_updated` = NOW() ";
        $query .= " WHERE `_receipts`.`id` = " . $item['receipt_id'] . ";";

        if (mysqli_query($conn, $query)) {
            $successes[] = "Receipt " . $item['receipt_id'] . " Updated Successfully";
            $item_query = "DELETE FROM `_items_purchased` ";
            $item_query .= "WHERE `id` = " . $item['id'] . ";";
            if (mysqli_query($conn, $item_query)) {
                $successes[] = "Item-" . $id . " Deleted Successfully";
				header("Location: {$link_url}/");
				exit;

            } else {
                $errors[] = "Error Deleting Item-" . $id . ": " . mysqli_error($conn);
            }
        } else {
            $errors[] = "Error updating Receipt-" . $item['receipt_id'] . ": " . mysqli_error($conn);
        }
    } else {
        $errors[] = "Unable to load Item " . $id;
    }

}


$entry_id = !(empty($_GET['ID'])) ? $_GET['ID'] : '';

if (!empty($entry_id)) {

	$result = mysqli_query($conn, "SELECT * FROM  `_receipts` WHERE `id` = " . $entry_id . " LIMIT 1");
	
	$entrys = $result->fetch_assoc();
	
	$query = "SELECT ";
	$query .= "`_items_purchased`.`id`,`_items_purchased`.`receipt_id`,`_items`.`name`, `_items_purchased`.`cost_per_unit` as `price`, `_items_purchased`.`size`, `_items_purchased`.`category`, `_items_purchased`.`type`, `_items_purchased`.`size_unit`, `_items_purchased`.`amount`, `_items_purchased`.`savings`, `_items_purchased`.`brand`, `_items_purchased`.`time_stamp_purchased` ";
	$query .= "FROM `_items_purchased` INNER JOIN ";
	$query .= "`_items` ON `_items_purchased`.`item_id` = `_items`.`id` ";
	$query .= "WHERE `_items_purchased`.`id` = " . $entry_id . ";";
	
	$item_result = mysqli_query($conn, $query);

	$item = $item_result -> fetch_assoc();
	
} else {
    header("Location: http://{$_SERVER['HTTP_HOST']}");
    exit;
}

include 'header.html';
include('html/edit-item.html.php');
include 'closedb.php';
include 'footer.html';
?>