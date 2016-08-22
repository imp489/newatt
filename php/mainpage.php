<?php 
 session_start();
  if($_POST['rs']!=$_SESSION['rs'])
  {
  	echo "<script>window.location.href='loginpage.php';</script>";
  	exit();
  }  
  @include_once 'mygenclasses.php';
  $_Mcon=new _CLconnection();

  $_Muname=trim(substr($_POST['username'],0,10));
  $_Mupas=trim(substr($_POST['userpass'],0,10));
  $_Mmdpas=md5($_Mupas);
  $_Msqlstr="select * from atusers where usrnm like '{$_Muname}' and usrps like '{$_Mmdpas}'";
  $_Mresult=mysql_query($_Msqlstr);
  if(mysql_num_rows($_Mresult)!=1)
  {echo "<script>window.location.href='loginpage.php';</script>";exit();}  
  
  if(mysql_num_rows($_Mresult)==1)
  {
  	$_Mrow=mysql_fetch_array($_Mresult);
  	$_SESSION['uname']=$_Muname;
  	$_SESSION['uid']=$_Mrow['userID'];
  	$_SESSION['utype=']=$_Mrow['usertype'];
  	$_SESSION['uprivileges']=$_Mrow['privileges'];
  	mysql_free_result($_Mresult);
	$_Mcon=new _CLconnection();
    $_Mactiveterm1=new _CLactiveterm();    
  }
// other main page content
?>