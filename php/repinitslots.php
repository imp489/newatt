<?php session_start();
  //reports the initial timesheets for resources
  @include_once "mygenclasses.php";
  @include_once "shrfuns.php" ;
  $_Mcon=new _CLconnection();
  $_Mtype=$_POST['type'];//$_Mtype='gt';
  if($_Mtype=='t')
  {
  	$_Mtid=$_POST['teacherid'];$_Mgroupid=$_POST['groupid'];
  	//$_Mtid=1;$ttimes='nnnnnnffffffeeeeoo';$_Mgroupid=1;
  	echo _Fgetteachertimes($_Mtid,$_Mgroupid);exit();
  }	
  elseif($_Mtype=='gt')
  {
  	$_Mgroupid=$_POST['groupid'];
  	//$_Mgroupid=1;$_SESSION['activetermid']=2;
  	echo _Fgetgroupteachertimes($_Mgroupid);exit();
  }	

  else if($_Mtype=='g')
  {
  	$_Mgroupid=$_POST['groupid'];
  	//$_Mtid=1;$ttimes='nnnnnnffffffeeeeoo';$_Mgroupid=1;
  	echo _Fgetgrouptimes($_Mgroupid);exit();
  }	
  else if($_Mtype=='c')
  {
  	$_Mcourseid=$_POST['courseid'];$_Mgroupid=$_POST['groupid'];
  	//$_Mcourseid=1;$_Mgroupid=1;
  	//$_Mtid=1;$ttimes='nnnnnnffffffeeeeoo';$_Mgroupid=1;
  	echo _Fgetcoursetimes($_Mcourseid,$_Mgroupid);exit();
  }
  else if($_Mtype=='r')
  {
  	$_Mroomid=$_POST['roomid'];//$buildingid=$_POST['buildingid'];
  	//$_Mtid=1;$ttimes='nnnnnnffffffeeeeoo';$_Mgroupid=1;
  	echo _Fgetroomtimes($_Mroomid);exit();
  }

    //****************************************
  //baraye ezafe kardane recordi baraye zamanhaye ostad(dar tchrtimes) dar hengame voroode ettelaat behine shavad.
  function _Fgetteachertimes($_Mtid,$_Mgroupid)
  {
  	$_Msqlstr="select initialtimes from tchrtimes where teacherID={$_Mtid} and termID={$_SESSION['activetermid']} and teachergroupid={$_Mgroupid}";
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
	  	if(mysql_num_rows($_Mresult)!=0)
	  	{
	  		$_Mrow=mysql_fetch_array($_Mresult);
	  		$_Mtimes=$_Mrow[0];
	  		mysql_free_result($_Mresult);
	  		return $_Mtimes;
	  	}
	  	else 
			return 0;
  	}
  	else return -1;
  		
  }
  function _Fgetgroupteachertimes($_Mgroupid)
  {
  	$_Moutstr="";$_Mallsubs=_Fgetallsubgroups($_Mgroupid);
  	$_Mgroups=_Fgetallsubgroups($_Mgroupid);
  	$_Msqlstr="select  teachername,teacherfamily,initialtimes,times from tchrtimes where termID={$_SESSION['activetermid']} and teachergroupid in ($_Mallsubs)";
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
	  	if(mysql_num_rows($_Mresult)!=0)
	  	{
	  		while($_Mrow=mysql_fetch_array($_Mresult))
	  			$_Moutstr.="#{$_Mrow['teachername']}{$_Mrow['teacherfamily']} ~{$_Mrow['initialtimes']}~{$_Mrow['times']}";

	  		mysql_free_result($_Mresult);
	  		return $_Moutstr;
	  	}else return 0;
  	}else return -1;
  }

    function _Fgetcoursetimes($_Mcid,$_Mgroupid)
  {

  	$_Mcidcpt=explode("!",$_Mcid);
  	$_Msqlstr="select coursepreferedtimes,coursepart from termcoursestatus where courseID={$_Mcidcpt[0]} and coursepart like '$_Mcidcpt[1]' and termID={$_SESSION['activetermid']} and groupidconf1={$_Mgroupid}";
  	//echo $_Msqlstr;
  	$_Mresult=mysql_query($_Msqlstr);
  	if(mysql_num_rows($_Mresult)!=0)
  	{
 		$_Mrow=mysql_fetch_array($_Mresult);
  		$_Mtimes=$_Mrow[0];
  		mysql_free_result($_Mresult);
  		return $_Mtimes;
  	}
  	else 
  		return 0;
}	

  function _Fgetgrouptimes($_Mgroupid)
  {
  	$_Msqlstr="select initialgroupstatus from groupstatus where termID={$_SESSION['activetermid']} and groupID={$_Mgroupid}";
  	$_Mresult=mysql_query($_Msqlstr);
  	if(mysql_num_rows($_Mresult)!=0)
  	{
 		$_Mrow=mysql_fetch_array($_Mresult);
  		$_Mtimes=$_Mrow[0];
  		mysql_free_result($_Mresult);
  		return $_Mtimes;
  	}
  	else 
  		return 0;
}	
  
  function _Fgetroomtimes($_Mroomid)
  {//will fix by adding groopid in query
  	$_Msqlstr="select initialroomstatus from roomsh where termID={$_SESSION['activetermid']} and roomID=$_Mroomid";
  	//echo $_Msqlstr;
  	if(!($_Mresult=mysql_query($_Msqlstr))) return -1;
	if(mysql_num_rows($_Mresult)<=0) return 0;
	$_Mrow=mysql_fetch_array($_Mresult);
	$_Mtimes=$_Mrow[0];
	return $_Mtimes;
}	
?>