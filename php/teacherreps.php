<?php
  @include_once "mygenclasses.php";
  //should be optimized no connecting in each page and no setting of activeterm
  $con=new connection('localhost','root','vertrigo','att');
  $con->connectdb();
  $activeterm1=new _CLactiveterm();
  $teacher1=new _CLteacher();
  $tid=$_POST['teacherid'];$tgroupid=$_POST['groupid'];$reporttype=$_POST['reporttype'];
  $teacher1->setteacheridfull($tid,$activeterm1->gettermid());
  $teachertt=_Fgetteacherscheduledcourses($tid,$tgroupid,$activeterm1->gettermid());
  echo $teachertt;
  
  
  function _Fgetteacherscheduledcourses($teacherid,$teachergroupid,$activetermid)
  {
  	$outstr='';
  	if($teachergroupid!=0)
  	{
  		$sqlstr="select timeslots,roomcaption,roomID,groupID from assignments where teacherID={$teacherid} and groupID={$teachergroupid} and termID={$activetermid}";
	  	$result=mysql_query($sqlstr);	
  		while($row=mysql_fetch_assoc($result))
  	  		$outstr.='#'.$row['timeslots'].'~'.$row['coursecaption'].'~'.$row['roomcaption'].'~';
  	
  	}	
  	else
  	{	
  		$sqlstr="select timeslots,coursecaption,roomcaption from assignments where teacherID={$teacherid} and termID={$activetermid}";
  		$result=mysql_query($sqlstr);	
  		while($row=mysql_fetch_assoc($result))
  	  		$outstr.='#'.$row['timeslots'].'~'.$row['coursecaption'].'~'.$row['roomcaption'].$row['groupcaption'];
  	}
  	return $outstr;		
  }
  
?>