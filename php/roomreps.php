<?php
  @include_once "mygenclasses.php";
  //should be optimized no connecting in each page and no setting of activeterm
  $con=new connection('localhost','root','vertrigo','att');
  $con->connectdb();
  $activeterm1=new activeterm();
  $room1=new room();
  $rid=$_POST['roomid'];$groupid=$_POST['groupid'];$userid=$_POST['userid'];$reporttype=$_POST['reporttype'];
  $activetermid=$activeterm1->gettermid();
  $room1->setroom($rid,$activeterm1->gettermid());
  $roomtt=getroomscheduledcourses($rid,$groupid,$userid);
  echo $roomtt;
  
  
  function getroomscheduledcourses($roomid,$groupid)
  {
  	$outstr='';
  	if($groupid!=0)
  	{
  		$sqlstr="select timeslots,coursecaption,teachername,teacherfamily 
  				 from assignments where roomID={$roomid} and groupID={$roomgroupid} and termID={$GLOBALS['activetermid']}";
	  	$result=mysql_query($sqlstr);	
  		while($row=mysql_fetch_assoc($result))
  	  		$outstr.='#'.$row['timeslots'].'~'.$row['coursecaption'].'~'.$row['teachername'].' '.$row['teacherfamily'];
  	
  	}	
  	else
  	{	
  		$sqlstr="select timeslots,coursecaption,teachername,teacherfamily,groupcaption 
  				 from assignments where roomID={$roomid} and termID={$GLOBALS['activetermid']}";
  		$result=mysql_query($sqlstr);	
  		while($row=mysql_fetch_assoc($result))
  	  		$outstr.='#'.$row['timeslots'].'~'.$row['coursecaption'].'~'.$row['teachername'].' '.$row['teacherfamily'].'~'.$row['groupcaption'];
  	}
  	mysql_freeresult($result);
  	return $outstr;		
  }
  
?>