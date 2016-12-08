<?php
include 'config.php';
include 'opendb.php';
include 'header.html';
?>

<?php
$entry_id = !(empty($_GET['ID'])) ? $_GET['ID'] : '';

$query = "SELECT ";
$query.= "`comments`.`id`,`client`.`name`, `client`.`project_name`, `comments`.`comment`, `comments`.`time` ";
$query.= "FROM `comments` INNER JOIN ";
$query.= "`client` ON `comments`.`cid` = `client`.`id` ";
$query.= (!empty($_GET['sort']) && $_GET['sort'] == 'today')? "WHERE date(`comments`.`timestamp_created`) = date(NOW())" : '';
$query.= "AND `comments`.`cid` = " . $entry_id;
var_dump($query);
$result = mysqli_query($conn, $query);

$rows = array();
while ($row = $result -> fetch_assoc()) {
	$rows[] = $row;
}
$entrys = $rows;
?>
<div>
<a href="/comments?sort=today&ID=<?=$entry_id;?>">Todays Entries</a>&nbsp;&nbsp;<a href="/comments?sort=all&ID=<?=$entry_id;?>">All Entries</a>	
<table border="0">
<tr class="tbl_header">
<th>Client Name</th>
<th>Project Title</th>
<th>Project Comments</th>
<th>Task Time</th>
</tr>
<?php

$stripe = false;
foreach ($entrys as $entry) {

// Shade every 2nd line
$stripe = !$stripe;
if ($stripe) {
echo '<tr class="odd"> ';
} else {
echo '<tr class="even"> ';
}

echo '<td>' . $entry['name'] . '</td>';
echo '<td>' . $entry['project_name'] . '</td>';
echo '<td>' . $entry['comment'] . '</td>';
echo '<td>' . $entry['time'] . '</td>';
echo '</div>';
echo '<td>';
echo '<div class="tbl_header">';
echo "<a href='/editComment?ID=" . $entry['id'] . "'>Edit Comment</a> ";
echo '</div>';
echo '</td>';
echo '</tr>';

}
?>
</table>
</div>

<?php
if($result->num_rows == 0){
	echo "<h3>No entries today</h3>";
}

include 'closedb.php';
include 'footer.html';
?>
