<?php @session_start();
@include_once "jalali.php";
@include_once "mygenclasses.php";
@include_once "clear_lib.php";
@include_once "getdata_lib.php";
@include_once "shrfuns.php";

$_Mcon=new _CLconnection();
//$_Mactivetermid=$_SESSION['activetermid'];
$_Mactivetermid=2;
$_Mgas=$_POST['sch'];
//$_Mgas="2,3#";
$_Mgroupsactions=explode("#",$_Mgas);array_pop($_Mgroupsactions);
$_Mgroups=array();$_Mroomgroups=array();
//correct group selection later
//_Fgetgroups4sch($_Mgroups,$_Mgroupidsstr,$_Mroomgroups,$_Mgroupscount,$_Mgroupsactions);
//echo $_Mgroupsactions[0];
$_Mga=explode(",",$_Mgroupsactions[0]);$_Mastergroupid=_Fgetmastergroup($_Mga[0],$GLOBALS['_Mactivetermid']);
//$_Mgroupidsstr=_Fgetsubgroups1(array("{$_Mga[0]},1#"));
$_Mgroupidsstr=_Fgetallsubgroups($_Mga[0]);

if($_Mga[1]==1)
{
	_Fresetscheduled($_Mgroupidsstr,$_Mastergroupid);echo 'completely cleared';
}
else if($_Mga[1]==3)
{_Fresetroomassignements($_Mgroupidsstr);echo "$_Mgroupidsstr room assignments cleared";}
?>