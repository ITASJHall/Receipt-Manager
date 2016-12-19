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
	$query .= !empty($_POST['transaction_id']) ? "`tr_id`        = '" . $conn -> real_escape_string(htmlentities($_POST['transaction_id'])) . "', " : '';
	$query .= "`time_stamp_updated` = NOW() ";
	$query .= " WHERE `_receipts`.`id` = " . $entry_id . ";";

	if (mysqli_query($conn, $query)) {
		$successes[] = "Receipt Updated Successfully";

        unset($_POST['edit']);
        $add_array = array('location', 'cost', 'cost_before_tax', 'pst', 'gst', 'method_of_payment', 'points_spent', 'receipt_type', 'points_earned', 'savings_total', 'cashier', 'purchaser', 'time_purchased', 'num_items', 'transaction_id');
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
<?php
include  'closedb.php';
include('html/edit.html.php');
include 'footer.html';
?>