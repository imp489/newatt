<?php session_start();
  @include_once "mygenclasses.php";
  @include_once "getdata_lib.php";
  $_Mcon=new _CLconnection();
 
  //$_Mactivetermid=2;
  //$_Masgntype="r2g";
  //$_Masgnstr="5~3~1~class A#5~3~2~class B#";
  
  //$_Masgntype="s2c";
  //$_Masgnstr="1!c1~3~~??????? ?????? ????-1~???? ?????#428!c1~2~~???? ???????-428~??? ??????#408!c1~1~5~????? ????????-408~???? ????????#-1";
  
  $_Mactivetermid=$_SESSION['activetermid'];
  $_Masgnstr=$_POST['assignstr'];//get manual asign type 
  $_Masgntype=$_POST['type'];

  if($_Masgntype=='c2t')//Set course teacher  c2t,c1,t1,g1,ccaption,tnamefam
  {	echo _Fsetcourse4teacher($_Masgnstr);exit();}
  else if($_Masgntype=='r2t')//Set teacher's room r2t,tid,rid,gid,tcaption,rcaption
  { echo _Fsetroom4teacher($_Masgnstr);exit();}
  else if($_Masgntype=='r2c')//Set course's room r2c,rid,cid,gid,rcaption,ccaption
  { echo _Fsetroom4course($_Masgnstr);exit();}
  else if($_Masgntype=='s2c')//Set course's time s2c,cid,slots{no,type},gid,ccaption
  {
  	//*** will change to set slot for every part of course separately,
  	//*** now manually breaks course part to insert to db
  	echo _Fsetslot4course($_Masgnstr);exit();
  }
  else if($_Masgntype=='rtg')//Set course's time s2c,cid,slots{no,type},gid,ccaption
  {	echo _Fsetroom4group($_Masgnstr);exit();}
  

  	
  	
  //****************************************
  function _Fsetcourse4teacher($_Masgnstr)//$_Mcourseid,$_Mteacherid,$_Mgroupid,$coursecaption,$teachernamefamily)
  {
  	$_Mout=1;//var_dump($_Masgnstr);
  	$_Mitemsar=explode("#",$_Masgnstr);
  	for($i=0 ; $i<(count($_Mitemsar)-1) ; $i++)
  	{
  		$_Mitems=explode("~",$_Mitemsar[$i]);$_Mcourseteacher=explode(" ==>",$_Mitems[3]);$coursecaption=$_Mcourseteacher[0];
	  	$_Mcourseid=_Fgetcourseidfrom($_Mitems[0],$_Mcoursepart);
  		$_Mteacherid=$_Mitems[1];$_Mgroupid=$_Mitems[2];$teachernamefamily=$_Mitems[4];$_Mfirstsec=$_Mitems[5];
	  	$au=false;$_Merflag=0;
  		if($_Mcoursepart[0]=='a')
	  	{$_Msqlstr="select cnstID from asgncnsts where cnsttype='ct' and courseID=$_Mcourseid and coursepart like '%a%' and termID={$GLOBALS['_Mactivetermid']} and groupID=$_Mgroupid";$au=true;}
		else $_Msqlstr="select cnstID from asgncnsts where cnsttype='ct' and courseID=$_Mcourseid and coursepart not like '%a%' and termID={$GLOBALS['_Mactivetermid']} and groupID=$_Mgroupid";	  		
	  	//echo "--$_Msqlstr--";
	  	if($_Mresult=mysql_query($_Msqlstr))
	  	{
	  		if(mysql_num_rows($_Mresult)!=0)
	  		{
	  			while($_Mrow=mysql_fetch_array($_Mresult))
	  			{
	  				if($_Mfirstsec==1)
	  					$_Msqlstr="update asgncnsts set teacherID='$_Mteacherid',teachernamefam='$teachernamefamily' where cnstID={$_Mrow['cnstID']}";
	  				else $_Msqlstr="update asgncnsts set teacherID2='$_Mteacherid',teachernamefam2='$teachernamefamily' where cnstID={$_Mrow['cnstID']}";
	  				if(!(mysql_query($_Msqlstr)))
	  					$_Merflag=1;
	  			}
	  			if($_Merflag==1) $_Mout=0;else $_Mout=1;
	  		}
		  	else
			{
			if($_Mcoursepart[0]=='a')
			{
				$_Msqlstr="select * from termcoursestatus where courseID=$_Mcourseid and coursepart like '%a%' and termID={$GLOBALS['_Mactivetermid']}";
				if($_Mresult=mysql_query($_Msqlstr))
				{
					if(mysql_num_rows($_Mresult)>0)
					{
						while($_Mrow=mysql_fetch_array($_Mresult))
						{
					  		$_Mcnsttype='ct';
						  	$_Mmgroupid=_Fgetmgroupid($_Mcourseid,$_Mawgroupid);
							if($_Mfirstsec==1)
						  		$_Msqlstr="insert into asgncnsts (cnsttype,teacherID,courseID,coursepart,groupID,maingroupID,awgroupID,termID,teachernamefam,coursecaption) values ('$_Mcnsttype','$_Mteacherid','$_Mcourseid','{$_Mrow['coursepart']}','$_Mgroupid','$_Mmgroupid','$_Mawgroupid','{$GLOBALS['_Mactivetermid']}','$teachernamefamily','$coursecaption')";
						  	else $_Msqlstr="insert into asgncnsts (cnsttype,teacherID2,courseID,coursepart,groupID,maingroupID,awgroupID,termID,teachernamefam2,coursecaption) values ('$_Mcnsttype','$_Mteacherid','$_Mcourseid','{$_Mrow['coursepart']}','$_Mgroupid','$_Mmgroupid','$_Mawgroupid','{$GLOBALS['_Mactivetermid']}','$teachernamefamily','$coursecaption')";		  							
		  					mysql_query($_Msqlstr);
						}
					}else $_Mout=0;
				}else $_Mout=-1;
		  			
		  	}
		  	else
		  	{
				$_Msqlstr="select coursepart from termcoursestatus where courseID=$_Mcourseid and coursepart not like '%a%' and termID={$GLOBALS['_Mactivetermid']}";
				if($_Mresult=mysql_query($_Msqlstr))
				{
					if(mysql_num_rows($_Mresult)>0)
					{
						while($_Mrow=mysql_fetch_array($_Mresult))
						{
		  					$_Mcnsttype='ct';
			  				$_Mmgroupid=_Fgetmgroupid($_Mcourseid,$_Mawgroupid);
		  					if($_Mfirstsec==1)
			  					$_Msqlstr="insert into asgncnsts (cnsttype,teacherID,courseID,coursepart,groupID,maingroupID,awgroupID,termID,teachernamefam,coursecaption) values ('$_Mcnsttype','$_Mteacherid','$_Mcourseid','{$_Mrow['coursepart']}','$_Mgroupid','$_Mmgroupid','$_Mawgroupid','{$GLOBALS['_Mactivetermid']}','$teachernamefamily','$coursecaption')";
			  				else $_Msqlstr="insert into asgncnsts (cnsttype,teacherID2,courseID,coursepart,groupID,maingroupID,awgroupID,termID,teachernamefam2,coursecaption) values ('$_Mcnsttype','$_Mteacherid','$_Mcourseid','{$_Mrow['coursepart']}','$_Mgroupid','$_Mmgroupid','$_Mawgroupid','{$GLOBALS['_Mactivetermid']}','$teachernamefamily','$coursecaption')";	
		  					if(!($_Mresult1=mysql_query($_Msqlstr)))
			  					$$_Mout=0;
						}
					}else $_Mout=0;
				}else $_Mout=-1;
			}
	  	}
	  	
  	}else $_Mout=0;
  	}
	  	//return $actionrep; can be optimised to reduce data transfer,not sending all the list,only changes
	  	//return _Frepgroupteacherscourses($_Mgroupid);
	  return $_Mout; 	
  }
  function _Fgetmgroupid($_Mcourseid,&$_Mawgroupid)
  {
  	 $_Msqlstr="select groupID,awgroupID from termcoursestatus where courseID=$_Mcourseid and termID={$GLOBALS['_Mactivetermid']}";
  	 if($_Mresult=mysql_query($_Msqlstr))
  	 {
  	 	if(mysql_num_rows($_Mresult)>0)
  	 	{
  	 		$_Mrow=mysql_fetch_array($_Mresult,MYSQL_NUM);
  	 		//var_dump($_Mrow);
  	 		$_Mawgroupid=$_Mrow[1];
  	 		return $_Mrow[0];
  	 	}else return 0;
  	 }else return -1;
  }
  //will complete to change asgntype according to the previous state,linking assignments
  function _Fsetroom4teacher($_Masgnstr)//$_Mroomid,$_Mteacherid,$_Mgroupid,$roomcaption,$teachernamefamily,$_Mop)
  {
	$_Mout=1;
  	$_Mitemsar=explode("#",$_Masgnstr);
  	for($i=0 ; $i<(count($_Mitemsar)-1) ; $i++)
  	{
  		$_Mitems=explode("~",$_Mitemsar[$i]);
  		$_Mteacherid=$_Mitems[0];$_Mroomid=$_Mitems[1];$_Mgroupid=$_Mitems[2];$teachernamefamily=$_Mitems[3];$roomcaption=$_Mitems[4];$_Mop="I";
	  	if($_Mop=="I")
	  	{//check for existance
		  	$_Msqlstr="select * from asgncnsts where cnsttype='tr' and teacherID=$_Mteacherid and termID={$GLOBALS['_Mactivetermid']} and groupID=$_Mgroupid ";
		  	//echo $_Mgroupid.'---';
		  	if($_Mresult=mysql_query($_Msqlstr))
		  	{
		  		if(mysql_num_rows($_Mresult)>0)
		  		{
		  			$_Msqlstr="update asgncnsts set roomID='$_Mroomid',roomcaption='$roomcaption' where cnsttype='tr' and teacherID=$_Mteacherid and termID={$GLOBALS['_Mactivetermid']} and groupID=$_Mgroupid";
		  			if(!(mysql_query($_Msqlstr)))
		  				$_Mout=0;
		  		}
		  		else
		  		{
			 		$_Mcnsttype='tr';
			  		$_Msqlstr="insert into asgncnsts (cnsttype,teacherID,roomID,groupID,termID,teachernamefam,roomcaption) values ('$_Mcnsttype','$_Mteacherid','$_Mroomid','$_Mgroupid','{$GLOBALS['_Mactivetermid']}','$teachernamefamily','$roomcaption')";
			  		//echo $_Msqlstr;
			  		if(!($_Mresult=mysql_query($_Msqlstr)))
			  			$_Mout=0;
		  		}
	  		}
	  		else $_Mout=-1;//echo $_Msqlstr;
	  	}
  	}
  	//return $actionrep; can be optimised to reduce data transfer,not sending all the list,only changes
  	//return _Frepgroupteachersrooms($_Mgroupid);
  	return $_Mout;
  }
  function _Fsetroom4course($_Masgnstr)//$_Mroomid,$_Mcourseid,$_Mgroupid,$roomcaption,$coursecaption,$_Mop)
  {
	$_Mout=1;
  	$_Mitemsar=explode("#",$_Masgnstr);
  	for($i=0 ; $i<(count($_Mitemsar)-1) ; $i++)
  	{
  		$_Mitems=explode("~",$_Mitemsar[$i]);$_Mcourseroom=explode(" ==>",$_Mitems[4]);$coursecaption=$_Mcourseroom[0];
  		$_Mroomid=$_Mitems[0];$_Mcourseid=_Fgetcourseidfrom($_Mitems[1],$_Mcoursepart);$_Mgroupid=$_Mitems[2];$roomcaption=$_Mitems[3];$_Mop="I";
	  	if($_Mop=="I")
	  	{//check for existance
		  	$_Msqlstr="select * from asgncnsts where cnsttype='cr' and courseID=$_Mcourseid and coursepart like '$_Mcoursepart' and termID={$GLOBALS['_Mactivetermid']} and groupID=$_Mgroupid ";
		  	//echo $_Msqlstr;
		  	if($_Mresult=mysql_query($_Msqlstr))
		  	{
		  		if(mysql_num_rows($_Mresult)!=0)
		  		{
		  			$_Msqlstr="update asgncnsts set roomID='$_Mroomid',roomcaption='$roomcaption' where cnsttype='cr' and coursepart like '$_Mcoursepart' and courseID=$_Mcourseid and termID={$GLOBALS['_Mactivetermid']} and groupID=$_Mgroupid";
		  			if(!(mysql_query($_Msqlstr)))
		  				$_Mout=0;
		  		}
		  	  	else
		  		{
		  			$_Mcnsttype='cr';
			  		$_Mmgroupid=_Fgetmgroupid($_Mcourseid,$_Mawgroupid);
	  				$_Msqlstr="insert into asgncnsts (cnsttype,courseID,coursepart,roomID,groupID,maingroupID,awgroupID,termID,coursecaption,roomcaption) values ('$_Mcnsttype','$_Mcourseid','$_Mcoursepart','$_Mroomid','$_Mgroupid','$_Mmgroupid','$_Mawgroupid','{$GLOBALS['_Mactivetermid']}','$coursecaption','$roomcaption')";
	  				if(!($_Mresult=mysql_query($_Msqlstr)))
	  					$_Mout=0;
		  		}
	  		}
	  		else $_Mout=-1;
	  	}
  	}
  	//return $actionrep; can be optimised to reduce data transfer,not sending all the list,only changes
  	//echo "--$_Mgroupid--";
  	//return _Frepgroupcoursesrooms($_Mgroupid);
  	return $_Mout;
  	
  }
  function _Fsetslot4course($_Masgnstr)
  {//$_Masgnstr="1300!c1~4:s,5:s,8:e,9:e,~5~10??????? ????????? -1300";
  	$_Mout=1;if($_Masgnstr[strlen($_Masgnstr)-1]!='#') $_Masgnstr[strlen($_Masgnstr)-1]='#';
  	$_Mitemsar=explode("#",$_Masgnstr);
  	for($i=0 ; $i<(count($_Mitemsar)-1) ; $i++)
  	{
  		$_Mitems=explode("~",$_Mitemsar[$i]);$k=0;//will set to 0 after correction
  		$_Mcourseid=_Fgetcourseidfrom($_Mitems[$k],$_Mcoursepart);$_Mslots=$_Mitems[$k+1];$_Mgroupid=$_Mitems[$k+2];$coursecaption=$_Mitems[$k+3];$_Mop="I";
  		if($_Mslots[strlen($_Mslots)-1]==',') $_Mslots=substr($_Mslots,0,strlen($_Mslots)-1);
  		//**will remove
  		  $_Mslots1=$_Mslots;$_Mslots2="";
  		  $_Mslotsar=explode(",",$_Mslots);
  		  $_Mslotsc=array();$_Mslotsw=array();
	  	  for($j=0 ; $j<count($_Mslotsar) ; $j+=2 )
	  	  {
  		  		if($_Mslotsar[$j][2]=="s")
  		  			array_push($_Mslotsc,"{$_Mslotsar[$j]},{$_Mslotsar[$j+1]}");
  		  		else if($_Mslotsar[$j][2]=="e" || $_Mslotsar[$j][2]=="o")
  		  			array_push($_Mslotsw,"{$_Mslotsar[$j]},{$_Mslotsar[$j+1]}");
  		  		//else if($_Mslotsar[$j][3]=="c")920703
  		  		else if($_Mslotsar[$j][3]=="s")
  		  			array_push($_Mslotsc,"{$_Mslotsar[$j]},{$_Mslotsar[$j+1]}");
  		  		else if($_Mslotsar[$j][3]=="e" || $_Mslotsar[$j][3]=="o")
  		  			array_push($_Mslotsw,"{$_Mslotsar[$j]},{$_Mslotsar[$j+1]}");
  		  }
  		//**
	  	if($_Mop=="I")
	  	{//check for existance
	  		/*920703
			if($_Mcoursepart[0]=='c' || $_Mcoursepart[0]=='w')
		  		 $_Msqlstr="select * from asgncnsts where cnsttype='cs' and courseID=$_Mcourseid and coursepart not like '%a%' and termID={$GLOBALS['_Mactivetermid']} and groupID=$_Mgroupid ";
			else $_Msqlstr="select * from asgncnsts where cnsttype='cs' and courseID=$_Mcourseid and coursepart     like '%a%' and termID={$GLOBALS['_Mactivetermid']} and groupID=$_Mgroupid ";
		  	if($_Mresult=mysql_query($_Msqlstr))
		  	{
		  		if(mysql_num_rows($_Mresult)!=0)
		  		{
		  			if($_Mcoursepart[0]=='a')
		  			{
		  				for($j=0 ; $j<count($_Mslotsc) ;$j++ )
		  				{$_Msqlstr="update asgncnsts set slots='{$_Mslotsc[$j]}' where cnsttype='cs' and coursepart like 'a".($j+1)."' and courseID=$_Mcourseid and termID={$GLOBALS['_Mactivetermid']} and groupID=$_Mgroupid";mysql_query($_Msqlstr);}
		  			}
		  			else if($_Mcoursepart[0]=='c' || $_Mcoursepart[0]=='w')
		  			{
		  				for($j=0 ; $j<count($_Mslotsc) ;$j++ )
		  				{
		  					$_Msqlstr="update asgncnsts set slots='{$_Mslotsc[$j]}' where cnsttype='cs' and coursepart like 'c".($j+1)."' and courseID=$_Mcourseid and termID={$GLOBALS['_Mactivetermid']} and groupID=$_Mgroupid";mysql_query($_Msqlstr);
		  				}
		  				for($j=0 ; $j<count($_Mslotsw) ;$j++ )
		  				{
		  					$_Msqlstr="update asgncnsts set slots='{$_Mslotsw[$j]}' where cnsttype='cs' and coursepart like 'w".($j+1)."' and courseID=$_Mcourseid and termID={$GLOBALS['_Mactivetermid']} and groupID=$_Mgroupid";mysql_query($_Msqlstr);
		  				}
		  			}
		  			 
		  		}
		  		*/
	  		//920703
	  		if($_Mcoursepart[0]=='c' || $_Mcoursepart[0]=='w')
	  			$_Msqlstr="delete from asgncnsts where cnsttype='cs' and courseID=$_Mcourseid and coursepart not like '%a%' and termID={$GLOBALS['_Mactivetermid']} and groupID=$_Mgroupid ";
	  		else $_Msqlstr="delete from asgncnsts where cnsttype='cs' and courseID=$_Mcourseid and coursepart     like '%a%' and termID={$GLOBALS['_Mactivetermid']} and groupID=$_Mgroupid ";
	  		
	  		if($_Mresult=mysql_query($_Msqlstr))
		  	{
		  		$_Mcnsttype='cs';$_Mmgroupid=_Fgetmgroupid($_Mcourseid,$_Mawgroupid);
		  		if($_Mcoursepart[0]=='a')
		  		{
		  			for($j=0 ; $j<count($_Mslotsc) ;$j++ )
		  			{$_Msqlstr="insert into asgncnsts (cnsttype,courseID,coursepart,slots,groupID,maingroupID,awgroupID,termID,coursecaption) values ('$_Mcnsttype','$_Mcourseid','a".($j+1)."','{$_Mslotsc[$j]}','$_Mgroupid','$_Mmgroupid','$_Mawgroupid','{$GLOBALS['_Mactivetermid']}','$coursecaption')";mysql_query($_Msqlstr);}
		  		}
		  		else if($_Mcoursepart[0]=='c' || $_Mcoursepart[0]=='w')
		  		{
		  			for($j=0 ; $j<count($_Mslotsc) ;$j++ )
		  			{$_Msqlstr="insert into asgncnsts (cnsttype,courseID,coursepart,slots,groupID,maingroupID,awgroupID,termID,coursecaption) values ('$_Mcnsttype','$_Mcourseid','c".($j+1)."','{$_Mslotsc[$j]}','$_Mgroupid','$_Mmgroupid','$_Mawgroupid','{$GLOBALS['_Mactivetermid']}','$coursecaption')";mysql_query($_Msqlstr);}
		  			for($j=0 ; $j<count($_Mslotsw) ;$j++ )
		  			{$_Msqlstr="insert into asgncnsts (cnsttype,courseID,coursepart,slots,groupID,maingroupID,awgroupID,termID,coursecaption) values ('$_Mcnsttype','$_Mcourseid','w".($j+1)."','{$_Mslotsw[$j]}','$_Mgroupid','$_Mmgroupid','$_Mawgroupid','{$GLOBALS['_Mactivetermid']}','$coursecaption')";mysql_query($_Msqlstr);}		  			
		  		}
		  	}else $_Mout=-1;
	  	}
  	}
  	return $_Mout;
  }
  
  function _Fsetroom4group($_Masgnstr)//#$_Mgroupid~$buildingid~$_Mroomid~~$roomcaption)
  {
	$_Mout=1;
  	$_Mitemsar=explode("#",$_Masgnstr);$_Mroomidstr='';$_Mroomcaptionstr='';
  	for($i=0 ; $i<(count($_Mitemsar)-1) ; $i++)
  	{
	  	$_Mitems=explode("~",$_Mitemsar[$i]);
		$_Mroomidstr.=$_Mitems[2].',';
		$_Mroomcaptionstr.=$_Mitems[3].',';
		if(!(_Fgrouphasroom($_Mitems[2],$_Mitems[0])))
		{
			$_Msqlstr="select * from rooms where roomID={$_Mitems[2]}";
			if($_Mresult=mysql_query($_Msqlstr))
			{
				if(mysql_num_rows($_Mresult)>0)
				{
					$_Mrow=mysql_fetch_array($_Mresult);
					_Faddroom4group($_Mitems[0],$_Mrow);
				}
			}
		}
  	}
  	$_Mroomidstr=substr($_Mroomidstr,0,strlen($_Mroomidstr)-1);
  	$_Mroomcaptionstr=substr($_Mroomcaptionstr,0,strlen($_Mroomcaptionstr)-1);
  	$_Mgroupid=$_Mitems[0];$buildingid=$_Mitems[1];
  	$_Mop="I";
  	//for($i=0 ; $i<(count($_Mitemsar)-1) ; $i++)
  	{
  		
	  	if($_Mop=="I")
	  	{//check for existance
		  	$_Msqlstr="select cnstID from asgncnsts where cnsttype='gr' and groupID=$_Mgroupid and termID={$GLOBALS['_Mactivetermid']} ";
		  	if($_Mresult=mysql_query($_Msqlstr))
		  	{
		  		if(mysql_num_rows($_Mresult)!=0)
		  		{
		  			$_Mrow=mysql_fetch_array($_Mresult);
		  			$_Msqlstr="update asgncnsts set roomIDs='$_Mroomidstr',roomcaption='$_Mroomcaptionstr' where cnstID={$_Mrow['cnstID']}";
		  			if(!(mysql_query($_Msqlstr)))
		  				$_Mout=0;
		  		}
		  	  	else
		  		{
		  			$_Mcnsttype='gr';
	  				$_Msqlstr="insert into asgncnsts (cnsttype,roomIDs,roomcaptions,groupID,termID) values ('$_Mcnsttype','$_Mroomidstr','$_Mroomcaptionstr','$_Mgroupid','{$GLOBALS['_Mactivetermid']}')";
	  				if(!($_Mresult=mysql_query($_Msqlstr)))
	  					$_Mout=0;
		  		}
	  		}
	  		else $_Mout=-1;
	  	}
  	}
  	return $_Mout;
  }
  function _Fgrouphasroom($_Mroomid,$_Mgroupid)
  {
  	$_Msqlstr="select roomID from roomstatus where roomID=$_Mroomid and roomgroupid=$_Mgroupid and termID={$GLOBALS['_Mactivetermid']}";
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
  		if(mysql_num_rows($_Mresult)>0)
  			return 1;
  		else return 0;	
  	}else return -1;
  }
  function _Faddroom4group($_Mgroupid,$rrow)
  {
	  $_Msqlstr="insert into roomstatus(roomID,termID,roomtype,roomcaption,roomgroupid,capacity,equipments)values
	  		  ('{$rrow['roomID']}','{$GLOBALS['_Mactivetermid']}','{$rrow['roomtype']}','{$rrow['roomcaption']}','$_Mgroupid','{$rrow['capacity']}','{$rrow['equipments']}')";
  	  //echo $_Msqlstr;
	  if(mysql_query($_Msqlstr)) return 1;
  	  return 0;
  }
  
  //**************remove future 
?>