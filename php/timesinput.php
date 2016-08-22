<?php
@include_once('mygenlib.php');
$_Mcon = new _CLconnection();
mysql_query("delete from timeslots");
$_Mout=1;
$_Mstr = $_POST['str'];
$_Marr = explode('#',$_Mstr);
sort($_Marr);
for ($i=1 ; $i<count($_Marr) ; $i++)
 {
   $_Mstr = explode(',',$_Marr[$i]);
   $_Mspl = explode('-',$_Mstr[0]);
   $_Mstr1 = $_Mspl[1] . '-' . $_Mspl[0];
   $_Mquery = "insert into timeslots(slotcaption,slotfromid,slottoid,usagepriority)values('$_Mstr1','$_Mspl[0]','$_Mspl[1]','$_Mstr[1]')";
   if(!(mysql_query($_Mquery)))
   	$_Mout=0;
 }
 return $_Mout;
?>