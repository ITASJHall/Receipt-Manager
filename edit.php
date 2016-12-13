<?php
include 'header.html';
include 'config.php';
include 'opendb.php';
include 'link.php';

$successes = array();
$errors = array();

$entry_id = !(empty($_GET['ID'])) ? $_GET['ID'] : '';

if (isset($_POST['edit'])) {

	//format dates
	$timepurchased = date('Y-m-d', strtotime(str_replace('-', '/', htmlentities($_POST['time_purchased']))));

	//Ignore empty values and build query
	$query = "UPDATE `_receipts` SET ";
	$query .= !empty($_POST['location']) ? "`location`       = '" . htmlentities($_POST['location']) . "', " : '';
	$query .= !empty($_POST['num_items']) ? "`num_items`      = '" . $conn -> real_escape_string(htmlentities($_POST['num_items'])) . "', " : '';
	$query .= !empty($_POST['cost_before_tax']) ? "`cost_before_tax`      = '" . $conn -> real_escape_string(htmlentities($_POST['cost_before_tax'])) . "', " : '';
	$query .= !empty($_POST['pst']) ? "`pst`      = '" . $conn -> real_escape_string(htmlentities($_POST['pst'])) . "', " : '';
	$query .= !empty($_POST['gst']) ? "`gst`     = '" . $conn -> real_escape_string(htmlentities($_POST['gst'])) . "', " : '';
	$query .= !empty($_POST['cost']) ? "`cost`        = '" . $conn -> real_escape_string(htmlentities($_POST['cost'])) . "', " : '';
	$query .= !empty($_POST['savings_total']) ? "`savings_total`     = '" . $conn -> real_escape_string(htmlentities($_POST['savings_total'])) . "', " : '';
	$query .= !empty($_POST['points_earned']) ? "`points_earned`        = '" . $conn -> real_escape_string(htmlentities($_POST['points_earned'])) . "', " : '';
	$query .= !empty($_POST['points_spent']) ? "`points_spent`        = '" . $conn -> real_escape_string(htmlentities($_POST['points_spent'])) . "', " : '';
	$query .= !empty($_POST['time_purchased']) ? "`time_purchased`        = '" . $conn -> real_escape_string($timepurchased) . "', " : '';
	$query .= !empty($_POST['purchaser']) ? "`purchaser`        = '" . $conn -> real_escape_string(htmlentities($_POST['purchaser'])) . "', " : '';
	$query .= !empty($_POST['method_of_payment']) ? "`method_of_payment`        = '" . $conn -> real_escape_string(htmlentities($_POST['method_of_payment'])) . "', " : '';
	$query .= "`time_stamp_updated` = NOW() ";
	$query .= " WHERE `_receipts`.`id` = " . $entry_id . ";";

	if (mysqli_query($conn, $query)) {
		$successes[] = "Receipt Updated Successfully";

        unset($_POST['edit']);
        $add_array = array('location', 'cost', 'cost_before_tax', 'pst', 'gst', 'method_of_payment', 'points_spent', 'receipt_type', 'points_earned', 'savings_total', 'cashier', 'purchaser', 'time_purchased', 'num_items');
        foreach($_POST as $key=>$value){
            if(in_array($key, $add_array)){
                unset($_POST[$key]);
            }
        }
        $items = array();
        foreach($_POST as $key=>$value){
            $key = explode('-',$key);
            $items[$key[1]][$key[0]] = $value;
        }

		foreach($items as $id => $item){

			$timestampurchased = date('Y-m-d', strtotime(str_replace('-', '/', htmlentities($item['time_stamp_purchased']))));

			$item_query = "UPDATE `_items_purchased` SET ";
			$item_query .= !empty($item['price']) ?				   "`cost_per_unit` = '" . 		  $conn -> real_escape_string(htmlentities($item['price'])) . "', " : '';
			$item_query .= !empty($item['cate']) ?				   "`category` = '" . 		  $conn -> real_escape_string(htmlentities($item['cate'])) . "', " : '';
			$item_query .= !empty($item['type']) ?				   "`type` = '" . 		  $conn -> real_escape_string(htmlentities($item['type'])) . "', " : '';
			$item_query .= !empty($item['size']) ?				   "`size` = '" . 				  $conn -> real_escape_string(htmlentities($item['size'])) . "', " : '';
			$item_query .= !empty($item['size_unit']) ?			   "`size_unit` = '" . 			  $conn -> real_escape_string(htmlentities($item['size_unit'])) . "', " : '';
			$item_query .= !empty($item['amount']) ? 		  	   "`amount` = '" . 			  $conn -> real_escape_string(htmlentities($item['amount'])) . "', " : '';
			$item_query .= !empty($item['savings']) ? 			   "`savings` = '" . 			  $conn -> real_escape_string(htmlentities($item['savings'])) . "', " : '';
			$item_query .= !empty($item['brand']) ? 			   "`brand` = '" . 			      $conn -> real_escape_string(htmlentities($item['brand'])) . "', " : '';
			$item_query .= !empty($item['time_stamp_purchased']) ? "`time_stamp_purchased` = '" . $conn -> real_escape_string($timestampurchased) . "', " : '';
			$item_query .= "`time_stamp_updated` = NOW() ";
			$item_query .= " WHERE `id` = " . $id . ";";

			if (mysqli_query($conn, $item_query)) {
				$successes[] = "Receipt Item-" . $id . " Updated Successfully";
			} else {
				$errors[] = "Error updating Item-" . $id . ": " . mysqli_error($conn);
			}

		}


	} else {
		$errors[] = "Error updating receipt: " . mysqli_error($conn);
	}

} else if (isset($_POST['delete'])) {

	//format dates
	$timepurchased = date('Y-m-d', strtotime(str_replace('-', '/', htmlentities($_POST['time_purchased']))));

	//Ignore empty values and build query
	$query = "DELETE FROM `_items_purchased`";
	$query .= " WHERE `_items_purchased`.`receipt_id` = " . $entry_id . ";";

	if (mysqli_query($conn, $query)) {
		$successes[] = "Receipt Items Deleted";

		$query = "DELETE FROM `_receipts`";
		$query .= " WHERE `_receipts`.`id` = " . $entry_id . ";";

		if (mysqli_query($conn, $query)) {
			$successes[] = "Receipt Deleted";
			header("Location: {$link_url}/");
			exit;

		} else {
			$errors[] = "Error Deleting " . $entry_id . ": " . mysqli_error($conn);
		}

	} else {
		$errors[] = "Error Items where receipt-" . $entry_id . ": " . mysqli_error($conn);
	}

}

if (!empty($entry_id)) {
	$result = mysqli_query($conn, "SELECT * FROM  `_receipts` WHERE `id` = " . $entry_id . " LIMIT 1");
	
	$entrys = $result->fetch_assoc();
	
	$query = "SELECT ";
	$query .= "`_items_purchased`.`id`, `_items_purchased`.`receipt_id`,`_items`.`name`, `_items_purchased`.`cost_per_unit` as `price`, `_items_purchased`.`size`, `_items_purchased`.`category`, `_items_purchased`.`type`, `_items_purchased`.`size_unit`, `_items_purchased`.`amount`, `_items_purchased`.`savings`, `_items_purchased`.`brand`, `_items_purchased`.`time_stamp_purchased` ";
	$query .= "FROM `_items_purchased` INNER JOIN ";
	$query .= "`_items` ON `_items_purchased`.`item_id` = `_items`.`id` ";
	$query .= "WHERE `receipt_id` = " . $entry_id . ";";	
	
	$item_result = mysqli_query($conn, $query);	
	
	$items = array();
	while ($item = $item_result -> fetch_assoc()) {
		$items[] = $item;
	}
	
}
?>

<script>
	$(function() {
		$("#datepicker1").datepicker();
		$("#datepicker2").datepicker();

        $('#receipt-items input[type="date"]').datepicker();

		$('input[name="delete"]').on('click',function(){
            var $this = $(this);
			if(!$this.closest('tr').hasClass('strikeout')){
				$.ajax({
					method:'POST',
					url:'edit-item.php?delete=true',
					data: {
						'delete_item': $this.closest('tr').data('i_id'),
						'item_receipt': $this.data('id')
					}
				}).done(function(opt, statusText, xhr){
                    var status = xhr.status;
					if(status == 200){
                        $this.closest('tr').addClass('strikeout');
                        $this.closest('tr').prev('tr').prev('tr').addClass('strikeout');
					} else {

					}
				});
			}
		});
	}); 
</script>
<?php if(!empty($errors)){ ?>
    <div class="error"><?=implode(', ',$errors); ?></div>
<?php } elseif(!empty($successes)){ ?>
    <div class="success"><?=implode(', ',$successes); ?></div>
<?php } ?>

<div class="colmask-2"  style="margin-left: 5%; margin-right: 5%;">
	<form action="?submit&ID=<?=$entry_id ?>" method="post"  style="text-align: center;">
		<div>
			<table border="0" style="margin: 0 auto;">
				<tr class="tbl_header">
					<th>Location Purchased</th>
					<th>Item Purchesed</th>
					<th>Subtotal</th>
					<th>PST</th>
				</tr>
				<tr>
					<td>
					<input type="text" name="location" value="<?=$entrys['location']; ?>" >
					</td>
					<td>
					<input type="text" name="num_items" value="<?=$entrys['num_items']; ?>" >
					</td>
					<td>
					<input type="text" name="cost_before_tax" value="<?=$entrys['cost_before_tax']; ?>" >
					</td>
					<td>
					<input type="text" name="pst" value="<?=$entrys['pst']; ?>" >
					</td>
				</tr>
				<tr class="tbl_header">
					<th>GST</th>
					<th>Cost</th>
					<th>Savings</th>
					<th>Points Earned</th>
				</tr>
				<tr>
					<td>
					<input type="text" name="gst" value="<?=$entrys['gst']; ?>" >
					</td>
					<td>
					<input type="text" name="cost" value="<?=$entrys['cost']; ?>" >
					</td>
					<td>
					<input type="text" name="savings_total" value="<?=$entrys['savings_total']; ?>" >
					</td>
					<td>
					<input type="text" name="points_earned" value="<?=$entrys['points_earned']; ?>" >
					</td>
				</tr>
				<tr class="tbl_header">
					<th>Points Spent</th>
					<th>Dated of Purchased</th>
					<th>Purchaser</th>
					<th>Method Of Payment</th>
				</tr>
				<tr>
					<td>
					<input type="text" name="points_spent" value="<?=$entrys['points_spent']; ?>" >
					</td>
					<td>
					<input id="datepicker1" type="date" name="time_purchased" value="<?=date('Y-m-d', strtotime(str_replace('-', '/', htmlentities($entrys['time_purchased'])))); ?>"  >
					</td>
					<td>
					<input type="text" name="purchaser" value="<?=$entrys['purchaser']; ?>" >
					</td>
					<td>
					<input type="text" name="method_of_payment" value="<?=$entrys['method_of_payment']; ?>" >
					</td>
				</tr>
	
			</table>
		</div>
		<div>
			<table border="0" style="margin: 0 auto;" id="receipt-items">
				<?php
		
				$stripe = false;
				foreach ($items as $item) {
					// Shade every 2nd line ?>
					<tr class="tbl_header">
						<th>Item Name</th>
						<th>Price</th>
						<th>Category</th>
						<th>Type</th>
						<th>Size</th>
					</tr>
					<?php
					$stripe = !$stripe;
					$time_stamp = date('Y-m-d', strtotime(str_replace('-', '/', htmlentities($item['time_stamp_purchased']))));
					if ($stripe) { ?>
						<tr class="odd" data-i_id="<?=$item['id']; ?>">
					<?php } else { ?>
						<tr class="even" data-i_id="<?=$item['id']; ?>">
					<?php } ?>
		
						<td><label></label><?=$item['name']; ?></label></td>
						<td><input type="text" name="price-<?=$item['id']; ?>" value="<?=$item['price']; ?>" placeholder="Price" style="width: 50%;"></td>
						<td><input type="text" name="cate-<?=$item['id']; ?>" value="<?=$item['category']; ?>" placeholder="Category" style="width: 50%;"></td>
						<td><input type="text" name="type-<?=$item['id']; ?>" value="<?=$item['type']; ?>" placeholder="Type" style="width: 50%;"></td>
						<td><input type="text" name="size-<?=$item['id']; ?>" value="<?=$item['size']; ?>" placeholder="Size" style="width: 50%;">
							<input type="text" name="size_unit-<?=$item['id']; ?>" value="<?=$item['size_unit']; ?>" placeholder="Unit" style="width: 50%;"></td>
					</tr>
					<tr class="tbl_header">
						<th>Number Purchased</th>
						<th>Savings</th>
						<th>Brand</th>
						<th>Date Purchased</th>
						<th>Action</th>
					</tr>
					<?php if ($stripe) { ?>
						<tr class="odd" data-i_id="<?=$item['id']; ?>">
					<?php } else { ?>
						<tr class="even" data-i_id="<?=$item['id']; ?>">
					<?php } ?>
						<td><input type="text" name="amount-<?=$item['id']; ?>" value="<?=$item['amount']; ?>" placeholder="Amount Purchased" style="width: 50%;"></td>
						<td><input type="text" name="savings-<?=$item['id']; ?>" value="<?=$item['savings']; ?>" placeholder="Savings" style="width: 50%;"></td>
						<td><input type="text" name="brand-<?=$item['id']; ?>" value="<?=$item['brand']; ?>" placeholder="Brand" style="width: 93%;"></td>
						<td><input type="date" name="time_stamp_purchased-<?=$item['id']; ?>" value="<?=$time_stamp; ?>" style="width: 92%;"></td>
						<td><input type="button" name="delete" value="Delete" data-id="<?=$item['receipt_id']; ?>"></td>
					</tr>
					<tr></tr>
					<tr></tr>
					<tr></tr>

			<?php } ?>
			</table>
		</div>
		<input type="submit" name="edit" value="Update Receipt" /><input type="submit" name="delete" value="Delete Receipt" />
		
		
	</form>
</div>
<?php

include 'footer.html';
?>