<?php
$teacherName = $_POST['tn'];
$teacherFamily = $_POST['tf'];
$teacherDegree = $_POST['td'];
$teacherField = $_POST['tfi'];
$teacherTel = $_POST['tt'];
$teacherMobile = $_POST['tm'];
$teacherState = $_POST['ts'];
$teacherAdrs = $_POST['ta'];
$teacherDesc = $_POST['tde'];
$db = mysql_connect('127.0.0.1','root','vertrigo');
mysql_select_db('att',$db);
$query = "insert into tchrs  (teacherName,teacherFamily,teacherDegree,teacherField,teacherTel,teacherMobile,teacherState,teacherAdrs,teacherDesc) values('$teacherName','$teacherFamily','$teacherDegree','$teacherField','$teacherTel','$teacherMobile','$teacherState','$teacherAdrs','$teacherDesc')";
mysql_query($query);
?>