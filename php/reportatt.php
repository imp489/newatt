<?php
@include_once "mygenclasses.php";
$_Mcon=new _CLconnection();
$_Msql="select * from tracelogs";
$_Mres=mysql_query($_Msql);
while($_Mrow=mysql_fetch_array($_Mres))
{
	echo $_Mrow[0].' - '.$_Mrow[1].'<br>';
}

mysql_free_result($_Mres);


?>