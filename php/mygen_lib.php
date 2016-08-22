<?php
function _Fschedulets(&$_Mcourse1,&$_Mteacher1,&$_Mgroup1,$_Mccnst)
{
  	$_Mtimeslots=array();$_Mtimeslotstate=array();
	_Fslottype2slotstypes($_Mcourse1->getcourseslotcnst(),$_Mtimeslots,$_Mtimeslotstate);
  	$_Mgroup1->updatestatus($_Mtimeslots,$_Mtimeslotstate);
  	_Fupdatecourseteachergroup($_Mcourse1,$_Mteacher1,$_Mgroup1,$_Mtimeslots,$_Mtimeslotstate,$_Mccnst);
  	if($_Mccnst=='ts-')
  		array_push($GLOBALS['_Mprocesslogs'],"Manual course-teacher-slot assignment :: course:{$_Mcourse1->getcoursecaption()} with teacher:{$_Mteacher1->teachername} at time:{$_Mcourse1->getcourseslotcnst()}");
  	else if($_Mccnst=='-s-')	
  	{array_push($GLOBALS['_Mprocesslogs'],"scheduled course:{$_Mcourse1->getcoursecaption()} with (a)teacher:{$_Mteacher1->teachername} on (M)time {}");}
}

function _Finitialisegroupvalues($_Minittype,$_Mgroup1,&$_Mgroupscheduledcourses,&$_Mgrouptermcourses,&$_Mgroupregedscheduledcourses,&$_Mgroupteachers,&$_Mgroupcoursecount,&$_Mgroupteachercount,&$_Mteacherslistcounter,&$_Mcourseslistcounter,&$_Mgroupregedcounter)
{
	$_Mout=true;
	$_Mgroupscheduledcourses=array();$_Mgrouptermcourses=array();$_Mgroupteachers=array();

	_Fgetgroupmscheduledcourses($_Mgroupscheduledcourses,$_Mgroup1->getgroupid());
	if($_Minittype=="constrained")
	{
		if(!(_Fgetgroupsedtermcourses($_Mgrouptermcourses,$_Mgroup1,$_Mgroupcoursecount))) 
		{
			//_Fcreatereport("no manual scheduled course defined (or all scheduled) {$_Mgroup1->getgroupcaption()}",$GLOBALS['group1']->getgroupid(),'','','','','');
			return  false;
		}
	}
	else if($_Minittype=="normal")
	{
		if(!(_Fgetgrouptermcourses($_Mgrouptermcourses,$_Mgroup1,$_Mgroupcoursecount))) 
		{
			_Fcreatereport("no course defined (or all scheduled) {$_Mgroup1->getgroupcaption()}",$GLOBALS['_Mgroup1']->getgroupid(),'','','','','');
			$_Mout=false;
		}
		if((_Fgetgroupregedcourses($_Mgroupregedscheduledcourses,$_Mgroup1,$_Mgroupregedcounter))==0) 
		{
			_Fcreatereport("no course scheduled yet {$_Mgroup1->getgroupcaption()}",$GLOBALS['_Mgroup1']->getgroupid(),'','','','','');
		}
		
	}
	$tchrsgroupid=_Fgetmastergroup($_Mgroup1->getgroupid(),$GLOBALS['_Mactivetermid']);
	if(!(_Fgetgroupteachers($_Mgroupteachers,$tchrsgroupid,$_Mgroupteachercount))) 
	{
		_Fcreatereport("no teacher defined for group {$_Mgroup1->getgroupcaption()}",$GLOBALS['_Mgroup1']->getgroupid(),'','','','','');
		$_Mout=false;
	}
	
	$_Mteacherslistcounter=0;$_Mcourseslistcounter=0;
	return $_Mout;
}
function _Fgetgroupmscheduledcourses(&$_Mgroupscheduledcourses,$_Mgroupid)
{//query will optimize
	$_Msqlstr="select * from asgncnsts where cnsttype like '%c%' and ((groupID=$_Mgroupid AND awgroupID=0) OR (awgroupID=$_Mgroupid) OR (groupidconf2=$_Mgroupid) OR (groupidconf3=$_Mgroupid)) and termID={$GLOBALS['_Mactivetermid']} and courseID not in(select courseID from termcoursestatus where ((groupidconf1=$_Mgroupid AND awgroupID=0) OR (awgroupID=$_Mgroupid) OR (groupidconf2=$_Mgroupid) OR (groupidconf3=$_Mgroupid)) and termID={$GLOBALS['_Mactivetermid']} and coursestatus='s') order by courseID,coursepart,cnsttype";
	if($_Mresult=mysql_query($_Msqlstr))
	{
		if(mysql_num_rows($_Mresult)>0)
		{
			while($_Mrow=mysql_fetch_array($_Mresult,MYSQL_ASSOC))
			{array_push($_Mgroupscheduledcourses,$_Mrow);}
		}else return 0;
	}else return -1;
}
function _Fgetgroupsedtermcourses(&$_Mgrouptermcourses,$_Mgroup1,&$_Mgroupcoursecount)
{
	$_Msqlstr="select courseID,coursecode,coursepart,coursecaption,coursehaspref,courseAunits,courseTunits,coursepartunits,coursepreferedtimes,coursetype,neededroomtypeID,otherpart1slots,otherpart2slots,groupID,groupidconf1,awgroupID,groupidconf2,groupidconf3,courseAunitstchrs,courseTunitstchrs,coursecar,courserealAunits,courserealTunits from termcoursestatus where ((groupidconf1={$_Mgroup1->getgroupid()} AND awgroupID=0) OR (awgroupID={$_Mgroup1->getgroupid()}) OR (groupidconf2={$_Mgroup1->getgroupid()}) OR (groupidconf3={$_Mgroup1->getgroupid()})) and termID={$GLOBALS['_Mactivetermid']} and coursestatus<>'s' and courseID in (select courseID from asgncnsts where termID={$GLOBALS['_Mactivetermid']} group by courseID) order by coursep,prefsum,coursecar,courseID,coursepart";
	//select not cheduled courses
	if($_Mresult=mysql_query($_Msqlstr))
	{
		$_Mgroupcoursecount=mysql_num_rows($_Mresult);
		if($_Mgroupcoursecount>0)
		{
			while($_Mrow=mysql_fetch_array($_Mresult,MYSQL_ASSOC))
			{array_push($_Mgrouptermcourses,$_Mrow);}
			mysql_free_result($_Mresult);
			return true;
		}else return false;
	}else return false;
}
function _Fgetgrouptermcourses(&$_Mgrouptermcourses,$_Mgroup1,&$_Mgroupcoursecount)
{
	//_Fsetcourseprefs1($_Mgroup1);
	$_Msqlstr="select courseID,coursecode,coursepart,coursecaption,coursehaspref,courseAunits,courseTunits,coursepartunits,coursepreferedtimes,coursetype,neededroomtypeID,otherpart1slots,otherpart2slots,groupID,groupidconf1,awgroupID,groupidconf2,groupidconf3,courseAunitstchrs,courseTunitstchrs,coursecar,courserealAunits,courserealTunits,cncslots from termcoursestatus where ((groupidconf1={$_Mgroup1->getgroupid()} AND awgroupID=0) OR (awgroupID={$_Mgroup1->getgroupid()}) OR (groupidconf2={$_Mgroup1->getgroupid()}) OR (groupidconf3={$_Mgroup1->getgroupid()})) and termID={$GLOBALS['_Mactivetermid']} and coursestatus<>'s' order by coursep,prefsum,coursetfs,15-(courseAunits+courseTunits),coursecar,courseID,coursepart";
	//select not cheduled courses
	if($_Mresult=mysql_query($_Msqlstr))
	{
		$_Mgroupcoursecount=mysql_num_rows($_Mresult);
		if($_Mgroupcoursecount>0)
		{
			while($_Mrow=mysql_fetch_array($_Mresult,MYSQL_ASSOC))
	  			array_push($_Mgrouptermcourses,$_Mrow);
			mysql_free_result($_Mresult);
			return true;
		}else return false;
	}else return false;
}
function _Fgetgroupregedcourses(&$_Mgroupregedscheduledcourses,$_Mgroup1,&$_Mgroupregedcounter)
{
	$_Msqlstr="select courseID,coursepart,coursepartunits,courseunits,teacherID,courseparttchrs,coursecar,courserealAunits,courserealTunits from assignments where (groupID={$_Mgroup1->getgroupid()} AND awgroupID=0) AND termID={$GLOBALS['_Mactivetermid']} order by coursep,prefsum,coursecar,courseID,coursepart";
	//select not cheduled courses
	if($_Mresult=mysql_query($_Msqlstr))
	{
		$_Mgroupregedcounter=mysql_num_rows($_Mresult);
		if($_Mgroupregedcounter>0)
		{
			while($_Mrow=mysql_fetch_array($_Mresult,MYSQL_ASSOC))
	  			array_push($_Mgroupregedscheduledcourses,$_Mrow);
			mysql_free_result($_Mresult);
			return 1;
		}else return 0;
	}else return -1;
}

//++++++++++from getdata_lib
/*
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
*/
//++++++++++
function _Fwritesession($n,$v)
{
	$_SESSION["{$n}"]=$v;
}
function multiexplode ($delimiters,$string) {
     
    $ready = str_replace($delimiters, $delimiters[0], $string);
     $launch = explode($delimiters[0], $ready);
     return  $launch;
 }
 

function _Fslottype2slotstypes($_Mtimetypes,&$_Mtimeslots,&$_Mtimeslotstate)
{
			  	$_Mtimes=multiexplode(array(':',','),$_Mtimetypes); 
			  	$_Mtimeslots=array();$_Mtimeslotstate=array();
			  	for($it=0; $it<(count($_Mtimes)/2) ;$it++)
			  	{$_Mtimeslots[$it]=$_Mtimes[$it*2];$_Mtimeslotstate[$it]=$_Mtimes[$it*2+1];}
}

function _Fcomplement($_Mchr)
{if($_Mchr=='e') return 'o';else if($_Mchr=='o') {return 'e';}else if($_Mchr=='s') {return 's';}else return -1;}

function _Fsmaller($_Mch1,$_Mch2)
{
	if($_Mch1==$_Mch2) return $_Mch1;
	else if( ($_Mch1=='n') || ($_Mch2=='n') ) {return 'n';}
	else if( ($_Mch1=='s') || ($_Mch2=='s') ) {return 'n';}
	else if( ($_Mch1=='f') & (($_Mch2=='o')||($_Mch2=='e')||($_Mch2=='p'))) {return ($_Mch2);}
	else if( ($_Mch2=='f') & (($_Mch1=='o')||($_Mch1=='e')||($_Mch1=='e'))) {return ($_Mch1);}	
	else return -1;
}

function _Fcreatereport($_Mreporttype,$_Mgroupid,$_Mcourseid,$_Mcoursecaption,$_Mteacherid,$_Mteachernamefam,$_Mcoursepart)
{
	global $_Mprocesslogs,$_Moptimisearray;
	
	array_push($_Mprocesslogs,"$_Mreporttype group : $_Mgroupid - course : $_Mcoursecaption,$_Mcourseid - teacher : $_Mteachernamefam,$_Mteacherid");
	
	//if($_Mreporttype=='no teacher found')
	//	array_push($_Moptimisearray,array("notsch",$_Mgroupid,$_Mcourseid,$_Mcoursecaption,$_Mteacherid,$_Mteachernamefam,$_Mcoursepart));
	
	if($_Mreporttype=='in 1 day')
	{array_push($_Moptimisearray,array("in1day",$_Mgroupid,$_Mcourseid,$_Mcoursecaption,$_Mteacherid,$_Mteachernamefam,$_Mcoursepart));}

	else if($_Mreporttype=='not completely scheduled')
	{array_push($_Moptimisearray,array("notcomp",$_Mgroupid,$_Mcourseid,$_Mcoursecaption,$_Mteacherid,$_Mteachernamefam,$_Mcoursepart));}

	else if(substr($_Mreporttype,0,17)=='no course defined')
	{array_push($_Moptimisearray,array("nocourse",$_Mgroupid,'','','','',''));}

	else if(substr($_Mreporttype,0,18)=='no teacher defined')
	{array_push($_Moptimisearray,array("noteach",$_Mgroupid,'','','','',''));}

	else if(substr($_Mreporttype,0,17)=='no room for group')
	{array_push($_Moptimisearray,array("nogroom",$_Mgroupid,'','','','',''));}

	//if($_Mreporttype=='Manual course-teacher-slot')

	//if($_Mreporttype=='Manual course-teacher-slot')
	$GLOBALS['_Mreportcounter']++;
	//if($GLOBALS['reportcounter']==20)
	  //savereportstodb();
}

function _Fresetcounters(&$_Mteacherslistcounter,&$_Mcourseslistcounter)
{
	$_Mteacherslistcounter=0;$_Mcourseslistcounter=0;
}

function savereporttodb()
{}
function _Fgetgroups4sch(&$_Mgroups,&$_Mgroupidsstr,&$roomgroups,&$_Mgroupscount,$_Mgroupsactions)
{
	$_Mnewga=array();$_Mfinalgroups=array();
	$_Mgroups1=array();
	$_Msqlstr="select groupID,groupcaption,groupstatus,initialgroupstatus,groupsmaxslotsperday,subgroups,finalgroup,subgroupof,mastergroup,'1' as flag from groupstatus where termID={$GLOBALS['_Mactivetermid']} ";//and groupID in(select groupID from groups where finalgroup=1)";
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
		//removed because all groups final or not should be scheduled
		//if($_Mgroups1[$_Mnewga[$i][0]]["finalgroup"]==1)
		{
			if(array_key_exists($_Mnewga[$i][0],$_Mfinalgroups)) 
			{
				
				if($_Mnewga[$i][1]!=$_Mfinalgroups[$_Mnewga[$i][0]]['flag'])
					$_Mfinalgroups[$_Mgroups1[$_Mnewga[$i][0]]['groupID']]['flag']=0;
			}
			else 
			{
				if($_Mnewga[$i][1]==3)
				{array_push($roomgroups,$_Mgroups1[$_Mnewga[$i][0]]);}
				else 
				{
					$_Mfinalgroups[$_Mgroups1[$_Mnewga[$i][0]]['groupID']]=$_Mgroups1[$_Mnewga[$i][0]];
					$_Mfinalgroups[$_Mgroups1[$_Mnewga[$i][0]]['groupID']]['flag']=$_Mnewga[$i][1];
				}
			}
			//removed because abov if removed
			//continue;
		}
		if(($_Mgroups1[$_Mnewga[$i][0]]["finalgroup"]!=1))
		{
		
			if(($_Mgroups1[$_Mnewga[$i][0]]["subgroups"]!=0)&($_Mgroups1[$_Mnewga[$i][0]]["subgroups"]!='')&($_Mgroups1[$_Mnewga[$i][0]]["subgroups"]!=' '))
			{		
			$subs=explode(",",$_Mgroups1[$_Mnewga[$i][0]]["subgroups"]);
			for($j=0 ; $j < count($subs) ; $j++)
				array_push($_Mnewga,array($subs[$j],$_Mnewga[$i][1]));
			}
		}
	}
	$_Mgroupscount=0;$_Mgroupidsstr="";
 	for( ; count($_Mfinalgroups)>0 ; )
	{
		$_Mrow=array_pop($_Mfinalgroups);
		if($_Mrow['flag']==1)
		{array_push($_Mgroups,$_Mrow);$_Mgroupidsstr.=$_Mrow['groupID'].',';}
		$_Mgroupscount++;
	}
	$_Mgroupidsstr=substr($_Mgroupidsstr,0,strlen($_Mgroupidsstr)-1);
	return true;
}

function _Fpickgroup(&$_Mgroup1,$_Mgroups,&$_Mgroupslistcounter,$_Mgroupscount)
{	
	if($_Mgroupslistcounter<$_Mgroupscount)
	{
		if($_Mrow=$_Mgroups[$_Mgroupslistcounter])
		{
			$_Mgroup1->setgroupid($_Mrow["groupID"]);
			$_Mgroup1->setgroupstatus($_Mrow["groupstatus"]);
			$_Mgroup1->setinitialgroupstatus($_Mrow["initialgroupstatus"]);
			$_Mgroup1->setgroupcaption($_Mrow["groupcaption"]);
			$_Mgroup1->setgroupsmaxslotsperday($_Mrow["groupsmaxslotsperday"]);
			$_Mgroup1->setuppergroups($_Mrow["subgroupof"],$_Mrow["mastergroup"],-1);
			//$_Mgroup1->setinitialgroupstatus($_Mrow["initialgroupstatus"]);
			$_Mgroupslistcounter++;
			return true;
		}
		else return false;
	}
	else return false;
}
//room scheduling
function _Fschedulerooms($_Mgroups)
{

	
	
	echo "room scheduling";
}
function _Fgetteachersroomcnst(&$_Mteacherroomcnsts,$_Mgroup1)
{
	$_Msqlstr="select teacherID,roomID,roomcaption from asgncnsts where groupID={$_Mgroup1->getgroupid()} and termID={$GLOBALS['_Mactivetermid']} and cnsttype like '%r%' and cnsttype like '%t%'";
	if($_Mresult=mysql_query($_Msqlstr))
	{
		$_Mteachercnstno=mysql_num_rows($_Mresult);
		if($_Mteachercnstno>0)
		{
			while($_Mrow=mysql_fetch_array($_Mresult,MYSQL_ASSOC))
	  			$_Mteacherroomcnsts[$_Mrow['teacherID']]=array($_Mrow['roomID'],$_Mrow['roomcaption']);
			mysql_free_result($_Mresult);
			return true;
		}else return false;
	}else return false;
}
function _Fgetcourseroomcnsts(&$_Mcourseroomcnsts,$_Mgroup1)
{
	$_Msqlstr="select courseID,roomID,roomcaption,coursepart from asgncnsts where groupID={$_Mgroup1->getgroupid()} and termID={$GLOBALS['_Mactivetermid']} and cnsttype like '%r%' and cnsttype like '%c%'";
	if($_Mresult=mysql_query($_Msqlstr))
	{
		$_Mteachercnstno=mysql_num_rows($_Mresult);
		if($_Mteachercnstno>0)
		{
			while($_Mrow=mysql_fetch_array($_Mresult,MYSQL_ASSOC))
				$_Mcourseroomcnsts["{$_Mrow['courseID']}-{$_Mrow['coursepart']}"]=array($_Mrow['roomID'],$_Mrow['roomcaption']);
			mysql_free_result($_Mresult);
			return true;
		}else return false;
	}else return false;
}

function _Fgetgrouprooms4sch(&$_Mgrouprooms,$_Mgroup1)
{
	$_Mmgroupid=_Fgetmastergroup($_Mgroup1->getgroupid(),$GLOBALS['_Mactivetermid']);
	//$_Mgroups=getsubgroups("$_Mmgroupid,1");
	$_Msqlstr="select groupID,roomIDs,roomcaptions from asgncnsts where (maingroupID=$_Mmgroupid and groupID={$_Mgroup1->getgroupid()}) and termID={$GLOBALS['_Mactivetermid']} and cnsttype like '%r%' and cnsttype like '%g%'";
	if($_Mresult=mysql_query($_Msqlstr))
	{
		$_Mrowsno=mysql_num_rows($_Mresult);
		if($_Mrowsno>0)
		{
			while($_Mrow=mysql_fetch_array($_Mresult,MYSQL_ASSOC))
	  			array_push($_Mgrouprooms,$_Mrow);
			mysql_free_result($_Mresult);
			return true;
		}
		else 
		{
			$_Msqlstr="select groupID,roomIDs,roomcaptions from asgncnsts where (maingroupID=$_Mmgroupid and groupID=$_Mmgroupid) and termID={$GLOBALS['_Mactivetermid']} and cnsttype like '%r%' and cnsttype like '%g%'";
			$_Mrowsno=mysql_num_rows($_Mresult);
			if($_Mrowsno>0)
			{
				while($_Mrow=mysql_fetch_array($_Mresult,MYSQL_ASSOC))
	  				array_push($_Mgrouprooms,$_Mrow);
				mysql_free_result($_Mresult);
				return true;
			}else return false;
		}
	}else return false;
}
function _Fassignroom2course($_Mcourseid,$_Mgroupid,$_Mcoursepart,$_Mroom1,$_Mslotid,$_Mslottype,$_Mactivetermid)
{
	if(!(_Fupdateroomsch($_Mroom1,$_Mslotid,$_Mslottype,$_Mactivetermid))) return false;
	$_Mstatus=$_Mroom1->getroomstatus();
	_Fmarkslots($_Mstatus,$_Mslotid,$_Mslottype);
	$_Mroom1->savestatus($_Mstatus,$_Mactivetermid);
	
	$_Msqlstr="update assignments set roomID='{$_Mroom1->getroomid()}',roomcaption='{$_Mroom1->getroomcaption()}' where courseID=$_Mcourseid and coursepart like '$_Mcoursepart' and groupID={$_Mgroupid} and termID={$GLOBALS['_Mactivetermid']}";
	if (mysql_query($_Msqlstr))
		return 1;
	else
		return 0;
}
function _Fupdateroomsch($_Mroom1,$_Mslotid,$_Mslottype,$_Mactivetermid)
{
	$_Msqlstr="select roomstatus from roomsh where roomID={$_Mroom1->getroomid()} and termID=$_Mactivetermid";
	if($_Mresult=mysql_query($_Msqlstr))
	{
		if(mysql_num_rows($_Mresult)>=0)
		{
			$_Mrow=mysql_fetch_array($_Mresult);
			$_Mstatus=$_Mrow['roomstatus'];
			if(_Fmarkslots($_Mstatus,$_Mslotid,$_Mslottype))
			{
				$_Msqlstr="update roomsh set roomstatus='$_Mstatus' where roomID={$_Mroom1->getroomid()} and termID=$_Mactivetermid";
				if (mysql_query($_Msqlstr)) return true;
				return false;
			}else return false;
		}
	}else return false;	
}
function _Fmarkslots(&$_Mstatus,$_Mslotid,$_Mslottype)
{
	if($_Mstatus[$_Mslotid]=='f')
	{
		if($_Mslottype=='s') {$_Mstatus[$_Mslotid]='s';$_Mstatus[$_Mslotid+1]='s';return true;}
		if($_Mslottype=='o') {$_Mstatus[$_Mslotid]='e';$_Mstatus[$_Mslotid+1]='e';return true;}
		if($_Mslottype=='e') {$_Mstatus[$_Mslotid]='o';$_Mstatus[$_Mslotid+1]='o';return true;}
		return false;
	}
	if($_Mstatus[$_Mslotid]==$_Mslottype)
	{$_Mstatus[$_Mslotid]='s';$_Mstatus[$_Mslotid+1]='s';return true;}
	return false;
}
function _Fmarkroomasignedslots($_Mroomid,$_Mgroupid,$_Mslotid,$_Mslottype)
{
	
}
function _Fgetteacherprevroomid($_Mteacherid,$_Mslotid)
{}
//*********************

function _Fcheckcoursecnst(&$_Mgroupscheduledcourses,&$_Mcourse1)
{
	global $_Mfullassigned,$_Mslotassigned,$_Mteacherassigned,$_Mroomassigned;
	$_Mcnsttype='';
	for($i=0; $i<count($_Mgroupscheduledcourses) ; $i++)
	{
		if($_Mgroupscheduledcourses[$i]['courseID']==$_Mcourse1->getcourseid())
		{
			if($_Mgroupscheduledcourses[$i]['cnsttype']==$_Mfullassigned)
			{
				$_Mcourse1->setcourseslotcnst($_Mgroupscheduledcourses[$i]['slots']);
				$_Mcourse1->setcourseteachercnst($_Mgroupscheduledcourses[$i]['teacherID']);
				$_Mcourse1->setcourseroomcnst($_Mgroupscheduledcourses[$i]['roomID']);
				return $_Mfullassigned;
			}
			else if($_Mgroupscheduledcourses[$i]['cnsttype']==$_Mroomassigned)
			{
				$_Mcourse1->setcourseroomcnst($_Mgroupscheduledcourses[$i]['roomID']);
				$_Mcnsttype.='r';
			}
			else if($_Mgroupscheduledcourses[$i]['cnsttype']==$_Mteacherassigned)
			{
				$_Mcourse1->setcourseteachercnst($_Mgroupscheduledcourses[$i]['teacherID']);
				$_Mcnsttype.='t';
			}
			else if($_Mgroupscheduledcourses[$i]['cnsttype']==$_Mslotassigned)
			{
				$_Mcourse1->setcourseslotcnst($_Mgroupscheduledcourses[$i]['slots']);
				$_Mcnsttype.='s';
			}

		}		
	}
	
	
	
	return _Fsetcnstorder($_Mcnsttype);
}
function _Fsetcnstorder($_Mcnsttype)
{
	$_Mtmpcnst='';
	if(strpos($_Mcnsttype,"t",0)===false) $_Mtmpcnst='-';
	else $_Mtmpcnst='t';
	if(strpos($_Mcnsttype,"s",0)===false) $_Mtmpcnst.='-';
	else $_Mtmpcnst.='s';
	if(strpos($_Mcnsttype,"r",0)===false) $_Mtmpcnst.='-';
	else $_Mtmpcnst.='r';
	return $_Mtmpcnst;
}

//***changed
function _Fgetgroupfinalsedtermcourses(&$_Mgroupscheduledcourses,$_Mgroupid,&$_Mgroupcoursecount)
{
	$_Mgrouptermcourses=array();
	$_Msqlstr="select assignments.courseID,assignments.coursepart,assignments.coursecaption,assignments.teacherID,assignments.roomID,assignments.groupidconf2,assignments.groupidconf3,assignments.timeslots,courses.neededroomtypeID,courses.neededroomtypeAID from assignments,courses where assignments.groupID={$_Mgroupid} and assignments.termID={$GLOBALS['_Mactivetermid']} and courses.courseID=assignments.courseID";
	//select not cheduled courses
	if($_Mresult=mysql_query($_Msqlstr))
	{
		$_Mrowsno=mysql_num_rows($_Mresult);
		$_Mgroupcoursecount=$_Mrowsno;
		if($_Mrowsno>0)
		{
			while($_Mrow=mysql_fetch_array($_Mresult,MYSQL_ASSOC))
				array_push($_Mgroupscheduledcourses,$_Mrow);
			mysql_free_result($_Mresult);
			return true;
		}else return false;
	}else return false;

}

function _Fpickgroupscheduledcourse($_Mgroupscheduledcourses,&$_Mcourse1,&$_Mcourseslistcounter,$_Mgroupcoursecount)
{
	if($_Mcourseslistcounter<$_Mgroupcoursecount)
	{
	    if($_Mrow=$_Mgroupscheduledcourses[$_Mcourseslistcounter])
	    {
	    	$_Mtimeslots=multiexplode(array(',',':'),$_Mrow["timeslots"]);
			_Fslottype2slotstypes($_Mrow["timeslots"],$_Mtimeslots,$_Mtimeslotstate);
		    $_Mcourse1->setcourseid($_Mrow["courseID"]);//"courseid"
			$_Mcourse1->setcoursecaption($_Mrow["coursecaption"]);
			$_Mcourse1->setneededroomtypeid($_Mrow["neededroomtypeID"]);
			$_Mcourse1->setneededroomtypeaid($_Mrow["neededroomtypeAID"]);
			$_Mcourse1->setassignedroomid($_Mrow["roomID"]);
			$_Mcourse1->setassignedteacherid($_Mrow["teacherID"]);
			$_Mcourse1->setcoursepart($_Mrow["coursepart"]);
			//$_Mcourse1->setcoursepartunits($_Mrow["coursepartunits"]);
			$_Mcourse1->setassignedslotid($_Mtimeslots[0]);
			$_Mcourse1->setassignedslottype($_Mtimeslotstate[0]);
			$_Mcourse1->setgroupidconf2($_Mrow["groupidconf2"]);
			$_Mcourse1->setgroupidconf3($_Mrow["groupidconf3"]);
			$_Mcourse1->setpartstchrs($_Mrow["courseparttchrs"]);
			$_Mcourseslistcounter++;
			return true;
	    }
	    else return false;
	}
    else return false;
}
function _Farraytostr($ar,$sep)
{
	$_Moutstr="";
	for($i=0 ; $i < count($ar) ; $i++)
		$_Moutstr.=$ar[$i].$sep;
	$_Moutstr=substr($_Moutstr,0,strlen($_Moutstr)-1);
	return $_Moutstr;	
}
function _Fpickgrouptermcourse($_Mgroupscheduledcourses,&$_Mcourse1,&$_Mgrouptermcourses,&$_Mcourseslistcounter,$_Mgroupcoursecount)
{
	if($_Mcourseslistcounter<$_Mgroupcoursecount)
	{
	    if($_Mrow=$_Mgrouptermcourses[$_Mcourseslistcounter])
	    {
		    $_Mcourse1->setcourseid($_Mrow["courseID"]);//"courseid"
			$_Mcourse1->setcoursecaption($_Mrow["coursecaption"]);
			$_Mcourse1->setcourseunits($_Mrow["courseAunits"],$_Mrow["courseTunits"]);
			$_Mcourse1->setcourserealunits($_Mrow["courserealAunits"],$_Mrow["courserealTunits"]);
			$_Mcourse1->setcoursepreftimes($_Mrow["coursepreferedtimes"]);
			$_Mcourse1->setcoursehaspref($_Mrow["coursehaspref"]);
			$_Mcourse1->setneededroomtypeid($_Mrow["neededroomtypeID"]);
			//$_Mcourse1->setneededroomtypeaid($_Mrow["neededroomtypeAID"]);
			$_Mcourse1->setcoursepart($_Mrow["coursepart"]);
			$_Mcourse1->setcoursepartunits($_Mrow["coursepartunits"]);
			$_Mcourse1->setotherpart1slots($_Mrow["otherpart1slots"]);
			$_Mcourse1->setotherpart2slots($_Mrow["otherpart2slots"]);
			$_Mcourse1->setcoursemaingroupid($_Mrow["groupID"]);
			$_Mcourse1->setcoursegroupidconf($_Mrow["groupidconf1"]);
			$_Mcourse1->setgroupidconf2($_Mrow["groupidconf2"]);
			$_Mcourse1->setgroupidconf3($_Mrow["groupidconf3"]);
			$_Mcourse1->setschwithgroupid($_Mrow["awgroupID"]);
			$_Mcourse1->setcoursecode($_Mrow["coursecode"]);
			$_Mcourse1->setcoursecnc($_Mrow["cncslots"]);
			if($_Mrow["coursepart"][0]=='a')
				$_Mcourse1->setpartstchrs($_Mrow["courseAunitstchrs"]);
			else $_Mcourse1->setpartstchrs($_Mrow["courseTunitstchrs"]);

			//$_Mcourse1->setcoursecode($_Mrow["coursecode"]); no course code needed 
			_Fsetcoursecnsts($_Mcourse1,$_Mgroupscheduledcourses);
			$_Mcourseslistcounter++;
			return true;
	    }
	    else return false;
	}    
    else return false;
}
function _Fsetcoursecnsts(&$_Mcourse1,$_Mgroupscheduledcourses)
{
	global $_Mfullassigned,$_Mteacherassigned,$_Mroomassigned,$_Mslotassigned,$_Mteacherslotassigned;
	$_Mcnsttype='';
	for($i=0 ; $i<count($_Mgroupscheduledcourses) ; $i++)
	{
		$_Mrow=$_Mgroupscheduledcourses[$i];
		if(($_Mcourse1->getcourseid()==$_Mrow['courseID'])&&($_Mcourse1->getcoursepart()==$_Mrow['coursepart']))
		{
			if($_Mrow['cnsttype']==$_Mfullassigned)
			{
				$_Mcourse1->setcourseteachercnst($_Mrow['teacherID']);
				$_Mcourse1->setcourseslotcnst($_Mrow['slots']);
				$_Mcourse1->setcourseroomcnst($_Mrow['roomID']);
				$_Mcourse1->setcnsttype($_Mfullassigned);
				return;
			}
			else if($_Mrow['cnsttype']==$_Mteacherslotassigned)
			{
				$_Mcourse1->setcourseteachercnst($_Mrow['teacherID']);
		    	$_Mcourse1->setcourseslotcnst($_Mrow['slots']);
		    	$_Mcourse1->setcnsttype($_Mteacherslotassigned);
		    	return;
			}
			if($_Mrow['cnsttype']==$_Mteacherassigned)
			{$_Mcourse1->setcourseteachercnst($_Mrow['teacherID']);$_Mcnsttype.='t';}
			else if($_Mrow['cnsttype']==$_Mslotassigned)
			{$_Mcourse1->setcourseslotcnst($_Mrow['slots']);$_Mcnsttype.='s';}
			else if($_Mrow['cnsttype']==$_Mroomassigned)
			{$_Mcourse1->setcourseroomcnst($_Mrow['roomID']);$_Mcnsttype.='r';}
		}
	}
	$_Mcourse1->setcnsttype(_Fsetcnstorder($_Mcnsttype));	
}
function _Fgetgroupteachers(&$_Mgroupteachers,$_Mgroupid,&$_Mgroupteachercount)
{
	$_Msqlstr="select teacherID,teachername,teacherfamily,teachercourseids,teachercoursepriors,times,hastimeconst,teacherPrior,teachermaxslots,teachercurslots from tchrtimes where ((teachergroupid={$_Mgroupid}) and 
										(termID={$GLOBALS['_Mactivetermid']})";
	if($GLOBALS['_Mcheckteachersmaxslotcnst'])
		$_Msqlstr.=" and(teachercurslots<teachermaxslots or teachermaxslots=0) ";
	if($GLOBALS['_Mcnstteachercontinousslots'])
		$_Msqlstr.=") order by teacherPrior,prefsum,teacherID";
	else $_Msqlstr.=") order by prefsum,teacherPrior,teacherID";
	if($_Mresult=mysql_query($_Msqlstr))
	{
		$_Mgroupteachercount=mysql_num_rows($_Mresult);
		if($_Mgroupteachercount>0)
		{
			while($_Mrow=mysql_fetch_array($_Mresult,MYSQL_ASSOC))
	  			array_push($_Mgroupteachers,$_Mrow);
	
			$_Mgroupteachercount=mysql_num_rows($_Mresult);
			mysql_free_result($_Mresult);

			return true;
		}else return false;
	}else return false;
}

function _Fpickteacher(&$_Mteacher1,$_Mcourse1,&$_Mgroupteachers,&$_Mcheckedteachersids,$_Mgroupteachercount,$_Mslotcnst,$_Mgroupregedscheduledcourses,$_Mgroupregedcounter)
{
		if(_Fselectbestteacher4course($_Mgroupteachers,$_Mrow,$_Mcourse1->getcourseid(),$_Mcourse1->getcoursecode(),$_Mcourse1->getcourseunits(),$_Mcourse1->getcoursepart(),$_Mcourse1->getcoursepartunits(),$_Mcheckedteachersids,$_Mgroupteachercount,'',$_Mgroupregedscheduledcourses,$_Mgroupregedcounter))
		{
						$_Mteacher1->setteacherid($_Mrow["teacherID"]);
						$_Mteacher1->setteachertimes($_Mrow["times"]);
						//$_Mteacher1->setteachergroupid($_Mrow["teachergroupid"]);
						$_Mteacher1->teachermaxslots=$_Mrow["teachermaxslots"];
						$_Mteacher1->teachername=$_Mrow["teachername"];
						$_Mteacher1->teacherfamily=$_Mrow["teacherfamily"];
						return true;
						break;
					/*{
						_Fcreatereport("teacher :{$_Mrow['teacherID']} hasn't enough free slots!\n");
						$_Mteacherslistcounter++;
					}*/
		}


	
	_Fcreatereport("no teacher found",$GLOBALS['_Mgroup1']->getgroupid(),$_Mcourse1->getcourseid(),$_Mcourse1->getcoursecaption(),'','',$_Mcourse1->getcoursepart());
	return false;	
}

//will complete
function _Fpickteacherforslottedcourse($_Mteacher1,$_Mcourse1,$_Mgroupteachers,$_Mcheckedteachersids,$_Mgroupteachercount,$_Mslotcnst,$_Mcourseid,$_Mcoursepart,$_Mgroupregedscheduledcourses,$_Mgroupregedcounter)
{
		if(_Fselectbestteacher4course($_Mgroupteachers,$_Mrow,$_Mcourse1->getcourseid(),$_Mcourse1->getcourseunits(),$_Mcourse1->getcoursepart(),$_Mcourse1->getcoursepartunits(),$_Mcheckedteachersids,$_Mgroupteachercount,$_Mslotcnst,$_Mgroupregedscheduledcourses,$_Mgroupregedcounter))
		{
						$_Mteacher1->setteacherid($_Mrow["teacherID"]);
						$_Mteacher1->setteachertimes($_Mrow["times"]);
						//$_Mteacher1->setteachergroupid($_Mrow["teachergroupid"]);
						$_Mteacher1->teachermaxslots=$_Mrow["teachermaxslots"];
						$_Mteacher1->teachername=$_Mrow["teachername"];
						$_Mteacher1->teacherfamily=$_Mrow["teacherfamily"];
						return true;
						break;
					/*{
						_Fcreatereport("teacher :{$_Mrow['teacherID']} hasn't enough free slots!\n");
						$_Mteacherslistcounter++;
					}*/
		}


	
	_Fcreatereport("no teacher found for manual timed course",$GLOBALS['_Mgroup1']->getgroupid(),$_Mcourse1->getcourseid(),$_Mcourse1->getcoursecaption(),'','',$_Mcourse1->getcoursepart());
	return false;	

}

//this func can be completed by teacher course priority
function _Fselectbestteacher4course($_Mgroupteachers,&$_Mrow,$_Mcourseid,$_Mcoursecode,$_Mcourseunits,$_Mcoursepart,$_Mcoursepartunits,&$_Mcheckedteachersids,$_Mgroupteachercount,$_Mslotcnst,$_Mgroupregedscheduledcourses,$_Mgroupregedcounter)
{
	$_Mteacherfound=false;
	if($_Mcourseunits>$_Mcoursepartunits)
	{
		//if(count($_Mgroupregedscheduledcourses)>0) this array is initialized when before this time any course is schedule,and if this is the first time,its empty
		{
			$_Mteacherid=_Fgetothercoursepartteacher($_Mcourseid,$_Mcoursepart,$_Mgroupregedscheduledcourses);
			if($_Mteacherid>0)
			{
				if(!($GLOBALS['_Motherpartteacherchecked']))
				{
					for($i=0 ; $i<count($_Mgroupteachers) ; $i++)
					if($_Mgroupteachers[$i]['teacherID']==$_Mteacherid)
					{
						$_Mrow1=$_Mgroupteachers[$i];
						$_Mselectedteacherid=$_Mteacherid;
						$_Mteacherfound=true;break;
					}
				}
			}
		}
	}

	if(!($_Mteacherfound))
	{
		if(count($_Mcheckedteachersids)==$_Mgroupteachercount)
			return false;
		$_Mselectedteacherid=0;$_Mselectedteacherprior=6;$_Mrow1='';
		for($i=0 ; $i<$_Mgroupteachercount ;$i++)
		{
			$_Mtrow=$_Mgroupteachers[$i];
			if(!(in_array($_Mtrow['teacherID'],$_Mcheckedteachersids)))
			{
				$_Mteachercourses=explode(",",$_Mtrow["teachercourseids"]);
				//changed courseid to coursecode 8711
				//$_Mindex=array_search($_Mcourseid,$_Mteachercourses);
				$_Mindex=array_search($_Mcoursecode,$_Mteachercourses);
				if($_Mindex!==false)
				{
					$_Mteachercoursepriors=explode(",",$_Mtrow["teachercoursepriors"]);
					//to check the teachers prior to teach course,teachers total prior should be added
					//a weight should be calculated(total p & course p) for teachers priority to teach course
					//if( ($_Mteachercoursepriors[$_Mindex]>=$_Mselectedteacherprior)&  ohhhhhh only a =
					if ((($GLOBALS['_Mcheckteachersmaxslotcnst'])&& 
					    (($GLOBALS['_Mgroupteachers'][$i]['teachercurslots']+$_Mcourseunits)<=$GLOBALS['_Mgroupteachers'][$i]['teachermaxslots']) ) 
					    ||(!($GLOBALS['_Mcheckteachersmaxslotcnst']))||($GLOBALS['_Mgroupteachers'][$i]['teachermaxslots']==0) )
					{    
						if( ($_Mteachercoursepriors[$_Mindex]<$_Mselectedteacherprior)&
					    	(($_Mslotcnst=='')||(_Fstatushastime4slots($_Mtrow['times'],$_Mslotcnst))) )//if slotcnst is '' no has checking needed
						{
							$_Mselectedteacherid=$_Mtrow['teacherID'];
							$_Mselectedteacherprior=$_Mteachercoursepriors[$_Mindex];
							$_Mrow1=$_Mtrow;
						}
						else _Fcreatereport("===>teacher isnot best ",$GLOBALS['_Mgroup1']->getgroupid(),$_Mcourseid,'',$_Mtrow['teacherID'],$_Mtrow["teacherfamily"],$_Mcoursepart);
					}
					else _Fcreatereport("===>curslots:{$GLOBALS['_Mgroupteachers'][$i]['teachercurslots']} - max: {$GLOBALS['_Mgroupteachers'][$i]['teachermaxslots']}",$GLOBALS['_Mgroup1']->getgroupid(),$_Mcourseid,'',$_Mtrow['teacherID'],$_Mtrow["teacherfamily"],$_Mcoursepart);
					$GLOBALS['_Mnoteacherhasthiscourse']=false;
				}
				else
				{
					_Fcreatereport("===>teacher hasnot course ",$GLOBALS['_Mgroup1']->getgroupid(),$_Mcourseid,'',$_Mtrow['teacherID'],$_Mtrow["teacherfamily"],$_Mcoursepart);
					array_push($_Mcheckedteachersids,$_Mtrow['teacherID']);
				}
			  }
		}
	}
	$_Mrow=$_Mrow1;
	if($_Mselectedteacherid)
	{array_push($_Mcheckedteachersids,$_Mselectedteacherid);return true;}
	else
		return false;
}
function _Fgetothercoursepartteacher($_Mcourseid,$_Mcoursepart,$_Mgroupregedscheduledcourses)
{
	
	for($i=0 ; $i< count($_Mgroupregedscheduledcourses) ; $i++)
	{
		if(($_Mcoursepart[0]=='c')||($_Mcoursepart[0]=='w'))//for not a course part
		{
			if(($_Mgroupregedscheduledcourses[$i]['courseID']==$_Mcourseid)&&
			   ($_Mgroupregedscheduledcourses[$i]['teacherID']>0) &&
			   (($_Mgroupregedscheduledcourses[$i]['coursepart'][0]=='c')||($_Mgroupregedscheduledcourses[$i]['coursepart'][0]=='w'))) 
			   	return $_Mgroupregedscheduledcourses[$i]['teacherID'];
		}
		else
		{
			if(($_Mgroupregedscheduledcourses[$i]['courseID']==$_Mcourseid)&&
			   ($_Mgroupregedscheduledcourses[$i]['teacherID']>0) &&
			   ($_Mgroupregedscheduledcourses[$i]['coursepart'][0]=='a'))
			   	return $_Mgroupregedscheduledcourses[$i]['teacherID'];			
		}
		//** a can teach t too
		if(($_Mgroupregedscheduledcourses[$i]['courseID']==$_Mcourseid)&&
			   ($_Mgroupregedscheduledcourses[$i]['teacherID']>0))
			   	return $_Mgroupregedscheduledcourses[$i]['teacherID'];
	}

	for($i=0 ; $i<count($GLOBALS['_Mgrouptermcourses']) ; $i++)
	{

		if(($_Mcoursepart[0]=='c')||($_Mcoursepart[0]=='w'))//for not a course part
		{
			if( ($GLOBALS['_Mgrouptermcourses'][$i]['courseID']==$_Mcourseid) &
				($GLOBALS['_Mgrouptermcourses'][$i]['teacherID']>0) &&
			   	(($GLOBALS['_Mgrouptermcourses'][$i]['coursepart'][0]=='c')||($GLOBALS['_Mgrouptermcourses'][$i]['coursepart'][0]=='w')) ) 
			   	return $GLOBALS['_Mgrouptermcourses'][$i]['teacherID'];
		}
		else
		{
			if( ($GLOBALS['_Mgrouptermcourses'][$i]['courseID']==$_Mcourseid) &
				($GLOBALS['_Mgrouptermcourses'][$i]['teacherID']>0) &&
			   	(($GLOBALS['_Mgrouptermcourses'][$i]['coursepart'][0]=='a')) ) 
			   	return $GLOBALS['_Mgrouptermcourses'][$i]['teacherID'];
		}
		//** a can teach t too
		if(($GLOBALS['_Mgrouptermcourses'][$i]['courseID']==$_Mcourseid)&&
			   ($GLOBALS['_Mgrouptermcourses'][$i]['teacherID']>0))
			   	return $GLOBALS['_Mgrouptermcourses'][$i]['teacherID'];			
		
	}
	
	return -1;
}
function _Fstatushastime4slots($_Mtimestatus,$_Mslotcnst) 
{
	$_Mhas=true;
	_Fslottype2slotstypes($_Mslotcnst,$_Mtimeslots,$_Mtimeslotstates);
	for($i=0;$i<count($_Mtimeslots) ; $i++)
	{
		if($_Mtimestatus[$_Mtimeslots[$i]]=='f') continue;
		if(($_Mtimeslotstates[$i]=='s'))//&($_Mtimestatus[$_Mtimeslots[$i]]!='f')) not needed, passing prev line means  not 'f'
		{$_Mhas=false;break;}
		if($_Mtimestatus[$_Mtimeslots[$i]]!=$_Mtimeslotstates[$i])
		{$_Mhas=false;break;}
	}
	return $_Mhas;
}
//check times according to course units
function _Fchecktimestchrgrpcnstr($_Mteacher1,$_Mgroup1,$_Mcourse1,&$_Mpossibletimes)
{
	$_Mdayslots=$GLOBALS['_Mdayslots'];$_Mtotslots=$GLOBALS['_Mtotslots'];
	$_Mcourseunits=$_Mcourse1->getcourseunits();
	$_Mcoursepartunits=$_Mcourse1->getcoursepartunits();
	//if($_Mcourseunits==3) $_Mcourseunits++;//3 units needs 4 free slot
	$_Mteachertime=$_Mteacher1->getteachertimes();
	$_Mgroupstatus=$_Mgroup1->getgroupstatus();
	$_Mfreetimecount=0;
	for($i=0 ; $i<$_Mtotslots ; $i++)
	{
		//checkconstraint4course();
		$_Mttime=$_Mteachertime[$i];$_Mgtime=$_Mgroupstatus[$i];
		//will correct ; p is assumed f
		if($_Mttime=='p') $_Mttime='f';
		//*********************
		
		if(($_Mttime=='f')&($_Mgroupstatus[$i]=='f'))
		  $_Mfreetimecount++;
		if((($_Mttime=='e')&($_Mgtime=='e'))||
		   (($_Mttime=='o')&($_Mgtime=='o'))||
		   (($_Mttime=='f')&($_Mgtime=='o'))||
		   (($_Mttime=='f')&($_Mgtime=='e'))||
		   (($_Mttime=='o')&($_Mgtime=='f'))||
		   (($_Mttime=='e')&($_Mgtime=='f'))//||
		   //(($_Mttime=='p')&($_Mgtime!='s'))//will check other statuses
		   )
			  $_Mfreetimecount+=.5;
	   	$_Mpossibletimes[$i]=_Fsmaller($_Mttime,$_Mgtime);
		
	}
	$_Mcourseremainedtimes=$_Mcourse1->getcourseremainedunits();
	if(($_Mfreetimecount>0)&($_Mfreetimecount<$_Mcourseremainedtimes))//if teacher has free slot but not >= courseunits,act according to a total constraint parameter
	{
		global $_Mpartialteacherschedule;
		if($_Mpartialteacherschedule)
		{  
			array_push($GLOBALS['_Mteachershavenocmpletefreeslots'],array($GLOBALS['_Mgroup1']->getgroupid(),$_Mcourse1->getcourseid(),$_Mcourse1->getcoursecaption(),$_Mteacher1->teacherid,$_Mteacher1->teacherfamily));
			if(($_Mfreetimecount>=$_Mcoursepartunits)&(($_Mcourse1->getcoursepart()=='c1')||($_Mcourse1->getcoursepart()=='a1')))return true;
			else if(($_Mfreetimecount>=$_Mcoursepartunits)&($_Mcourse1->getotherparttid()==$_Mteacher1->getteacherid()))return true;
			else return false;
		}
		else return false;
	}

	else if (($_Mfreetimecount>=$_Mcourseremainedtimes)&&($_Mfreetimecount>0))
	     return true;
	else return false;
}

function _Fgetgroupcourseconstraints($_Mgroup1,&$_Mgroupcourseconstraint)
{
	$_Msqlstr="select * from coursetimeconst where groupID={$_Mgroup1->getgroupid()} 
				and termID={$GLOBALS['_Mactivetermid']}";
	$_Mresult=mysql_query($_Msqlstr);
	while($_Mrow=mysql_fetch_assoc($_Mresult))
	  array_push($_Mgroupcourseconstraint,$_Mrow);
	return;
}

function _Fmeetscourseconst($_Mcourse1,&$_Mpossibletimes,&$_Mschedulabaleslots)
{
	global $_Mfullfree;
	$_Mdayslots=$GLOBALS['_Mdayslots'];$_Mtotslots=$GLOBALS['_Mtotslots'];
	$_Mcourseunits=$_Mcourse1->getcourseunits();
	//if(($_Mcourseunits==3)||($_Mcourseunits==1)) $_Mcourseunits++;//3 units needs 4 free slot
	if($_Mcourse1->getcoursehaspref()) $_Mcoursetime=$_Mcourse1->getcoursepreftimes();
	else $_Mcoursetime=$_Mfullfree;
	$_Mfreetimecount=0;
	for($i=0 ; $i<$_Mtotslots ; $i++)
	{
		if((($_Mcoursetime[$i]==$_Mpossibletimes[$i])&($_Mcoursetime[$i]!='n'))||
		   (($_Mcoursetime[$i]=='f')&(($_Mpossibletimes[$i]=='o')||($_Mpossibletimes[$i]=='e')))||
		   (($_Mpossibletimes[$i]=='f')&(($_Mcoursetime[$i]=='o')||($_Mcoursetime[$i]=='e'))))
		{
		   	$_Mfreetimecount++;
		   	$_Mpossibletimes[$i]=_Fsmaller($_Mpossibletimes[$i],$_Mcoursetime[$i]);
		}
		else $_Mpossibletimes[$i]='n';
	}
	//if ($_Mfreetimecount>=$_Mcourseunits) -- if there isn't enough slot to schedule course but $_Mfreetimecount >0 ,course can be partially scheduled
	if ($_Mfreetimecount>0)
	{   $_Mschedulabaleslots=$_Mfreetimecount; return true;}
	else return false;
}

function _Fscheduleit($_Mteacher1,$_Mcourse1,$_Mpossibletimes,$_Mgroup1,&$_Mprocesslogs,$_Massigntype)
{
  $_Mtimeslots=array();
  $_Mtimeslotstate=array();//full scheduled or periodic
	//*****will update to be optimized selecting from final possible slots 
	$_Mcourseunits=$_Mcourse1->getcourseunits();
	$_Mcoursepart=$_Mcourse1->getcoursepart();
	//$currentparttime=_Fgetcurrentparttime($_Mcoursepart);

	if(($_Mcourseunits==1))
	{
		_Fschedul4oneu($_Mteacher1,$_Mcourse1,$_Mcoursepart,$_Mgroup1,$_Mpossibletimes,$_Mtimeslots,$_Mtimeslotstate,$_Massigntype);
	}
	if($_Mcoursepart[0]=='c')
    	_Fschedul4newc($_Mteacher1,$_Mcourse1,$_Mgroup1,$_Mpossibletimes,$_Mtimeslots,$_Mtimeslotstate,$_Massigntype);
	else if($_Mcoursepart[0]=='a')
    	_Fschedul4newc($_Mteacher1,$_Mcourse1,$_Mgroup1,$_Mpossibletimes,$_Mtimeslots,$_Mtimeslotstate,$_Massigntype);
	else if($_Mcoursepart[0]=='w')
    	_Fschedul4neww($_Mteacher1,$_Mcourse1,$_Mgroup1,$_Mpossibletimes,$_Mtimeslots,$_Mtimeslotstate,$_Massigntype);
    	

/*
	else if($_Mcourseunits==2)
	{
		_Fschedul4twou($_Mteacher1,$_Mcourse1,$_Mgroup1,$_Mpossibletimes,$_Mtimeslots,$_Mtimeslotstate,$_Massigntype);
	}
	else if($_Mcourseunits==3)
	{
		_Fschedul4threeu($_Mteacher1,$_Mcourse1,$_Mgroup1,$_Mpossibletimes,$_Mtimeslots,$_Mtimeslotstate,$_Massigntype);
	}
*/
	if(count($_Mtimeslots)==0)
	{
		array_push($GLOBALS['_Mprocesslogs'],"*****coudn't be scheduled course : {$_Mcourse1->getcoursecaption()}");
		return 0;
	}
	//if(count($_Mtimeslots)<$_Mcourseunits)
	 // {_Fcreatereport("not completely scheduled",$GLOBALS['_Mgroup1']->getgroupid(),$_Mcourse1->getcourseid(),$_Mcourse1->getcoursecaption(),$_Mteacher1->teacherid,$_Mteacher1->teachername);}
	
	_Fupdatecourseteachergroup($_Mcourse1,$_Mteacher1,$_Mgroup1,$_Mtimeslots,$_Mtimeslotstate,$_Massigntype);
	_Fcreatereport("scheduled : ",$GLOBALS['_Mgroup1']->getgroupid(),$_Mcourse1->getcourseid(),$_Mcourse1->getcoursecaption(),$_Mteacher1->teacherid,$_Mteacher1->teacherfamily,$_Mcourse1->getcoursepart());
	return 1;
}
function _Fgetcurrentparttime($_Mcoursepart)
{
	if($_Mcoursepart[0]=='c') return 2;
	if($_Mcoursepart[0]=='a') return 1;
	if($_Mcoursepart[0]=='w') return 1;
}
function _Fschedul4newc($_Mteacher1,$_Mcourse1,$_Mgroup1,$_Mpossibletimes,&$_Mtimeslots,&$_Mtimeslotstate,$_Massigntype)
{
	$sl=$GLOBALS['_Mslotlen'];$_Mtimeslots=array();$_Mtimeslotstate=array();
	$_Mdayslots=$GLOBALS['_Mdayslots'];$_Mtotslots=$GLOBALS['_Mtotslots'];
	$_Motherpart1slots=explode(",",$_Mcourse1->getotherpart1slots());
	$fsod1=-1;$fsod2=-1;
	if($_Motherpart1slots[0]!='')
		$fsod1=_Ffirstslotofday($_Motherpart1slots[0]);
	$_Motherpart2slots=explode(",",$_Mcourse1->getotherpart2slots());
	if($_Motherpart2slots[0]!='')
		$fsod2=_Ffirstslotofday($_Motherpart2slots[0]);
	//con	
	if($_Mcourse1->getcoursecnc())
	{
		for($i=$_Motherpart1slots[0] ; $i<($_Mtotslots-1) ; $i+=$sl)
		{
			$st=_Fgetslottype($_Mpossibletimes,$i);
			if($st=='f')
			{
				if(_Fgroupcheckmaxslotsperday($_Mgroup1,$i,2))
				{
					_Fsetslotstypes($_Mtimeslots,$_Mtimeslotstate,$i,'s');
					_Fsettimes($_Mpossibletimes,$i,'s');
					$_Mgroup1->updatestatus($_Mtimeslots,$_Mtimeslotstate);
					return;
				}
			}
		}
	}	
		
//next 2 days space between ccourse parts : 2 days
//will optimize : if today is the day befor next part,move to next 3 day
	for($i=0 ; $i<($_Mtotslots-1) ; $i+=$sl)
	{
		if(($fsod1!=-1)&($i==$fsod1))
		{$i=_Fgotonextday($i);$i=_Fgotonextday($i);}
		if(($fsod2!=-1)&($i==$fsod2))
		{$i=_Fgotonextday($i);$i=_Fgotonextday($i);}
			
		$st=_Fgetslottype($_Mpossibletimes,$i);
		if($st=='f')
		{
			if(_Fgroupcheckmaxslotsperday($_Mgroup1,$i,2))
			{
				_Fsetslotstypes($_Mtimeslots,$_Mtimeslotstate,$i,'s');
				_Fsettimes($_Mpossibletimes,$i,'s');
				$_Mgroup1->updatestatus($_Mtimeslots,$_Mtimeslotstate);
				return;
			}
		}
	}
//only next day	

	for($i=0 ; $i<($_Mtotslots-1) ; $i+=$sl)
	{
		if(($fsod1!=-1)&($i==$fsod1))
		{$i=_Fgotonextday($i);}
		if(($fsod2!=-1)&($i==$fsod2))
		{$i=_Fgotonextday($i);}
			
		$st=_Fgetslottype($_Mpossibletimes,$i);
		if($st=='f')
		{
			if(_Fgroupcheckmaxslotsperday($_Mgroup1,$i,2))
			{
				_Fsetslotstypes($_Mtimeslots,$_Mtimeslotstate,$i,'s');
				_Fsettimes($_Mpossibletimes,$i,'s');
				$_Mgroup1->updatestatus($_Mtimeslots,$_Mtimeslotstate);
				return;
			}
		}
	}
	
//schedule in 1 day
	for($i=$fsod1 ; $i<$fsod1+$_Mdayslots ; $i+=$sl)
	{		
		$st=_Fgetslottype($_Mpossibletimes,$i);
		if($st=='f')
		{
			if(_Fgroupcheckmaxslotsperday($_Mgroup1,$i,2))
			{
				_Fsetslotstypes($_Mtimeslots,$_Mtimeslotstate,$i,'s');
				_Fsettimes($_Mpossibletimes,$i,'s');
				$_Mgroup1->updatestatus($_Mtimeslots,$_Mtimeslotstate);
				_Fcreatereport("in 1 day",$_Mgroup1->getgroupid(),$_Mcourse1->getcourseid(),$_Mcourse1->getcoursecaption(),$_Mteacher1->teacherid,$_Mteacher1->teachername.' '.$_Mteacher1->teacherfamily,$_Mcourse1->getcoursepart());
				return;
			}
		}
	}
	for($i=$fsod2 ; $i<$fsod2+$_Mdayslots ; $i+=$sl)
	{		
		$st=_Fgetslottype($_Mpossibletimes,$i);
		if($st=='f')
		{
			if(_Fgroupcheckmaxslotsperday($_Mgroup1,$i,2))
			{
				_Fsetslotstypes($_Mtimeslots,$_Mtimeslotstate,$i,'s');
				_Fsettimes($_Mpossibletimes,$i,'s');
				$_Mgroup1->updatestatus($_Mtimeslots,$_Mtimeslotstate);
				_Fcreatereport("in 1 day",$_Mgroup1->getgroupid(),$_Mcourse1->getcourseid(),$_Mcourse1->getcoursecaption(),$_Mteacher1->teacherid,$_Mteacher1->teachername.' '.$_Mteacher1->teacherfamily,$_Mcourse1->getcoursepart());
				return;
			}
		}
	}

	
}
function _Fschedul4neww($_Mteacher1,$_Mcourse1,$_Mgroup1,$_Mpossibletimes,&$_Mtimeslots,&$_Mtimeslotstate,$_Massigntype)
{
	$sl=$GLOBALS['_Mslotlen'];$_Mtimeslots=array();$_Mtimeslotstate=array();
	$_Mdayslots=$GLOBALS['_Mdayslots'];$_Mtotslots=$GLOBALS['_Mtotslots'];
	$_Motherpart1slots=explode(",",$_Mcourse1->getotherpart1slots());
	$fsod1=-1;$fsod2=-1;
	if($_Motherpart1slots[0]!='')
		$fsod1=_Ffirstslotofday($_Motherpart1slots[0]);
	$_Motherpart2slots=explode(",",$_Mcourse1->getotherpart2slots());
	if($_Motherpart2slots[0]!='')
//next 2 days space between ccourse parts : 2 days
		$fsod2=_Ffirstslotofday($_Motherpart2slots[0]);

	for($i=0 ; $i<($_Mtotslots-1) ; $i+=$sl)
	{
		if(($fsod1!=-1)&($i==$fsod1))
		{$i=_Fgotonextday($i);$i=_Fgotonextday($i);}
		if(($fsod2!=-1)&($i==$fsod2))
		{$i=_Fgotonextday($i);$i=_Fgotonextday($i);}
			
		$st=_Fgetslottype($_Mpossibletimes,$i);
		if($st=='f')
		{
			if(_Fgroupcheckmaxslotsperday($_Mgroup1,$i,1))
			{
				_Fsetslotstypes($_Mtimeslots,$_Mtimeslotstate,$i,'o');//if full free,odd is selected
				_Fsettimes($_Mpossibletimes,$i,'o');
				$_Mgroup1->updatestatus($_Mtimeslots,$_Mtimeslotstate);
				return;
			}
		}
		else if(($st=='o')||($st=='e'))
		{
			if(_Fgroupcheckmaxslotsperday($_Mgroup1,$i,1))
			{
				_Fsetslotstypes($_Mtimeslots,$_Mtimeslotstate,$i,$st);
				_Fsettimes($_Mpossibletimes,$i,'s');
				$_Mgroup1->updatestatus($_Mtimeslots,$_Mtimeslotstate);
				return;
			}
		}

	}
//only next day	

	for($i=0 ; $i<($_Mtotslots-1) ; $i+=$sl)
	{
		if(($fsod1!=-1)&($i==$fsod1))
			$i=_Fgotonextday($i);
		if(($fsod2!=-1)&($i==$fsod2))
			$i=_Fgotonextday($i);
			
		$st=_Fgetslottype($_Mpossibletimes,$i);
		if($st=='f')
		{
			if(_Fgroupcheckmaxslotsperday($_Mgroup1,$i,1))
			{
				_Fsetslotstypes($_Mtimeslots,$_Mtimeslotstate,$i,'o');//if full free,odd is selected
				_Fsettimes($_Mpossibletimes,$i,'o');
				$_Mgroup1->updatestatus($_Mtimeslots,$_Mtimeslotstate);
				return;
			}
		}
		else if(($st=='o')||($st=='e'))
		{
			if(_Fgroupcheckmaxslotsperday($_Mgroup1,$i,1))
			{
				_Fsetslotstypes($_Mtimeslots,$_Mtimeslotstate,$i,$st);
				_Fsettimes($_Mpossibletimes,$i,'s');
				$_Mgroup1->updatestatus($_Mtimeslots,$_Mtimeslotstate);
				return;
			}
		}

	}
	
//schedule in 1 day
	
	for($i=$fsod1 ; $i<$fsod1+$_Mdayslots ; $i+=$sl)
	{		
		$st=_Fgetslottype($_Mpossibletimes,$i);
		if($st=='f')
		{
			if(_Fgroupcheckmaxslotsperday($_Mgroup1,$i,1))
			{
				_Fsetslotstypes($_Mtimeslots,$_Mtimeslotstate,$i,'o');//if full free,odd is selected
				_Fsettimes($_Mpossibletimes,$i,'o');
				$_Mgroup1->updatestatus($_Mtimeslots,$_Mtimeslotstate);
				_Fcreatereport("in 1 day",$_Mgroup1->getgroupid(),$_Mcourse1->getcourseid(),$_Mcourse1->getcoursecaption(),$_Mteacher1->teacherid,$_Mteacher1->teachername.' '.$_Mteacher1->teacherfamily,$_Mcourse1->getcoursepart());
				return;
			}
		}
		else if(($st=='o')||($st=='e'))
		{
			if(_Fgroupcheckmaxslotsperday($_Mgroup1,$i,1))
			{
				_Fsetslotstypes($_Mtimeslots,$_Mtimeslotstate,$i,$st);
				_Fsettimes($_Mpossibletimes,$i,'s');
				$_Mgroup1->updatestatus($_Mtimeslots,$_Mtimeslotstate);
				_Fcreatereport("in 1 day",$_Mgroup1->getgroupid(),$_Mcourse1->getcourseid(),$_Mcourse1->getcoursecaption(),$_Mteacher1->teacherid,$_Mteacher1->teachername.' '.$_Mteacher1->teacherfamily,$_Mcourse1->getcoursepart());
				return;				
			}
		}		
	}
	for($i=$fsod2 ; $i<$fsod2+$_Mdayslots ; $i+=$sl)
	{		
		$st=_Fgetslottype($_Mpossibletimes,$i);
		if($st=='f')
		{
			if(_Fgroupcheckmaxslotsperday($_Mgroup1,$i,1))
			{
				_Fsetslotstypes($_Mtimeslots,$_Mtimeslotstate,$i,'o');//if full free,odd is selected
				_Fsettimes($_Mpossibletimes,$i,'o');
				$_Mgroup1->updatestatus($_Mtimeslots,$_Mtimeslotstate);
				_Fcreatereport("in 1 day",$_Mgroup1->getgroupid(),$_Mcourse1->getcourseid(),$_Mcourse1->getcoursecaption(),$_Mteacher1->teacherid,$_Mteacher1->teachername.' '.$_Mteacher1->teacherfamily,$_Mcourse1->getcoursepart());
				return;
			}
		}
		else if(($st=='o')||($st=='e'))
		{
			if(_Fgroupcheckmaxslotsperday($_Mgroup1,$i,1))
			{
				_Fsetslotstypes($_Mtimeslots,$_Mtimeslotstate,$i,$st);
				_Fsettimes($_Mpossibletimes,$i,'s');
				$_Mgroup1->updatestatus($_Mtimeslots,$_Mtimeslotstate);
				_Fcreatereport("in 1 day",$_Mgroup1->getgroupid(),$_Mcourse1->getcourseid(),$_Mcourse1->getcoursecaption(),$_Mteacher1->teacherid,$_Mteacher1->teachername.' '.$_Mteacher1->teacherfamily,$_Mcourse1->getcoursepart());
				return;				
			}
		}		
	}
	
	
	
}

function _Fschedul4oneu($_Mteacher1,$_Mcourse1,$_Mcoursepart,$_Mgroup1,$_Mpossibletimes,$_Mtimeslots,$_Mtimeslotstate,$_Massigntype)
{
	$sl=$GLOBALS['_Mslotlen'];$_Mtimeslots=array();$_Mtimeslotstate=array();
	$_Mdayslots=$GLOBALS['_Mdayslots'];$_Mtotslots=$GLOBALS['_Mtotslots'];
		for($i=0 ; $i<($_Mtotslots-1) ; $i+=$sl)
		{
			$st=_Fgetslottype($_Mpossibletimes,$i);
			if($st=='f')
			{
				if(_Fgroupcheckmaxslotsperday($_Mgroup1,$i,1))
				{
					if($_Mcoursepart[0]!='a') $_Mtype='o';else $_Mtype='s';
					_Fsetslotstypes($_Mtimeslots,$_Mtimeslotstate,$i,$_Mtype);
					// from full free times,odd slots will be assigned for 1unit courses
					_Fsettimes($_Mpossibletimes,$i,$_Mtype);
					$_Mgroup1->updatestatus($_Mtimeslots,$_Mtimeslotstate);
					
					break;
				}
			}
			if((($st=='o')||($st=='e')) & ($_Mcoursepart[0]!='a'))
			{
				if(_Fgroupcheckmaxslotsperday($_Mgroup1,$i,1))
				{
					_Fsetslotstypes($_Mtimeslots,$_Mtimeslotstate,$i,$_Mpossibletimes[$i]);
					_Fsettimes($_Mpossibletimes,$i,'s');
					$_Mgroup1->updatestatus($_Mtimeslots,$_Mtimeslotstate);
					//_Fupdateassignmentarray();
					break;
				}
			}
		}
}

function _Fschedul4twou($_Mteacher1,$_Mcourse1,$_Mgroup1,$_Mpossibletimes,&$_Mtimeslots,&$_Mtimeslotstate,$_Massigntype)
{
	$sl=$GLOBALS['_Mslotlen'];$_Mtimeslots=array();$_Mtimeslotstate=array();
	$_Mdayslots=$GLOBALS['_Mdayslots'];$_Mtotslots=$GLOBALS['_Mtotslots'];
		for($i=0 ; $i<($_Mtotslots-1) ; $i+=$sl)
		{
			$st=_Fgetslottype($_Mpossibletimes,$i);
			if($st=='f')
			{
				if(_Fgroupcheckmaxslotsperday($_Mgroup1,$i,2))
				{
					_Fsetslotstypes($_Mtimeslots,$_Mtimeslotstate,$i,'s');
					_Fsettimes($_Mpossibletimes,$i,'s');
					$_Mgroup1->updatestatus($_Mtimeslots,$_Mtimeslotstate);
					break;
				}
			}
		}
}
function _Fschedul4threeu($_Mteacher1,$_Mcourse1,$_Mgroup1,&$_Mpossibletimes,&$_Mtimeslots,&$_Mtimeslotstate,$_Massigntype)
{
	$sl=$GLOBALS['_Mslotlen'];$_Mtimeslots=array();$_Mtimeslotstate=array();
	$_Mdayslots=$GLOBALS['_Mdayslots'];$_Mtotslots=$GLOBALS['_Mtotslots'];
		$_Mscheduledslots=0;$_Mfullpartselected=0;$_Mhalfpartselected=0;$st='';
		for($i=0 ; $i<($_Mtotslots-1) ; $i+=$sl)
		{
			$st=_Fgetslottype($_Mpossibletimes,$i);
			if(($st!=-1))
			{
				if(!($_Mfullpartselected))
				{
					if($st=='f')
					{
						if(_Fgroupcheckmaxslotsperday($_Mgroup1,$i,2))
						{
							_Fselectslot($_Mgroup1,$_Mpossibletimes,$_Mtimeslots,$_Mtimeslotstate,$i,'s','s');
							$_Mfullpartselected=1;$_Mscheduledslots+=2;
							if($_Mscheduledslots==4) break;
							$i=_Fgotonextday($i)-$sl;continue;
						}
					}
				}
				if(!($_Mhalfpartselected))
				{
					if($st=='f')
					{
						if(_Fgroupcheckmaxslotsperday($_Mgroup1,$i,2))
						{
							_Fselectslot($_Mgroup1,$_Mpossibletimes,$_Mtimeslots,$_Mtimeslotstate,$i,'o','e');
							$_Mfullpartselected=1;$_Mscheduledslots+=2;
							if($_Mscheduledslots==4) break;
							$i=_Fgotonextday($i)-$sl;
						}
					}
					else if(($st=='e') || ($st=='o'))
					{
						if(_Fgroupcheckmaxslotsperday($_Mgroup1,$i,1))
						{
							_Fselectslot($_Mgroup1,$_Mpossibletimes,$_Mtimeslots,$_Mtimeslotstate,$i,$_Mpossibletimes[$i],'s');
							$_Mhalfpartselected=1;$_Mscheduledslots+=2;
							if($_Mscheduledslots==4) break;
							$i=_Fgotonextday($i)-$sl;
						}
					}
				}
			}
		}
		if($_Mscheduledslots!=4)//full and half slots should be located in 1 day
		{
			for($i=_Ffirstslotofday($_Mtimeslots[0]) ; $i<($_Mtotslots-1) ; $i+=$sl)
			{
				if($_Mscheduledslots==4) break;
				$st=_Fgetslottype($_Mpossibletimes,$i);
				if(($st!=-1)& ($st!='s'))
				{				
					if(!($_Mfullpartselected))
					{
						if($st=='f')
						{
							if(_Fgroupcheckmaxslotsperday($_Mgroup1,$i,2))
							{
								_Fselectslot($_Mgroup1,$_Mpossibletimes,$_Mtimeslots,$_Mtimeslotstate,$i,'s','s');
								$_Mscheduledslots+=2;
								break;
							}
						}	
					}					
					else if((!($_Mhalfpartselected)))
					{				
						if(($st=='f')||($st=='o')||($st=='e'))
						{
							if(_Fgroupcheckmaxslotsperday($_Mgroup1,$i,1))
							{
								$_Mtmpslottype=$st;
								if($st=='f') $_Mtmpslottype='o';//if a slot is free, o is selected for half ones
								_Fselectslot($_Mgroup1,$_Mpossibletimes,$_Mtimeslots,$_Mtimeslotstate,$i,$_Mtmpslottype,'s');
								$_Mscheduledslots+=2;
								break;
							}
						}
					}
				}
			}
			if($_Mscheduledslots==4)
				_Fcreatereport("in 1 day",$_Mgroup1->getgroupid(),$_Mcourse1->getcourseid(),$_Mcourse1->getcoursecaption(),$_Mteacher1->teacherid,$_Mteacher1->teachername.' '.$_Mteacher1->teacherfamily,$_Mcourse1->getcoursepart());
		}
	
}
function _Fselectslot(&$_Mgroup1,&$_Mpossibletimes,&$_Mtimeslots,&$_Mtimeslotstate,$i,$_Mslottype,$postype)
{
	_Fsetslotstypes($_Mtimeslots,$_Mtimeslotstate,$i,$_Mslottype);
	_Fsettimes($_Mpossibletimes,$i,$postype);//if a time is fully free, odd slots will be assigned this can be change
	$_Mgroup1->updatestatus($_Mtimeslots,$_Mtimeslotstate);
}
function _Fslottypeis($_Mtimes,$_Mslotid,$_Mslottype)
{//later check for double slot checking(i,i+1)
	if(($_Mtimes[$_Mslotid]==$_Mtimes[$_Mslotid+1])&($_Mtimes[$_Mslotid]==$_Mslottype))
	return true;
	else return false;
}
function _Fgetslottype($_Mtimes,$_Mno)
{
	$_Mdayslots=$GLOBALS['_Mdayslots'];$_Mtotslots=$GLOBALS['_Mtotslots'];
	if($_Mno<0 || $_Mno>($_Mtotslots-1)) return -1;
	if(($_Mtimes[$_Mno]==$_Mtimes[$_Mno+1])&($_Mtimes[$_Mno]!='n')) return $_Mtimes[$_Mno];
	else return -1;
}

function _Fsetslotstypes(&$_Mtimeslots,&$_Mtimeslotstate,$_Mno,$_Mtype)
{
	array_push($_Mtimeslots,$_Mno,$_Mno+1);
	array_push($_Mtimeslotstate,$_Mtype,$_Mtype);
}
function _Fsettimes(&$_Mtimes,$_Mno,$_Mtype)
{
	$_Mtimes[$_Mno]=$_Mtype;$_Mtimes[$_Mno+1]=$_Mtype;
}
function _Fgotonextday($_Mno)
{
	$_Mdayslots=$GLOBALS['_Mdayslots'];
	return (((int)($_Mno / $_Mdayslots)+1)*$_Mdayslots);
}
function _Ffirstslotofday($_Mno)
{
	$_Mdayslots=$GLOBALS['_Mdayslots'];
	return (((int)($_Mno/$_Mdayslots)) *$_Mdayslots);
}
function _Fgroupcheckmaxslotsperday($_Mgroup1,$_Mno,$_Munits)
{
	if(!($GLOBALS['_Mcheckgroupsmaxslotsperdaycnst'])) return true;
	$_Mgroupmspd=$_Mgroup1->getgroupsmaxslotsperday();
	if($_Mgroupmspd>0)
	{
		$_Mcurslotsperthisday=0;
		$_Mfromslot=_Ffirstslotofday($_Mno);$_Mtoslot=_Fgotonextday($_Mno)-1;
		$_Mtimes=$_Mgroup1->getgroupstatus();
		$inittimes=$_Mgroup1->getinitialgroupstatus();
		for($i=$_Mfromslot ; $i<=$_Mtoslot ;$i+=2)
		{
			if($_Mtimes[$i]=='s') $_Mcurslotsperthisday+=2;
			else if((($_Mtimes[$i]=='e')||($_Mtimes[$i]=='o'))&($_Mtimes[$i]!=$inittimes[$i])) $_Mcurslotsperthisday++;
		}
		if(($_Mcurslotsperthisday+$_Munits)<=$_Mgroupmspd) return true;
		else return false;
	}else return true;
	
}
//****************************
function _Fupdatecourseteachergroup($_Mcourse1,$_Mteacher1,$_Mgroup1,$_Mtimeslots,$_Mtimeslotstate,$_Massigntype)
{
	if($_Mcourse1->getcourseid()!=0) _Fupdatecoursestatus($_Mcourse1,$_Mgroup1->getgroupid(),$_Mtimeslots,$_Mtimeslotstate);
 	if($_Mteacher1->getteacherid()!=0) _Fupdateteachertimes($_Mteacher1,$_Mgroup1->getgroupid(),$_Mtimeslots,$_Mtimeslotstate,$_Mcourse1->getcoursepartunits());
	_Fupdategrouptimes($_Mgroup1,$_Mcourse1->getcourseunits());
	_Fupdateassignments($_Mcourse1,$_Mteacher1,$_Mgroup1,$_Mtimeslots,$_Mtimeslotstate,$_Massigntype);
	_Fupdateassignmentarray($_Mcourse1,$_Mteacher1->teacherid,$_Mgroup1);
	_Fupdateotg($_Mcourse1,$_Mgroup1->getgroupid(),$_Mtimeslots,$_Mtimeslotstate,$_Mcourse1->getcoursepartunits());
}

function _Fupdatecoursestatus($_Mcourse1,$_Mgroupid,$_Mtimeslots,$_Mtimeslotstate)
{
	global $_Mprocesslogs;
	$_Msqlstr="update termcoursestatus set coursestatus='s' where courseID={$_Mcourse1->getcourseid()} 
					and termID={$GLOBALS['_Mactivetermid']} and ((groupidconf1=$_Mgroupid AND awgroupID=0) OR (awgroupID=$_Mgroupid) OR (groupidconf2=$_Mgroupid) OR (groupidconf3=$_Mgroupid)) and termID={$GLOBALS['_Mactivetermid']}";// and courseID not in(select courseID from termcoursestatus where ((groupidconf1=$_Mgroupid AND awgroupID=0) OR (awgroupID=$_Mgroupid) OR (groupidconf2=$_Mgroupid) OR (groupidconf3=$_Mgroupid))";
	if(!(mysql_query($_Msqlstr)))
	{
		global $_Merrorsarray;
		array_push($_Merrorsarray,"course : {$_Mcourse1->getcourseid()} : {$_Mcourse1->getcoursecaption()} at time: $_Mtimeslots ,status couldnt be updated!");
		_Fcreatereport("course status couldn't be updated",$_Mgroupid,$_Mcourse1->getcourseid(),$_Mcourse1->getcoursecaption(),"","",$_Mcourse1->getcoursepart());// $_Mprocesslogs,"course : {$_Mcourse1->getcourseid()} : {$_Mcourse1->getcoursecaption()} at time: $_Mtimeslots ,status couldnt be updated!");

	}
	$_Msqlstr="select otherpart1slots,otherpart2slots,coursepart from termcoursestatus where courseID={$_Mcourse1->getcourseid()} 
					and termID={$GLOBALS['_Mactivetermid']} and coursepart<>'{$_Mcourse1->getcoursepart()}' and groupidconf1={$_Mgroupid}";
	if($_Mresult=mysql_query($_Msqlstr))
	{
		if($_Mrow=mysql_fetch_array($_Mresult))
		{
			$_Mtimestr=_Ftimeslotsarray2timestr($_Mtimeslots,$_Mtimeslotstate);
			if($_Mrow['otherpart1slots']=='')
			{
				$_Msqlstr="update termcoursestatus set otherpart1slots='$_Mtimestr' where courseID={$_Mcourse1->getcourseid()} 
					and termID={$GLOBALS['_Mactivetermid']} and groupidconf1={$_Mgroupid}";
				mysql_query($_Msqlstr);
				_Fupdategroupcoursesarray($_Mcourse1,'otherpart1slots',$_Mtimestr);
			}
			else if($_Mrow['otherpart2slots']=='')
			{
				$_Msqlstr="update termcoursestatus set otherpart2slots='$_Mtimestr' where courseID={$_Mcourse1->getcourseid()} 
					and termID={$GLOBALS['_Mactivetermid']} and groupidconf1={$_Mgroupid}";
				mysql_query($_Msqlstr);
				_Fupdategroupcoursesarray($_Mcourse1,'otherpart1slots',$_Mtimestr);
			}
			/*  for courses more than 3 parts
			else if($_Mrow['otherpart3slots']=='')
			{
				$_Msqlstr="update termcoursestatus set otherpart3slots='$_Mtimestr' where courseID={$_Mcourse1->getcourseid()} 
					and termID={$GLOBALS['_Mactivetermid']} and groupidconf1={$_Mgroupid}";
				mysql_query($_Msqlstr);
				_Fupdategroupcoursesarray($_Mcourse1,'otherpart3slots',$_Mtimestr);
			}
			*/
			
		}
	}
	
}

function _Fupdategroupcoursesarray($_Mcourse1,$_Mfieldname,$_Mtimestr)
{
	for($i=0 ; $i<count($GLOBALS['_Mgrouptermcourses']) ; $i++)
	{
		if( ($GLOBALS['_Mgrouptermcourses'][$i]['courseID']==$_Mcourse1->getcourseid()) &
			($GLOBALS['_Mgrouptermcourses'][$i]['coursepart']!=$_Mcourse1->getcoursepart()) )
					$GLOBALS['_Mgrouptermcourses'][$i][$_Mfieldname]=$_Mtimestr;
	}
}
function _Fupdateteachertimes($_Mteacher1,$_Mgroupid,$_Mtimeslots,$_Mtimeslotstate,$_Mcourseunits)
{
	$_Mtimesstr=$_Mteacher1->getteachertimes();
	$j=0;$_Mcoursepartunits=0;
	if(($_Mtimeslotstate[0]=='e')||($_Mtimeslotstate[0]=='o')) $_Mcoursepartunits=1;
	else $_Mcoursepartunits=2;
	
	foreach ($_Mtimeslots as &$id)
	{  
		if($_Mtimesstr[$id]=='f')
			$_Mtimesstr[$id]=_Fcomplement($_Mtimeslotstate[$j++]);
	   else 
	   		$_Mtimesstr[$id]='s';
	}
	$_Mmgroupid=_Fgetmastergroup($_Mgroupid,$GLOBALS['_Mactivetermid']);
	$_Mteacher1->saveteachertimestodb($_Mmgroupid,$_Mtimesstr,$_Mcourseunits,$GLOBALS['_Mactivetermid']);
	if(isset($GLOBALS['_Mgroupteachers']))
	{
		for($i=0 ; $i<count($GLOBALS['_Mgroupteachers']) ; $i++)
		{
			if($GLOBALS['_Mgroupteachers'][$i]['teacherID']==$_Mteacher1->getteacherid())
			{$GLOBALS['_Mgroupteachers'][$i]['times']=$_Mtimesstr;$GLOBALS['_Mgroupteachers'][$i]['teachercurslots']+=$_Mcoursepartunits;break;}
		}
	}
}

function _Fupdategrouptimes($_Mgroup1,$_Munits)
{
	$_Mtimesstr=$_Mgroup1->getgroupstatus();

	/* while selecting course slots setted
	$j=0;
	foreach ($_Mtimeslots as &$id)
	{
		if($_Mtimesstr[$id]=='f')
			$_Mtimesstr[$id]=_Fcomplement($_Mtimeslotstate[$j++]);
		else	
			$_Mtimesstr[$id]='s';
	}*/
	$_Mgroup1->savegrouptimescurunitstodb($_Mtimesstr,$_Munits,$GLOBALS['_Mactivetermid']);
}
function _Fupdateotg($_Mcourse1,$_Mgroupid,$_Mtimeslots,$_Mtimeslotstate,$_Mcoursepartunits)
{
	if($_Mcourse1->getgroupidconf2()!=0 && $_Mcourse1->getgroupidconf2()!=$_Mgroupid)
		_Fupdatecong($_Mcourse1->getgroupidconf2(),$_Mtimeslots,$_Mtimeslotstate,$_Mcoursepartunits);
	if($_Mcourse1->getgroupidconf3()!=0 && $_Mcourse1->getgroupidconf3()!=$_Mgroupid)
		_Fupdatecong($_Mcourse1->getgroupidconf3(),$_Mtimeslots,$_Mtimeslotstate,$_Mcoursepartunits);
	if($_Mcourse1->getcoursegroupidconf()!=$_Mgroupid)
		_Fupdatecong($_Mcourse1->getcoursegroupidconf(),$_Mtimeslots,$_Mtimeslotstate,$_Mcoursepartunits);
		
}
function _Fupdatecong($_Mgidc,$_Mtimeslots,$_Mtimeslotstate,$_Mcoursepartunits)
{
	$_Mgrouptemp=new _CLgroup();
	$_Msqlstr="select * from groupstatus where groupID=$_Mgidc and termID={$GLOBALS['_Mactivetermid']}";
	if($_Mresult=mysql_query($_Msqlstr))
	{
		if(mysql_num_rows($_Mresult)>0)
		{
			$_Mrow=mysql_fetch_array($_Mresult);
			$_Mgrouptemp->setgroupid($_Mgidc);
			$_Mgrouptemp->setgroupstatus($_Mrow['groupstatus']);
			$_Mgrouptemp->updatestatus($_Mtimeslots,$_Mtimeslotstate);
			$_Mgrouptemp->savegrouptimescurunitstodb($_Mgrouptemp->getgroupstatus(),$_Mcoursepartunits,$GLOBALS['_Mactivetermid']);
			$i=0;
			while(($i<count(($GLOBALS['_Mgroups'][$i])))&&($GLOBALS['_Mgroups'][$i]['groupID']!=$_Mgidc)) $i++;
			
			$GLOBALS['_Mgroups'][$i]['groupstatus']=$_Mgrouptemp->getgroupstatus();
		}
	}else return -1;
}

function _Fupdateassignments($_Mcourse1,$_Mteacher1,$_Mgroup1,$_Mtimeslots,$_Mtimeslotstate,$_Massigntype)
{
	$_Mtimestr=_Ftimeslotsarray2timestr($_Mtimeslots,$_Mtimeslotstate);
	$_Mtimedate1=new _CLmytimedate();
	@$_Mtimedate1->setjdate();
	$_Msqlstr="insert into assignments(termID,groupID,groupcaption,maingroupID,awgroupID,teacherID,teachername,teacherfamily,courseID,coursecaption,coursepart,courseunits,timeslots,asgndate,asgntype,asgnuserid) values
				('{$GLOBALS['_Mactivetermid']}','{$_Mcourse1->getcoursegroupidconf()}','{$_Mgroup1->groupcaption}','{$_Mcourse1->getcoursemaingroupid()}','{$_Mcourse1->getschwithgroupid()}','{$_Mteacher1->teacherid}','{$_Mteacher1->teachername}','{$_Mteacher1->teacherfamily}',
				'{$_Mcourse1->getcourseid()}','{$_Mcourse1->getcoursecaption()}','{$_Mcourse1->getcoursepart()}','{$_Mcourse1->getcourseunits()}','$_Mtimestr','{$_Mtimedate1->jdate}','$_Massigntype','testuser')";//will change with real user
	mysql_query($_Msqlstr);
}
function _Fupdateassignmentarray($_Mcourse1,$_Mteacherid,$_Mgroup1)
{
	$_Mupdated=0;
	/*remarked at 880716
	for($i=0 ; $i<count($GLOBALS['_Mgroupregedscheduledcourses']) ; $i++)
	{
		if( ($GLOBALS['_Mgrouptermcourses'][$i]['courseID']==$_Mcourse1->getcourseid()) &
			($GLOBALS['_Mgrouptermcourses'][$i]['coursepart']==$_Mcourse1->getcoursepart()) )
			{
				$GLOBALS['_Mgrouptermcourses'][$i]['teacherID']=$_Mteacherid;
				$_Mupdated=1;break;
			}
	}
	*/
	//inserted at 880716
	for($i=0 ; $i<count($GLOBALS['_Mgrouptermcourses']) ; $i++)
	{
		if( ($GLOBALS['_Mgrouptermcourses'][$i]['courseID']==$_Mcourse1->getcourseid()) &
			($GLOBALS['_Mgrouptermcourses'][$i]['coursepart']==$_Mcourse1->getcoursepart()) )
			{
				$GLOBALS['_Mgrouptermcourses'][$i]['teacherID']=$_Mteacherid;
				$_Mupdated=1;break;
			}
	}

	if(!($_Mupdated))
	{
		$newrow=array();
		$newrow=$GLOBALS['_Mgrouptermcourses'][0];
		
		
		
	}
}
function _Ftimeslotsarray2timestr($_Mtimeslots,$_Mtimeslotstate)
{
	$_Mtimestr="";$j=0;
	foreach ($_Mtimeslots as &$id)
	{if($id<10) $_Mtimestr.='0'.$id.':'.$_Mtimeslotstate[$j++].',';
	 else $_Mtimestr.=$id.':'.$_Mtimeslotstate[$j++].',';}
	 $_Mtimestr=substr($_Mtimestr,0,strlen($_Mtimestr)-1);//will test
	 return $_Mtimestr;	
}
function _Fispossibleins($_Mstatus,$_Mslotid,$_Mslottype)
{
	$_Mdayslots=$GLOBALS['_Mdayslots'];$_Mtotslots=$GLOBALS['_Mtotslots'];
	if($_Mslotid<0 || $_Mslotid>($_Mtotslots-1)) return false;
	if($_Mstatus[$_Mslotid]=='f') return true;
	if($_Mstatus[$_Mslotid]=='s') return false;
	if($_Mstatus[$_Mslotid]==$_Mslottype) return true;
	return false;
}
	function _Ftotalroomschisfree($_Mroomid,$_Mslotid,$_Mslottype,$_Mactivetermid)
	{
		$_Msqlstr="select roomstatus from roomsh where roomID=$_Mroomid and termID=$_Mactivetermid";
		if(!($_Mresult=mysql_query($_Msqlstr))) return -1;
		if(mysql_num_rows($_Mresult)<=0) return 0;
		$_Mrow=mysql_fetch_assoc($_Mresult);
		if(_Fispossibleins($_Mrow['roomstatus'],$_Mslotid,$_Mslottype)) return true;
		return false;
	}

function _Fsetcourseprefs1($_Mgroups)
{
	//will optimize and correct for applying course part
	    $_Mtcids=array();$_Mtids=array();$_Mtcparts=array();$_Mteachersfscs=array();
		$_Msqlstr="select teachercourseids,courseparts,teacherID,teacherfsc from tchrtimes where teachergroupid in ($_Mgroups) and termID=2";//{$GLOBALS['_Mactivetermid']}
		if(!($_Mresult=mysql_query($_Msqlstr))) return -1;
		if(mysql_num_rows($_Mresult)<=0) return 0;
		while ($_Mrow=mysql_fetch_assoc($_Mresult))
		{array_push($_Mtcids,$_Mrow['teachercourseids']);array_push($_Mtids,$_Mrow['teacherID']);array_push($_Mtcparts,$_Mrow['courseparts']);$_Mteachersfscs[$_Mrow['teacherID']]=$_Mrow['teacherfsc'];}

	$_Msqlstr="select courseID,coursecode,coursepart from termcoursestatus where ((groupidconf1 in ($_Mgroups) AND awgroupID=0) OR (awgroupID in ($_Mgroups)) OR (groupidconf2 in ($_Mgroups)) OR (groupidconf3 in ($_Mgroups))) and termID=2 and coursestatus<>'s' ";//{$GLOBALS['_Mactivetermid']}
	//select not cheduled courses
	if($_Mresult=mysql_query($_Msqlstr))
	{
		$_Mgroupcoursecount=mysql_num_rows($_Mresult);
		if($_Mgroupcoursecount>0)
		{
			while($_Mrow=mysql_fetch_array($_Mresult,MYSQL_ASSOC))
			{
				$_Mcoursecode=$_Mrow['coursecode'];
				$_Mcoursetfs=0;
				$_Mcoursec=_Fclccoursecrd($_Mcoursecode,$_Mtcids,$_Mtids,$_Mteachersfscs,$_Mcoursetfs);
				
				$_Msqlstr="update termcoursestatus set coursecar='$_Mcoursec',COURSETFS='$_Mcoursetfs' where coursecode='$_Mcoursecode' and termID=2";//$_Mactivetermid
				mysql_query($_Msqlstr);
			}
		}
			mysql_free_result($_Mresult);
			return true;
		}else return false;
}
function _Fclccoursecrd($_Mcoursecode,$_Mtcids,$_Mtids,$_Mteachersfscs,&$_Mcoursetfs)
{
	$_Mcount=0;
	for($i=0 ; $i<count($_Mtcids) ; $i++)
	{
		$_Mtcid=explode(",",$_Mtcids[$i]);
		if(in_array( $_Mcoursecode,$_Mtcid, false))
		{$_Mcount++;$_Mteacherid=$_Mtids[$i];$_Mcoursetfs+=$_Mteachersfscs[$_Mteacherid];}
	}
	return $_Mcount;
}
function _Fpreprocessdata($_Mgroups)
{
	$_Mgroups='2,3,4,'.$_Mgroups;
	_Fsetteachersslotstats($_Mgroups);
	_Fsetcourseprefs1($_Mgroups);
	_Fsetcoursestchrslots($_Mgroups);
	
}
function _Fsetcoursestchrslots($_Mgroups)
{
	
}
function _Fsetteachersslotstats($_Mgroups)
{
	$_Msqlstr="select teacherID,initialtimes from tchrtimes where (teachergroupid in ($_Mgroups) AND termID={$GLOBALS['_Mactivetermid']})";
	if($_Mresult=mysql_query($_Msqlstr))
	{
		if(mysql_num_rows($_Mresult)>0)
		{
			while($_Mrow=mysql_fetch_array($_Mresult,MYSQL_ASSOC))
			{
				$_Mttimes=$_Mrow['initialtimes'];
				$_Mfreeslotscount=round(_Fclcfreeslotscount($_Mttimes));
				$_Msqlstr="update tchrtimes set teacherfsc='$_Mfreeslotscount' where teacherID={$_Mrow['teacherID']} and termID={$GLOBALS['_Mactivetermid']}";
				mysql_query($_Msqlstr);
			}
		}
			mysql_free_result($_Mresult);
			return true;
		}else return false;
	
	
}
function _Fclcfreeslotscount($_Mttimes)
{
	$_Mcnt=0;
	for($i=0 ; $i<strlen($_Mttimes) ; $i++)
	{
		if($_Mttimes[$i]=='f') $_Mcnt++;
		else if($_Mttimes[$i]=='o' || $_Mttimes[$i]=='e') $_Mcnt+=.5;
	}
	return $_Mcnt;
}
function _Fgetbesteacher4course($_Mcourseid)
{
}

function _Fgetgrouplots($_Mgroupid)
{
}

function _Fcheckassignpossibility($_Mgroupid,$_Mcourseid,$_Mteacherid)
{
}
?>