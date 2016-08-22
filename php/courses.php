<?php
$courseName = $_POST['cn'];
$courseGroup1 = $_POST['cg1'];
$courseGroup2 = $_POST['cg2'];
$courseGroup3 = $_POST['cg3'];
$coursetype = $_POST['ct'];
$courseAUnits = $_POST['cau'];
$courseTUnits = $_POST['ctu'];
$coursehrdnes = $_POST['ch'];
$coursePre1 = $_POST['cp1'];
$coursePre2 = $_POST['cp1'];
$coursePre3 = $_POST['cp2'];
$courseSim1 = $_POST['cs1'];
$courseSim2 = $_POST['cs2'];
$courseSim3 = $_POST['cs3'];
$courseState = $_POST['cs'];
$courseDesc = $_POST['cd'];
$db = mysql_connect('127.0.0.1','root','vertrigo');
mysql_select_db('att',$db);
$query = "insert into courses  (coursecaption,courseGroup1,courseGroup2,courseGroup3,coursetype,courseAUnits,courseTUnits,coursehrdnes,coursePre1,coursePre2,coursePre3,courseSim1,courseSim2,courseSim3,courseState,courseDesc) values('$courseName','$courseGroup1','$courseGroup2','$courseGroup3','$coursetype','$courseAUnits','$courseTUnits','$coursehrdnes','$coursePre1','$coursePre2','$coursePre3','$courseSim1','$courseSim2','$courseSim3','$courseState','$courseDesc')";
mysql_query($query);
?>