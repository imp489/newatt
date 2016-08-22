<?php session_start();
  //@include "mygenclasses.php";
  @include_once "mygenlib.php";

  $_Mcon=new _CLconnection();
  //remove later
  
  //remove to here

  //@include "mygenclasses.php";
  //should be optimized no connecting in each page and no setting of activeterm
  //$_Mcon=new connection('localhost','root','vertrigo','att');
  //$_Mcon->connectdb();
  $_Mcourseid=$_POST['courseid'];$_Mgroupid=$_POST['groupid'];$_Mreporttype=$_POST['reporttype'];//course in all groups or for a special group

  //$_Mcourseid=1;$_Mgroupid=0;
  $_Mcoursett=_Fgetcourseschedule($_Mcourseid,$_Mgroupid);
  echo $_Mcoursett;
  
  
  function _Fgetcourseschedule($_Mcourseid,$_Mgroupid)
  {
  	$_Moutstr='';
  	if($_Mgroupid!=0)
  	{
  		$_Msqlstr="select timeslots,roomcaption,roomID,teacherID,teachername,teacherfamily from assignments where courseID={$_Mcourseid} and groupID={$_Mgroupid} and termID={$_SESSION['activetermid']}";
	  	$_Mresult=mysql_query($_Msqlstr);	
  		while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  		$_Moutstr.='#'.$_Mrow['timeslots'].'~~'.$_Mrow['roomcaption'].'~'.$_Mrow['teachername'].' '.$_Mrow['teacherfamily'].'~~'.$_Mrow['courseID'].'~'.$_Mrow['roomID'].'~'.$_Mrow['teacherID'].'~'.$_Mrow['groupID'];
  	}	
  	else
  	{	
  		$_Msqlstr="select teacherID,roomID,groupID,courseID,timeslots,coursecaption,roomcaption,teachername,teacherfamily,groupcaption from assignments where courseID={$_Mcourseid} and termID={$_SESSION['activetermid']}";
  		$_Mresult=mysql_query($_Msqlstr);	
  		while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  		$_Moutstr.='#'.$_Mrow['timeslots'].'~~'.$_Mrow['roomcaption'].'~'.$_Mrow['teachername'].' '.$_Mrow['teacherfamily'].'~'.$_Mrow['groupcaption'].'~'.$_Mrow['courseID'].'~'.$_Mrow['roomID'].'~'.$_Mrow['teacherID'].'~'.$_Mrow['groupID'];
  	}
  	return $_Moutstr;		
  }
  
?>