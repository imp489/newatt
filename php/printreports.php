<?php @session_start();
@include_once "jalali.php";
@include_once "mygenclasses.php";
$_Mcon=new _CLconnection();

	
	
function resetallbutinitials()
{
	_Fresetscheduled();
	resetallcnst();
	resetalltermresources();
}

function _Fresetscheduled()
{
	resetassignment();
	resetteachertimes();
	resetcourses();
	resetgroups();
}
function resetassignment()
{
	$_Msqlstr="delete from assignment";
	mysql_query($_Msqlstr);
}
function resetteachertimes()
{	
	$_Msqlstr="update tchrtimes set times=initialtimes,teachercurslots=0";
	mysql_query($_Msqlstr);
}	
function resetcourses()
{
	$_Msqlstr="update tchrtimes set coursestatus='n'";
	mysql_query($_Msqlstr);	
}
	
function resetgroups()
{
	$_Msqlstr="update groupstatus set groupstatus=initialgroupstatus";
	mysql_query($_Msqlstr);	
}
function resetallcnst()
{
	$_Msqlstr="delete from asgncnsts";
	mysql_query($_Msqlstr);
}
function resetsomecnsts($_Mcnsts)
{
	$_Msqlstr="delete from asgncnsts where cnsttype like '$_Mcnsts'";
	mysql_query($_Msqlstr);
}	

function resetcnststeacherslots()
{resetsomecnsts('ts');}	
function resetcnststeacherrooms()
{resetsomecnsts('tr');}	

function resetcnstscourseteachers()
{resetsomecnsts('ct');}
function resetcnstscourseslots()
{resetsomecnsts('cs');}	
function resetcnstscourserooms()
{resetsomecnsts('cr');}	
	
function resettermcourses()
{
	$_Msqlstr="delete from termcoursestatus";
	mysql_query($_Msqlstr);
}
function resettermteachers()
{
	$_Msqlstr="delete from tchrtimes";
	mysql_query($_Msqlstr);
}
function resettermrooms()
{
	$_Msqlstr="delete from roomstatus";
	mysql_query($_Msqlstr);
}
function resettermslots()
{
	$_Msqlstr="delete from timeslots";
	mysql_query($_Msqlstr);
}
function resetalltermresources()
{
	resettermcourses();
	resettermteachers();
	resettermrooms();
	resettermslots();
}
?>