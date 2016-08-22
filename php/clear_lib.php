<?php
 
function resetallbutinitials($_Mgroupslist)
{
	_Fresetscheduled($_Mgroupslist);
	resetallcnst($_Mgroupslist);
	resetalltermresources($_Mgroupslist);
}

function _Fresetscheduled($_Mgroupslist,$_Mastergroupid)
{
	if(!(resetassignment($_Mgroupslist))) echo "assignments not cleared.";
	if(!(resetteachertimes($_Mgroupslist,$_Mastergroupid))) echo "teachers not cleared.";
	if(!(resetcourses($_Mgroupslist))) echo "courses not cleared";
	if(!(resetgroups($_Mgroupslist))) echo "groups not cleared";
}
function resetalltermresources($_Mgroupslist)
{
	resettermcourses($_Mgroupslist);
	resettermteachers($_Mgroupslist);
	resettermrooms($_Mgroupslist);
	resettermslots($_Mgroupslist);
}
function resetassignment($_Mgroupslist)
{
	if($_Mgroupslist=='*')
		$_Msqlstr="delete from assignments where termID={$GLOBALS['_Mactivetermid']}";
	else	
		$_Msqlstr="delete from assignments where termID={$GLOBALS['_Mactivetermid']} and ((groupID in ($_Mgroupslist) AND awgroupID=0)OR(awgroupID in ($_Mgroupslist)))"; 
	if(mysql_query($_Msqlstr)) return 1;
	else return 0;
}
function _Fresetroomassignements($_Mgroupslist)
{
	resettermrooms($_Mgroupslist);
	if($_Mgroupslist=='*')
		$_Msqlstr="update assignments set roomID=0,roomcaption='' where termID={$GLOBALS['_Mactivetermid']}";
	else	
		$_Msqlstr="update assignments set roomID=0,roomcaption='' where termID={$GLOBALS['_Mactivetermid']} and ((groupID in ($_Mgroupslist) AND awgroupID=0)OR(awgroupID in ($_Mgroupslist)))"; 
	if(mysql_query($_Msqlstr)) return 1;
	else return 0;
}
function resetteachertimes($_Mgroupslist,$_Mastergroupid)
{	
	if($_Mgroupslist=='*')
		$_Msqlstr="update tchrtimes set times=initialtimes,teachercurslots=0 where termID={$GLOBALS['_Mactivetermid']}";
	else
		$_Msqlstr="update tchrtimes set times=initialtimes,teachercurslots=0 where termID={$GLOBALS['_Mactivetermid']} and teachergroupid in ($_Mgroupslist,$_Mastergroupid)"; 
	//echo $_Msqlstr;
	if(mysql_query($_Msqlstr)) return 1;
	else return 0;
}	
function resetcourses($_Mgroupslist)
{
	if($_Mgroupslist=='*')
		$_Msqlstr="update termcoursestatus set coursestatus='n',otherpart1slots='',otherpart2slots='' where termID={$GLOBALS['_Mactivetermid']}";
	else 
		$_Msqlstr="update termcoursestatus set coursestatus='n',otherpart1slots='',otherpart2slots='' where termID={$GLOBALS['_Mactivetermid']} and groupidconf1 in ($_Mgroupslist)";
	if(mysql_query($_Msqlstr)) return 1;
	else return 0;
}
	
function resetgroups($_Mgroupslist)
{
	if($_Mgroupslist=='*')
		$_Msqlstr="update groupstatus set groupstatus=initialgroupstatus,groupscheduledslots=0 where termID={$GLOBALS['_Mactivetermid']}";
	else 	
		$_Msqlstr="update groupstatus set groupstatus=initialgroupstatus,groupscheduledslots=0 where termID={$GLOBALS['_Mactivetermid']} and groupID in ($_Mgroupslist)";

	if(mysql_query($_Msqlstr)) return 1;
	else return 0;
}
function resetallcnst($_Mgroupslist)
{
	if($_Mgroupslist=='*')
		$_Msqlstr="delete from asgncnsts where termID={$GLOBALS['_Mactivetermid']}";
	else 
		$_Msqlstr="delete from asgncnsts where termID={$GLOBALS['_Mactivetermid']} and groupID in ($_Mgroupslist)";
	if(mysql_query($_Msqlstr)) return 1;
	else return 0;
}
function resetsomecnsts($_Mcnsts,$_Mgroupslist)
{
	if($_Mgroupslist=='*')
		$_Msqlstr="delete from asgncnsts where cnsttype like '$_Mcnsts' and termID={$GLOBALS['_Mactivetermid']}";
	else 
		$_Msqlstr="delete from asgncnsts where cnsttype like '$_Mcnsts' and termID={$GLOBALS['_Mactivetermid']} and groupID in ($_Mgroupslist)";
	if(mysql_query($_Msqlstr)) return 1;
	else return 0;
}

function resetcnststeacherslots($_Mgroupslist)
{resetsomecnsts('ts',$_Mgroupslist);}	
function resetcnststeacherrooms($_Mgroupslist)
{resetsomecnsts('tr',$_Mgroupslist);}	

function resetcnstscourseteachers($_Mgroupslist)
{resetsomecnsts('ct',$_Mgroupslist);}
function resetcnstscourseslots($_Mgroupslist)
{resetsomecnsts('cs',$_Mgroupslist);}	
function resetcnstscourserooms($_Mgroupslist)
{resetsomecnsts('cr',$_Mgroupslist);}	
	
function resettermcourses($_Mgroupslist)
{
	if($_Mgroupslist=='*')
		$_Msqlstr="delete from termcoursestatus where termID={$GLOBALS['_Mactivetermid']}";
	else 
		$_Msqlstr="delete from termcoursestatus where termID={$GLOBALS['_Mactivetermid']} and groupID in ($_Mgroupslist)";
	if(mysql_query($_Msqlstr)) return 1;
	else return 0;
}
function resettermteachers($_Mgroupslist)
{
	if($_Mgroupslist=='*')
		$_Msqlstr="delete from tchrtimes where termID={$GLOBALS['_Mactivetermid']}";
	else
		$_Msqlstr="delete from tchrtimes where termID={$GLOBALS['_Mactivetermid']} and groupID in ($_Mgroupslist)";
	if(mysql_query($_Msqlstr)) return 1;
	else return 0;
}
function resettermrooms($_Mgroupslist)
{
	if($_Mgroupslist=='*')		
		$_Msqlstr="delete from roomstatus where termID={$GLOBALS['_Mactivetermid']}";
	else 	
		$_Msqlstr="delete from roomstatus where termID={$GLOBALS['_Mactivetermid']} and groupID in ($_Mgroupslist)";
	if(mysql_query($_Msqlstr)) return 1;
	else return 0;
}
function resettermslots($_Mgroupslist)
{
	if($_Mgroupslist=='*')	
		$_Msqlstr="delete from timeslots where termID={$GLOBALS['_Mactivetermid']}";
	else
		$_Msqlstr="delete from timeslots where termID={$GLOBALS['_Mactivetermid']} and groupID in ($_Mgroupslist)";
	if(mysql_query($_Msqlstr)) return 0;
	else return 0;
}
?>