<?php session_start();
  //@include "mygenclasses.php";
  @include_once "mygenlib.php";
  $con=new connection();
  $t1=new activeterm();
  //remove later
  //teacherscourseadd(1,2,2,1,3,'16:s,17:s,30:e,31:e');
  //changecourseassignment('1','1','16','42',$prevcourseslots,$newcourseslotar);
  //changeteachertimestatus(1,1,array(16,'s',17,'s',30,'e',31,'e'),array(16,'s',17,'s',35,'e',36,'e'));
  changegrouptimes(1,array(16,'s',17,'s',30,'e',31,'e'),array(42,'s',43,'s',30,'e',31,'e'));

function changegrouptimes($groupid,$prevcourseslotsar,$newcourseslotar)
{
	  	$sqlstr="select groupstatus,initialgroupstatus from groupstatus where groupID={$groupid} and termID={$_SESSION['activetermid']}";
		$result=mysql_query($sqlstr);$row=mysql_fetch_assoc($result);
		$times=$row['groupstatus'];$initialtimes=$row['initialgroupstatus'];
		mysql_free_result($result);
		//from change teachertimes
		changestatusslots(&$times,$initialtimes,$prevcourseslotsar,$newcourseslotar);	  	
		$sqlstr="update groupstatus set groupstatus='{$times}' where groupID={$groupid} and termID={$_SESSION['activetermid']}";
		mysql_query($sqlstr);
}	

function changestatusslots(&$times,$initialtimes,$prevcourseslotsar,$newcourseslotar)
{
		for($i=0;$i<count($prevcourseslotsar);$i+=4)
		{
			if($prevcourseslotsar[$i]!=$newcourseslotar[$i])
			{
				if($prevcourseslotsar[$i+1]=='s')
				{
					$times[$prevcourseslotsar[$i]]='f';$times[$prevcourseslotsar[$i]+1]='f';
				}
				else 
				{
					if($times[$prevcourseslotsar[$i]]==complement($prevcourseslotsar[$i+1]))
					{$times[$prevcourseslotsar[$i]]='f';$times[$prevcourseslotsar[$i]+1]='f';}
					else	
					{
						$times[$prevcourseslotsar[$i]]=$initialtimes[$prevcourseslotsar[$i]];
						$times[$prevcourseslotsar[$i]+1]=$initialtimes[$prevcourseslotsar[$i]+1];
					}
				}
				
			}			
		}
		//set new time
		for($i=0;$i<count($newcourseslotar);$i+=4)
		{
			if($prevcourseslotsar[$i]!=$newcourseslotar[$i])
			{
				if($prevcourseslotsar[$i+1]=='s')//**************will check for teachers time availability and set error report on error
				{
					{$times[$newcourseslotar[$i]]='s';$times[$newcourseslotar[$i]+1]='s';}
				}
				else 
				{
					if($times[$newcourseslotar[$i]]==$prevcourseslotsar[$i+1])
						{$times[$newcourseslotar[$i]]='s';$times[$newcourseslotar[$i]+1]='s';}
					else	
					{
						$times[$newcourseslotar[$i]]=complement($prevcourseslotsar[$i+1]);
						$times[$newcourseslotar[$i]+1]=complement($prevcourseslotsar[$i+1]);
					}
				}
				
			}			
		}
	
}

// taqir konad,
//be jaye neveshtane code joda baraye eslahe eslathaye dars o gorooh o ostad o otaq,
//codi be soorat functional neveshte shavad ke 6timestatus ra gerefte va taqirat ra rooye an emal namayad.  
  function changeteachertimestatus($teacherid,$groupid,$prevcourseslotsar,$newcourseslotar)
{
	  	$sqlstr="select times,initialtimes from tchrtimes where teacherID={$teacherid} and termID={$_SESSION['activetermid']} and teachergroupid={$groupid}";
		$result=mysql_query($sqlstr);$row=mysql_fetch_assoc($result);
		$times=$row['times'];$initialtimes=$row['initialtimes'];
		mysql_free_result($result);
		//remove lasttime
		changestatusslots($times,$initialtimes,$prevcourseslotsar,$newcourseslotar);
	  	$sqlstr="update tchrtimes set times='{$times}' where teacherID={$teacherid} and termID={$_SESSION['activetermid']} and teachergroupid={$groupid}";
		mysql_query($sqlstr);
}


function changecourseassignment($courseid,$groupid,$slotid,$newslotid,&$prevcourseslotsar,&$newcourseslotar)
{
	  	$sqlstr="select courseunits,timeslots from assignments where courseID={$courseid} and termID={$_SESSION['activetermid']} and groupID={$groupid}";
		echo $sqlstr;		
	  	$result=mysql_query($sqlstr);$row=mysql_fetch_assoc($result);$courseunits=$row['courseunits'];
	  	$coursetimeslots=$row['timeslots'];
		$prevcourseslotsar=explode("[,:]",$coursetimeslots);
		$newcourseslotar=$prevcourseslotsar;
		$newslotstr="";$newslotid1=$newslotid+1;
		for($i=0; $i<count($prevcourseslotsar) ; $i+=4)
		{
			if($prevcourseslotsar[$i]==$slotid)
			{
				$newcourseslotar[$i]=$newslotid;$newcourseslotar[$i+2]=$newslotid+1;
				$newslotstr.="{$newslotid}:{$prevcourseslotsar[$i+1]},{$newslotid1}:{$prevcourseslotsar[$i+3]},";
			}
			else
				$newslotstr.="{$prevcourseslotsar[$i]}:{$prevcourseslotsar[$i+1]},{$prevcourseslotsar[$i+2]}:{$prevcourseslotsar[$i+3]},";
		}
		$newslotstr=substr($newslotstr,0,strlen($newslotstr)-1);
	  	$sqlstr="update assignments set timeslots='{$newslotstr}' where courseID={$courseid} and termID={$_SESSION['activetermid']} and groupID={$groupid}";
		mysql_query($sqlstr);

}  
  
  
  
function teacherscourseadd($teacherid,$prevteacherid,$courseid,$groupid,$courseunits,$coursetimeslots)//write another function for insert course
{
  	$sqlstr="select times,teachername,teacherfamily from tchrtimes where teacherID={$teacherid} and termID={$_SESSION['activetermid']} and teachergroupid={$groupid}";
  	$result=mysql_query($sqlstr);
  	$row=mysql_fetch_assoc($result);
  	$ttimes=$row['times'];$teachername=$row['teachername'];$teacherfamily=$row['teacherfamily'];
	
	$courseslots=explode("[,:]",$coursetimeslots);

  	for($i=0 ; $i<((count($courseslots)-1)) ; $i+=2)
	{	  	  	  	  	  	
	  	if($courseslots[$i+1]=='f')
	  	{$ttimes[$courseslots[$i]]='s';$ttimes[$courseslots[$i]+1]='s';$i+=2;}
	  	else
	  	{
	  		if($ttimes[$courseslots[$i]]=='f') {$ttimes[$courseslots[$i]]=complement($courseslots[$i+1]);$ttimes[$courseslots[$i]+1]=complement($courseslots[$i+1]);$i+=2;}
	  		else {$ttimes[$courseslots[$i]]='s';$ttimes[$courseslots[$i]+1]='s';$i+=2;}
	  	}
	}
  	$sqlstr="update tchrtimes set times='{$ttimes}',teachercurslots=teachercurslots-{$courseunits} where teacherID={$teacherid} and termID={$_SESSION['activetermid']} and teachergroupid={$groupid}";
  	mysql_query($sqlstr);

  	$sqlstr="update assignments set teacherID={$teacherid},teachername='{teachername}',teacherfamily='{teacherfamily}' where courseID={$courseid} and termID={$_SESSION['activetermid']} and groupID={$groupid} and teacherID={$prevteacherid}";
	mysql_query($sqlstr);
}

	
	?>