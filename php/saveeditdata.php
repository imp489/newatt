<?php session_start();
  //@include "mygenclasses.php";
  @include_once "editdatarep.php";
  $_Mcon=new _CLconnection();

  $_Mactivetermid=$_SESSION['activetermid'];
  $_Mtype=$_POST['edittype'];
  if(isset($_POST['fv'])) $_Mfv=$_POST['fv'];
  if(isset($_POST['groupid'])) $_Mgroupid=$_POST['groupid'];
  
  //if(isset($_POST['groupid']))
  //{$_Mgroups=_Fgetsubgroups1(array("{$_POST['groupid']},1#"));}
/*
  $_Mactivetermid=2;
  $_Mtype='egt';
  $_Mgroups=_Fgetsubgroups1(array("3,1#"));
*/  


//** edit***********
  if($_Mtype=='egt')
  {$_Mteacherid=$_POST['teacherid'];echo _Fsaveeditgroupteachers($_Mfv,$_Mteacherid,$_Mgroupid);exit();}
  else if($_Mtype=='egc')//group courses
  { $_Mcourseid=$_POST['courseid'];echo _Fsaveeditgroupcourses($_Mfv,$_Mcourseid);exit();  }
  else if($_Mtype=='ebr')
  {  $_Mroomid=$_POST['roomid'];echo _Fsaveeditbrooms($_Mfv,$_Mroomid);exit();}
  else if($_Mtype=='eb')
  {  echo _Fgetbuildings();exit();  }
  
  else if($_Mtype=='dgt')
  {$_Mteacherid=$_POST['teacherid'];_Fsavedelgroupteacher($_Mteacherid,$_Mgroupid);
  
  	$_Mgroups=_Fgetsubgroups1(array("{$_POST['mgroupid']},1#"));echo _Frepeditgroupteachers($_Mgroups);exit();}
  
  else if($_Mtype=='dgc')
  {$_Mcourseid=$_POST['courseid'];_Fsavedelgroupcourse($_Mcourseid);
  
  	//$_Mgroups=_Fgetsubgroups1(array("{$_POST['mgroupid']},1#"));echo _Freproomtypes().'@@@@'._Frepeditgroupcourses($_Mgroups);exit();
  	$_Mgroups=_Fgetallsubgroups($_POST['mgroupid']);echo _Freproomtypes().'@@@@'._Frepeditgroupcourses($_Mgroups);exit();//920623
  }
  else if($_Mtype=='dbr')//920623
  {//should complete920623
  	$_Mroomid=$_POST['roomid'];_Fsavedelbuildingroom($_Mroomid);
  //
  	//echo  _Freproomtypes().'@@@@';$_Mlowerparts=_Fgetlowerparts($_POST['buildingid']);echo _Frepeditrooms($_Mlowerparts);exit();
  	echo  _Freproomtypes().'@@@@';$_Mlowerparts=_Fgetlowerparts(1);echo _Frepeditrooms($_Mlowerparts);exit();
  }
  
  else {echo "not known type";exit();}	


//** edit  ***************
  
  
// edit*********
  function _Fsaveeditgroupteachers($_Mfv,$_Mteacherid,$_Mgroupid)
  {	
  	$_Msqlstr="update tchrs set ";$_Msqlstr2="update tchrtimes set ";
  	$_Mfieldslist=array('teachername','teacherfamily','cooptype','teacherdegree','teacherfield','teachermaxslots','teacherminslots','teachertel','teachermobile','email','teacheradrs','teacherdesc');
	$_Mfieldvalues=explode("~",$_Mfv);
	for($i=0 ; $i<count($_Mfieldvalues) ; $i++)
	{
		$_Msqlstr.="{$_Mfieldslist[$i]}='{$_Mfieldvalues[$i]}',";
		if ($i==5 || $i==6) $_Msqlstr2.="{$_Mfieldslist[$i]}='{$_Mfieldvalues[$i]}',";
	}
	$_Msqlstr=substr($_Msqlstr,0,strlen($_Msqlstr)-1);$_Msqlstr2=substr($_Msqlstr2,0,strlen($_Msqlstr2)-1);
	$_Msqlstr.=" where groupID=$_Mgroupid and teacherID=$_Mteacherid";//echo $_Msqlstr;
	$_Msqlstr2.=" where teachergroupid=$_Mgroupid and teacherID=$_Mteacherid";
	mysql_query($_Msqlstr2);
	if(mysql_query($_Msqlstr))
		return 1;
	else return  -1;
  }
  function _Fsaveeditgroupcourses($_Mfv,$_Mcourseid)
  {
  	var_dump($_Mfv);
  	$_Msqlstr="update courses set ";
  	$_Mfieldslist=array('coursefcaption','coursecaption','coursetype','courseAunits','courseTunits','courseAunitstchrs','courseTunitstchrs','coursepres','coursesims','neededroomtypeID','neededroomtypeAID','coursedesc','coursehrdnes');
	$_Mfieldvalues=explode("~",$_Mfv);$j=0;
	for($i=0 ; $i<count($_Mfieldvalues) ; $i++)
	{
		/*
		if($_Mfieldslist[$i]=='neededroomtypeID')
		{
			$_Mrids=explode(",",$_Mfieldvalues[$j]);
			$_Msqlstr.="{$_Mfieldslist[$i]}='{$_Mrids[0]}',{$_Mfieldslist[$j+1]}='{$_Mrids[1]}',";
			$i++;
		}
		else 
		*/
		$_Msqlstr.="{$_Mfieldslist[$i]}='{$_Mfieldvalues[$i]}',";
		$j++;
	}
	$_Msqlstr=substr($_Msqlstr,0,strlen($_Msqlstr)-1);
	$_Msqlstr.=" where courseID=$_Mcourseid";	
	//echo $_Msqlstr;
	if(mysql_query($_Msqlstr))
		return 1;
	else return  -1;
  }
  function _Fsaveeditbrooms($_Mfv,$_Mroomid)
  {	
  	$_Msqlstr="update rooms set ";
  	$_Mfieldslist=array('roomcaption','capacity','equipments','roomtype');
	$_Mfieldvalues=explode("~",$_Mfv);
	for($i=0 ; $i<count($_Mfieldvalues) ; $i++)
		$_Msqlstr.="{$_Mfieldslist[$i]}='{$_Mfieldvalues[$i]}',";
	$_Msqlstr=substr($_Msqlstr,0,strlen($_Msqlstr)-1);
	$_Msqlstr.=" where roomID=$_Mroomid";//echo $_Msqlstr;
	if(mysql_query($_Msqlstr))
		return 1;
	else return  -1;
  }
//later will correct : save room changes to other tables 


// delete
function _Fsavedelgroupteacher($_Mteacherid,$_Mgroupid)
{
	//$_Msqlstr="delete from tchrtimes where teacherID=$_Mteacherid and groupID=$_Mgroupid and termID={$GLOBALS['_Mactivetermid']}";920702
	$_Msqlstr="delete from tchrtimes where teacherID=$_Mteacherid and teachergroupid=$_Mgroupid and termID={$GLOBALS['_Mactivetermid']}";
	mysql_query($_Msqlstr);
	
	$_Msqlstr="update tchrs set teacherstate=0 where teacherID=$_Mteacherid and groupID=$_Mgroupid";
	if(mysql_query($_Msqlstr)) return 1;
	else return 0;
}

function _Fsavedelgroupcourse($_Mcourseid)
{
	$_Msqlstr="delete from courses where courseID=$_Mcourseid and termID={$GLOBALS['_Mactivetermid']}";
	//echo '**'.$_Msqlstr.'**';920702
	if(mysql_query($_Msqlstr)) return 1;
	else return 0;
}

function _Fsavedelbuildingroom($_Mroomid)
{
	$_Msqlstr="delete from rooms where roomID=$_Mroomid and termID={$GLOBALS['_Mactivetermid']}";
	if(mysql_query($_Msqlstr))
		return 1;
	else return 0;
/*
	$_Msqlstr="update tchrs set teacherstate=0 where teacherID=$_Mteacherid and groupID=$_Mgroupid";
	if(mysql_query($_Msqlstr)) return 1;
	else return 0;
*/	
}

// edit*********
  
?>