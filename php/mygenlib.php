<?php @session_start();
@include_once "jalali.php";
@include_once "mygenclasses.php";
@include_once "mygen_lib.php";
@include_once "getdata_lib.php";
//after entering data check for data & constraint integrities,
//such as : a teacher has a constraint time to teach a course at a time that isn't 
//in the groups valid times
//teacher course priorities 5:highest, 1: lowest
$_Mcoursestatus=array("s"=>"scheduled","n"=>"notscheduled","f"=>"timenotfound","t"=>"teachernotfound");
$_Mteachertimestatus=array("s"=>"scheduled","e"=>"evenscheduled","o"=>"oddscheduled","f"=>"free","2"=>"2ndfree","n"=>"nothere");
//e : even schedulabale
$_Mroomstatus=array("s"=>"fullscheduled","e"=>"evenscheduled","o"=>"oddscheduled","f"=>"free","n"=>"notusabale");
$_Mgroupstatus=array("s"=>"fullscheduled","e"=>"evenscheduled","o"=>"oddscheduled","f"=>"free","n"=>"notusabale");
$_Massigntype=array("a"=>"automatic","m"=>"manual");
//$courseconsttype=array();
$_Mreportsarray=array();
$_Mreportcounter=0;
$_Mactivetermid=$_SESSION['activetermid'];
	$_Mfullassigned="tsrc";
	$_Mroomassigned="cr";
	$_Mteacherassigned="ct";
	$_Mslotassigned="cs";
	$_Mteacherslotassigned="tsc";

	$_Mfullfree=str_repeat('f',98);
$_Mtimedate1=new _CLmytimedate();
?>