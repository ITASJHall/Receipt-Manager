<?php
include 'config.php';
include 'opendb.php';
include 'link.php';
include 'header.html';

?>
<script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script type="text/javascript"></script>
<div class="colmask-2">
	<?php

$result = mysqli_query($conn, "SELECT * FROM  `_receipts`");

if ($result){
$rows = array();
while ($row = $result -> fetch_assoc()) {
$rows[] = $row;
}
}

$entrys = $rows;

if (!empty($entrys)){
	?>
	<table border="0" style="margin: 0 auto;">
		<tr class="tbl_header">
			<th>Location Purchased</th>
			<th>Type</th>
			<th>Item Purchesed</th>
			<th>Subtotal</th>
			<th>Tax</th>
			<th>Cost</th>
			<th>Savings</th>
			<th>Points earned</th>
			<th>Dated of Purchased</th>
			<th>Purchaser</th>
			<th>Method Of Payment</th>
		</tr>
		<?php

		$stripe = false;
		foreach ($entrys as $entry) {
			$date = date_create($entry['time_purchased']);
			// Shade every 2nd line
			$stripe = !$stripe;
			if ($stripe) {
				echo '<tr class="odd"> ';
			} else {
				echo '<tr class="even"> ';
			}

			echo '<td>' . $entry['location'] . '</td>';
			echo '<td>' . $entry['type'] . '</td>';
			echo '<td>' . $entry['num_items'] . '</td>';
			echo '<td>$' . $entry['cost_before_tax'] . '</td>';
			echo '<td>PST($' . $entry['pst'] . ') - GST($' . $entry['gst'] . ')</td>';
			echo '<td>$' . $entry['cost'] . '</td>';
			echo '<td>$' . $entry['savings_total'] . '</td>';
			echo '<td>' . ($entry['points_earned'] - $entry['points_spent']) . '</td>';
			echo '<td>' . date_format($date, "M jS ") . '</td>';
			echo '<td>' . $entry['purchaser'] . '</td>';
			echo '<td>' . $entry['method_of_payment'] . '</td>';
			echo '</div>';
			echo '<td>';
			echo '<div class="tbl_header">';
			echo "<a href='" . $link_url . "/edit?ID=" . $entry['id'] . "'>Edit</a> ";
			echo '</div>';
			echo '</td>';
			echo '</tr>';

		}
	?>
	</table>
	<?php } ?>
	<p>
		<a href="<?=$link_url; ?>/add">Add Receipt</a>&nbsp;&nbsp;&nbsp;<a href="<?=$link_url; ?>/all-items">Items</a>&nbsp;&nbsp;&nbsp;<a href="<?=$link_url; ?>/list">List</a>&nbsp;&nbsp;&nbsp;<a href="<?=$link_url; ?>/report">Report</a>&nbsp;&nbsp;&nbsp;<a href="<?=$link_url; ?>/manage">Manage</a>
	</p>

</div>

<?php
if (empty($entrys))
	echo "No receipt Found";

include  'closedb.php';
include  'footer.html';
?>
