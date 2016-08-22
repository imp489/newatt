<?php session_start();
	//  report initial data to use in this term
  @include_once "mygenclasses.php";
  @include_once "getdata_lib.php";
  @include_once "shrfuns.php";
  $_Mcon=new _CLconnection();

  $_Mactivetermid=$_SESSION['activetermid'];
  if(isset($_POST['groupid']))
  {
  	$_Mgroups=_Fgetallsubgroups($_POST['groupid']);
  	//$_Mgroups=_Fgetsubgroups1(array("{$_POST['groupid']},1#"));
  	$_Mmgid1=_Fgetmastergroup($_POST["groupid"],$_Mactivetermid);$_Mgroups1=_Fgetallsubgroups($_Mmgid1);//_Fgetsubgroups1(array("$_Mmgid1,1#"));
  }
  $_Mreporttype=$_POST['reporttype'];
/*
  $_Mactivetermid=2;
  $_Mreporttype='br';
  $_Mgroups=_Fgetsubgroups1(array("3,1#"));
 */ 
  
  if($_Mreporttype=='b')
  {  echo _Fgetbuildings();exit();  }
  else if($_Mreporttype=='br')
  {if((isset($_POST['buildingid']))& $_POST['buildingid']!=0){ $_Mbuildingid=$_POST['buildingid'];$_Mlowerparts=_Fgetlowerparts($_Mbuildingid);echo _Fgetbuildingroomstermres($_Mlowerparts);}else echo -1;exit();  }

  else if($_Mreporttype=='u')//universities
  {echo _Fgetunvs();}
  else if($_Mreporttype=='ug')// a university groups
  {$_Munvid=$_POST['unvid'];echo _Fgetunvmaingroups();}
  else if($_Mreporttype=='ugs')// all universities and groups
  {echo _Frepunvmaingroups();}

  else if($_Mreporttype=='g')
  {  echo _Fgetgroupslist();exit();}
  elseif($_Mreporttype=='gc')//group courses
  { echo _Fgetgroupcoursestermres($_Mgroups);exit();  }
  else if($_Mreporttype=='gt')
  {echo _Frepgroupteacherstermres($_Mgroups1);exit();}
  else{ echo "not of predefined request types!";exit();}
  
  
  

  //**************************************** specially for initials

function _Frepgroupteacherstermres($_Mgroups)
{	
	$_Moutstr="";
	$_Msqlstr="select tchrs.teacherID,tchrs.teachername,tchrs.teacherfamily,groupID,sign(tchrtimes.teacherID) as hasset  from tchrs left outer join tchrtimes on ((tchrs.teacherID=tchrtimes.teacherID)and(tchrtimes.termID={$GLOBALS['_Mactivetermid']})) where (tchrs.groupID in({$_Mgroups}) and tchrs.teacherstate=1) order by teacherfamily";
	if($_Mgroupteachers=mysql_query($_Msqlstr))//920623
	{
		if(mysql_num_rows($_Mgroupteachers)>0){
		while($_Mrow=mysql_fetch_assoc($_Mgroupteachers))
		{
			$_Moutstr.="#".$_Mrow['teacherID'].'~'.$_Mrow['teachername'].' '.$_Mrow['teacherfamily'].'~'.$_Mrow['hasset'].'~'.$_Mrow['groupID'];
		}
		mysql_free_result($_Mgroupteachers);
		return $_Moutstr;
		}else return 0;
		
	}else return -1;//920623
}

function _Fgetgroupcoursestermres($_Mgroups)
{
  	$_Moutstr='';
  	//$_Msqlstr="select courses.courseID,courses.coursecaption,courses.groupID,sign(termcoursestatus.courseID) as hasset from courses left outer join  termcoursestatus on ((courses.courseID=termcoursestatus.courseID)and(termcoursestatus.termID={$GLOBALS['_Mactivetermid']})and(courses.groupID=termcoursestatus.groupidconf1))where courses.groupID in($_Mgroups) group by courses.courseID order by courses.groupID,courses.coursecaption";
  	$_Msqlstr="select courseID,coursefcaption,groupID,coursespecs from courses where groupID in($_Mgroups) order by groupID,coursefcaption";
  	//echo $_Msqlstr;
  	if(!($_Mresult=mysql_query($_Msqlstr))) return -1;
  	if(mysql_num_rows($_Mresult)<=0) return 0;
  	while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  $_Moutstr.="#{$_Mrow['courseID']}~{$_Mrow['coursefcaption']}~{$_Mrow['groupID']}~{$_Mrow['coursespecs']}";
  	mysql_free_result($_Mresult);
  	return $_Moutstr;
}

function _Fgetbuildingroomstermres($_Mbuildingids)
{
  	$_Moutstr='';
  	$_Msqlstr="select distinct rooms.roomID,rooms.roomcaption,sign(roomstatus.roomID) as hasset  from rooms  left outer join roomstatus on ((rooms.roomID=roomstatus.roomID)and(roomstatus.termID={$GLOBALS['_Mactivetermid']})) where rooms.subgroupof in ($_Mbuildingids) order by roomcaption";
  	//echo $_Msqlstr;
  	$_Mresult=mysql_query($_Msqlstr);
  	if($_Mresult)
  	{
  		if(mysql_num_rows($_Mresult)>0)
  		{
  			while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  			$_Moutstr.='#'.$_Mrow['roomID'].'~'.$_Mrow['roomcaption'].'~'.$_Mrow['hasset'];
  			mysql_freeresult($_Mresult);
  	  		return $_Moutstr;
  		}else return 0;
  	}else return -1;
}

//************************************
 
  
  
?>