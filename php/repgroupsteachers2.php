<?php
  //@include "mygenclasses.php";
  @include_once "mygen_lib.php";

  $_Mcon=new _CLconnection();
  
  //remove to here
  //should be optimized no connecting in each page and no setting of activeterm
  $_Mtype=$_POST['type'];
  //$_Mtype='t';
  if($_Mtype=='g')
  {}
  else if($_Mtype=='t')
  {
  	$_Mgroupid=$_POST['groupid'];
  	//$_Mgroupid=1;
  	echo _Frepgroupteachers($_Mgroupid);
  }
  

  
  
function repgroups()  
{
	$_Moutstr='';
	getgroups(&$_Mgroups,&$_Mgroupslistcounter,&$_Mgroupscount);
	while($_Mrow=mysql_fetch_assoc($_Mgroups))
	{
		if($_Mrow['finalgroup']==1)
			$_Moutstr.='#'.$_Mrow['groupID'].','.$_Mrow['groupcode'].','.$_Mrow['groupcaption'];
	}
	mysql_free_result($_Mgroups);
	return $_Moutstr;
	
}


function _Frepgroupteachers($_Mgroupid)
{	
	$_Moutstr="";
	$_Msqlstr="select * from tchrtimes where ((teachergroupid={$_Mgroupid}) and 
										(termID={$_SESSION['activetermid']}))";
	$_Mgroupteachers=mysql_query($_Msqlstr);
	while($_Mrow=mysql_fetch_assoc($_Mgroupteachers))
	{
		$_Moutstr.="#".$_Mrow['teacherID'].','.$_Mrow['teachername'].','.$_Mrow['teacherfamily'].','.$_Mrow['teachermaxslots'].','.$_Mrow['teachercurslots'];
	}
	mysql_free_result($_Mgroupteachers);
	return $_Moutstr;			

}
?>