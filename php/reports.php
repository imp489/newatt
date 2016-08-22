<?php session_start();
  //@include "mygenclasses.php";
  @include_once "mygenlib.php";

  $con=new _CLconnection();

  
  

  $groupid=$_POST['groupid'];//courses,teachers,rooms,both
  
  
  $reporttype=$_POST['reporttype'];
  if($reporttype=='t')
  {$teacherid=$_POST['teacherid'];$groupid=$_POST['groupid'];echo _Fgetteacherscheduledcourses($teacherid,$groupid);}
  else if($reporttype=='c')
  {$courseid=$_POST['courseid'];$groupid=$_POST['groupid'];echo _Fgetcourseschedule($courseid,$groupid);}
  else if($reporttype=='tg')//teachers of a group
  {$groupid=$_POST['groupid'];echo _Frepgroupteachers($groupid);}
  else if($reporttype=='s')
  {$slotid=$_POST['slotid'];$groupid=$_POST['groupid'];echo _Fgetslotschedule($slotid,$groupid);}
  else if($reporttype=='r')
  {$roomid=$_POST['roomid'];$groupid=$_POST['groupid'];echo _Fgetroomscheduledcourses($roomid,$groupid);}
  else if($reporttype[0]=='g')
  {echo _Fgetgroupscheduledcourses1($groupid,$reporttype);}
  
  
 
  function _Fgetgroupscheduledcourses1($groupid,$reporttype)
  {
  	$outstr='';
  	switch ($reporttype)
  	{
  		case 'gc':
 		$sqlstr="select timeslots,coursecaption,courseID 
  				 from assignments where groupID={$groupid} and termID={$_SESSION['activetermid']}";
	  	$result=mysql_query($sqlstr);	
  		while($row=mysql_fetch_assoc($result))
  	  		$outstr.='#'.$row['timeslots'].'~'.$row['coursecaption'].'~~~~'.$row['courseID'].'~0~0~'.$row['groupID'];;
  	  	break;

  		case 'gt':
 		$sqlstr="select teacherID,timeslots,teachername,teacherfamily 
  				 from assignments where groupID={$groupid} and termID={$_SESSION['activetermid']}";
	  	$result=mysql_query($sqlstr);	
  		while($row=mysql_fetch_assoc($result))
  	  		$outstr.='#'.$row['timeslots'].'~~~'.$row['teachername'].' '.$row['teacherfamily'].'~~0~0~'.$row['teacherID'].'~'.$row['groupID'];
  	  	break;
  	  	
  		case 'gct':
 		$sqlstr="select timeslots,coursecaption,teachername,teacherfamily 
  				 from assignments where groupID={$groupid} and termID={$_SESSION['activetermid']}";
	  	$result=mysql_query($sqlstr);	
  		while($row=mysql_fetch_assoc($result))
  	  		$outstr.='#'.$row['timeslots'].'~'.$row['coursecaption'].'~~'.$row['teachername'].' '.$row['teacherfamily'].'~~'.$row['courseID'].'~0~'.$row['teacherID'].'~'.$row['groupID'];
  	  	break;
  	  	
  		case 'gcr':
 		$sqlstr="select timeslots,coursecaption,roomcaption,courseID,roomID 
  				 from assignments where groupID={$groupid} and termID={$_SESSION['activetermid']}";
	  	$result=mysql_query($sqlstr);	
  		while($row=mysql_fetch_assoc($result))
  	  		$outstr.='#'.$row['timeslots'].'~'.$row['coursecaption'].'~'.$row['roomcaption'].'~~~'.$row['courseID'].'~'.$row['roomID'].'~0~'.$row['groupID'];
  	  	break;
  	  	
  	  	
  		default:
 		$sqlstr="select timeslots,coursecaption,teachername,teacherfamily,roomcaption,courseID,roomID,teacherID 
  				 from assignments where groupID={$groupid} and termID={$_SESSION['activetermid']}";
	  	$result=mysql_query($sqlstr);	
  		while($row=mysql_fetch_assoc($result))
  	  		$outstr.='#'.$row['timeslots'].'~'.$row['coursecaption'].'~'.$row['roomcaption'].'~'.$row['teachername'].' '.$row['teacherfamily'].'~~'.$row['courseID'].'~'.$row['roomID'].'~'.$row['teacherID'].'~'.$row['groupID'];
  	  	break;
  	}
  	mysql_freeresult($result);
  	return $outstr;		
  }

  function _Fgetteacherscheduledcourses($teacherid,$groupid)
  {
  	$outstr='';
  	if($teachergroupid!=0)
  	{
  		$sqlstr="select timeslots,coursecaption,courseID,roomcaption,roomID,groupID from assignments where teacherID={$teacherid} and groupID={$groupid} and termID={$_SESSION['activetermid']}";
	  	$result=mysql_query($sqlstr);	
  		while($row=mysql_fetch_assoc($result))
  	  		$outstr.='#'.$row['timeslots'].'~'.$row['coursecaption'].'~'.$row['roomcaption'].'~~~'.$row['courseID'].'~'.$row['roomID'].'~'.$row['teacherID'].'~'.$row['groupID'];
  	
  	}	
  	else
  	{	
  		$sqlstr="select timeslots,coursecaption,courseID,roomcaption,roomID,groupID from assignments where teacherID={$teacherid} and termID={$_SESSION['activetermid']}";
  		$result=mysql_query($sqlstr);	
  		while($row=mysql_fetch_assoc($result))
  	  		$outstr.='#'.$row['timeslots'].'~'.$row['coursecaption'].'~'.$row['roomcaption'].'~~'.$row['groupcaption'].'~'.$row['courseID'].'~'.$row['roomID'].'~'.$row['teacherID'].'~'.$row['groupID'];
  	}
  	echo $sqlstr;
  	mysql_freeresult($result);
  	return $outstr;		
  }

  function _Fgetcourseschedule($courseid,$groupid)
  {
  	$outstr='';
  	if($groupid!=0)
  	{
  		$sqlstr="select timeslots,roomcaption,roomID,teacherID,teachername,teacherfamily from assignments where courseID={$courseid} and groupID={$groupid} and termID={$_SESSION['activetermid']}";
	  	$result=mysql_query($sqlstr);	echo $sqlstr;
  		while($row=mysql_fetch_assoc($result))
  	  		$outstr.='#'.$row['timeslots'].'~~'.$row['roomcaption'].'~'.$row['teachername'].' '.$row['teacherfamily'].'~~'.$row['courseID'].'~'.$row['roomID'].'~'.$row['teacherID'].'~'.$row['groupID'];  	
  	}	
  	else
  	{	
  		$sqlstr="select teacherID,roomID,groupID,courseID,timeslots,coursecaption,roomcaption,teachername,teacherfamily,groupcaption from assignments where courseID={$courseid} and termID={$_SESSION['activetermid']}";
  		$result=mysql_query($sqlstr);	
  		while($row=mysql_fetch_assoc($result))
  	  		$outstr.='#'.$row['timeslots'].'~~'.$row['roomcaption'].'~'.$row['teachername'].' '.$row['teacherfamily'].'~'.$row['groupcaption'].'~'.$row['courseID'].'~'.$row['roomID'].'~'.$row['teacherID'].'~'.$row['groupID'];
  	}
  	return $outstr;		
  }

function _Frepgroupteachers($groupid)
{	
	$outstr="";
	$sqlstr="select * from tchrtimes where ((teachergroupid={$groupid}) and 
										(termID={$_SESSION['activetermid']}))";
	$groupteachers=mysql_query($sqlstr);
	while($row=mysql_fetch_assoc($groupteachers))
	{
		$outstr.="#".$row['teacherID'].','.$row['teachername'].','.$row['teacherfamily'].','.$row['teachermaxslots'].','.$row['teachercurslots'];
	}
	mysql_free_result($groupteachers);
	return $outstr;			

}

  function _Fgetslotschedule($slotid,$groupid)
  {
  	$outstr='';
  	if($groupid!=0)
  	{
  		$sqlstr="select timeslots,coursecaption,roomcaption,teachername,teacherfamily,courseID,roomID,teacherID from assignments where timeslots like '%{$slotid}%' and groupID={$groupid} and termID={$_SESSION['activetermid']}";
	  	$result=mysql_query($sqlstr);	
  		while($row=mysql_fetch_assoc($result))
  	  		$outstr.='#'.$row['timeslots'].'~'.$row['coursecaption'].'~'.$row['roomcaption'].'~'.$row['teachername'].' '.$row['teacherfamily'].'~~'.$row['courseID'].'~'.$row['roomID'].'~'.$row['teacherID'].'~'.$row['groupID'];;
  	
  	}	
  	else
  	{
  		$sqlstr="select timeslots,coursecaption,roomcaption,teachername,teacherfamily,groupcaption,courseID,roomID,teacherID,groupID from assignments where timeslots like '%{$slotid}%' and termID={$_SESSION['activetermid']} order by groupcaption";
  		$result=mysql_query($sqlstr);	
  		while($row=mysql_fetch_assoc($result))
  	  		$outstr.='#'.$row['timeslots'].'~'.$row['coursecaption'].'~'.$row['roomcaption'].'~'.$row['teachername'].' '.$row['teacherfamily'].'~'.$row['groupcaption'].'~'.$row['courseID'].'~'.$row['roomID'].'~'.$row['teacherID'].'~'.$row['groupID'];
  	}
  	return $outstr;
  }

  function _Fgetroomscheduledcourses($roomid,$groupid)
  {
  	$outstr='';
  	if($groupid!=0)
  	{
  		$sqlstr="select timeslots,coursecaption,teachername,teacherfamily,courseID,teacherID 
  				 from assignments where roomID={$roomid} and groupID={$roomgroupid} and termID={$_SESSION['activetermid']}";
	  	$result=mysql_query($sqlstr);	
  		while($row=mysql_fetch_assoc($result))
  	  		$outstr.='#'.$row['timeslots'].'~'.$row['coursecaption'].'~~'.$row['teachername'].' '.$row['teacherfamily'].'~~'.$row['courseID'].'~0~'.$row['teacherID'].'~'.$row['groupID'];
  	
  	}	
  	else
  	{	
  		$sqlstr="select timeslots,coursecaption,teachername,teacherfamily,groupcaption 
  				 from assignments where roomID={$roomid} and termID={$_SESSION['activetermid']}";
  		$result=mysql_query($sqlstr);	
  		while($row=mysql_fetch_assoc($result))
  	  		$outstr.='#'.$row['timeslots'].'~'.$row['coursecaption'].'~~'.$row['teachername'].' '.$row['teacherfamily'].'~'.$row['groupcaption'].'~'.$row['courseID'].'~0~'.$row['teacherID'].'~'.$row['groupID'];
  	}
  	mysql_freeresult($result);
  	return $outstr;		
  }
  
  
?>