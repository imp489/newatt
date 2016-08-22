<?php session_start();
  //@include "mygenclasses.php";
  @include_once "mygenclasses.php";
  $_Mcon=new _CLconnection();
  //remove later
  
  //remove to here

  //@include "mygenclasses.php";
  //should be optimized no connecting in each page and no setting of activeterm
  //$_Mcon=new connection('localhost','root','vertrigo','att');
  //$_Mcon->connectdb();
  $_Munits=array("f"=>1,"o"=>0.5,"e"=>0.5);
  $_Msc=$_POST['setchange'];//get manual changes 
  //$pos='c,2,1,1,1,f';
  $_Mitems=explode(",",$_Msc);
  $_Mchngtype=$_Mitems[0];
  //$datatype='s';
  if($_Mchngtype=='t')//change course teacher
  { // var_dump($_Mitems);
  	$_Mcourseid=$_Mitems[1];$$_Mcoursetimeslots=$_Mitems[3];$_Mgroupid=$_Mitems[4];$_Mcurteacherid=substr($_Mitems[7],1);$_Mslottype=$_Mitems[5];$_Mslotid=$_Mitems[6];
  	//$_Mcourseid=2;$_Mcurteacherid=1;$_Mgroupid=1;
  	echo _Fcoursechangeteachers($_Mcourseid,$$_Mcoursetimeslots,$_Mcurteacherid,$_Mgroupid,$_Mslotid,$_Mslottype);exit();  
  }
  if($_Mchngtype=='c')//change course slot
  { 
  	$_Mcourseid=$_Mitems[1];$_Mteacherid=$_Mitems[3];$_Mgroupid=$_Mitems[4];$_Mslotid=$_Mitems[6];$_Mnewslotid=$_Mitems[7];$_Mslottype=$_Mitems[5];
  //Full slot,Odd slot,Even slot
    //$_Mcourseid=2;$_Mteacherid=2;$_Mgroupid=1;$_Mslottype='f';$roomid=1;
  	echo _Fcourseslotchange($_Mteacherid,$_Mcourseid,$_Mgroupid,$_Mslotid,$_Mnewslotid);exit();
  }

  	
  	
  //****************************************

  function _Fcourseslotchange($_Mteacherid,$_Mcourseid,$_Mgroupid,$_Mslotid,$_Mnewslotid)
	{
	   _Fchangecourseassignment($_Mcourseid,$_Mgroupid,$_Mslotid,$_Mnewslotid,&$_Mprevcourseslotsar,&$_Mnewcourseslotar);//update assignment table
	   
	   _Fchangeteachertimestatus($_Mteacherid,$_Mgroupid,$_Mprevcourseslotsar,$_Mnewcourseslotar);//updating teacher times.

	   _Fchangegrouptimes($_Mgroupid,$_Mprevcourseslotsar,$_Mnewcourseslotar);//updating group times.

	}
	
function _Fchangegrouptimes($_Mgroupid,$_Mprevcourseslotsar,$_Mnewcourseslotar)
{
	  	$_Msqlstr="select groupstatus,initialgroupstatus from groupstatus where groupID={$_Mgroupid} and termID={$_SESSION['activetermid']}";
	  	$_Mresult=mysql_query($_Msqlstr);$_Mrow=mysql_fetch_assoc($_Mresult);
		$_Mtimes=$_Mrow['groupstatus'];$_Minitialtimes=$_Mrow['initialgroupstatus'];
		mysql_free_result($_Mresult);
		//from change teachertimes
		_Fchangestatusslots($_Mtimes,$_Minitialtimes,$_Mprevcourseslotsar,$_Mnewcourseslotar);	  	
		$_Msqlstr="update groupstatus set groupstatus='{$_Mtimes}' where groupID={$_Mgroupid} and termID={$_SESSION['activetermid']}";
		mysql_query($_Msqlstr);
}	


// taqir konad,
//be jaye neveshtane code joda baraye eslahe eslathaye dars o gorooh o ostad o otaq,
//codi be soorat functional neveshte shavad ke 6timestatus ra gerefte va taqirat ra rooye an emal namayad.  
  function _Fchangeteachertimestatus($_Mteacherid,$_Mgroupid,$_Mprevcourseslotsar,$_Mnewcourseslotar)
{
	  	$_Msqlstr="select times,initialtimes from tchrtimes where teacherID={$_Mteacherid} and termID={$_SESSION['activetermid']} and teachergroupid={$_Mgroupid}";

	  	$_Mresult=mysql_query($_Msqlstr);$_Mrow=mysql_fetch_assoc($_Mresult);
		$_Mtimes=$_Mrow['times'];$_Minitialtimes=$_Mrow['initialtimes'];
		mysql_free_result($_Mresult);
		//remove lasttime
		_Fchangestatusslots($_Mtimes,$_Minitialtimes,$_Mprevcourseslotsar,$_Mnewcourseslotar);
	  	$_Msqlstr="update tchrtimes set times='{$_Mtimes}' where teacherID={$_Mteacherid} and termID={$_SESSION['activetermid']} and teachergroupid={$_Mgroupid}";
		mysql_query($_Msqlstr);
}
	
function _Fchangestatusslots(&$_Mtimes,$_Minitialtimes,$_Mprevcourseslotsar,$_Mnewcourseslotar)
{
		for($i=0;$i<count($_Mprevcourseslotsar);$i+=4)
		{
			if($_Mprevcourseslotsar[$i]!=$_Mnewcourseslotar[$i])
			{
				if($_Mprevcourseslotsar[$i+1]=='s')
				{
					$_Mtimes[$_Mprevcourseslotsar[$i]]='f';$_Mtimes[$_Mprevcourseslotsar[$i]+1]='f';
				}
				else 
				{
					if($_Mtimes[$_Mprevcourseslotsar[$i]]==_Fcomplement($_Mprevcourseslotsar[$i+1]))
					{$_Mtimes[$_Mprevcourseslotsar[$i]]='f';$_Mtimes[$_Mprevcourseslotsar[$i]+1]='f';}
					else	
					{
						$_Mtimes[$_Mprevcourseslotsar[$i]]=$_Minitialtimes[$_Mprevcourseslotsar[$i]];
						$_Mtimes[$_Mprevcourseslotsar[$i]+1]=$_Minitialtimes[$_Mprevcourseslotsar[$i]+1];
					}
				}
				
			}			
		}
		//set new time
		for($i=0;$i<count($_Mnewcourseslotar);$i+=4)
		{
			if($_Mprevcourseslotsar[$i]!=$_Mnewcourseslotar[$i])
			{
				if($_Mprevcourseslotsar[$i+1]=='s')//**************will check for teachers time availability and set error report on error
				{
					{$_Mtimes[$_Mnewcourseslotar[$i]]='s';$_Mtimes[$_Mnewcourseslotar[$i]+1]='s';}
				}
				else 
				{
					if($_Mtimes[$_Mnewcourseslotar[$i]]==$_Mprevcourseslotsar[$i+1])
						{$_Mtimes[$_Mnewcourseslotar[$i]]='s';$_Mtimes[$_Mnewcourseslotar[$i]+1]='s';}
					else	
					{
						$_Mtimes[$_Mnewcourseslotar[$i]]=_Fcomplement($_Mprevcourseslotsar[$i+1]);
						$_Mtimes[$_Mnewcourseslotar[$i]+1]=_Fcomplement($_Mprevcourseslotsar[$i+1]);
					}
				}
				
			}			
		}
	
}
function multiexplode ($delimiters,$string) {
     
    $ready = str_replace($delimiters, $delimiters[0], $string);
     $launch = explode($delimiters[0], $ready);
     return  $launch;
 }
   
function _Fchangecourseassignment($_Mcourseid,$_Mgroupid,$_Mslotid,$_Mnewslotid,&$_Mprevcourseslotsar,&$_Mnewcourseslotar)
{
		$_Msqlstr="select courseunits,timeslots from assignments where courseID={$_Mcourseid} and termID={$_SESSION['activetermid']} and groupID={$_Mgroupid}";
	  	$_Mresult=mysql_query($_Msqlstr);$_Mrow=mysql_fetch_assoc($_Mresult);$courseunits=$_Mrow['courseunits'];
	  	$_Mcoursetimeslots=$_Mrow['timeslots'];
		$_Mprevcourseslotsar=multiexplode(array(",",":"),$_Mcoursetimeslots);
		$_Mnewcourseslotar=$_Mprevcourseslotsar;
		$_Mnewslotstr="";$_Mnewslotid1=$_Mnewslotid+1;
		for($i=0; $i<count($_Mprevcourseslotsar) ; $i+=4)
		{
			if($_Mprevcourseslotsar[$i]==$_Mslotid)
			{
				$_Mnewcourseslotar[$i]=$_Mnewslotid;$_Mnewcourseslotar[$i+2]=$_Mnewslotid+1;
				$_Mnewslotstr.="{$_Mnewslotid}:{$_Mprevcourseslotsar[$i+1]},{$_Mnewslotid1}:{$_Mprevcourseslotsar[$i+3]},";
			}
			else
				$$_Mnewslotstr.="{$_Mprevcourseslotsar[$i]}:{$_Mprevcourseslotsar[$i+1]},{$_Mprevcourseslotsar[$i+2]}:{$_Mprevcourseslotsar[$i+3]},";
		}
		$_Mnewslotstr=substr($_Mnewslotstr,0,strlen($_Mnewslotstr)-1);
	  	$_Msqlstr="update assignments set timeslots='{$_Mnewslotstr}' where courseID={$_Mcourseid} and termID={$_SESSION['activetermid']} and groupID={$_Mgroupid}";
	  	mysql_query($_Msqlstr);

}  
  
  function _Fcoursechangeteachers($_Mcourseid,$$_Mcoursetimeslots,$_Mcurteacherid,$_Mgroupid,$_Mslotid,$_Mslottype)
  {
  	_Fteacherscourseremove($$_Mcoursetimeslots,$_Mcourseid,$_Mgroupid,$courseunits,$_Mcoursetimeslots);
  	_Fteacherscourseadd($_Mcurteacherid,$$_Mcoursetimeslots,$_Mcourseid,$_Mgroupid,$courseunits,$_Mcoursetimeslots);
  }
     
  function _Fteacherscourseremove($_Mteacherid,$_Mcourseid,$_Mgroupid,&$courseunits,&$$_Mcoursetimeslots)
	{
		$_Msqlstr="select times from tchrtimes where teacherID={$_Mteacherid} and termID={$_SESSION['activetermid']} and teachergroupid={$_Mgroupid}";
	  	$_Mresult=mysql_query($_Msqlstr);
	  	$_Mrow=mysql_fetch_assoc($_Mresult);
	  	$_Mttimes=$_Mrow['times'];
	
	  	$_Msqlstr="select courseunits,timeslots from assignments where courseID={$_Mcourseid} and termID={$_SESSION['activetermid']} and groupID={$_Mgroupid} and teacherID={$_Mteacherid}";
		$_Mresult=mysql_query($_Msqlstr);$_Mrow=mysql_fetch_assoc($_Mresult);$courseunits=$_Mrow['courseunits'];
	  	$_Mcoursetimeslots=$_Mrow['timeslots'];
		$_Mcourseslots=multiexplode(array(",",":"),$_Mcoursetimeslots);
	
		for($i=0 ; $i<((count($_Mcourseslots)-1)) ; $i+=2)
		{	  	
		  	if($_Mcourseslots[$i+1]=='s')
		  	{$_Mttimes[$_Mcourseslots[$i]]='f';$_Mttimes[$_Mcourseslots[$i]+1]='f';$i+=2;}
		  	else
		  	{
		  		if($_Mttimes[$_Mcourseslots[$i]]=='s') {$_Mttimes[$_Mcourseslots[$i]]=$_Mcourseslots[$i+1];$_Mttimes[$_Mcourseslots[$i]+1]=$_Mcourseslots[$i+1];$i+=2;}
		  		else {$_Mttimes[$_Mcourseslots[$i]]='f';$_Mttimes[$_Mcourseslots[$i]+1]='f';$i+=2;}
		  	}
		}
	  	$_Msqlstr="update tchrtimes set times='{$_Mttimes}',teachercurslots=teachercurslots-{$courseunits} where teacherID={$_Mteacherid} and termID={$_SESSION['activetermid']} and teachergroupid={$_Mgroupid}";
	  	mysql_query($_Msqlstr);

	  	$_Msqlstr="update assignments set teacherID=0 where courseID={$_Mcourseid} and termID={$_SESSION['activetermid']} and groupID={$_Mgroupid} and teacherID={$_Mteacherid}";
		mysql_query($_Msqlstr);
	}
  
function _Fteacherscourseadd($_Mteacherid,$$_Mcoursetimeslots,$_Mcourseid,$_Mgroupid,$courseunits,$_Mcoursetimeslots)//write another function for insert course
{
  	$_Msqlstr="select times,teachername,teacherfamily from tchrtimes where teacherID={$_Mteacherid} and termID={$_SESSION['activetermid']} and teachergroupid={$_Mgroupid}";
  	$_Mresult=mysql_query($_Msqlstr);
  	$_Mrow=mysql_fetch_assoc($_Mresult);
  	$_Mttimes=$_Mrow['times'];$teachername=$_Mrow['teachername'];$teacherfamily=$_Mrow['teacherfamily'];
	
	$_Mcourseslots=multiexplode(array(",",":"),$_Mcoursetimeslots);

  	for($i=0 ; $i<((count($_Mcourseslots)-1)) ; $i+=2)
	{	  	  	  	  	  	
	  	if($_Mcourseslots[$i+1]=='f')
	  	{$_Mttimes[$_Mcourseslots[$i]]='s';$_Mttimes[$_Mcourseslots[$i]+1]='s';$i+=2;}
	  	else
	  	{
	  		if($_Mttimes[$_Mcourseslots[$i]]=='f') {$_Mttimes[$_Mcourseslots[$i]]=_Fcomplement($_Mcourseslots[$i+1]);$_Mttimes[$_Mcourseslots[$i]+1]=_Fcomplement($_Mcourseslots[$i+1]);$i+=2;}
	  		else {$_Mttimes[$_Mcourseslots[$i]]='s';$_Mttimes[$_Mcourseslots[$i]+1]='s';$i+=2;}
	  	}
	}
  	$_Msqlstr="update tchrtimes set times='{$_Mttimes}',teachercurslots=teachercurslots+{$courseunits} where teacherID={$_Mteacherid} and termID={$_SESSION['activetermid']} and teachergroupid={$_Mgroupid}";
  	mysql_query($_Msqlstr);

  	$_Msqlstr="update assignments set teacherID={$_Mteacherid},teachername='{$teachername}',teacherfamily='{$teacherfamily}' where courseID={$_Mcourseid} and 
  			 termID={$_SESSION['activetermid']} and groupID={$_Mgroupid}";//prevteacher lahaz shavad
	//echo $_Msqlstr;
  	mysql_query($_Msqlstr);
}	

  
  function _Fcoursepossibleteachers($_Mcourseid,$_Mcurteacherid,$_Mgroupid)
  {
  	$_Moutstr='';
  	if($_Mresult=mysql_query("select groupstatus from groupstatus where groupID={$_Mgroupid} and  termID={$_SESSION['activetermid']}"))
  	{
  		$_Mrow=mysql_fetch_array($_Mresult);
  		$_Mgroupstatus=$_Mrow['groupstatus'];
  		mysql_freeresult($_Mresult);
  	}
  	
  	$_Mnotincourseslots='*';$_Moutstr.='*';
  	$_Mpossibletimes=array_fill(0,98,'n');
  	$_Mteacher1=new _CLteacher();
  	$_Mgroup1=new _CLgroup();
  	$_Mgroup1->setgroupid($_Mgroupid);
  	$_Mgroup1->setgroupstatus($_Mgroupstatus);
  	$_Mcourse1=new _CLcourse();
  	$_Mcourse1->setcourseunits(0,$courseunits);//**will change to correct units
  	$_Msqlstr="select teacherID,teachername,teacherfamily,times from tchrtimes where teachercourseids like '%{$_Mcourseid}%' and curslotsfordays<=(maxslotperday-{$courseunits}) and termID={$_SESSION['activetermid']} order by teacherfamily";
  	$_Mresult=mysql_query($_Msqlstr);
  	while($_Mrow=mysql_fetch_assoc($_Mresult))
  	{
	 	if($_Mcurteacherid!=$_Mrow['teacherID'])
	 	{
	  		$_Mteacher1->teacherid=$_Mrow['teacherID'];
		 	$_Mteacher1->setteachertimes($_Mrow['times']);
	  		if(_Fchecktimestchrgrpcnstr($_Mteacher1,$_Mgroup1,$_Mcourse1,$_Mpossibletimes))
		  	{
		  		if($_Mcourse1->getcoursehaspref())
		  		{  
		  			if(_Fmeetscourseconst($_Mcourse1,$_Mpossibletimes))
	  					 $_Moutstr.='#'.$_Mrow['teacherID'].'~'.$_Mrow['teachername'].' '.$_Mrow['teacherfamily'];
	  				else $_Mnotincourseslots.='#'.$_Mrow['teacherID'].'~'.$_Mrow['teachername'].' '.$_Mrow['teacherfamily'];
		  		}
		  		$_Moutstr.='#'.$_Mrow['teacherID'].'~'.$_Mrow['teachername'].' '.$_Mrow['teacherfamily'];
		  	}
	 	}
  	}
  	mysql_freeresult($_Mresult); 	
  	if($_Mnotincourseslots!='*')
  	  $_Moutstr.=$_Mnotincourseslots;
  	return $_Moutstr;
  	
  }
  
  function _Fcoursepossibleslots($_Mcourseid,$_Mteacherid,$_Mgroupid,$_Mslottype,$roomid)  
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

 	if($_Mresult=mysql_query("select roomstatus1 from roomstatus where roomID={$roomid} and  termID={$_SESSION['activetermid']}"))
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
  	//echo "{$_Mcourseid} - {$roomid} - {$_Mteacherid} - {$_Mgroupid}";
  	return $_Moutstr;  	
  }

  
   
?>