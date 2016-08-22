<?php session_start();
  //@include "mygenclasses.php";
    @include_once "mygenclasses.php";
	@include_once "shrfuns.php";
	
  $_Mcon=new _CLconnection();
  //remove to here

  //@include "mygenclasses.php";
  //should be optimized no connecting in each page and no setting of activeterm
  //$_Mcon=new connection('localhost','root','vertrigo','att');
  //$_Mcon->connectdb();
  $_Mtype=$_POST['type'];
  if($_Mtype=='t')
  {
  	$_Mtid=$_POST['teacherid'];$_Mtimes=$_POST['times'];$_Mgroupid=$_POST['groupid'];
  	//$_Mtid=1;$ttimes='nnnnnnffffffeeeeoo';$_Mgroupid=1;
  	echo _Fsaveteachertimestodb($_Mtid,$_Mtimes,$_Mgroupid);exit();
  }	
  else if($_Mtype=='g')
  {
  	$_Mtimes=$_POST['times'];$_Mgroupid=$_POST['groupid'];
  	//$_Mtid=1;$ttimes='nnnnnnffffffeeeeoo';$_Mgroupid=1;
  	echo _Fsavegrouptimestodb($_Mtimes,$_Mgroupid);exit();
  }
  else if($_Mtype=='c')
  {
  	$_Mcidcpt=explode("!",$_POST['courseid']);

  	$_Mcourseid=$_Mcidcpt[0];$_Mcoursepart=$_Mcidcpt[1];$_Mtimes=$_POST['times'];$_Mgroupid=$_POST['groupid'];
  	//if($_Mcoursepart=='c')
  	//$_Mtid=1;$ttimes='nnnnnnffffffeeeeoo';$_Mgroupid=1;
  	echo _Fsavecoursetimestodb($_Mcourseid,$_Mcoursepart,$_Mtimes,$_Mgroupid);exit();
  }	
  else if($_Mtype=='r')
  {
  	$_Mroomid=$_POST['roomid'];$_Mtimes=$_POST['times'];$_Mgroupid=$_POST['groupid'];$_Mbuildingid=$_POST['buildingid'];
  	//$_Mtid=1;$ttimes='nnnnnnffffffeeeeoo';$_Mgroupid=1;
  	echo _Fsaveroomtimestodb($_Mroomid,$_Mtimes,$_Mgroupid,$_Mbuildingid);exit();
  }	
  
  
  
  
  

  //****************************************
  //baraye ezafe kardane recordi baraye zamanhaye ostad(dar tchrtimes) dar hengame voroode ettelaat behine shavad.
  function _Fsaveteachertimestodb($_Mtid,$_Mtimes,$_Mgroupid)
  {
  	$_Msqlstr="select * from tchrtimes where teacherID={$_Mtid} and termID={$_SESSION['activetermid']} and teachergroupid={$_Mgroupid}";
  	if(!($_Mresult=mysql_query($_Msqlstr)))
  		return -1;
  	$_Mprefsum=_Fgetprefsum($_Mtimes);
  	if(mysql_num_rows($_Mresult)==0)
  	{
  		$_Msqlstr="insert into tchrtimes(teacherID,termID,teachergroupid,initialtimes,times,prefsum) values
  		   		 ('{$_Mtid}','{$_SESSION['activetermid']}','{$_Mgroupid}','{$_Mtimes}','{$_Mtimes}','$_Mprefsum')";
  		$_Mresult=mysql_query($_Msqlstr);
  		if($_Mresult)
  		{return 1;}
  		else {return 0;  }
  	}
  	else 
  	{
  		$_Mrow=mysql_fetch_array($_Mresult);
  		$_Minitialtimes=$_Mrow['initialtimes'];
  		_Fcheck4scheduledtimes($_Mrow['times'],$_Mtimes,$_Minitialtimes);
  		$_Msqlstr="update tchrtimes set initialtimes='{$_Minitialtimes}',times='{$_Mtimes}',prefsum='$_Mprefsum' where teacherID={$_Mtid} and termID={$_SESSION['activetermid']} and teachergroupid={$_Mgroupid}";
  		$_Mresult=mysql_query($_Msqlstr);
  		if($_Mresult)
  		{return 1;}
  		else {return 0;  }  		
  	}	
  }

  function _Fcheck4scheduledtimes($_Mcurrentimes,&$_Mnewtimes,&$_Minitialtimes)
  {
  	for($i=0 ; $i<_Fgettotalslotsno() ; $i++)
  	{
  		if($_Mnewtimes[$i]!=$_Mcurrentimes[$i])
  		{//needs future check & complete for e-o
  			if(_Fscheduled($_Mcurrentimes[$i],$_Minitialtimes[$i]) & $_Mnewtimes[$i]=='n')
  				$_Mnewtimes[$i]=$_Mcurrentimes[$i];
  			if($_Mcurrentimes[$i]=='n')
  				$_Minitialtimes[$i]=$_Mnewtimes[$i];
  			if( !(_Fscheduled($_Mcurrentimes[$i],$_Minitialtimes[$i])))
  				$_Minitialtimes[$i]=$_Mnewtimes[$i];
  		}
  	}
  }
  function _Fscheduled($_Mcur,$_Minit)
  {
  	if(($_Mcur=='s')|| ( (($_Mcur=='o')||($_Mcur=='e')||($_Mcur=='p')) & ($_Minit=='f') )) return true;
  	else return false;
  }
  
  function _Fsavecoursetimestodb($_Mcourseid,$_Mcoursepart,$_Mtimes,$_Mgroupid)
  {
  	/*
  	$_Msqlstr="select * from termcoursestatus where courseID={$cid} and coursepart like '$_Mcoursepart' and termID={$_SESSION['activetermid']} and groupidconf1={$_Mgroupid}";
  	if(!($_Mresult=mysql_query($_Msqlstr)))
  		return -1;
  	$_Mprefsum=_Fgetprefsum($_Mtimes);
  	if(mysql_num_rows($_Mresult)==0)
  	{
  		$_Msqlstr="insert into termcoursestatus(courseID,termID,coursepart,groupidconf1,coursepreferedtimes,prefsum,coursehaspref) values
  		   		 ('{$cid}','{$_SESSION['activetermid']}','','{$_Mgroupid}','{$_Mtimes}','$_Mprefsum','1')";
  		$_Mresult=mysql_query($_Msqlstr);  		
  		if($_Mresult)
  		{return 1;}
  		else {return 0;  }
  	}
  	else
  	*/ 
  	{
		$_Mprefsum=_Fgetprefsum($_Mtimes);
  		if($_Mcoursepart[0]!='a') 
  		{
  			$_Mcoursepart='%a%';
  			$_Msqlstr="update termcoursestatus set coursepreferedtimes='{$_Mtimes}',coursehaspref=1,prefsum='$_Mprefsum' where courseID={$_Mcourseid} and coursepart not like '$_Mcoursepart' and termID={$_SESSION['activetermid']} and groupidconf1={$_Mgroupid}";
  		}
		else $_Msqlstr="update termcoursestatus set coursepreferedtimes='{$_Mtimes}',coursehaspref=1,prefsum='$_Mprefsum' where courseID={$_Mcourseid} and coursepart like '%a%' and termID={$_SESSION['activetermid']} and groupidconf1={$_Mgroupid}";
  		$_Mresult=mysql_query($_Msqlstr);
  		if($_Mresult)
  		{return 1;}
  		else {return 0;  }  		
  	}	
  }
  function _Fgetprefsum($_Mtimes)
  {
  	$_Mprefsum=0;
  	for($i=0 ; $i<_Fgettotalslotsno() ; $i+=2)
  	{
  		if($_Mtimes[$i]=='f') $_Mprefsum+=2;
  		else if(($_Mtimes[$i]=='o')||($_Mtimes[$i]=='e')) $_Mprefsum+=1;
  	}
  	return $_Mprefsum;
  }
  function _Fsavegrouptimestodb($_Mtimes,$_Mgroupid)
  {
  	$_Msqlstr="select * from groupstatus where termID={$_SESSION['activetermid']} and groupID={$_Mgroupid}";
  	if(!($_Mresult=mysql_query($_Msqlstr)))
  		return -1;
  	
	if(mysql_num_rows($_Mresult)==0)
	{	
	 	$_Msqlstr="insert into groupstatus(groupID,termID,initialgroupstatus,groupstatus) values
	 	   		 ('{$_Mgroupid}','{$_SESSION['activetermid']}','{$_Mtimes}','{$_Mtimes}')";
	 	$_Mresult=mysql_query($_Msqlstr);  		
	 	if($_Mresult)
	 	{return 1;}
	 	else return 0;
	 }
	 else 
	 {//will change for not to overwriting scheduled slots, or masking scheduled slots on it after updating
	 	$_Msqlstr="update groupstatus set initialgroupstatus='{$_Mtimes}',groupstatus='{$_Mtimes}' where termID={$_SESSION['activetermid']} and groupID={$_Mgroupid}";
	 	$_Mresult=mysql_query($_Msqlstr);  		
	 	if($_Mresult)
	 	{return 1;}
	 	else {return 0;}  		
	 }
	  	
  	
  }

  function _Fsaveroomtimestodb($_Mroomid,$_Mtimes,$_Mbuildingid)
  {
  	$_Msqlstr="select * from roomsh where termID={$_SESSION['activetermid']} and roomID={$_Mroomid}";
  	if(!($_Mresult=mysql_query($_Msqlstr))) return -1;
  	
  	if(mysql_num_rows($_Mresult)==0)
  	{
  		$_Msqlstr="insert into roomsh(roomID,termID,initialroomstatus,roomstatus) values
  		   		 ('$_Mroomid','{$_SESSION['activetermid']}','{$_Mtimes}','{$_Mtimes}')";
  		if($_Mresult=mysql_query($_Msqlstr)) return 1;
  		else return 0;
  	}
  	else
  	{
  		$_Msqlstr="update roomsh set initialroomstatus='$_Mtimes' where termID={$_SESSION['activetermid']} and roomID=$_Mroomid";
  		if($_Mresult=mysql_query($_Msqlstr)) return 1;
  		else return 0;
  	}
  }
?>