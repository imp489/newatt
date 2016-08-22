<?php session_start();
  //@include "mygenclasses.php";
    @include_once "mygenlib.php";

  $_Mcon=new _CLconnection();

  //remove later
  
   $_Muser1=new _CLatuser();
   $_Muser1->setusername("testuser");
   $_Mactiveterm1=new _CLactiveterm();
   _Fwritesession($_Muser1,$_Mactiveterm1);  
  //remove to here

  //@include "mygenclasses.php";
  //should be optimized no connecting in each page and no setting of activeterm
  //$_Mcon=new connection('localhost','root','vertrigo','att');
  //$_Mcon->connectdb();
  $_Mtid=$_POST['teacherid'];$_Mttimes=$_POST['teachertimes'];$_Mgroupid=$_POST['groupid'];
  //$_Mtid=1;$_Mttimes='nnnnnnffffffeeeeoo';$_Mgroupid=1;
  echo _Fsaveteachertimestodb($_Mtid,$_Mttimes,$_Mgroupid);
  
  
  

  //****************************************
  function _Fsaveteachertimestodb($_Mtid,$_Mttimes,$_Mgroupid)
  {
  	$_Msqlstr="select * from tchrtimes where teacherID={$_Mtid} and termID={$_SESSION['activetermid']} and teachergroupid={$_Mgroupid}";
  	$_Mresult=mysql_query($_Msqlstr);
  	if(mysql_num_rows($_Mresult)==0)
  	{
  		$_Msqlstr="insert into tchrtimes(teacherID,termID,teachergroupid,times) values
  		   		 ('{$_Mtid}','{$_SESSION['activetermid']}','{$_Mgroupid}','{$_Mttimes}')";
  		$_Mresult=mysql_query($_Msqlstr);  		
  		if($_Mresult)
  		{return 1;}
  		else {return 0;  }
  	}
  	else 
  	{
  		$_Msqlstr="update tchrtimes set times='{$_Mttimes}' where teacherID={$_Mtid} and termID={$_SESSION['activetermid']} and teachergroupid={$_Mgroupid}";
  		$_Mresult=mysql_query($_Msqlstr);  		
  		if($_Mresult)
  		{return 1;}
  		else {return 0;  }  		
  	}	
  }

?>