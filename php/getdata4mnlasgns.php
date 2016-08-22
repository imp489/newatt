<?php session_start();
  //@include "mygenclasses.php";
  @include_once "mygen_lib.php";
  @include_once "mygenclasses.php";
  $_Mcon=new _CLconnection();

  //remove later
  
  //remove to here

  //@include "mygenclasses.php";
  //should be optimized no connecting in each page and no setting of activeterm
  //$_Mcon=new connection('localhost','root','vertrigo','att');
  //$_Mcon->connectdb();
  
  $_Mtype=$_POST['type'];
//$_Mreporttype='g';
  if($_Mtype=='b')
  {  echo _Fgetbuildings();exit();  }
  
  if($_Mreporttype=='br')
  {  $_Mbuildingid=$_POST['buildingid']; echo _Fgetbuildingrooms($_Mbuildingid);exit();  }

  if($_Mreporttype=='u')//universities
  {echo _Fgetunvs();exit();}
  
  if($_Mtype=='ug')// a university groups
  {$_Munvid=$_POST['unvid'];echo _Fgetunvmaingroups();exit();}
  
  if($_Mtype=='ugs')// all universities and groups
  {echo repunvmaingroups();}

  if($_Mtype=='g')
  {  echo _Fgetgroupslist();exit();}
  
  if($_Mreporttype=='gc')//group courses
  {  $_Mgroupid=$_POST['groupid']; echo _Fgetgroupcourses($_Mgroupid);exit();  }
  
  if($_Mtype=='gtc')
  {$_Mgroupid=$_POST['groupid'];echo _Frepgroupteacherscourses($_Mgroupid);}

  
  
  

  //****************************************
function _Frepgroupteacherscourses($_Mgroupid)
{	
	$_Moutstr="";
	$_Msqlstr="select * from tchrtimes where ((teachergroupid={$_Mgroupid}) and 
										(termID={$_SESSION['activetermid']}))";
	$_Mgroupteachers=mysql_query($_Msqlstr);
	while($_Mrow=mysql_fetch_assoc($_Mgroupteachers))
	{
		$_Moutstr.="#".$_Mrow['teacherID'].'~'.$_Mrow['teachername'].' '.$_Mrow['teacherfamily'];
	}
	mysql_free_result($_Mgroupteachers);
	return $_Moutstr;			
}
  
  //*********************will remove functions below
  function _Fgetunvs()
  {
  	$_Moutstr='';
  	$_Msqlstr="select groupID,groupcaption,groupcode from groups where subgroupof=0 and grouplevel=1 order by groupcaption";
  	$_Mresult=mysql_query($_Msqlstr);
  	while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  $_Moutstr.='#'.$_Mrow['groupID'].'~'.$_Mrow['groupcaption'].'~'.$_Mrow['groupcode'];
  	mysql_freeresult($_Mresult); 	
  	return $_Moutstr;
  }

  function _Fgetunvmaingroups()
  {
  	$_Moutstr='';
  	$_Msqlstr="select groupID,groupcaption,groupcode from groups where subgroupof<>0 and grouplevel=2 order by groupcaption";
  	$_Mresult=mysql_query($_Msqlstr);
  	while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  $_Moutstr.='#'.$_Mrow['groupID'].'~'.$_Mrow['groupcaption'].'~'.$_Mrow['groupcode'];
  	mysql_freeresult($_Mresult); 	
  	return $_Moutstr;
  }
  
  function _Fgetunvsandgroups()
  {
  	$_Moutstr='';
  	$_Msqlstr="select groupID,groupcaption,groupcode from groups where subgroupof<>0 and grouplevel=2 order by groupcaption";
  	$_Mresult=mysql_query($_Msqlstr);
  	while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  $_Moutstr.='#'.$_Mrow['groupID'].'~'.$_Mrow['groupcaption'].'~'.$_Mrow['groupcode'];
  	mysql_freeresult($_Mresult); 	
  	return $_Moutstr;
  }
  
  function _Fgetgroupcourses($_Mgroupid)
  {
  	$_Moutstr='';
  	$_Msqlstr="select courseID,coursecaption from termcoursestatus where groupidconf1={$_Mgroupid} and termID={$_SESSION['activetermid']} order by coursecaption";
  	$_Mresult=mysql_query($_Msqlstr);
  	while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  $_Moutstr.='#'.$_Mrow['courseID'].'@@**@@~'.$_Mrow['coursecaption'];
  	mysql_freeresult($_Mresult); 	
  	return $_Moutstr;
  }
  
  function _Fgetbuildingrooms($_Mbuildingid)
  {
  	$_Moutstr='';
  	$_Msqlstr="select roomID,roomcaption from roomstatus where termID={$_SESSION['activetermid']} and buildingID={$_Mbuildingid} order by roomcaption";
  	$_Mresult=mysql_query($_Msqlstr);
  	while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  $_Moutstr.='#'.$_Mrow['roomID'].'~'.$_Mrow['roomcaption'];
  	mysql_freeresult($_Mresult);
  	  return $_Moutstr;
  }
  
  function _Fgetbuildings()
  {
  	$_Moutstr='';
  	$_Msqlstr="select buildingID,buildingcaption from buildings order by buildingID";
  	$_Mresult=mysql_query($_Msqlstr);
  	while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  $_Moutstr.='#'.$_Mrow['buildingID'].'~'.$_Mrow['buildingcaption'];
  	mysql_freeresult($_Mresult); 	
  	return $_Moutstr;
  }
  
  function _Fgetgroupslist()
  {
  	$_Moutstr='';
  	$_Msqlstr="select groupID,groupcaption,groupcollege from groups where finalgroup=1 order by groupcollege,subgroupof,groupcaption";
  	$_Mresult=mysql_query($_Msqlstr);
  	while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  $_Moutstr.='#'.$_Mrow['groupID'].'~'.$_Mrow['groupcollege'].' - '.$_Mrow['groupcaption'];
  	mysql_freeresult($_Mresult); 	
  	return $_Moutstr;
  }

  function _Fgetgroupslist2()
  {
  	$_Moutstr='';
  	$_Msqlstr="select groupID,groupcaption,groupcode from groups where finalgroup=0 and grouplevel=2 order by subgroupof,groupcaption";
  	$_Mresult=mysql_query($_Msqlstr);
  	while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  $_Moutstr.='#'.$_Mrow['groupID'].'~'.$_Mrow['groupcode'].' - '.$_Mrow['groupcaption'];
  	mysql_freeresult($_Mresult); 	
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
		$_Moutstr.="#".$_Mrow['teacherID'].'~'.$_Mrow['teachername'].' '.$_Mrow['teacherfamily'].'~'.$_Mrow['teachermaxslots'].'~'.$_Mrow['teachercurslots'];
	}
	mysql_free_result($_Mgroupteachers);
	return $_Moutstr;			

}
 
?>