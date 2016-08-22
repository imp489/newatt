<?php session_start();
  //@include "mygenclasses.php";
    @include_once "mygenlib.php";

  $con=new connection();
  //remove to here

  //@include "mygenclasses.php";
  //should be optimized no connecting in each page and no setting of activeterm
  //$con=new connection('localhost','root','vertrigo','att');
  //$con->connectdb();
  $rid=$_POST['roomid'];$groupid=$_POST['groupid'];$userid=$_POST['userid'];$reporttype=$_POST['reporttype'];$buildingid=$_POST['buildingid'];
  $roomtt=getroomscheduledcourses($rid,$groupid);
  echo $roomtt;
  
  
  function getroomscheduledcourses($roomid,$groupid)
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