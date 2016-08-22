<?php
//******************** full manual
function _Fgetlowerparts($_Mbuildingid)
{
	$_Mbqeueu=array();$_Mbqeueu[0]=$_Mbuildingid;$_Mfinalrooms=array();
	$_Msqlstr="select buildingID,buildingcaption,subgroups,finalpart,'1' as flag from buildings";//and groupID in(select groupID from groups where finalgroup=1)";
	//echo $_Msqlstr."----------";
	if($_Mresult=mysql_query($_Msqlstr))
	{
		$_Mbcount=mysql_num_rows($_Mresult);
		if($_Mbcount>0)
		{
			while($_Mrow=mysql_fetch_array($_Mresult,MYSQL_ASSOC))
				$_Mbarray[$_Mrow['buildingID']]=$_Mrow;
			mysql_free_result($_Mresult);
		}else return false;
	}else return false;
		
	for($i=0 ; $i < count($_Mbqeueu) ; $i++)
	{
		if($_Mbarray[$_Mbqeueu[$i]]["finalpart"]==1)
		{
			array_push($_Mfinalrooms,$_Mbarray[$_Mbqeueu[$i]]['buildingID']);
		}
		if(($_Mbarray[$_Mbqeueu[$i]]["finalpart"]!=1))
		{
			if(($_Mbarray[$_Mbqeueu[$i]]["subgroups"]!=0)&($_Mbarray[$_Mbqeueu[$i]]["subgroups"]!='')&($_Mbarray[$_Mbqeueu[$i]]["subgroups"]!=' '))
			{
				$_Msubs=explode(",",$_Mbarray[$_Mbqeueu[$i]]["subgroups"]);
				for($j=0 ; $j < count($_Msubs) ; $j++)
					array_push($_Mbqeueu,$_Msubs[$j]);
			}
		}
		//array_shift($_Mnewga);
	}
	$_Mbuildingids="";
 	for( ; count($_Mfinalrooms)>0;)
	{
		$_Mrow=array_pop($_Mfinalrooms);
		$_Mbuildingids.=$_Mrow["buildingID"].',';
	}
	$_Mbuildingids=substr($_Mbuildingids,0,strlen($_Mbuildingids)-1);
	return $_Mbuildingids;
	
}
function _Fgetgroupcaption($_Mgid)
{
	$_Msqlstr="select groupcaption from groupstatus where groupID=$_Mgid";
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
  		if(mysql_num_rows($_Mresult)>0)
  		{
  			$_Mrow=mysql_fetch_assoc($_Mresult);
  			return $_Mrow['groupcaption'];
  		}else return 0;
  	}else return -1;
	
}
function _Frepgtcandschedules($_Mgroups,$_Mgroups1)
  {
	
	$_Ms=_Fgetgroupnotscheduledcourses($_Mgroups);
	$_Ms.='|';
	$_Ms.=_Frepgroupteachers($_Mgroups1,'');
	$_Ms.="^^^^"._Fgetscheduledcourses($_Mgroups);
	return $_Ms;
  }
function _Fgetscheduledcourses($_Mgroups)
  {
  	$_Moutstr='';
  	$_Msqlstr="select courseID,coursecaption,timeslots,teacherID,teachername,teacherfamily,teacherID2,teachername2,teacherfamily2,groupID,groupcaption,roomID,roomcaption,asgnState,coursepart,cachast,courseparttchrs from assignments where termID={$GLOBALS['_Mactivetermid']} and ( (groupID in ($_Mgroups)) OR (awgroupID in ($_Mgroups)) )";
  	//echo $_Msqlstr;
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
  		if(mysql_num_rows($_Mresult)>0)
  		{
  			while($_Mrow=mysql_fetch_assoc($_Mresult))
  			{
   				$_Moutstr.="#{$_Mrow['groupID']}~{$_Mrow['groupcaption']}~{$_Mrow['timeslots']}~{$_Mrow['courseID']}~{$_Mrow['coursecaption']}~{$_Mrow['teacherID']}~{$_Mrow['teachername']} {$_Mrow['teacherfamily']}~{$_Mrow['roomID']}~{$_Mrow['roomcaption']}~{$_Mrow['coursepart']}~{$_Mrow['cachast']}~{$_Mrow['courseparttchrs']}~{$_Mrow['teacherID2']}~{$_Mrow['teachername2']} {$_Mrow['teacherfamily2']}";
  				
				//$_Moutstr.="~A*{$_Mrow['courseAunits']}~T*{$_Mrow['courseTunits']}";
  			}
  			mysql_free_result($_Mresult); 	
  			return $_Moutstr;
  		}else return 0;
  	}else return -1;
  }
//*********************
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
function _Fgetbgtree()
{
	$_Moutstr=_Fgettree(0);
	$_Moutstr.='|';
	$_Moutstr.=_Fgetbtree();
	return $_Moutstr;
}
function _Fgettree($f)
{
  	$_Moutstr='';
  	$_Msqlstr="select groupID,groupcaption,subgroupof from groupstatus ";
  	if($f==0) $_Msqlstr.=" where finalgroup<>1 ";
  	$_Msqlstr.=" order by subgroupof";
  	//echo $_Msqlstr;
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
  		if(mysql_num_rows($_Mresult)>0)
  		{
  			while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  			$_Moutstr.="#{$_Mrow['groupID']}~{$_Mrow['groupcaption']}~{$_Mrow['subgroupof']}";
  			mysql_freeresult($_Mresult); 	
  			return $_Moutstr;
  		}else return 0;
  	}else return -1;

}
function _Fgetstree($_Mgroups,$_MFullsubsw)
{
  	$_Moutstr='';
  	$_Msqlstr="select groupID,groupcaption,subgroupof,groupprior from groupstatus where (((groupID in($_Mgroups)) OR (subgroupof in($_Mgroups)))";
  	 if($_MFullsubsw!='fullsubs') $_Msqlstr.="AND (finalgroup=1)) ";
  	 else $_Msqlstr.=") ";
  	$_Msqlstr.="order by subgroupof";
  	//echo $_Msqlstr;
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
  		if(mysql_num_rows($_Mresult)>0)
  		{
  			if($_MFullsubsw!='fullsubs')
  			{
  				while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  				$_Moutstr.="#{$_Mrow['groupID']}~{$_Mrow['groupcaption']}@@@@1@@@@{$_Mrow['groupprior']}~{$_Mrow['subgroupof']}";
  			}else
  			{
  				while($_Mrow=mysql_fetch_assoc($_Mresult))
  					$_Moutstr.="#{$_Mrow['groupID']}~{$_Mrow['groupcaption']}~{$_Mrow['subgroupof']}";
  			}
  			mysql_freeresult($_Mresult); 	
  			return $_Moutstr;
  		}else return 0;
  	}else return -1;
}

function _Fgetbtree()
{
  	$_Moutstr='';
  	$_Msqlstr="select buildingID,buildingcaption,subgroupof from buildings order by subgroupof";
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
  		if(mysql_num_rows($_Mresult)>0)
  		{
  			while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  			$_Moutstr.="#{$_Mrow['buildingID']}~{$_Mrow['buildingcaption']}~{$_Mrow['subgroupof']}";
  			mysql_free_result($_Mresult); 	
  			return $_Moutstr;
  		}else return 0;
  	}else return -1;

}
  //***************************
  function _Frepteachersprefcourses($_Mteacherid)
  {
  	$_Msqlstr="select teachercourseids,teachercoursepriors,courseparts,teacherno from tchrtimes where termID={$GLOBALS['_Mactivetermid']} and teacherID=$_Mteacherid";// and teachergroupid in ({$GLOBALS['_Mgroups']})";
  	//echo $_Msqlstr;
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
  		if(mysql_num_rows($_Mresult)>0)
  		{	
  			if($_Mrow=mysql_fetch_array($_Mresult))
  			if($_Mrow[0]!='0')
  			{
  				$courses=explode(",",$_Mrow[0]);$coursesprefs=explode(",",$_Mrow[1]);$_Mcourseparts=explode(",",$_Mrow[2]);$_Mteacherno=explode(",",$_Mrow[3]);
  				$_Moutstr="";
  				for($i=0 ; $i<count($courses) ; $i++)
					$_Moutstr.="$courses[$i]!{$_Mcourseparts[$i]}::$coursesprefs[$i]::$_Mteacherno[$i],";
  				$_Moutstr=substr($_Moutstr,0,strlen($_Moutstr)-1);	
  				mysql_free_result($_Mresult);
  				return $_Moutstr;
  			}else return 0;
  		}else return 0;
  	}else return -1;
  }
  //**************************************
  function _Frepgroupteachersprefcourses($_Mgroupid)
  {
  	$_Mallsubs=_Fgetallsubgroups($_Mgroupid);
  	$_Msqlstr="select teachercourseids,teachercoursepriors,courseparts,teacherno,teachername,teacherfamily from tchrtimes where termID={$GLOBALS['_Mactivetermid']} and teachergroupid in($_Mallsubs)";// and teachergroupid in ({$GLOBALS['_Mgroups']})";
  	//echo $_Msqlstr;
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
  		if(mysql_num_rows($_Mresult)>0)
  		{	
  			$_Moutstr='';
  			while($_Mrow=mysql_fetch_array($_Mresult))
  			{
  				$courses=explode(",",$_Mrow[0]);$coursesprefs=explode(",",$_Mrow[1]);$_Mcourseparts=explode(",",$_Mrow[2]);$_Mteacherno=explode(",",$_Mrow[3]);
  				$_Moutstr.="#{$_Mrow[4]} {$_Mrow[5]} ~ ";
  				for($i=0 ; $i<count($courses) ; $i++)
  				{
  					$_Msqlstr="select coursecaption from termcoursestatus where termID={$GLOBALS['_Mactivetermid']} and coursecode={$courses[$i]} and coursepart like '{$_Mcourseparts[$i]}'";// and teachergroupid in ({$GLOBALS['_Mgroups']})";
  					if($_Mresult2=mysql_query($_Msqlstr))
  					{
  						if(mysql_num_rows($_Mresult2)>0)
  						{	
  							if($_Mrow2=mysql_fetch_array($_Mresult2))
								$_Moutstr.="{$_Mrow2['coursecaption']}^{$_Mcourseparts[$i]}^$coursesprefs[$i]~";
  						}
  					}
					
  				}	
  			}
  			//$_Moutstr=substr($_Moutstr,0,strlen($_Moutstr)-1);	
  			mysql_free_result($_Mresult);
  			return $_Moutstr;

  		}else return 0;
  	}else return -1;
  	
  	
  	
  }
  //****************************************
  function _Fgetgroupinitialtimes($_Mgroupid)
  {
  	$_Msqlstr="select initialgroupstatus from groupstatus where termID={$GLOBALS['_Mactivetermid']} and groupID={$_Mgroupid}";
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
  		if(mysql_num_rows($_Mresult)!=0)
  		{
	 		$_Mrow=mysql_fetch_array($_Mresult);
  			$_Mtimes=$_Mrow[0];
  			mysql_free_result($_Mresult);
  			return $_Mtimes;
  		}else return 0;
  	}else return -1;
}	

  
  function _Fgetgrouprooms($_Mgroups)
  {
  	$_Msqlstr="select DISTINCT roomID,roomcaption from roomstatus where termID={$GLOBALS['_Mactivetermid']} and roomgroupid in ($_Mgroups) order by roomcaption";
  	$_Moutstr='';
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
	  	if(mysql_num_rows($_Mresult)>0)
	  	{
	  		$_Mbid=0;
		  	while($_Mrow=mysql_fetch_assoc($_Mresult))
		  	{
		  		//if($_Mbid!=$_Mrow['buildingID'])
		  		//$_Moutstr.="@{$_Mrow['buildingcaption']}";
		  		$_Moutstr.='#'.$_Mrow['roomID'].'~'.$_Mrow['roomcaption'];
		  		//$_Mbid=$_Mrow['buildingID'];
		  	}
	  	    mysql_free_result($_Mresult);
	  	}else return 0;
  	}else return -1;
  	return $_Moutstr;
  	
  }
  
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

  function _Fgetgroupnotscheduledcourses($_Mgroups)
  {
  	$_Moutstr='';
  	//$_Msqlstr="select courseID,coursecaption,courseAunits,courseTunits,coursepart,groupidconf1,groupidconf2,group1caption from termcoursestatus where termID={$GLOBALS['_Mactivetermid']} and ((groupidconf1 in ($_Mgroups)) or (groupidconf2 in ($_Mgroups))) and courseID not in (select courseID from assignments where ((groupID in ($_Mgroups))or(awgroupID in ($_Mgroups))) and termID={$GLOBALS['_Mactivetermid']} group by courseID) order by groupidconf1,courseID,coursepart";
  	$_Msqlstr="select courseID,coursecaption,courseAunits,courseTunits,courseAunitstchrs,courseTunitstchrs,coursepart,awgroupID,groupidconf1,groupidconf2,group1caption from termcoursestatus where termID={$GLOBALS['_Mactivetermid']} and ((groupidconf1 in ($_Mgroups) and awgroupID=0) or (awgroupID in ($_Mgroups))) order by groupidconf1,courseID,coursepart";
  	//echo $_Msqlstr;
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
  		if(mysql_num_rows($_Mresult)>0)
  		{
  			while($_Mrow=mysql_fetch_assoc($_Mresult))
  			{

				$_Msqlstr="select courseID from assignments where termID={$GLOBALS['_Mactivetermid']} and courseID={$_Mrow['courseID']} and coursepart like '{$_Mrow['coursepart']}'";
				if($_Mresult1=mysql_query($_Msqlstr))
				{
					if (mysql_num_rows($_Mresult1)>0) continue;
				}else return -1;
  				//will check
  				//if(!(array_search()))
  				$_Mtchrsno=0;
  				if($_Mrow['coursepart'][0]=='a') $_Mtchrsno=$_Mrow['courseAunitstchrs'];
  				else $_Mtchrsno=$_Mrow['courseTunitstchrs'];
  				$_Moutstr.="#{$_Mrow['courseID']}~";
  				if(($_Mrow['awgroupID']!=0)&(_Fisnotinthisbranch($_Mgroups,$_Mrow['groupidconf1']))) $_Moutstr.="{$_Mrow['group1caption']} - ";
  				$_Moutstr.="{$_Mrow['coursecaption']}~{$_Mrow['coursepart']}~A*{$_Mrow['courseAunits']}~T*{$_Mrow['courseTunits']}~{$_Mrow['groupidconf1']}~$_Mtchrsno";
  			}
  			mysql_free_result($_Mresult); 	
  			return $_Moutstr;
  		}else return 0;
  	}else return -1;
  }

  function _Fisnotinthisbranch($_Mgroups,$_Mthisgrpoupid)
  {
  	$_Mgroupsar=explode(",",$_Mgroups);
  	for($i=0 ; $i < count($_Mgroupsar) ; $i++)
  	{
  		if($_Mgroupsar[$i]==$_Mthisgrpoupid) return false;
  	}
  	return true;
  } 
  function _Fgetgroupcourses($_Mextrafields)
  {
  	$_Moutstr='';
  	$_Msqlstr="select courseID,coursecaption,courseAunits,courseTunits,coursepart from termcoursestatus where termID={$GLOBALS['_Mactivetermid']} and groupidconf1 in ({$GLOBALS['_Mgroups']}) order by coursecaption";
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
  		if(mysql_num_rows($_Mresult)>0)
  		{
  			while($_Mrow=mysql_fetch_assoc($_Mresult))
  			{
  				
  				$_Moutstr.="#{$_Mrow['courseID']}~{$_Mrow['coursecaption']}";
   				if($_Mextrafields=="teachers")
  				{
  					$tn=_Fgetcourseteachercnst($_Mrow['courseID'],$_Mrow['coursepart']);
  					if(($tn!='-1') & ($tn!='0'))
						$_Moutstr.=" ==> $tn";
  				}else
  				if($_Mextrafields=="rooms")
  				{
  					$tn=_Fgetcourseroomcnst($_Mrow['courseID'],'');
  					if(($tn!='-1') & ($tn!='0'))
						$_Moutstr.=" ==> $tn";
  				}
  				
				$_Moutstr.="~A*{$_Mrow['courseAunits']}~T*{$_Mrow['courseTunits']}";
  			}
  			mysql_free_result($_Mresult); 	
  			return $_Moutstr;
  		}else return 0;
  	}else return -1;
  }
  
  function _Fgetbuildingrooms($_Mbuildingid)
  {
  	$_Moutstr='';
  	$_Msqlstr="select roomID,roomcaption from rooms where termID={$GLOBALS['_Mactivetermid']} and subgroupof in ($_Mbuildingid) order by roomcaption";
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
	  	if(mysql_num_rows($_Mresult)>0)
	  	{	
  			while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  			$_Moutstr.='#'.$_Mrow['roomID'].'~'.$_Mrow['roomcaption'];
  			mysql_free_result($_Mresult);
  	  		return $_Moutstr;
	  	}else return 0;
  	}else return -1;
  }
  
  function _Fgetbuildings()
  {
  	$_Moutstr='';
  	$_Msqlstr="select buildingID,buildingcaption from buildings order by buildingID";
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
	  	if(mysql_num_rows($_Mresult)>0)
	  	{	
  			while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  			$_Moutstr.='#'.$_Mrow['buildingID'].'~'.$_Mrow['buildingcaption'];
  			mysql_free_result($_Mresult); 	
  			return $_Moutstr;
	  	}else return 0;
  	}else return -1;
  }
  
  function _Fgetgroupslist()
  {
  	$_Moutstr='';
  	$_Msqlstr="select groupID,groupcaption,groupcollege from groups where finalgroup=1 order by groupcollege,subgroupof,groupcaption";
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
	  	if(mysql_num_rows($_Mresult)>0)
	  	{	
  			while($_Mrow=mysql_fetch_assoc($_Mresult))
  	  			$_Moutstr.='#'.$_Mrow['groupID'].'~'.$_Mrow['groupcollege'].' - '.$_Mrow['groupcaption'];
  			mysql_free_result($_Mresult); 	
  			return $_Moutstr;
	  	}else return 0;
  	}else return -1;
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

function _Frepgroupteachers($_Mgroups,$_Mextrafields)
{
	$_Moutstr="";
	$_Msqlstr="select teacherID,teachername,teacherfamily,teachergroupid,teacherPrior from tchrtimes where termID={$GLOBALS['_Mactivetermid']} and teachergroupid in($_Mgroups) order by teachergroupid";
	//echo $sqlstr;
	if($_Mextrafields=='prior') $_Msqlstr.=",teacherPrior";
	if($groupteachers=mysql_query($_Msqlstr))
	{
		if(mysql_num_rows($groupteachers)>0)
		{
			while($_Mrow=mysql_fetch_assoc($groupteachers))
			{
				$_Moutstr.="#{$_Mrow['teacherID']}~{$_Mrow['teachername']} {$_Mrow['teacherfamily']}";
  				if($_Mextrafields=='rooms')
  				{
  					$tn=_Fgetteacherroomcnst($_Mrow['teacherID']);
  					if(($tn!='-1') & ($tn!='0'))
						$_Moutstr.=" ==> $tn";
  				}
				/*
  				if($_Mextrafields=='prior')
  				{$_Moutstr.=" ==> $tn";}
  				*/
  				$_Moutstr.="@@@@{$_Mrow['teachergroupid']}";
  				if($_Mextrafields=='prior')
  				{
						$_Moutstr.="@@@@{$_Mrow['teacherPrior']}";
  				}
  				
			}	
			mysql_free_result($groupteachers);
			return $_Moutstr;			
		}else return 0;
	}else return -1;//$_Msqlstr;
}
function _Fgetgroupmasters($_Mgroupid)
{
	$_Msqlstr="select * from gms where groupID=$_Mgroupid and termID={$GLOBALS['_Mactivetermid']}";
	//echo "--$_Msqlstr--";
	if($_Mresult=mysql_query($_Msqlstr))
	{
		if(mysql_num_rows($_Mresult)>0)
		{
			$_Mrow=mysql_fetch_array($_Mresult);
			return $_Mrow['teacherID'];
		}return 0;
	}else return -1;
}
function _Fgetmastergroup($_Mgroupid,$_Mactivetermid)
{
	$_Moutstr="";
	$_Msqlstr="select mastergroup from groupstatus where groupID=$_Mgroupid and termID=$_Mactivetermid";
	if($_Mresult=mysql_query($_Msqlstr))
	{
		if(mysql_num_rows($_Mresult)>0)
		{
			if($_Mrow=mysql_fetch_assoc($_Mresult))
				$_Moutstr=$_Mrow['mastergroup'];
			mysql_free_result($_Mresult);
			if($_Moutstr<=0) return $_Mgroupid;
			else return $_Moutstr;
		}else return 0;
	}else return -1;
}
//************** report constraints**************
function _Fgetcourseteachercnst($_Mcourseid,$_Mcoursepart)
{
	$_Moutstr="";
	$_Msqlstr="select teachernamefam,teachernamefam2 from asgncnsts where cnsttype like '%c%' and cnsttype like '%t%' and coursepart like '$_Mcoursepart' and courseID={$_Mcourseid} and termID={$GLOBALS['_Mactivetermid']} and groupID in ({$GLOBALS['_Mgroups']})";
	if($groupteachersroomcnst=mysql_query($_Msqlstr))
	{
		if(mysql_num_rows($groupteachersroomcnst)>0)
		{
			if($_Mrow=mysql_fetch_assoc($groupteachersroomcnst))
				$_Moutstr=$_Mrow['teachernamefam'].'::'.$_Mrow['teachernamefam2'];
			mysql_free_result($groupteachersroomcnst);
			return $_Moutstr;
		}else return 0;
	}else return -1;
}

function _Fgetcourseroomcnst($_Mcourseid,$_Mcoursepart)
{
	$_Moutstr="";
	$_Msqlstr="select roomcaption from asgncnsts where cnsttype='cr' and courseID={$_Mcourseid} and coursepart like '$_Mcoursepart' and termID={$GLOBALS['_Mactivetermid']} and groupID in ({$GLOBALS['_Mgroups']})";
	if($groupcoursesroomcnst=mysql_query($_Msqlstr))
	{
		if(mysql_num_rows($groupcoursesroomcnst)>0)
		{
			if($_Mrow=mysql_fetch_assoc($groupcoursesroomcnst))
				$_Moutstr=$_Mrow['roomcaption'];
			mysql_free_result($groupcoursesroomcnst);
			return $_Moutstr;
		}else return 0;
	}else return -1;
}

function _Fgetteacherroomcnst($_Mteacherid)
{
	$_Moutstr="";
	$_Msqlstr="select roomcaption from asgncnsts where ((cnsttype='tr') and (teacherID={$_Mteacherid}) and (groupID in ({$GLOBALS['_Mgroups']})) and (termID={$GLOBALS['_Mactivetermid']}))";
	//echo $_Msqlstr;
	if($groupteachersroomcnst=mysql_query($_Msqlstr))
	{
		if(mysql_num_rows($groupteachersroomcnst)>0)
		{
			if($_Mrow=mysql_fetch_assoc($groupteachersroomcnst))
				$_Moutstr=$_Mrow['roomcaption'];
			mysql_free_result($groupteachersroomcnst);
			return $_Moutstr;
		}else return 0;
	}else return -1;
}

//***********************
function repteacherroomcnst()
{
	$_Moutstr="";
	$_Msqlstr="select roomID,roomcaption from asgncnsts where ((cnsttype='tr') and (teacherID={$_Mteacherid}) and (groupID in ({$GLOBALS['_Mgroups']}) and (termID={$GLOBALS['_Mactivetermid']}))";
	if($groupteachersroomcnst=mysql_query($_Msqlstr))
	{
		if(mysql_num_rows($groupteachersroomcnst)>0)
		{
			if($_Mrow=mysql_fetch_assoc($groupteachersroomcnst))
				$_Moutstr=$_Mrow['roomID'].'~'.$_Mrow['roomcaption'];
			mysql_free_result($groupteachers);
			return $_Moutstr;
		}else return 0;
	}else return -1;
}
function _Frepcourseslotcnst($_Mcourseid,$_Mcoursepart,$_Mgroups)
{
	$_Moutstr="";
	if($_Mcoursepart[0]=='a')
		$_Msqlstr="select slots from asgncnsts where cnsttype='cs' and coursepart like '%a%' and groupID in ($_Mgroups) and courseID={$_Mcourseid} and termID={$GLOBALS['_Mactivetermid']}";
	else $_Msqlstr="select slots from asgncnsts where cnsttype='cs' and coursepart not like '%a%' and groupID in ($_Mgroups) and courseID={$_Mcourseid} and termID={$GLOBALS['_Mactivetermid']}";
	//echo $_Msqlstr;
	if($groupcoursestimecnst=mysql_query($_Msqlstr))
	{
		if(mysql_num_rows($groupcoursestimecnst)>0)
		{
			while($_Mrow=mysql_fetch_assoc($groupcoursestimecnst))
				$_Moutstr.=$_Mrow['slots'].',';
			mysql_free_result($groupcoursestimecnst);
			$_Moutstr=substr($_Moutstr,0,strlen($_Moutstr)-1);
			return $_Moutstr;
		}else return 0;
	}else return -1;
}
  function _Fgetgroupcourses4asgn($_Mgroups,$_Mextrafields)
  {
  	$_Moutstr='';//                change to coursecaption
  	$_Msqlstr="select courseID,groupidconf1,coursecaption,courseAunits,courseTunits,courseAunitstchrs,courseTunitstchrs,coursepart,groupidconf2,groupidconf3,group1caption,coursep from termcoursestatus where termID={$GLOBALS['_Mactivetermid']} and ( (groupidconf1 in ($_Mgroups) AND awgroupID=0) OR (awgroupID in ($_Mgroups))) order by groupidconf1,coursecaption";
  	//echo $_Msqlstr;
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
  		if(mysql_num_rows($_Mresult)>0)
  		{
  			while($_Mrow=mysql_fetch_assoc($_Mresult))
  			{
  				if(($_Mrow['coursepart']=='c2')||($_Mrow['coursepart']=='c3')||($_Mrow['coursepart']=='w2')||($_Mrow['coursepart']=='a2')||($_Mrow['coursepart']=='a3')) continue;
  				if(($_Mrow['coursepart']=='w1')&($_Mrow['courseTunits']>1)) continue;
  				
  				$_Moutstr.="#{$_Mrow['courseID']}~";
  				//if($_Mrow['groupidconf2']!=0) $_Moutstr.="{$_Mrow['group1caption']} - ";
  				$_Moutstr.="{$_Mrow['coursecaption']}";
  				
   				if($_Mextrafields=="teachers")
  				{
  					$tn=_Fgetcourseteachercnst($_Mrow['courseID'],$_Mrow['coursepart']);
  					if(($tn!='-1') & ($tn!='0'))
						$_Moutstr.=" ==> $tn";
  				}else
  				if($_Mextrafields=="rooms")
  				{
  					$tn=_Fgetcourseroomcnst($_Mrow['courseID'],$_Mrow['coursepart']);
  					if(($tn!='-1') & ($tn!='0'))
						$_Moutstr.=" ==> $tn";
  				}
				$_Moutstr.="@@@@{$_Mrow['groupidconf1']}";//920617
  				$_Moutstr.="@@@@{$_Mrow['coursep']}~{$_Mrow['coursepart']}";
  				if($_Mextrafields=="groupcaption")
  				{
  					$tn=_Fgetcoursegroupcaption($_Mrow['groupidconf1'],$_Mrow['groupidconf2'],$_Mrow['groupidconf3']);
  					if(($tn!='-1') & ($tn!='0'))
						$_Moutstr.="~$tn";
  				}
				$_Mtchrsno=0;
  				if($_Mrow['coursepart'][0]=='a') $tu=0;
  				else {$tu=$_Mrow['courseTunits'];$_Mtchrsno=$_Mrow['courseTunitstchrs'];}
  				if($_Mrow['coursepart'][0]!='a') $au=0;
  				else {$au=$_Mrow['courseAunits'];$_Mtchrsno=$_Mrow['courseAunitstchrs'];}
				$_Moutstr.="~A*$au~T*$tu~";
				//if($_Mrow['groupidconf2']!=0) $_Moutstr.="{$_Mrow['groupidconf2']}~{$_Mrow['group1caption']}";
				$_Moutstr.="{$_Mrow['groupidconf1']}~$_Mtchrsno";
  			}
  			mysql_free_result($_Mresult); 	
  			return $_Moutstr;
  		}else return 0;
  	}else return -1;
  }
  function _Fgetgroupcourses4teach($_Mgroups,$_Mextrafields)
  {
  	$_Moutstr='';
  	$_Msqlstr="select distinct coursecode,coursecaption,coursepart,courseTunits,courseAunits from termcoursestatus where termID={$GLOBALS['_Mactivetermid']} and ( (groupidconf1 in ($_Mgroups) AND awgroupID=0) OR (awgroupID in ($_Mgroups))) order by coursecaption";
  	//echo $_Msqlstr;
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
  		if(mysql_num_rows($_Mresult)>0)
  		{
  			while($_Mrow=mysql_fetch_assoc($_Mresult))
  			{
				if(($_Mrow['coursepart']!='c1')AND($_Mrow['coursepart']!='a1')AND(!(($_Mrow['coursepart']=='w1')AND($_Mrow['courseTunits']==1)))) continue;
				//if(($_Mrow['coursepart']=='w1')AND($_Mrow['courseTunits']==1)) continue;
  				$_Moutstr.="#{$_Mrow['coursecode']}~";
  				//if($_Mrow['groupidconf2']!=0) $_Moutstr.="{$_Mrow['group1caption']} - ";
  				$_Moutstr.=substr($_Mrow['coursecaption'],0,50)." ~{$_Mrow['coursepart']}";
  			}
  			mysql_free_result($_Mresult); 	
  			return $_Moutstr;
  		}else return 0;
  	}else return -1;
  }
  
  
  //will be optimised,this is the abov function by "WHERE" clouse changed
  function _Fgetallgroupcourses4asgn($_Mgroups,$_Mextrafields)
  {
  	$_Moutstr='';
  	$_Msqlstr="select courseID,groupidconf1,coursecaption,courseAunits,courseTunits,coursepart,groupidconf2,groupidconf3,awgroupID,group1caption from termcoursestatus where termID={$GLOBALS['_Mactivetermid']} and (groupidconf1 in ($_Mgroups)) order by groupidconf1,coursecaption";
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
  		if(mysql_num_rows($_Mresult)>0)
  		{
  			while($_Mrow=mysql_fetch_assoc($_Mresult))
  			{
  				if(($_Mrow['coursepart']=='c2')||($_Mrow['coursepart']=='c3')||($_Mrow['coursepart']=='w1')||($_Mrow['coursepart']=='w2')||($_Mrow['coursepart']=='a2')||($_Mrow['coursepart']=='a3')) continue;
  				
  				$_Moutstr.="#{$_Mrow['courseID']}~";
  				//if($_Mrow['groupidconf2']!=0) $_Moutstr.="{$_Mrow['group1caption']} - ";
  				$_Moutstr.="{$_Mrow['coursecaption']}";
  				
   				if($_Mextrafields=="teachers")
  				{
  					$tn=_Fgetcourseteachercnst($_Mrow['courseID'],$_Mrow['coursepart']);
  					if(($tn!='-1') & ($tn!='0'))
						$_Moutstr.=" ==> $tn";
  				}else
  				if($_Mextrafields=="rooms")
  				{
  					$tn=_Fgetcourseroomcnst($_Mrow['courseID'],$_Mrow['coursepart']);
  					if(($tn!='-1') & ($tn!='0'))
						$_Moutstr.=" ==> $tn";
  				}

  				
  				$_Moutstr.="@@@@{$_Mrow['groupidconf1']}~{$_Mrow['coursepart']}";
  				if($_Mextrafields=="groupcaption")
  				{
  					$tn=_Fgetcoursegroupcaption($_Mrow['groupidconf1'],$_Mrow['groupidconf2'],$_Mrow['groupidconf3']);
  					if(($tn!='-1') & ($tn!='0'))
						$_Moutstr.="~$tn";
  				}
  				if($_Mextrafields=="awgcaption")
  				{
  					if($_Mrow['awgroupID']!=0)
  					{
  						$_Mgroupcaption=_Fgetgroupcaption($_Mrow['awgroupID']);
  						$_Moutstr.="~$_Mgroupcaption";
  					}else $_Moutstr.="~0";
  				}
  				 				
  				if($_Mrow['coursepart'][0]=='a') $tu=0;
  				else $tu=$_Mrow['courseTunits'];
  				if($_Mrow['coursepart'][0]!='a') $au=0;
  				else $au=$_Mrow['courseAunits'];
				$_Moutstr.="~A*$au~T*$tu~";
				//if($_Mrow['groupidconf2']!=0) $_Moutstr.="{$_Mrow['groupidconf2']}~{$_Mrow['group1caption']}";
				$_Moutstr.="{$_Mrow['groupidconf1']}";
  			}
  			mysql_free_result($_Mresult); 	
  			return $_Moutstr;
  		}else return 0;
  	}else return -1;
  }
  
function _Fgetcoursegroupcaption($_Mgroupid1,$_Mgroupid2,$_Mgroupid3)
{
	$_Moutstr="";
	$_Msqlstr="select groupcaption from groupstatus where groupID=$_Mgroupid1 and termID={$GLOBALS['_Mactivetermid']}";
	if($_Mresult=mysql_query($_Msqlstr))
	{
		if(mysql_num_rows($_Mresult)>0)
		{
			$_Mrow=mysql_fetch_array($_Mresult);
			$_Moutstr=$_Mrow['groupcaption'];
		}
	}
	$_Moutstr.='^^^';
	$_Msqlstr="select groupcaption from groupstatus where groupID=$_Mgroupid2 and termID={$GLOBALS['_Mactivetermid']}";
	if($_Mresult=mysql_query($_Msqlstr))
	{
		if(mysql_num_rows($_Mresult)>0)
		{
			$_Mrow=mysql_fetch_array($_Mresult);
			$_Moutstr.=$_Mrow['groupcaption'];
		}
	}
	$_Moutstr.='^^^';
	$_Msqlstr="select groupcaption from groupstatus where groupID=$_Mgroupid3 and termID={$GLOBALS['_Mactivetermid']}";
	if($_Mresult=mysql_query($_Msqlstr))
	{
		if(mysql_num_rows($_Mresult)>0)
		{
			$_Mrow=mysql_fetch_array($_Mresult);
			$_Moutstr.=$_Mrow['groupcaption'];
		}
	}
	return $_Moutstr;
}
//******************end of report constraints**************
function _Frepteachersslots($_Mteacherid)
{
	$_Msqlstr="select times from tchrtimes where teachergroupid in ({$GLOBALS['_Mgroups']}) and termID={$GLOBALS['_Mactivetermid']} and teacherID=$_Mteacherid";
	if($_Mresult=mysql_query($_Msqlstr))
	{
		if($_Mrow=mysql_fetch_array($_Mresult))
		{
			$_Moutstr=$_Mrow['times'];
			mysql_free_result($_Mresult);
			return $_Moutstr;
		}else return 0;
	}else return -1;
}

function _Frepgroupallteachersslots($_Mgroups)
{
	$_Msqlstr="select teacherID,teachername,teacherfamily,initialtimes,times from tchrtimes where teachergroupid in ($_Mgroups) and termID={$GLOBALS['_Mactivetermid']}";
	$_Moutstr="";
	if($_Mresult=mysql_query($_Msqlstr))
	{
		if(mysql_num_rows($_Mresult)>0)
		{
			while($_Mrow=mysql_fetch_array($_Mresult))
			{
				$_Moutstr.="{$_Mrow['teacherID']}**{$_Mrow['teachername']}**{$_Mrow['teacherfamily']}**";
				$_Moutstr.=substr($_Mrow['initialtimes'],0,84)."**".substr($_Mrow['times'],0,84)."##";
				
			}
			mysql_free_result($_Mresult);
			//echo "$_Msqlstr";
			return substr($_Moutstr,0,strlen($_Moutstr)-2);
		}else return 0;
	}else return -1;
}

function _Frepgroupcoursesteachers($_Mgroups1,$_Mgroupid,$_Mcoursecode)
{
	//echo $_Mgroupid.'**'.$_Mcoursecode.'----';
	//return '#tid~tname tfamily~2#tid2~tname2 tfamily2~3';
	//will optimize and correct for applying course part
	$_Moutstr='';
	    $_Mtcids=array();$_Mtcparts=array();$_Mtcpriors=array();$_Mtids=array();$_Mtnamefams=array();$_Mtnos=array();
		$_Msqlstr="select teacherID,teachername,teacherfamily,teachercourseids,teachercoursepriors,courseparts,teacherno from tchrtimes where teachergroupid in($_Mgroups1) and termID=2";//{$GLOBALS['_Mactivetermid']}
		//echo $_Msqlstr;
		if(!($_Mresult=mysql_query($_Msqlstr))) return -1;
		if(mysql_num_rows($_Mresult)<=0) return 0;
		while ($_Mrow=mysql_fetch_assoc($_Mresult))
		{
			array_push($_Mtcids,$_Mrow['teachercourseids']);array_push($_Mtcparts,$_Mrow['courseparts']);
			array_push($_Mtcpriors,$_Mrow['teachercoursepriors']);array_push($_Mtids,$_Mrow['teacherID']);array_push($_Mtnos,$_Mrow['teacherno']);
			array_push($_Mtnamefams,$_Mrow['teachername'].' '.$_Mrow['teacherfamily']);}


				//$_Mcoursecode=$_Mrow['coursecode'];
				//$_Mcoursec=_Fclccoursecrd($_Mcoursecode,$_Mtcids);
	$_Mcount=0;
	for($i=0 ; $i<count($_Mtcids) ; $i++)
	{
		$_Mtcid=explode(",",$_Mtcids[$i]);
		$_Mtcprior=explode(",",$_Mtcpriors[$i]);
		//$_Mtno=explode(",",$_Mtnos[$i]);
		if(in_array( $_Mcoursecode,$_Mtcid, false))
		{	$_Mcount++;
			$_Mpos=array_search( $_Mcoursecode,$_Mtcid,false);
			$_Moutstr.='#'.$_Mtids[$i].'~'.
			$_Mtnamefams[$i].'~'.
			$_Mtcprior[$_Mpos].'~'.
			$_Mtnos[$i];
		}
	}
	return $_Moutstr;
				

}
function _Frepgroupteacherscourses($_Mgroups,$_Mgroups1)
{
	$_Mextrafields="teachers";
	//$_Mextrafields='';
	$_Ms=_Fgetgroupcourses4asgn($_Mgroups,$_Mextrafields);
	$_Ms.='|';
	$_Ms.=_Frepgroupteachers($_Mgroups1,'');
	return $_Ms;
}

function _Frepgroupteacherscoursesuni($_Mgroups,$_Mgroups1)
{
	$_Mextrafields="teachers";
	//$_Mextrafields='';
	$_Ms=_Fgetgroupcourses4teach($_Mgroups,$_Mextrafields);
	$_Ms.='|';
	$_Ms.=_Frepgroupteachers($_Mgroups1,'');
	return $_Ms;
}

function _Frepgroupcoursesrooms($_Mgroups)
{
	$_Mextrafields="rooms";
	//$_Mextrafields="";
	$_Ms=_Fgetgroupcourses4asgn($_Mgroups,$_Mextrafields);
	$_Ms.='|';
	$_Ms.=_Fgetgrouprooms($_Mgroups);
	return $_Ms;	
}

function _Frepgroupteachersrooms($_Mgroups,$_Mgroups1,$_Mmgid1)
{
	$_Mextrafields="rooms";
	$_Ms=_Frepgroupteachers($_Mgroups1,$_Mextrafields);
	$_Ms.='|';
	$_Mtemp=_Fgetgrouprooms($_Mgroups);
	//920618
	if(($_Mtemp!="") or ($_Mtemp!=0))
	{$_Ms.=$_Mtemp;}
	else {$_Ms.=_Fgetgrouprooms($_Mmgid1);}
	return $_Ms;		
}
function _Frepgroupcoursesslots($_Mgroups,$_Mgroupid)
{
	$_Ms=_Fgetgroupinitialtimes($_Mgroupid);
	$_Ms.='|';
	$_Ms.=_Fgetgroupcourses4asgn($_Mgroups,'');
	return $_Ms;
}
function _Fgetcourseidfrom($_Ms,&$_Mcoursepart)
{
	$_Mcidcpt=explode("!",$_Ms);
	$_Mcourseid=$_Mcidcpt[0];
	$_Mcoursepart=$_Mcidcpt[1];
	return $_Mcourseid;
}

?>