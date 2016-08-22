<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML><HEAD>
<BODY>
<?php 
echo 'vgfhkvhjvhj';
  @include_once "mygenclasses.php";
  $con=new connection();

  $sqlstr="select * from activitylogs";
	$result=mysql_query($sqlstr);
	$row=mysql_fetch_array($result);
	var_dump($row);

?>
</BODY></HTML>