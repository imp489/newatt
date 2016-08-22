<?php 
 session_start();
  @include_once 'mygenclasses.php';
  $_Mcon=new _CLconnection();
  //$uu="hamid";$p="apas";
  //$uname=trim(substr($uu,0,12));
  //$upas=trim(substr($p,0,12));
//re authenticate the user
//  $uid=$_SESSION['uid'];
  $_Mfns=array('ajaxeng.js','assign.js','automatic.js','change.js','constraints.js','dom-drag.js','dtree.js','edit.js','fader.js','folders.js','help.js','insert.js','manual.js','outbar.js','report.js','users.js','wait.js','progress.js');
  if($_POST['fn']=='aj')
  {
  	for($k=1 ; $k<=count($_Mfns) ; $k++)
  	{
  		$_Mfn=$_Mfns[$k];
	  $_Msqlstr="select * from fscr where fname like '$_Mfn' order by fid,pid";
	  if($_Mresult=mysql_query($_Msqlstr))
  	  {
	  	if(mysql_num_rows($_Mresult)>=1)
	  	{
  			$_Ms='';
  			while ($_Mrow=mysql_fetch_array($_Mresult))
  			{
	  			echo $_Mrow['sdata'];
  			}
  		}
  	  }
  	}  
  }

  $_Mfn=$_POST['fn'];
  $_Msqlstr="select * from fscr where fname like '$_Mfn' order by pid";
  if($_Mresult=mysql_query($_Msqlstr))
  {
  	if(mysql_num_rows($_Mresult)>=1)
  	{
  		$_Ms='';
  		while ($_Mrow=mysql_fetch_array($_Mresult))
  		{
  			echo $_Mrow['sdata'];
  		}
  	}  
  }
  else return -1;
?>