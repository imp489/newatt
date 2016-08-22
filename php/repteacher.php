<?php
  //@include "mygenclasses.php";
    @include_once "mygenlib.php";

  $con=new connection();
  //remove later
  
  //remove to here
  //should be optimized no connecting in each page and no setting of activeterm
  $teacher1=new teacher();
  $tid=$_POST['teacherid'];$tgroupid=$_POST['groupid'];$reporttype=$_POST['reporttype'];
  //$tid=1;
  $teacher1->setteacheridfull($tid);
  echo getteacherscheduledcourses($tid,$tgroupid);
  
  
  
  function getteacherscheduledcourses($teacherid,$teachergroupid)
  {
  	$outstr='';
  	if($teachergroupid!=0)
  	{
  		$sqlstr="select timeslots,coursecaption,courseID,roomcaption,roomID,groupID from assignments where teacherID={$teacherid} and groupID={$teachergroupid} and termID={$_SESSION['activetermid']}";
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
  
?>