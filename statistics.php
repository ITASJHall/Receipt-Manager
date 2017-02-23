<?php
include 'config.php';
include 'opendb.php';
include 'link.php';
include 'header.html';
?>

<script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script type="text/javascript" src="slick/slick.min.js"></script>
<link rel="stylesheet" type="text/css" href="slick/slick.css"/>
<link rel="stylesheet" type="text/css" href="slick/slick-theme.css"/>
<script type="text/javascript">
   $(document).ready(function(){
      $('.stats-slider').slick({
          infinite: true,
          slidesToShow: 3,
          slidesToScroll: 1,
          arrows: true,
          dots:true
      });
   });
</script>


<?php
$stats = array();
//By location stats
$by_location_query = mysqli_query($conn, "SELECT SUM(`cost`) as `total`, `location` FROM `_receipts` GROUP BY `location`");
if($by_location_query){
    $by_location = array();
    while($location = $by_location_query->fetch_assoc()){
        $by_location[] = array('title' => $location['location'], 'stat' => round($location['total'],2));
    }
    $stats[] = array('title' => 'Spent by Location', 'stats' => $by_location, 'formatter' => '$');
}
//By Purchaser stats
$by_purchaser_query = mysqli_query($conn, "SELECT SUM(`cost`) as `total`, `purchaser` FROM `_receipts` GROUP BY `purchaser`");
if($by_purchaser_query){
    $by_purchaser = array();
    while($purchaser = $by_purchaser_query->fetch_assoc()){
        $by_purchaser[] = array('title' => $purchaser['purchaser'], 'stat' => round($purchaser['total'],2));
    }
    $stats[] = array('title' => 'Spent by Purchaser', 'stats' => $by_purchaser, 'formatter' => '$');
}
//By Item stats
$by_item_query = mysqli_query($conn, "SELECT SUM(`cost_per_unit` * `amount`) as `total`, `name`  FROM  `_items_purchased` INNER JOIN `_items` ON `_items_purchased`.`item_id` = `_items`.`id` GROUP BY `item_id`");
if($by_item_query){
    $by_item = array();
    while($item = $by_item_query->fetch_assoc()){
        $by_item[] = array('title' => $item['name'], 'stat' => round($item['total'],2));
    }
    $stats[] = array('title' => 'Spent per Item', 'stats' => $by_item, 'formatter' => '$');
}
//By Item Category stats
$by_item_category_query = mysqli_query($conn, "SELECT SUM(`cost_per_unit` * `amount`) as `total`, `category`  FROM  `_items_purchased` GROUP BY `category`");
if($by_item_category_query){
    $by_item_category = array();
    while($item_category = $by_item_category_query->fetch_assoc()){
        $by_item_category[] = array('title' => $item_category['category'], 'stat' => round($item_category['total'],2));
    }
    $stats[] = array('title' => 'Spent per Category', 'stats' => $by_item_category, 'formatter' => '$');
}
//By Category stats
$by_category_query = mysqli_query($conn, "SELECT SUM(`cost`) as `total`, `type` FROM `_receipts` GROUP BY `type`");
if($by_category_query){
    $by_category = array();
    while($category = $by_category_query->fetch_assoc()){
        $by_category[] = array('title' => $category['type'], 'stat' => round($category['total'],2));
    }
    $stats[] = array('title' => 'Spent per Receipt Category', 'stats' => $by_category, 'formatter' => '$');
}
//By frequency stats
$by_frequency_query = mysqli_query($conn, "SELECT IFNULL(TIMESTAMPDIFF(DAY, MIN(`time_stamp_purchased`), MAX(`time_stamp_purchased`)) / NULLIF(COUNT(DISTINCT `time_stamp_purchased`) - 1,0),0) as `frequency` , `name` FROM  `_items_purchased` INNER JOIN `_items` ON `_items_purchased`.`item_id` = `_items`.`id` GROUP BY `item_id`");
if($by_frequency_query){
    $by_frequency = array();
    while($frequency = $by_frequency_query->fetch_assoc()){
        $by_frequency[] = array('title' => $frequency['name'], 'stat' => round($frequency['frequency'],2));
    }
    $stats[] = array('title' => 'Frequency of Item being purchased', 'stats' => $by_frequency, 'formatter' => 'Days');
}
//By avg_cost stats
$by_avg_cost_query = mysqli_query($conn, "SELECT AVG(`cost_per_unit`) as `avg_cost`, `name` FROM  `_items_purchased` INNER JOIN `_items` ON `_items_purchased`.`item_id` = `_items`.`id` GROUP BY `item_id`");
if($by_avg_cost_query){
    $by_avg_cost = array();
    while($avg_cost = $by_avg_cost_query->fetch_assoc()){
        $by_avg_cost[] = array('title' => $avg_cost['name'], 'stat' => round($avg_cost['avg_cost'],2));
    }
    $stats[] = array('title' => 'Average Cost of Items', 'stats' => $by_avg_cost, 'formatter' => '$');
}
//By Item Count stats
$by_item_count_query = mysqli_query($conn, "SELECT COUNT(*)*`amount` as `total`, `name`  FROM  `_items_purchased` INNER JOIN `_items` ON `_items_purchased`.`item_id` = `_items`.`id` GROUP BY `item_id`");
if($by_item_count_query){
    $by_item_count = array();
    while($item_count = $by_item_count_query->fetch_assoc()){
        if($item_count['name'] == 'Gas'){
            $by_item_count[] = array('title' => $item_count['name'], 'stat' => $item_count['total'] . " L");
        } else {
            $by_item_count[] = array('title' => $item_count['name'], 'stat' => $item_count['total']);
        }
    }
    $stats[] = array('title' => 'How many Items Purchased', 'stats' => $by_item_count, 'formatter' => '');
}
//By Location frequency stats
$by_location_frequency_query = mysqli_query($conn, "SELECT IFNULL(TIMESTAMPDIFF(DAY, MIN(`time_purchased`), MAX(`time_purchased`)) / NULLIF(COUNT(DISTINCT `time_purchased`) - 1,0),0) as `frequency`, `location` FROM  `_receipts` GROUP BY `location`");
if($by_location_frequency_query){
    $by_location_frequency = array();
    while($location_frequency = $by_location_frequency_query->fetch_assoc()){
        $by_location_frequency[] = array('title' => $location_frequency['location'], 'stat' => round($location_frequency['frequency'],2));
    }
    $stats[] = array('title' => 'Frequency of Location Visits', 'stats' => $by_location_frequency, 'formatter' => 'Days');
}
//By Cheapest Location stats
$by_location_cheap_query = mysqli_query($conn, "SELECT MIN(`cost_per_unit`) as `cost`, `size`, `size_unit` , `location`, `name` FROM `_items_purchased`,`_receipts`, `_items` WHERE `_items_purchased`.`item_id` = `_items`.`id` AND `_items_purchased`.`receipt_id` = `_receipts`.`id` GROUP BY `item_id`");
if($by_location_cheap_query){
    $by_location_cheap = array();
    while($location_cheap = $by_location_cheap_query->fetch_assoc()){
        $by_location_cheap[] = array('title' => $location_cheap['name'], 'stat' => $location_cheap['location'] . " At $" . round($location_cheap['cost'],2) . " per " . $location_cheap['size'].$location_cheap['size_unit']);
    }
    $stats[] = array('title' => 'Cheapest Location for Item', 'stats' => $by_location_cheap, 'formatter' => '');
}


include('html/statistics.html.php');
include  'closedb.php';
include  'footer.html';
?>
