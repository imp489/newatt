<?php
$capacity = $_POST['capacity'];
$roomcaption = $_POST['rc'];
$equipments = $_POST['eq'];
$roomType = $_POST['rt'];
$buildingID = $_POST['bID'];
$roomplaceID = $_POST['rpID'];
$db = mysql_connect('127.0.0.1','root','vertrigo');
mysql_select_db('att',$db);
$query = "insert into rooms (capacity,roomcaption,equipments,roomType,buildingID,roomplaceID) values('$capacity','$roomcaption','$equipments','$roomType','$buildingID','$roomplaceID')";
mysql_query($query);
?>