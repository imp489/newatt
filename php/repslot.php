<?php session_start();
  //@include "mygenclasses.php";
    @include_once "mygenlib.php";

  $con=new connection('localhost','root','vertrigo','att');
  $con->connectdb();

  //remove later
  
   $user1=new atuser();
   $user1->setusername("testuser");
   $activeterm1=new activeterm();
   writesession($user1,$activeterm1);  
  //remove to here

  //@include "mygenclasses.php";
  //should be optimized no connecting in each page and no setting of activeterm
  //$con=new connection('localhost','root','vertrigo','att');
  //$con->connectdb();
  $courseid=$_POST['slotid'];$groupid=$_POST['groupid'];$reporttype=$_POST['reporttype'];//course in all groups or for a special group
  //$slotid=30;$groupid=0;
  $slottt=getslotschedule($slotid,$groupid);
  echo $slottt;
  
  
  function getslotschedule($slotid,$groupid)
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

?>