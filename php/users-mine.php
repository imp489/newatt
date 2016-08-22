<?php session_start();
  @include_once "mygenclasses.php";
//grouptype : 1:kardani, 2:karshenasi,3:arshad,4:doctora ,5: karshenasi napeyvaste
  $_Mcon=new _CLconnection();

  $_Mtype=$_POST['type'];
  if($_Mtype=='t')
  {
	$_Mteachergroupid=  $_POST['tgid']  ;$_Mteachername = $_POST['tn'];  	$_Mteacherfamily = $_POST['tf'];	$_Mteacherdegree = $_POST['td']; 	$_Mteacherfield = $_POST['tfi'];	$_Mteachertel = $_POST['tt'];  	$_Mteachermobile = $_POST['tm'];	$_MteacherState = $_POST['ts'];  	$_Mteacheradrs = $_POST['ta'];	$_Mteacherdesc = $_POST['tde'];
  	echo _Fsaveteachertodb($_Mteachergroupid,$_Mteachername,$_Mteacherfamily,$_Mteacherdegree,$_Mteacherfield,$_Mteachertel,$_Mteachermobile,$_Mteacheradrs,$_Mteacherdesc);
  }	
  else if($_Mtype=='g')
  {
  	$_Mgroupcaption=$_POST['gcaption']; $_Mgroupcode=$_POST['gc']; $_Mgroupyear=$_POST['gy'];$_Msubgroupof=$_POST['mg'];$_Mgrouptype=$_POST['gt']; $_Mgrouplevel=4; $_Mfinalgroup='1';
  	echo _Fsavegrouptodb($_Mgroupcaption,$_Mgroupcode,$_Mgroupyear,$_Msubgroupof,$_Mgrouptype,$_Mgrouplevel,$_Mfinalgroup);
  }	
  else if($_Mtype=='c')
  {
	$_Mcoursecaption = $_POST['cn'];$_Mcourseid=$_POST['cc'];	$_Mgroupid = $_POST['cg'];	$_Mgroupcaption = $_POST['cgc'];	$_Mcoursetype = $_POST['ct'];	$_McourseAUnits = $_POST['cau'];	$_McourseTUnits = $_POST['ctu'];	$_Mcoursehrdnes = $_POST['ch'];
	$_Mcoursepres="";
	if($_POST['cp1']!='') $_Mcoursepres.=$_POST['cp1'].",";
	if($_POST['cp2']!='') $_Mcoursepres.=$_POST['cp2'].",";
	if($_POST['cp3']!='') $_Mcoursepres.=$_POST['cp3'].",";
	$_Mcoursepres=substr($_Mcoursepres,0,strlen($_Mcoursepres)-1);

	$_Mcoursesims="";
	if($_POST['cs1']!='') $_Mcoursesims.=$_POST['cs1'].",";
	if($_POST['cs2']!='') $_Mcoursesims.=$_POST['cs2'].",";
	if($_POST['cs3']!='') $_Mcoursesims.=$_POST['cs3'].",";
	$_Mcoursesims=substr($_Mcoursesims,0,strlen($_Mcoursesims)-1);
	
	$_McourseState = $_POST['cs'];	$_Mcoursedesc = $_POST['cd'];
  	echo _Fsavecoursetodb($_Mcoursecaption,$_Mcourseid,$_Mgroupid,$_Mcoursetype,$_McourseAUnits,$_McourseTUnits,$_Mcoursehrdnes,$_Mcoursepres,$_Mcoursesims,$_McourseState,$_Mcoursedesc);
  }	
  else if($_Mtype=='r')
  {
	$_Mcapacity = $_POST['capacity'];		$_Mroomcaption = $_POST['rc'];	$_Mequipments = $_POST['eq'];		$_MroomType = $_POST['rt'];	$_MbuildingID = $_POST['bID'];		$_MroomfloorID = $_POST['rpID'];
  	echo _Fsaveroomtodb($_Mcapacity,$_Mroomcaption,$_Mequipments,$_MroomType,$_MbuildingID,$_MroomfloorID);
  }	
  else if($_Mtype=='rt')
  {
	$_MroomType = $_POST['roomtype'];
  	echo _Fsaveroomtypetodb($_MroomType);
  }	
  
  
  
  
  

  //****************************************
  //baraye ezafe kardane recordi baraye zamanhaye ostad(dar tchrtimes) dar hengame voroode ettelaat behine shavad.
  function _Fsaveteachertodb($_Mteachergroupid,$_Mteachername,$_Mteacherfamily,$_Mteacherdegree,$_Mteacherfield,$_Mteachertel,$_Mteachermobile,$_Mteacheradrs,$_Mteacherdesc)
  {
  	$_Msqlstr="insert into tchrs   (teachergroupid,teachername,teacherfamily,teacherdegree,teacherfield,teachertel,teachermobile,teacherState,teacheradrs,teacherdesc) values('$_Mteachergroupid','$_Mteachername','$_Mteacherfamily','$_Mteacherdegree','$_Mteacherfield','$_Mteachertel','$_Mteachermobile','$_MteacherState','$_Mteacheradrs','$_Mteacherdesc')";
  	if(mysql_query($_Msqlstr)) return  1;
  	else return 0;
  	//will check for duplicates
  }

    function _Fsavecoursetodb($_Mcoursecaption,$_Mcourseid,$_Mgroupid,$_Mcoursetype,$_McourseAUnits,$_McourseTUnits,$_Mcoursehrdnes,$_Mcoursepres,$_Mcoursesims,$_McourseState,$_Mcoursedesc)
  {
  	$_Msqlstr="insert into courses  (coursecaption,courseid,groupid,coursetype,courseAUnits,courseTUnits,coursehrdnes,coursepres,coursesims,courseState,coursedesc,termID) values('$_Mcoursecaption','$_Mcourseid','$_Mgroupid','$_Mcoursetype','$_McourseAUnits','$_McourseTUnits','$_Mcoursehrdnes','$_Mcoursepres','$_Mcoursesims','$_McourseState','$_Mcoursedesc','{$_SESSION['activetermid']}')";
  	echo $_Msqlstr;
  	if(mysql_query($_Msqlstr)) return  1;
  	else return 0;
  }
  function _Fsavegrouptodb($_Mgroupcaption,$_Mgroupcode,$_Mgroupyear,$_Msubgroupof,$_Mgrouptype,$_Mgrouplevel,$_Mfinalgroup)
  {
  	$_Msqlstr="insert into groups (groupcaption,groupcode,groupyear,subgroupof,grouptype,grouplevel,finalgroup) values('$_Mgroupcaption','$_Mgroupcode','$_Mgroupyear','$_Msubgroupof','$_Mgrouptype','$_Mgrouplevel','$_Mfinalgroup')";
  	if(mysql_query($_Msqlstr)) return  1;
  	else return 0;	
  }

  function _Fsaveroomtodb($_Mcapacity,$_Mroomcaption,$_Mequipments,$_MroomType,$_MbuildingID,$_MroomfloorID)
  {
  	$_Msqlstr="insert into rooms (capacity,roomcaption,equipments,roomType,buildingID,roomfloorID) values('$_Mcapacity','$_Mroomcaption','$_Mequipments','$_MroomType','$_MbuildingID','$_MroomfloorID')";
  	if(mysql_query($_Msqlstr)) return  1;
  	else return 0;
  }

  function _Fsaveroomtypetodb($_MroomType)
  {
  	$_Msqlstr="insert into rooms (capacity,roomcaption,equipments,roomType,buildingID,roomfloorID) values('$_Mcapacity','$_Mroomcaption','$_Mequipments','$_MroomType','$_MbuildingID','$_MroomfloorID')";
  	if(mysql_query($_Msqlstr)) return  1;
  	else return 0;
  }

?>