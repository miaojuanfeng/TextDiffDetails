<?php
/*
 * Last modified: 2016-12-01
 * Translate english <-> chinese
 * Agoda modified  + 
 * Axa coupon
 * 
 * */

$is_login = isset($_SESSION['uid']);

$sorder_country = $_GET['country'];
$sorder_departuredate = $_GET['d_date'];
$sorder_arrivaldate = $_GET['a_date'];
$sorder_qty = $_GET['so_qty'];
$sdevice_name = $_GET['device_name'];

$promotion_id =$_GET['promotion_id'];

if($promotion_id != '') $sorder_qty = 1;
if($sorder_country != '' && $sorder_qty =='') {$sorder_qty = 1;}
if($sorder_country == '' && $promotion_id =='') {$sorder_qty = '3';}


if(!$is_login) header("Location: login.php");
$is_fb_login =$_SESSION['utype'] == 'fb'? true: false;


if($iss_login){
$uname = $_SESSION['uname'];
$uemail = $_SESSION['uamail'];
$uid = $_SESSION['uid'];
}
