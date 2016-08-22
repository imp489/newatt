<?php session_start();
  @include_once "mygenclasses.php";
  //should be optimized no connecting in each page and no setting of activeterm
  $_Mcon=new _CLconnection();

  $_Mcourseid=$_POST['courseid'];$_Mgroupid=$_POST['groupid'];$_Mreporttype=$_POST['reporttype'];//course in all groups or for a special group
  //$_Mcourseid=1;$_Mgroupid=0;
  $_Mcourseteachers=getcourseposteachers($_Mcourseid,$_Mgroupid);
  echo $_Mcoursett;
  
  
  function _Fgetcourseschedule($_Mcourseid,$_Mgroupid)
  {
  	$_Moutstr='';
  	if($_Mgroupid!=0)
  	{
  		$_Msqlstr="select timeslots,roomcaption,teachername,teacherfamily from assignments where courseID={$_Mcourseid} and groupID={$_Mgroupid} and termID={$_SESSION['activetermid']}";
	  	$_Mresult=mysql_query($_Msqlstr);	
  		while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  		$_Moutstr.='#'.$_Mrow['timeslots'].'~'.'~'.$_Mrow['roomcaption'].'~'.$_Mrow['teachername'].' '.$_Mrow['teacherfamily'];
  	
  	}	
  	else
  	{	
  		$_Msqlstr="select timeslots,coursecaption,roomcaption,teachername,teacherfamily from assignments where courseID={$_Mcourseid} and termID={$_SESSION['activetermid']}";
  		$_Mresult=mysql_query($_Msqlstr);echo $_Msqlstr;	
  		while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  		$_Moutstr.='#'.$_Mrow['timeslots'].'~'.'~'.$_Mrow['roomcaption'].$_Mrow['teachername'].' '.$_Mrow['teacherfamily'].$_Mrow['groupcaption'];
  	}
  	return $_Moutstr;		
  }
?>