<?php @session_start();
@include_once "mygenclasses.php";
@include_once "mygen_lib.php";
@include_once "clear_lib.php";
@include_once "getdata_lib.php";
@include_once "jalali.php";
@include_once "shrfuns.php";
$_Mcon=new _CLconnection();
$_Mcourse1=new _CLcourse();
$_Mteacher1=new _CLteacher();
$_Mroom1=new _CLroom();
$_Mgroup1=new _CLgroup();
$_Merrorsarray=array();
$_Mprocesslogs=array();
$_Mgrouptermcourses=array();
$_Mreportcounter=0;

$_Mactivetermid=$_SESSION['activetermid'];
$_Mschstr=$_POST['schstr'];
/*
$_Mactivetermid=2;
$_Mschstr="2@@@@28##f##1290!c1##14##1##4@@@@32##o##1290!w1##14##6##undefined";
*/
$_Mschar=explode("@@@@",$_Mschstr);

$_Mmgroupid=$_Mschar[0];$_Mawgroupid=$_Mmgroupid;
//$_Mgroups=_Fgetsubgroups1(array("$_Mmgroupid,1#"));
$_Mgroups=_Fgetallsubgroups($_Mmgroupid);
$_Mmastergroupid=_Fgetmastergroup($_Mmgroupid,$_Mactivetermid);
_Fresetscheduled($_Mgroups,$_Mmastergroupid);//echo 'cleareddd';
//echo '-->'.$_Mmgroupid.'-->'.$_Mgroups.'-->'.$_Mmastergroupid;

for ($i=1 ; $i < count($_Mschar) ; $i++)
{
	$_Mschitem=explode("##",$_Mschar[$i]);
	$_Mslotid=$_Mschitem[0];$_Mslottype=$_Mschitem[1];$_Mgroupid=$_Mschitem[5];$_Mroomid=$_Mschitem[4];$_Mteacherid=$_Mschitem[3];
	if($_Mslottype=='f') $_Mslottype='s';

	$_Mcourseid=_Fgetcourseidfrom($_Mschitem[2],$_Mcoursepart);


	if($_Mcourseid!='' & $_Mcourseid!=0) 
		_Fsetcourseinfos($_Mcourse1,$_Mcourseid,$_Mcoursepart);
	if($_Mteacherid!='' & $_Mteacherid!=0) 
		$_Mteacher1->setteacheridfull($_Mschitem[3],$_Mactivetermid);
	if($_Mroomid!='' & $_Mroomid!=0) 
		$_Mroom1->setroom($_Mschitem[4],$_Mschitem[5],$_Mactivetermid);
	_Fsetgroup($_Mgroup1,$_Mmgroupid);
	$res.=_Fschedulectsr($_Mcourse1,$_Mteacher1,$_Mgroup1,$_Mawgroupid,$_Mroom1,$_Mslotid,$_Mslottype);
	$_Mcourse1->reset();$_Mteacher1->reset();$_Mroom1->reset();
}
echo '**'.$res.'**';
if ($res==111) echo 1;else echo 0;


function _Fsetgroup(&$_Mgroup1,$_Mgroupid)
{
	$_Msqlstr="select groupcaption,groupstatus,initialgroupstatus from groupstatus where groupID=$_Mgroupid and termID={$GLOBALS['_Mactivetermid']} ";//and groupID in(select groupID from groups where finalgroup=1)";	
	if($_Mresult=mysql_query($_Msqlstr))
	{
		if(mysql_num_rows($_Mresult)==1)
		{
			$_Mrow=mysql_fetch_array($_Mresult);
			$_Mgroup1->setgroupid($_Mgroupid);
			$_Mgroup1->setgroupstatus($_Mrow["groupstatus"]);
			$_Mgroup1->setinitialgroupstatus($_Mrow["initialgroupstatus"]);
			$_Mgroup1->setgroupcaption($_Mrow["groupcaption"]);
		}else return 0;
	}else return -1;
}


function _Fsetcourseinfos($_Mcourse1,$_Mcourseid,$_Mcoursepart)
{
	$_Msqlstr="select * from termcoursestatus where courseID=$_Mcourseid and coursepart like '$_Mcoursepart' and termID={$GLOBALS['_Mactivetermid']}";
	if($_Mresult=mysql_query($_Msqlstr))
	{
		if(mysql_num_rows($_Mresult)==1)
		{
			$_Mrow=mysql_fetch_array($_Mresult);
			$_Mcourse1->setcourseid($_Mcourseid);
			$_Mcourse1->setcoursecaption($_Mrow["coursecaption"]);
			$_Mcourse1->setcourseunits($_Mrow["courseAunits"],$_Mrow["courseTunits"]);
			$_Mcourse1->setcoursepart($_Mcoursepart);
			$_Mcourse1->setcoursepartunits($_Mrow["coursepartunits"]);
			$_Mcourse1->setotherpart1slots($_Mrow["otherpart1slots"]);
			$_Mcourse1->setotherpart2slots($_Mrow["otherpart2slots"]);
			$_Mcourse1->setschwithgroupid($_Mrow['awgroupID']);
			$_Mcourse1->setcoursemaingroupid($_Mrow['groupID']);
			return 1;
		}else return 0;
	}else return -1;
}

function _Fschedulectsr($_Mcourse1,$_Mteacher1,$_Mgroup1,$_Mawgroupid,$_Mroom1,$_Mslotid,$_Mslottype)
{ 	
	$i=$_Mslotid*1;
  	$_Mtimeslots=array($i,$i+1);$_Mtimeslotstate=array($_Mslottype,$_Mslottype);
	//_Fslottype2slotstypes($_Mcourse1->getcourseslotcnst(),$_Mtimeslots,$_Mtimeslotstate);
  	$_Mgroup1->updatestatus($_Mtimeslots,$_Mtimeslotstate);
  	return(_Fupdatecourseteachergroupfa($_Mcourse1,$_Mteacher1,$_Mgroup1,$_Mawgroupid,$_Mroom1,$_Mtimeslots,$_Mtimeslotstate,''));
  	/*
  	if(($_Mroom1->getroomid()!='') & ($_Mroom1->getroomid()!=0))
  	{
  		if($_Mcourse1->getcourseid()!='')
  			_Fassignroom2course($_Mcourse1->getcourseid(),$_Mgroup1->getgroupid(),$_Mcourse1->getcoursepart(),$_Mroom1->getroomid(),$_Mroom1->roomcaption,$_Mcourse1->getassignedslottype());
  		else if	($_Mteacher1->getteacherid()!='')
  		{
  			$st="$_Mslotid[0]:$_Mslottype[0],$_Mslotid[0]+1:$_Mslottype[0]";
  			$_Msqlstr="update assignments set roomID='$_Mroomid',roomcaption='$roomcaption' where teacherID={$_Mteacher1->getteacherid()} and coursepart like '$_Mcoursepart' and groupID={$_Mgroupid} and termID={$GLOBALS['_Mactivetermid']}";
  			mysql_query($_Msqlstr);
  		}

  	}
  	*/
}
function _Fupdatecourseteachergroupfa($_Mcourse1,$_Mteacher1,$_Mgroup1,$_Mawgroupid,$_Mroom1,$_Mtimeslots,$_Mtimeslotstate,$_Massigntype)
{
	if($_Mcourse1->getcourseid()!=0) _Fupdatecoursestatus($_Mcourse1,$_Mgroup1->getgroupid(),$_Mtimeslots,$_Mtimeslotstate);
 	if($_Mteacher1->getteacherid()!=0)_Fupdateteachertimes($_Mteacher1,$_Mgroup1->getgroupid(),$_Mtimeslots,$_Mtimeslotstate,$_Mcourse1->getcoursepartunits());
	_Fupdategrouptimes($_Mgroup1,$_Mcourse1->getcourseunits());
	return(_Fupdateassignmentsfa($_Mcourse1,$_Mteacher1,$_Mgroup1,$_Mawgroupid,$_Mroom1,$_Mtimeslots,$_Mtimeslotstate,$_Massigntype));
}
function _Fupdateassignmentsfa($_Mcourse1,$_Mteacher1,$_Mgroup1,$_Mawgroupid,$_Mroom1,$_Mtimeslots,$_Mtimeslotstate,$_Massigntype)
{
	$_Mtimestr=_Ftimeslotsarray2timestr($_Mtimeslots,$_Mtimeslotstate);
	$_Mtimedate1=new _CLmytimedate();
	@$_Mtimedate1->setjdate();
	$_Msqlstr="insert into assignments(termID,groupID,awgroupID,maingroupID,groupcaption,teacherID,teachername,teacherfamily,courseID,coursecaption,coursepart,coursepartunits,courseunits,roomID,roomcaption,timeslots,asgndate,asgntype,asgnuserid) values
				('{$GLOBALS['_Mactivetermid']}','{$_Mgroup1->getgroupid()}','{$_Mcourse1->getschwithgroupid()}','{$_Mcourse1->getcoursemaingroupid()}','{$_Mgroup1->groupcaption}','{$_Mteacher1->teacherid}','{$_Mteacher1->teachername}','{$_Mteacher1->teacherfamily}',
				'{$_Mcourse1->getcourseid()}','{$_Mcourse1->getcoursecaption()}','{$_Mcourse1->getcoursepart()}','{$_Mcourse1->getcoursepartunits()}','{$_Mcourse1->getcourseunits()}','{$_Mroom1->getroomid()}','{$_Mroom1->getroomcaption()}','$_Mtimestr','{$_Mtimedate1->jdate}','$_Massigntype','testuser')";//will change with real user
	//echo $_Msqlstr;
	mysql_query($_Msqlstr);
	if($_Msqlstr) return 1;
	else return -1;
}
?>