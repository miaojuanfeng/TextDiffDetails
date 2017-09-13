<?php
/*
 * Last modified: 2016-12-01
 * Translate english <-> chinese
 * Agoda modified  + 
 * Axa coupon
 * 
 * */
session_start();

$sorder_country = $_GET['country'];
$sorder_departuredate = $_GET['d_date'];
$sdevice_name = $_GET['device_name'];

$promotion_id =$_GET['prtion_id'];

if($promotion_id != '') $sorder_qty = 1;
if($sorder_country != '' && $sorder_qty =='') {$sorder_qty = 1;}
if($sorder_country == '' && $promotion_id =='') {$sorder_qty = '';}


if(!$is_login) header("Location: sigin.php");
$is_fb_login =$_SESSION['utype'] == 'fb'? true: false;


if($is_login){
	$uname = $_SESSION['uname'];
	$uemail = $_SESSION['uemail'];
	$uid = $_SESSION['uid'];
}

?>