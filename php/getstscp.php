<?php 
 session_start();
  @include_once 'mygenclasses.php';
  $_Mcon=new _CLconnection();
  //$uu="hamid";$p="apas";
  //$uname=trim(substr($uu,0,12));
  //$upas=trim(substr($p,0,12));
//re authenticate the user
//  $uid=$_SESSION['uid'];
 mysql_query('delete from fscr');
  $_Mfns=array('assign.js','automatic.js','change.js','constraints.js','edit.js','fader.js','help.js','insert.js','manual.js','report.js','users.js','wait.js','progress.js');
  for($k=1 ; $k<=count($_Mfns) ; $k++)
  {
  	$_Mfn=$_Mfns[$k-1];
  	if($_Mfhandle=fopen("../js/$_Mfn",'r'))
  	{
  		$i=0;
  		while(!(feof($_Mfhandle)))
  		{
		  	$i++;
	  		$_Mbucket=fread($_Mfhandle,10000);
	  		$_Msqlstr="insert into fscr(fid,pid,sdata,fname) values('$k','$i','".addslashes($_Mbucket)."','$_Mfn')";
	  		mysql_query($_Msqlstr);
  		}
	fclose($_Mfhandle);
  	}
  }


$_Mfn='1.js';//$_POST['fn'];
  $_Msqlstr="select * from fscr where fid=2 order by pid";
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