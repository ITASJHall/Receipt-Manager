<?php

require_once 'Mobile_Detect.php';

$detect = new Mobile_Detect;

if( $detect->isMobile() || $detect->isTablet() ){
$link_url = "http://192.168.0.21/receipt-manager";
} else {
$link_url = "http://receipt-tracker";
}

?>