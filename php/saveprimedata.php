<?php session_start();
    @include_once "mygenclasses.php";
//grouptype : 1:kardani, 2:karshenasi,3:arshad,4:doctora ,5: karshenasi napeyvaste
  $_Mcon=new _CLconnection();
  $_Mactivetermid=$_SESSION['activetermid'];
  $_Mtype=$_POST['type'];
  if($_Mtype=='t')
  {$_Mtcoop=$_POST['tcoop'];
	$_Mgroupid=  $_POST['tgid']  ; $_Mgroupcaption='';//$_Mgroupcaption=$_POST['tgc']  ;920623
	$_Mteachername = $_POST['tn'];  	$_Mteacherfamily = $_POST['tf'];	$_Mteacherdegree = $_POST['td']; 	$_Mteacherfield = $_POST['tfi'];	$_Mteachertel = $_POST['tt'];  	$_Mteachermobile = $_POST['tm'];	 	$_Mteacheradrs = $_POST['ta'];	$_Mteacherdesc = $_POST['tde'];	$_Mteachermail = $_POST['tmail'];
  	echo _Fsaveteachertodb($_Mgroupid,$_Mgroupcaption,$_Mteachername,$_Mteacherfamily,$_Mteacherdegree,$_Mteacherfield,$_Mteachertel,$_Mteachermobile,$_Mteacheradrs,$_Mteacherdesc,$_Mteachermail,$_Mtcoop);exit();
  	
  }	
  else if($_Mtype=='g')
  {
  	$_Mgroupcaption=$_POST['gcaption']; $_Mgroupcode=$_POST['gc']; $_Mgroupyear=$_POST['gy'];$_Msubgroupof=$_POST['mg'];$_Mgrouptype=$_POST['gt']; $_Mgrouplevel=4; $_Mfinalgroup='1';
  	echo _Fsavegrouptodb($_Mgroupcaption,$_Mgroupcode,$_Mgroupyear,$_Msubgroupof,$_Mgrouptype,$_Mgroupdes,$_Mgrouplevel,$_Mfinalgroup);exit();
  }
  else if($_Mtype=='fg')
  {
  	$_Mgroupcaption=$_POST['gcaption'];$_Msubgroupof=$_POST['mg'];$_Mgroupdes=$_POST['gd'];$_Mgrouplevel=4; $_Mfinalgroup='1';
  	echo _Fsavefgrouptodb($_Mgroupcaption,$_Msubgroupof,$_Mgroupdes,$_Mfinalgroup);exit();
  }	
  else if($_Mtype=='c')
  {
	$_Mcoursecaption = $_POST['cn'];$_Mcourseid=$_POST['cc'];	$_Mgroupid = $_POST['cg'];	//$_Mgroupcaption = $_POST['cgc'];	
	$_Mcoursetype = $_POST['ct'];	$_McourseAUnits = $_POST['cau'];	$_McourseTUnits = $_POST['ctu'];	$_Mcoursehrdnes = $_POST['ch'];
	$_Mneededroomtypeid=$_POST['rid'];$_Mneededroomtypeaid=$_POST['arid'];$_Mnttimes=$_POST['ttn'];$_Mnatimes=$_POST['atn'];$_Mtunitteachers=$_POST['ttu'];$_Maunitteachers=$_POST['tau'];$_Mcourseshortcaption=$_POST['cnn'];
	if($_Mcourseshortcaption=='')$_Mcourseshortcaption=$_Mcoursecaption;
	$_Mcoursepres="";$_Mcourseprescaptions="";
	if($_POST['cp1']!='') $_Mcoursepres.=$_POST['cp1'].",";
	if($_POST['cp2']!='') $_Mcoursepres.=$_POST['cp2'].",";
	if($_POST['cp3']!='') $_Mcoursepres.=$_POST['cp3'].",";
	$_Mcoursepres=substr($_Mcoursepres,0,strlen($_Mcoursepres)-1);

	$_Mcoursesims="";$_Mcoursesimscaptions="";
	if($_POST['cs1']!='') $_Mcoursesims.=$_POST['cs1'].",";
	if($_POST['cs2']!='') $_Mcoursesims.=$_POST['cs2'].",";
	if($_POST['cs3']!='') $_Mcoursesims.=$_POST['cs3'].",";
	$_Mcoursesims=substr($_Mcoursesims,0,strlen($_Mcoursesims)-1);
	$_McourseState =0;
	//$_McourseState = $_POST['cs'];	
	$_Mcoursedesc = $_POST['cd'];
  	echo _Fsavecoursetodb($_Mcoursecaption,$_Mcourseid,$_Mneededroomtypeid,$_Mneededroomtypeaid,$_Mgroupid,$_Mcoursetype,$_McourseAUnits,$_McourseTUnits,$_Mcoursehrdnes,$_Mcoursepres,$_Mcoursesims,$_McourseState,$_Mcoursedesc,$_Mnttimes,$_Mnatimes,$_Mtunitteachers,$_Maunitteachers,$_Mcourseshortcaption);exit();
  }
  else if($_Mtype=='r')
  {
	$_Mcapacity = $_POST['capacity'];		$_Mroomcaption = $_POST['rc'];	$_Mequipments = $_POST['eq'];		$_MroomType = $_POST['rt'];	$_MbuildingID = $_POST['bID'];
  	echo _Fsaveroomtodb($_Mcapacity,$_Mroomcaption,$_Mequipments,$_MroomType,$_MbuildingID);exit();
  }	
  else if($_Mtype=='rt')
  {
	$_MroomType = $_POST['roomtype'];
  	echo _Fsaveroomtypetodb($_MroomType);exit();
  }	
  else if($_Mtype=='ts')
  {
  	echo _Fsavetimeslotstodb($_POST['str']);exit();
  }	
  
  
  
  
  

  //****************************************
  function _Fsavetimeslotstodb($_Mstr)
  {
	mysql_query("delete from timeslots");
	$_Mout=1;
	$_Marr = explode('#',$_Mstr);
	sort($_Marr);
	for ($i=1 ; $i<count($_Marr) ; $i++)
 	{
   		$_Mstr = explode(',',$_Marr[$i]);
   		$_Mspl = explode('-',$_Mstr[0]);
   		$_Mstr1 = $_Mspl[1] . '-' . $_Mspl[0];
   		$query = "insert into timeslots(slotcaption,slotfromid,slottoid,usagepriority)values('$_Mstr1','$_Mspl[0]','$_Mspl[1]','$_Mstr[1]')";
   		if(!(mysql_query($query)))
   			$_Mout=0;
 	}
 	return $_Mout;
  }
  //baraye ezafe kardane recordi baraye zamanhaye ostad(dar tchrtimes) dar hengame voroode ettelaat behine shavad.
  function _Fsaveteachertodb($_Mgroupid,$_Mgroupcaption,$_Mteachername,$_Mteacherfamily,$_Mteacherdegree,$_Mteacherfield,$_Mteachertel,$_Mteachermobile,$_Mteacheradrs,$_Mteacherdesc,$_Mteachermail,$_Mtcoop)
  {
  	$_Msqlstr="insert into tchrs   (groupID,groupcaption,teachername,teacherfamily,teacherdegree,teacherfield,teachertel,teachermobile,teacheradrs,teacherdesc,email,cooptype) values('$_Mgroupid','','$_Mteachername','$_Mteacherfamily','$_Mteacherdegree','$_Mteacherfield','$_Mteachertel','$_Mteachermobile','$_Mteacheradrs','$_Mteacherdesc','$_Mteachermail','$_Mtcoop')";
  	//echo $_Msqlstr;
  	if(mysql_query($_Msqlstr)) return  1;
  	else return 0;
  	//will check for duplicates
  }
             //_Fsavecoursetodb($_Mcoursecaption,$_Mcourseid,$_Mneededroomtypeid,$_Mneededroomtypeaid,$_Mgroupid,$_Mcoursetype,$_McourseAUnits,$_McourseTUnits,$_Mcoursehrdnes,$_Mcoursepres,$_Mcoursesims,$_McourseState,$_Mcoursedesc,$_Mnttimes,$_Mnatimes,$_Mtunitteachers,$_Maunitteachers,$_Mcourseshortcaption);
    function _Fsavecoursetodb($_Mcoursecaption,$_Mcourseid,$_Mneededroomtypeid,$_Mneededroomtypeaid,$_Mgroupid,$_Mcoursetype,$_McourseAUnits,$_McourseTUnits,$_Mcoursehrdnes,$_Mcoursepres,$_Mcoursesims,$_McourseState,$_Mcoursedesc,$_Mnttimes,$_Mnatimes,$_Mtunitteachers,$_Maunitteachers,$_Mcourseshortcaption)
  {
  	$_Msqlstr="insert into courses  (coursecaption,coursefcaption,courseid,coursecode,groupid,neededroomtypeID,neededroomtypeAID,coursetype,courseAUnits,courseTUnits,courseAunitstchrs,courseTunitstchrs,coursettimes,courseatimes,coursehrdnes,coursepres,coursesims,courseState,coursedesc,termID) values('$_Mcourseshortcaption','$_Mcoursecaption','$_Mcourseid','$_Mcourseid','$_Mgroupid','$_Mneededroomtypeid','$_Mneededroomtypeaid','$_Mcoursetype','$_McourseAUnits','$_McourseTUnits','$_Maunitteachers','$_Mtunitteachers','$_Mnttimes','$_Mnatimes','$_Mcoursehrdnes','$_Mcoursepres','$_Mcoursesims','$_McourseState','$_Mcoursedesc','{$_SESSION['activetermid']}')";
  	//echo $_Msqlstr;
  	if(mysql_query($_Msqlstr)) return  1;
  	else return 0;
  }
  function _Fsavegrouptodb($_Mgroupcaption,$_Mgroupcode,$_Mgroupyear,$_Msubgroupof,$_Mgrouptype,$_Mgroupdes,$_Mgrouplevel,$_Mfinalgroup)
  {
  	$_Msqlstr="insert into groups (groupcaption,groupcode,groupyear,subgroupof,grouptype,grouplevel,finalgroup) values('$_Mgroupcaption','$_Mgroupcode','$_Mgroupyear','$_Msubgroupof','$_Mgrouptype','$_Mgrouplevel','$_Mfinalgroup')";
  	if(mysql_query($_Msqlstr)) return  1;
  	else return 0;	
  }

  function _Fsavefgrouptodb($_Mgroupcaption,$_Msubgroupof,$_Mgroupdes,$_Mfinalgroup)
  {
  	$_Msqlstr="insert into groupstatus (groupcaption,subgroupof,groupdes,finalgroup,termID) values('$_Mgroupcaption','$_Msubgroupof','$_Mgroupdes','$_Mfinalgroup','{$GLOBALS['_Mactivetermid']}')";
  	if(mysql_query($_Msqlstr)) return  1;
  	else return 0;
  	
  }

  function _Fsaveroomtodb($_Mcapacity,$_Mroomcaption,$_Mequipments,$_MroomType,$_MbuildingID)
  {
  	$_Msqlstr="insert into rooms (capacity,roomcaption,equipments,roomType,subgroupof,termID) values('$_Mcapacity','$_Mroomcaption','$_Mequipments','$_MroomType','$_MbuildingID','{$GLOBALS['_Mactivetermid']}')";
  	if(mysql_query($_Msqlstr)) return  1;
  	else return 0;
  }

  function _Fsaveroomtypetodb($_MroomType)
  {
  	$_MroomTypes=explode("#",$_MroomType);
  	$_Msqlstr="";
  	for($i=0 ; $i<count($_MroomTypes)-1 ; $i++)
  	{
  		$rt=explode("~",$_MroomTypes[$i]);
  		$_Msqlstr.="('{$rt[0]}','{$rt[1]}'),";
  	}
  	$_Msqlstr=substr($_Msqlstr,0,strlen($_Msqlstr)-1);
  	$_Msqlstr="insert into roomtypes (roomtypecaption,roomtypedes) values $_Msqlstr";
  	if(mysql_query($_Msqlstr)) return  1;
  	else return 0;
  }

?>