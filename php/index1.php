<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fa" lang="fa" dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>
    <p>آزمايشگاه مدار منطقي</p>
    <p>آزمايشگاه </p>
<?php 
echo 'vgfhkvhjvhj';
  @include_once "mygenclasses.php";
  $_Mcon=new _CLconnection();

  $_Msqlstr="select * from activitylogs";
	$_Mresult=mysql_query($_Msqlstr);
	$_Mrow=mysql_fetch_array($_Mresult);
	var_dump($_Mrow);

?>
    
</body>
</html>
