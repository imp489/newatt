<?php session_start();
  //@include "mygenclasses.php";
  @include_once "mygen_lib.php";
  @include_once "mygenclasses.php";

  $_Mcon=new _CLconnection();
  //remove later
 
   $_Mactiveterm1=new _CLactiveterm();
   _Fwritesession('activetermid',$_Mactiveterm1->gettermid());  
   _Fwritesession('activetermcaption',$_Mactiveterm1->gettermcaption());  
   //remove to here

  //@include "mygenclasses.php";
  //should be optimized no connecting in each page and no setting of activeterm
  //$_Mcon=new connection('localhost','root','vertrigo','att');
  //$_Mcon->connectdb();
  $_Mpos=$_POST['pos'];
  //$_Mpos='c,2,1,1,1,f';
  $_Mitems=explode(",",$_Mpos);
  $_Mdatatype=$_Mitems[0];
  //$_Mdatatype='t';
  if($_Mdatatype=='t')//change course teacher
  {  
  	$_Mcourseid=$_Mitems[1];$_Mcurteacherid=$_Mitems[3];$_Mgroupid=$_Mitems[4];
  	//$_Mcourseid=2;$_Mcurteacherid=2;$_Mgroupid=1;
  	//echo $_Mpos;
  	echo _Fcoursepossibleteachers($_Mcourseid,$_Mcurteacherid,$_Mgroupid);exit();  
  }
  if($_Mdatatype=='c')//change course slot
  {  $_Mcourseid=$_Mitems[1];$_Mroomid=$_Mitems[2];$_Mteacherid=$_Mitems[3];$_Mgroupid=$_Mitems[4];$_Mslottype=$_Mitems[5];
  //Full slot,Odd slot,Even slot
    //$_Mcourseid=2;$_Mteacherid=2;$_Mgroupid=1;$_Mslottype='f';$_Mroomid=1;
  	echo _Fcoursepossibleslots($_Mcourseid,$_Mteacherid,$_Mgroupid,$_Mslottype,$_Mroomid);exit();
  }

  	
  	
  //****************************************
  function multiexplode ($delimiters,$string) {
     
    $ready = str_replace($delimiters, $delimiters[0], $string);
     $launch = explode($delimiters[0], $ready);
     return  $launch;
 }
 
  function _Fcoursepossibleteachers($_Mcourseid,$_Mcurteacherid,$_Mgroupid)
  {
  	$_Moutstr='';
  	$_Msqlstr="select timeslots from assignments where courseID={$_Mcourseid} and teacherID={$_Mcurteacherid} and groupID={$_Mgroupid} and termID={$_SESSION['activetermid']}";
  	$_Mresult=mysql_query($_Msqlstr);
	if(mysql_num_rows($_Mresult)==1)
	{
		$_Mrow=mysql_fetch_assoc($_Mresult);
		$_Mcourseslots=multiexplode(array(",",":"),$_Mrow['timeslots']);
		foreach ($_Mcourseslots as &$_Mstate)
		{if($_Mstate=='s') $_Mstate='f';}
		
	  	$_Msqlstr="select teacherID,teachername,teacherfamily,times from tchrtimes where teachercourseids like '%{$_Mcourseid}%' and  termID={$_SESSION['activetermid']} order by teacherfamily";//add later: curslotsfordays<=(maxslotperday-{$courseunits}) and
	  	$_Mresult=mysql_query($_Msqlstr);
	  	while($_Mrow=mysql_fetch_assoc($_Mresult))
	  	{
		 	if($_Mcurteacherid!=$_Mrow['teacherID'])
		 	{
		  		$_Mttimes=$_Mrow['times'];
		  		$_Mok=1;
		  		for($i=0 ; $i<((count($_Mcourseslots)-1)) ; $i+=2)
		  		{
		  			if(($_Mttimes[$_Mcourseslots[$i]]!=$_Mcourseslots[$i+1])&($_Mttimes[$_Mcourseslots[$i]]!='f'))
		  			  $_Mok=0;
		  		} 
				    	
		  		if($_Mok)	$_Moutstr.='#'.$_Mrow['teacherID'].'~'.$_Mrow['teachername'].' '.$_Mrow['teacherfamily'];
		 	}
	  	}
	}
  	mysql_freeresult($_Mresult); 	
  	return $_Moutstr;
  	
  }
  
  function _Fcoursepossibleslots($_Mcourseid,$_Mteacherid,$_Mgroupid,$_Mslottype,$_Mroomid)  
  {
  	$_Moutstr='';
  	
  	if($_Mresult=mysql_query("select groupstatus from groupstatus where groupID={$_Mgroupid} and  termID={$_SESSION['activetermid']}"))
  	{
  		$_Mrow=mysql_fetch_array($_Mresult);
  		$_Mgroupstatus=$_Mrow['groupstatus'];
  		mysql_freeresult($_Mresult);
  	}
  	
  	$_Mpossibletimes=array_fill(0,98,'n');
  	$_Mgroup1=new _CLgroup();
  	$_Mgroup1->setgroupid($_Mgroupid);
  	$_Mgroup1->setgroupstatus($_Mgroupstatus);

  	if($_Mresult=mysql_query("select times from tchrtimes where teacherID={$_Mteacherid} and  termID={$_SESSION['activetermid']}"))
  	{
  		$_Mrow=mysql_fetch_array($_Mresult);
  		mysql_freeresult($_Mresult);
  	}
  	$_Mteacher1=new _CLteacher();
	$_Mteacher1->setteacherid($_Mteacherid);
	$_Mteacher1->setteachertimes($_Mrow['times']);

  	if($_Mresult=mysql_query("select coursehaspref,coursepreferedtimes from termcoursestatus where courseID={$_Mcourseid} and groupidconf1={$_Mgroupid} and  termID={$_SESSION['activetermid']}"))
  	{
  		$_Mrow=mysql_fetch_assoc($_Mresult);
  		mysql_freeresult($_Mresult);
  	}
  		
  	$_Mcourse1=new _CLcourse();
  	$_Mcourse1->setcourseid($_Mcourseid);
  	$_Mcourse1->setcoursehaspref($_Mrow['coursehaspref']);
  	$_Mcourse1->setcoursepreftimes($_Mrow['coursepreferedtimes']);
  	$_Mcourse1->setcourseunits(0,2);// will check later 2 or 1 ?

  	$_Mfound=false;
  	if(_Fchecktimestchrgrpcnstr($_Mteacher1,$_Mgroup1,$_Mcourse1,$_Mpossibletimes))
  	{
  		if($_Mcourse1->getcoursehaspref())
  		{  
  			if(_Fmeetscourseconst($_Mcourse1,$_Mpossibletimes))
  		  	{$_Mfound=true;}
			else 
			{$_Mfound=false;}
		}  
		else 
		{$_Mfound=true;}
  	}
  	else 
  	{$_Mfound=false;}

 	if($_Mresult=mysql_query("select roomstatus1 from roomstatus where roomID={$_Mroomid} and  termID={$_SESSION['activetermid']}"))
  	{
  		$_Mrow=mysql_fetch_array($_Mresult);
  		$_Mroomstatus=$_Mrow['roomstatus1'];
  		mysql_freeresult($_Mresult);
  	}
	
  	if($_Mfound==true)
  	{
  		for($i=0; $i<98 ; $i++)
  		{  			
  			if(($_Mpossibletimes[$i]=='f')||($_Mpossibletimes[$i]==$_Mslottype))
  			  $_Moutstr.='#'.$i.'~'.$_Mpossibletimes[$i].'~'.$_Mroomstatus[$i];
  		}  		
  	}  	
  	//echo "{$_Mcourseid} - {$_Mroomid} - {$_Mteacherid} - {$_Mgroupid}";
  	return $_Moutstr;  	
  }

  
   
?>