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
$query .= ($today) ? "WHERE date(`_items_purchased`.`time_stamp_purchased`) = date(NOW())" : '';
$result = mysqli_query($conn, $query);

$rows = array();
while ($row = $result -> fetch_assoc()) {
	$rows[] = $row;
}
$entrys = $rows;
?>

<?php

include 'closedb.php';
include('html/all-items.html.php');
include 'footer.html';
?>
