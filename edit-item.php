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
	$query .= "`_items_purchased`.`id`,`_items_purchased`.`receipt_id`,`_items`.`name`, `_items_purchased`.`cost_per_unit` as `price`, `_items_purchased`.`size`, `_items_purchased`.`size_unit`, `_items_purchased`.`amount`, `_items_purchased`.`savings`, `_items_purchased`.`brand`, `_items_purchased`.`time_stamp_purchased` ";
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

if(!empty($item)) { ?>
	<script>
		$(function () {
			$("#datepicker1").datepicker();
		});
	</script>
	<?php if(!empty($errors)){ ?>
		<div class="error"><?=implode(', ',$errors); ?></div>
	<?php } elseif(!empty($successes)){ ?>
		<div class="success"><?=implode(', ',$successes); ?></div>
	<?php } ?>
	<?php $time_stamp = date('Y-m-d', strtotime(str_replace('-', '/', htmlentities($item['time_stamp_purchased'])))); ?>
	<div class="colmask-2" style="margin-left: 5%; margin-right: 5%;">
		<form action="?ID=<?= $entry_id ?>" method="post" style="text-align: center;">
			<div>
				<table border="0" style="margin: 0 auto;">
					<tr class="tbl_header">
						<th>Item Name</th>
						<th>Price</th>
						<th>Size</th>
						<th>Number Purchased</th>
						<th>Savings</th>
						<th>Brand</th>
						<th>Date Purchased</th>
						<th>Actions</th>
					</tr>
					<tr class="odd" data-i_id="<?=$item['id']; ?>">
					<td><label></label><?=$item['name']; ?></label></td>
					<td><input type="text" name="price" value="<?=$item['price']; ?>" placeholder="Price" style="width: 50%;"></td>
					<td><input type="text" name="size" value="<?=$item['size']; ?>" placeholder="Size" style="width: 50%;">
						<input type="text" name="size_unit" value="<?=$item['size_unit']; ?>" placeholder="Unit" style="width: 50%;"></td>
					<td><input type="text" name="amount" value="<?=$item['amount']; ?>" placeholder="Amount Purchased" style="width: 50%;"></td>
					<td><input type="text" name="savings" value="<?=$item['savings']; ?>" placeholder="Savings" style="width: 50%;"></td>
					<td><input type="text" name="brand" value="<?=$item['brand']; ?>" placeholder="Brand" style="width: 93%;"></td>
					<td><input id="datepicker1" type="date" name="time_stamp_purchased" value="<?=$time_stamp; ?>" style="width: 92%;"></td>
					<td><input type="submit" name="delete" value="Delete"></td>
					</tr>
				</table>
			</div>
			<input type="submit" name="edit" value="Update Item"/>


		</form>
	</div>
	<?php
}
include 'footer.html';
?>