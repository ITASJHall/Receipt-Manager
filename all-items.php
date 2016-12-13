<?php
include 'config.php';
include 'opendb.php';
include 'header.html';
?>

<?php
$today = (!empty($_GET['sort']) && $_GET['sort'] == 'today') ? true : false;
$query = "SELECT ";
$query .= "`_items_purchased`.`id`,`_items`.`name`, `_items_purchased`.`cost_per_unit` as `price`, CONCAT(`_items_purchased`.`size`, `_items_purchased`.`size_unit`) as `size`, `_items_purchased`.`category`, `_items_purchased`.`type`, `_items_purchased`.`amount`, `_items_purchased`.`savings`, `_items_purchased`.`brand`, `_items_purchased`.`time_stamp_purchased`, `_items_purchased`.`time_stamp_created` ";
$query .= "FROM `_items_purchased` INNER JOIN ";
$query .= "`_items` ON `_items_purchased`.`item_id` = `_items`.`id` ";
$query .= ($today) ? "WHERE date(`_items_purchased`.`time_stamp_created`) = date(NOW())" : '';
$result = mysqli_query($conn, $query);

$rows = array();
while ($row = $result -> fetch_assoc()) {
	$rows[] = $row;
}
$entrys = $rows;
?>
<div class="colmask" style="margin-left: 5%; margin-right: 5%; text-align: center;">
	<a href="/all-items?sort=today">Todays Entries</a>&nbsp;&nbsp;<a href="/all-items?sort=all">All Entries</a>
	<table border="0" style="margin: 0 auto;">
		<tr class="tbl_header">
			<th>Item Name</th>
			<th>Price</th>
			<th>Category</th>
			<th>Type</th>
			<th>Size</th>
			<th>Number Purchased</th>
			<th>Savings</th>
			<th>Brand</th>
			<th>Date Purchased</th>
			<?php echo(!$today) ? "<th>Date Entered</th>" : ""; ?>
		</tr>
		<?php

		$stripe = false;
		foreach ($entrys as $entry) {
			$date1 = date_create($entry['time_stamp_purchased']);
			$date2 = date_create($entry['time_stamp_created']);
			// Shade every 2nd line
			$stripe = !$stripe;
			if ($stripe) {
				echo '<tr class="odd"> ';
			} else {
				echo '<tr class="even"> ';
			}

			echo '<td>' . $entry['name'] . '</td>';
			echo '<td>$' . $entry['price'] . '</td>';
			echo '<td>' . $entry['category'] . '</td>';
			echo '<td>' . $entry['type'] . '</td>';
			echo '<td>' . $entry['size'] . '</td>';
			echo '<td>' . $entry['amount'] . '</td>';
			echo '<td>$' . (!empty($entry['savings'])? $entry['savings'] : "0") . '</td>';
			echo '<td>' . $entry['brand'] . '</td>';
			echo '<td>' . date_format($date1, "M jS ") . '</td>';
			echo(!$today) ? '<td>' . date_format($date2, "M jS g:i a ") . '</td>' : '';
			echo '<td>';
			echo '<div class="tbl_header">';
			echo "<a href='" . $link_url . "/edit-item?ID=" . $entry['id'] . "'>Edit Item</a> ";
			echo '</div>';
			echo '</td>';
			echo '</tr>';

		}
		?>
	</table>
</div>

<?php

include 'closedb.php';
include 'footer.html';
?>
