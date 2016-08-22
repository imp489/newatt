<?php
@include_once('mygenlib.php');
$_Mcon = new _CLconnection();
$_Mquery = 'select slotcaption from timeslots';
$_Mresult = mysql_query($_Mquery);
$_Mnum = mysql_num_rows($_Mresult);
for ($i=0 ; $i<$_Mnum ; $i++)
 {
  $_Mrow = mysql_fetch_array($_Mresult);
  echo $_Mrow["slotcaption"] . '#';
 } 
?>