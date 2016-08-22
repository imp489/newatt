<?php @session_start();
@include_once "mygenclasses.php";
$_Mcon=new _CLconnection();
//$_Mactivetermid=$_SESSION['activetermid'];
$_Mactivetermid=2;
//_Fgetallfinalsubgroups(3);


function _Fgettotalslotsno()
{return 84;}
function _Fgetallsubgroups($_Mgroupid)
{
	$_Msqlstr="select groupID,groupcaption,asub from groupstatus where termID={$GLOBALS['_Mactivetermid']} and groupID=$_Mgroupid";//and groupID in(select groupID from groups where finalgroup=1)";
	//echo $_Msqlstr;
	if($_Mresult=mysql_query($_Msqlstr))
	{
		$_Mgroupscount=mysql_num_rows($_Mresult);
		if($_Mgroupscount>0)
		{
			$_Mrow=mysql_fetch_array($_Mresult,MYSQL_ASSOC);
				$_Masub=$_Mrow['asub'];
				if($_Masub==-1) $_Masub=$_Mgroupid;
			mysql_free_result($_Mresult);
			return $_Masub;
		}else return false;
	}else return false;
}
function _Fgetallfinalsubgroups(&$_Mgroups,&$_Mgroupidsstr,&$roomgroups,&$_Mgroupscount,&$_Mgroupsactions)
{
	$_Mar=explode(",",$_Mgroupsactions[0]);$_Mgroups2=array();
	$_Mgroupid=$_Mar[0];
	$_Msqlstr="select groupID,groupcaption,afsub from groupstatus where termID={$GLOBALS['_Mactivetermid']} and groupID=$_Mgroupid";//and groupID in(select groupID from groups where finalgroup=1)";
	//echo $_Msqlstr;
	if($_Mresult=mysql_query($_Msqlstr))
	{
		$_Mgroupscount=mysql_num_rows($_Mresult);
		if($_Mgroupscount>0)
		{
			$_Mrow=mysql_fetch_array($_Mresult,MYSQL_ASSOC);
				$_Masub=$_Mrow['afsub'];
				if($_Masub==-1) $_Masub=$_Mgroupid;
			mysql_free_result($_Mresult);
			$_Mgroupstmp=explode(",",$_Masub);
			$_Mgroupidsstr='';
			for($i=0 ; $i<count($_Mgroupstmp) ; $i++)
				$_Mgroupidsstr.=$_Mgroupstmp[$i].',';
			$_Mgroupidsstr=substr($_Mgroupidsstr,0,strlen($_Mgroupidsstr)-1);	
			$_Msqlstr="select groupID,groupcaption,groupstatus,initialgroupstatus,groupsmaxslotsperday,subgroups,finalgroup,subgroupof,mastergroup,'1' as flag from groupstatus where termID={$GLOBALS['_Mactivetermid']} ";//and groupID in(select groupID from groups where finalgroup=1)";
			if($_Mresult=mysql_query($_Msqlstr))
			{
				$_Mgroupscount=mysql_num_rows($_Mresult);
				if($_Mgroupscount>0)
				{
					while($_Mrow=mysql_fetch_array($_Mresult,MYSQL_ASSOC))
						if( in_array($_Mrow['groupID'],$_Mgroupstmp))
							array_push($_Mgroups2,$_Mrow);
					mysql_free_result($_Mresult);
					for($i=0;$i<count($_Mgroups2) ; $i++)
						$_Mgroups[$i]=$_Mgroups2[$i];
					
				}else return false;
				return true;
			}
		}else return false;
	}else return false;
}

?>