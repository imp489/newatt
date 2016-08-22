<?php session_start();
  //@include "mygenclasses.php";
  @include_once "mygen_lib.php";
  @include_once "mygenclasses.php";
  @include_once "shrfuns.php";  
  $_Mcon=new _CLconnection();

  
  
  //920702
  $_Mgroupid=0;
  if(isset($_POST['groupid'])){//courses,teachers,rooms,both
  	$_Mgroupid=$_POST['groupid'];
  	$_Mgroups=_Fgetallsubgroups($_Mgroupid);
  }
  //$_Mgroups=_Fgetsubgroups1(array("$_Mgroupid,1#"));
  
  $_Mreporttype=$_POST['reporttype'];
    //echo "*$_Mgroupid-$_Mgroups-$_Mreporttype*";
  if($_Mreporttype=='t')
  {$_Mteacherid=$_POST['teacherid'];echo _Fgetteacherscheduledcourses($_Mteacherid,$_Mgroups);}
  else if($_Mreporttype=='c')
  {$_Mcourseid=_Fgetcourseidfrom($_POST['courseid'],$_Mcoursepart);echo _Fgetcourseschedule($_Mcourseid,$_Mcoursepart,$_Mgroupid);}
  else if($_Mreporttype=='tg')//teachers of a group
  {echo _Frepgroupteachers($_Mgroupid);}
  else if($_Mreporttype=='s')
  {$_Mslotid=$_POST['slotid'];echo _Fgetslotschedule($_Mslotid,$_Mgroupid);}
  else if($_Mreporttype=='r')
  {$_Mroomid=$_POST['roomid'];echo _Fgetroomscheduledcourses($_Mroomid,$_Mgroupid);}
  else if($_Mreporttype=='rg')//return rooms assigned for groups
  {
  	$_Mroomid=$_POST['roomid'];echo _Fgetgroupsrooms($_Mroomid,$_Mgroupid);
  }
  else if($_Mreporttype[0]=='g')
  {echo _Fgetgroupscheduledcourses1($_Mgroups,$_Mreporttype);}
  else if($_Mreporttype=='gtcatslot')
  {$_Mroomid=$_POST['roomid'];echo _Fgetroomscheduledcourses($_Mroomid,$_Mgroupid);}
  exit();
  
  
  function _Fgetgroupscheduledcourses1($_Mgroups,$_Mreporttype)
  {
  	$_Moutstr='';
  	switch ($_Mreporttype)
  	{
  		case 'gc':
 		$_Msqlstr="select timeslots,coursecaption,courseID,groupID 
  				 from assignments where groupID in ({$_Mgroups}) and termID={$_SESSION['activetermid']}";
	  	$_Mresult=mysql_query($_Msqlstr);	
  		while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  		$_Moutstr.='#'.$_Mrow['timeslots'].'~'.$_Mrow['coursecaption'].'~~~~'.$_Mrow['courseID'].'~0~0~'.$_Mrow['groupID'];;
  	  	break;

  		case 'gt':
 		$_Msqlstr="select teacherID,timeslots,teachername,teacherfamily,groupID 
  				 from assignments where groupID in ({$_Mgroups}) and termID={$_SESSION['activetermid']}";
	  	$_Mresult=mysql_query($_Msqlstr);	
  		while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  		$_Moutstr.='#'.$_Mrow['timeslots'].'~~~'.$_Mrow['teachername'].' '.$_Mrow['teacherfamily'].'~~0~0~'.$_Mrow['teacherID'].'~'.$_Mrow['groupID'];
  	  	break;
  	  	
  		case 'gct':
 		$_Msqlstr="select timeslots,coursecaption,teachername,teacherfamily,groupID 
  				 from assignments where groupID in ({$_Mgroups}) and termID={$_SESSION['activetermid']}";
	  	$_Mresult=mysql_query($_Msqlstr);	
  		while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  		$_Moutstr.='#'.$_Mrow['timeslots'].'~'.$_Mrow['coursecaption'].'~~'.$_Mrow['teachername'].' '.$_Mrow['teacherfamily'].'~~'.$_Mrow['courseID'].'~0~'.$_Mrow['teacherID'].'~'.$_Mrow['groupID'];
  	  	break;
  	  	
  		case 'gcr':
 		$_Msqlstr="select timeslots,coursecaption,roomcaption,courseID,roomID,groupID 
  				 from assignments where groupID in ({$_Mgroups}) and termID={$_SESSION['activetermid']}";
	  	$_Mresult=mysql_query($_Msqlstr);	
  		while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  		$_Moutstr.='#'.$_Mrow['timeslots'].'~'.$_Mrow['coursecaption'].'~'.$_Mrow['roomcaption'].'~~~'.$_Mrow['courseID'].'~'.$_Mrow['roomID'].'~0~'.$_Mrow['groupID'];
  	  	break;
  	  	
  	  	
  		default:
 		$_Msqlstr="select timeslots,coursecaption,teachername,teacherfamily,roomcaption,courseID,roomID,teacherID,groupID 
  				 from assignments where groupID in ({$_Mgroups}) and termID={$_SESSION['activetermid']}";//echo "##$_Msqlstr##";
	  	$_Mresult=mysql_query($_Msqlstr);	
  		while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  		$_Moutstr.='#'.$_Mrow['timeslots'].'~'.$_Mrow['coursecaption'].'~'.$_Mrow['roomcaption'].'~'.$_Mrow['teachername'].' '.$_Mrow['teacherfamily'].'~~'.$_Mrow['courseID'].'~'.$_Mrow['roomID'].'~'.$_Mrow['teacherID'].'~'.$_Mrow['groupID'];
  	  	break;
  	}//echo $_Msqlstr;
  	mysql_freeresult($_Mresult);
  	if ($_Moutstr=='') return 0 ;else return $_Moutstr;		
  }

  function _Fgetteacherscheduledcourses($_Mteacherid,$_Mgroupid)
  {
  	$_Moutstr='';
  	if($_Mgroupid!='')
  	{
  		$_Msqlstr="select timeslots,coursecaption,courseID,roomcaption,roomID,groupID,teacherID from assignments where teacherID={$_Mteacherid} and groupID in ({$_Mgroupid}) and termID={$_SESSION['activetermid']}";
	  	$_Mresult=mysql_query($_Msqlstr);	
  		while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  		$_Moutstr.='#'.$_Mrow['timeslots'].'~'.$_Mrow['coursecaption'].'~'.$_Mrow['roomcaption'].'~~~'.$_Mrow['courseID'].'~'.$_Mrow['roomID'].'~'.$_Mrow['teacherID'].'~'.$_Mrow['groupID'];
  	
  	}	
  	else
  	{	
  		$_Msqlstr="select timeslots,coursecaption,courseID,roomcaption,roomID,groupID,teacherID from assignments where teacherID={$_Mteacherid} and termID={$_SESSION['activetermid']}";
  		$_Mresult=mysql_query($_Msqlstr);	
  		while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  		$_Moutstr.='#'.$_Mrow['timeslots'].'~'.$_Mrow['coursecaption'].'~'.$_Mrow['roomcaption'].'~~'.$_Mrow['groupcaption'].'~'.$_Mrow['courseID'].'~'.$_Mrow['roomID'].'~'.$_Mrow['teacherID'].'~'.$_Mrow['groupID'];
  	}
  	//echo $_Msqlstr;
  	mysql_freeresult($_Mresult);
  	return $_Moutstr;		
  }

  function _Fgetcourseschedule($_Mcourseid,$_Mcoursepart,$_Mgroupid)
  {
  	$_Moutstr='';
  	if($_Mgroupid!=0)
  	{
  		if($_Mcoursepart[0]!='a')
  			$_Msqlstr="select timeslots,roomcaption,roomID,teacherID,teachername,teacherfamily,coursepart,courseID,groupID from assignments where courseID={$_Mcourseid} and coursepart not like '%a%' and groupID=$_Mgroupid and termID={$_SESSION['activetermid']}";
		else
  			$_Msqlstr="select timeslots,roomcaption,roomID,teacherID,teachername,teacherfamily,coursepart,courseID,groupID from assignments where courseID={$_Mcourseid} and coursepart like '%{$_Mcoursepart[0]}%' and groupID=$_Mgroupid and termID={$_SESSION['activetermid']}";
	  	$_Mresult=mysql_query($_Msqlstr);
  		while($_Mrow=mysql_fetch_assoc($_Mresult))
  		{$_Moutstr.='#'.$_Mrow['timeslots'].'~~'.$_Mrow['roomcaption'].'~'.$_Mrow['teachername'].' '.$_Mrow['teacherfamily'].'~~'.$_Mrow['courseID'].'!'.$_Mrow['coursepart'].'~'.$_Mrow['roomID'].'~'.$_Mrow['teacherID'].'~'.$_Mrow['groupID'];}
  	}
  	else
  	{	
  		if($_Mcoursepart[0]!='a')
  			$_Msqlstr="select teacherID,roomID,groupID,courseID,timeslots,coursecaption,roomcaption,teachername,teacherfamily,groupcaption,coursepart from assignments where courseID=$_Mcourseid and coursepart not like '%a%' and  termID={$_SESSION['activetermid']}";
  		else
  			$_Msqlstr="select teacherID,roomID,groupID,courseID,timeslots,coursecaption,roomcaption,teachername,teacherfamily,groupcaption,coursepart from assignments where courseID=$_Mcourseid and coursepart like '$_Mcoursepart' and  termID={$_SESSION['activetermid']}";

		$_Mresult=mysql_query($_Msqlstr);	
  		while($_Mrow=mysql_fetch_assoc($_Mresult))
  		{$_Moutstr.='#'.$_Mrow['timeslots'].'~~'.$_Mrow['roomcaption'].'~'.$_Mrow['teachername'].' '.$_Mrow['teacherfamily'].'~'.$_Mrow['groupcaption'].'~'.$_Mrow['courseID'].'!'.$_Mrow['coursepart'].'~'.$_Mrow['roomID'].'~'.$_Mrow['teacherID'].'~'.$_Mrow['groupID'];}
  	}
  	return $_Moutstr;		
  }

function _Frepgroupteachers($_Mgroupid)
{	
	$_Moutstr="";
	$_Msqlstr="select * from tchrtimes where ((teachergroupid={$_Mgroupid}) and 
										(termID={$_SESSION['activetermid']}))";
	$_Mgroupteachers=mysql_query($_Msqlstr);
	while($_Mrow=mysql_fetch_assoc($_Mgroupteachers))
	{
		$_Moutstr.="#".$_Mrow['teacherID'].','.$_Mrow['teachername'].','.$_Mrow['teacherfamily'].','.$_Mrow['teachermaxslots'].','.$_Mrow['teachercurslots'];
	}
	mysql_free_result($_Mgroupteachers);
	return $_Moutstr;			

}
function _Fgetgroupsrooms($_Mroomid,$_Mgroupid)// for room sharing - not down- codes copied
{
	$_Moutstr="";
	$_Msqlstr="select * from roomstatus where ((roomid={$_Mroomid}) and
	(termID={$_SESSION['activetermid']}))";
	$_Mgroupteachers=mysql_query($_Msqlstr);
	while($_Mrow=mysql_fetch_assoc($_Mgroupteachers))
	{
	$_Moutstr.="#".$_Mrow['teacherID'].','.$_Mrow['teachername'].','.$_Mrow['teacherfamily'].','.$_Mrow['teachermaxslots'].','.$_Mrow['teachercurslots'];
	}
	mysql_free_result($_Mgroupteachers);
	return $_Moutstr;
	
	
}
  function _Fgetslotschedule($_Mslotid,$_Mgroupid)
  {
  	$_Moutstr='';
  	if($_Mgroupid!=0)
  	{
  		$_Msqlstr="select timeslots,coursecaption,roomcaption,teachername,teacherfamily,courseID,roomID,teacherID from assignments where timeslots like '%{$_Mslotid}%' and groupID={$_Mgroupid} and termID={$_SESSION['activetermid']}";
	  	$_Mresult=mysql_query($_Msqlstr);	
  		while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  		$_Moutstr.='#'.$_Mrow['timeslots'].'~'.$_Mrow['coursecaption'].'~'.$_Mrow['roomcaption'].'~'.$_Mrow['teachername'].' '.$_Mrow['teacherfamily'].'~~'.$_Mrow['courseID'].'~'.$_Mrow['roomID'].'~'.$_Mrow['teacherID'].'~'.$_Mrow['groupID'];;
  	
  	}	
  	else
  	{
  		$_Msqlstr="select timeslots,coursecaption,roomcaption,teachername,teacherfamily,groupcaption,courseID,roomID,teacherID,groupID from assignments where timeslots like '%{$_Mslotid}%' and termID={$_SESSION['activetermid']} order by groupcaption";
  		$_Mresult=mysql_query($_Msqlstr);	
  		while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  		$_Moutstr.='#'.$_Mrow['timeslots'].'~'.$_Mrow['coursecaption'].'~'.$_Mrow['roomcaption'].'~'.$_Mrow['teachername'].' '.$_Mrow['teacherfamily'].'~'.$_Mrow['groupcaption'].'~'.$_Mrow['courseID'].'~'.$_Mrow['roomID'].'~'.$_Mrow['teacherID'].'~'.$_Mrow['groupID'];
  	}
  	return $_Moutstr;
  }

  function _Fgetroomscheduledcourses($_Mroomid,$_Mgroupid)
  {
  	$_Moutstr='';
  	if($_Mgroupid!=0)
  	{
  		$_Msqlstr="select timeslots,coursecaption,teachername,teacherfamily,courseID,teacherID 
  				 from assignments where roomID={$_Mroomid} and groupID={$_Mgroupid} and termID={$_SESSION['activetermid']}";
	  	if($_Mresult=mysql_query($_Msqlstr))
	  	{	
  		while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  		$_Moutstr.='#'.$_Mrow['timeslots'].'~'.$_Mrow['coursecaption'].'~~'.$_Mrow['teachername'].' '.$_Mrow['teacherfamily'].'~~'.$_Mrow['courseID'].'~0~'.$_Mrow['teacherID'].'~'.$_Mrow['groupID'];
	  	}else return -1;
  	}	
  	else
  	{	
  		$_Msqlstr="select timeslots,coursecaption,teachername,teacherfamily,groupcaption 
  				 from assignments where roomID={$_Mroomid} and termID={$_SESSION['activetermid']}";
  		if($_Mresult=mysql_query($_Msqlstr))
  		{	
  			while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  			$_Moutstr.='#'.$_Mrow['timeslots'].'~'.$_Mrow['coursecaption'].'~~'.$_Mrow['teachername'].' '.$_Mrow['teacherfamily'].'~'.$_Mrow['groupcaption'].'~'.$_Mrow['courseID'].'~0~'.$_Mrow['teacherID'].'~'.$_Mrow['groupID'];
  		}else return -1;
  	}
  	$_Mout=0;
  	if(mysql_num_rows($_Mresult)==0) $_Mout=0;
  	mysql_freeresult($_Mresult);
  	if($_Mout==0)return $_Mout;//920702
  	return $_Moutstr;		
  }

function _Fgetcourseidfrom($s,&$_Mcoursepart)
{
	$_Mcidcpt=explode("!",$s);
	$_Mcourseid=$_Mcidcpt[0];
	$_Mcoursepart=$_Mcidcpt[1];
	return $_Mcourseid;
}  
//will be optimized, should be one library only
function _Fgetsubgroups1($_Mgroupsactions)
{
	$_Mgroupsqeueu=array();$_Mnewga=array();$_Mfinalgroups=array();
	$_Mgroups1=array();$_Mgroupids=array();
	$_Msqlstr="select groupID,groupcaption,subgroups,finalgroup,'1' as flag from groupstatus where termID={$GLOBALS['_Mactivetermid']} ";//and groupID in(select groupID from groups where finalgroup=1)";
	if($_Mresult=mysql_query($_Msqlstr))
	{
		$_Mgroupscount=mysql_num_rows($_Mresult);
		if($_Mgroupscount>0)
		{
			while($_Mrow=mysql_fetch_array($_Mresult,MYSQL_ASSOC))
				$_Mgroups1[$_Mrow['groupID']]=$_Mrow;
			mysql_free_result($_Mresult);
		}else return false;
	}else return false;
	foreach ($_Mgroupsactions as $ga)
		array_push($_Mnewga,explode(",",$ga));

	for($i=0 ; $i < count($_Mnewga) ; $i++)
	{
		//if($_Mgroups1[$_Mnewga[$i][0]]["finalgroup"]==1) 
		//because all mastergroup ids should be inclouded too
		{
			if(!(array_key_exists($_Mnewga[$i][0],$_Mfinalgroups)) )
			{
				$_Mfinalgroups[$_Mgroups1[$_Mnewga[$i][0]]['groupID']]=$_Mgroups1[$_Mnewga[$i][0]];
				$_Mfinalgroups[$_Mgroups1[$_Mnewga[$i][0]]['groupID']]['flag']=1;
			}
		}
		if(($_Mgroups1[$_Mnewga[$i][0]]["finalgroup"]!=1))
		{
			if(($_Mgroups1[$_Mnewga[$i][0]]["subgroups"]!=0)&($_Mgroups1[$_Mnewga[$i][0]]["subgroups"]!='')&($_Mgroups1[$_Mnewga[$i][0]]["subgroups"]!=' '))
			{
				$_Msubs=explode(",",$_Mgroups1[$_Mnewga[$i][0]]["subgroups"]);
				for($j=0 ; $j < count($_Msubs) ; $j++)
					array_push($_Mnewga,array($_Msubs[$j],$_Mnewga[$i][1]));
			}
		}
		//array_shift($_Mnewga);
	}
	$_Mgroups="";
 	for( ; count($_Mfinalgroups)>0;)
	{
		$_Mrow=array_pop($_Mfinalgroups);
		if(($_Mrow["groupID"]!='0')&($_Mrow["groupID"]!=''))
			$_Mgroups.=$_Mrow["groupID"].',';
	}
	$_Mgroups=substr($_Mgroups,0,strlen($_Mgroups)-1);
	return $_Mgroups;
}
//*******************************
?>