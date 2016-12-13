<?php
include 'config.php';
include 'opendb.php';
include 'link.php';
include 'header.html';

if(isset($_POST['list']) && !empty($_POST['view'])){
    $sql_where = " WHERE `id` in (" . implode(', ', $_POST['view']) . ")";
}

$result = mysqli_query($conn, "SELECT * FROM  `_receipts`" . (!empty($sql_where)? $sql_where : '') . ";");

if ($result){
    $entrys = array();
    while ($row = $result -> fetch_assoc()) {
        $entrys[] = $row;
    }
}



include('html/list.html.php');
