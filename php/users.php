<?php session_start();
    @include_once "mygenclasses.php";
//grouptype : 1:kardani, 2:karshenasi,3:arshad,4:doctora ,5: karshenasi napeyvaste
  $_Mcon=new _CLconnection();

  $_Mtype=$_POST['type'];
  
  if($_Mtype=='chp')//change password
  {
	$_Muserid = $_POST['userid'];
	$_Mnewpass = $_POST['newpass'];
	$_Moldpass = $_POST['oldpass'];
  	echo _Fchangepassword($_Muserid,$_Mnewpass,$_Moldpass);
  }	
  //****************************************
  function _Fchangepassword($_Muserid,$_Mnewpass,$_Moldpass)
  {
  	$_Mnewpass = md5($_Mnewpass);
	$_Moldpass = md5($_Moldpass);
	$_Msqlstr="update atusers set usrps='$_Mnewpass' where userID='$_Muserid' and usrps='$_Moldpass'";
  	if($_Mresult = mysql_query($_Msqlstr))
	 {if(mysql_affected_rows()>0) return  1;
  	else return 0;
	}
	else return -1;
  }

?>