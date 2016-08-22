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
  $_Mroom1=new _CLroom();
  $_Mgroupid=$_POST['groupid'];$_Muserid=$_POST['userid'];$_Mreporttype=$_POST['reporttype'];//courses,teachers,rooms,both
  //$_Mgroupid=1;$_Mreporttype='ct';//courses,teachers,rooms
  $_Mgrouptt=_Fgetgroupscheduledcourses1($_Mgroupid,$_Mreporttype);
  echo $_Mgrouptt;
  
  
  function _Fgetgroupscheduledcourses1($_Mgroupid,$_Mreporttype)
  {
  	$_Moutstr='';
  	switch ($_Mreporttype)
  	{
  		case 'c':
 		$_Msqlstr="select timeslots,coursecaption,courseID 
  				 from assignments where groupID={$_Mgroupid} and termID={$_SESSION['activetermid']}";
	  	$_Mresult=mysql_query($_Msqlstr);	
  		while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  		$_Moutstr.='#'.$_Mrow['timeslots'].'~'.$_Mrow['coursecaption'].'~~~~'.$_Mrow['courseID'].'~0~0~'.$_Mrow['groupID'];;
  	  	break;

  		case 't':
 		$_Msqlstr="select teacherID,timeslots,teachername,teacherfamily 
  				 from assignments where groupID={$_Mgroupid} and termID={$_SESSION['activetermid']}";
	  	$_Mresult=mysql_query($_Msqlstr);	
  		while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  		$_Moutstr.='#'.$_Mrow['timeslots'].'~~~'.$_Mrow['teachername'].' '.$_Mrow['teacherfamily'].'~~0~0~'.$_Mrow['teacherID'].'~'.$_Mrow['groupID'];
  	  	break;
  	  	
  		case 'ct':
 		$_Msqlstr="select timeslots,coursecaption,teachername,teacherfamily 
  				 from assignments where groupID={$_Mgroupid} and termID={$_SESSION['activetermid']}";
	  	$_Mresult=mysql_query($_Msqlstr);	
  		while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  		$_Moutstr.='#'.$_Mrow['timeslots'].'~'.$_Mrow['coursecaption'].'~~'.$_Mrow['teachername'].' '.$_Mrow['teacherfamily'].'~~'.$_Mrow['courseID'].'~0~'.$_Mrow['teacherID'].'~'.$_Mrow['groupID'];
  	  	break;
  	  	
  		case 'cr':
 		$_Msqlstr="select timeslots,coursecaption,roomcaption,courseID,roomID 
  				 from assignments where groupID={$_Mgroupid} and termID={$_SESSION['activetermid']}";
	  	$_Mresult=mysql_query($_Msqlstr);	
  		while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  		$_Moutstr.='#'.$_Mrow['timeslots'].'~'.$_Mrow['coursecaption'].'~'.$_Mrow['roomcaption'].'~~~'.$_Mrow['courseID'].'~'.$_Mrow['roomID'].'~0~'.$_Mrow['groupID'];
  	  	break;
  	  	
  	  	
  		default:
 		$_Msqlstr="select timeslots,coursecaption,teachername,teacherfamily,roomcaption,courseID,roomID,teacherID 
  				 from assignments where groupID={$_Mgroupid} and termID={$_SESSION['activetermid']}";
	  	$_Mresult=mysql_query($_Msqlstr);	
  		while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  		$_Moutstr.='#'.$_Mrow['timeslots'].'~'.$_Mrow['coursecaption'].'~'.$_Mrow['roomcaption'].'~'.$_Mrow['teachername'].' '.$_Mrow['teacherfamily'].'~~'.$_Mrow['courseID'].'~'.$_Mrow['roomID'].'~'.$_Mrow['teacherID'].'~'.$_Mrow['groupID'];
  	  	break;
  	}
  	mysql_freeresult($_Mresult);
  	return $_Moutstr;		
  }
  
?>