<?php session_start();
  //@include "mygenclasses.php";
  @include_once "mygen_lib.php";  
  @include_once "mygenclasses.php";

  //@include_once "generatett.php";
  $_Mcon=new _CLconnection();
  $_Mslotlen=2;$_Mdayslots=12;$_Mtotslots=84;

  $_Mactivetermid=$_SESSION['activetermid'];
  $_Mpos=$_POST['pos'];
  $_Mitems=explode(",",$_Mpos);
  $_Mdatatype=$_Mitems[0];
  
/*
  $_Mactivetermid=2;
  $_Mpos='pos=t,2!a1,0,1,5,f,44';
  $_Mdatatype='t';
  $_Mitems=explode(",",$_Mpos);
//
  $_Mactivetermid=2;
  $_Mpos='c,65421!a1,0,2,5,f,5';
  $_Mdatatype='c';
  $_Mitems=explode(",",$_Mpos);
*/  

  if($_Mdatatype=='t')//change course teacher  input str : 
  {
  	$_Mcourseid=_Fgetcourseidfrom($_Mitems[1],$_Mcoursepart);$_Mcurteacherid=$_Mitems[3];$_Mgroupid=$_Mitems[4];
  	//$_Mcourseid=418;$_Mcurteacherid=4;$_Mgroupid=5;
  	//echo $_Mpos;
  	echo _Fcoursepossibleteachers($_Mcourseid,$_Mcoursepart,$_Mcurteacherid,$_Mgroupid);exit();  
  }
  if($_Mdatatype=='c')//change course slot  input str : 10231!c1,0,52,5,f,30 : current slot no    courseid!coursepart,
  {  $_Mcourseid=_Fgetcourseidfrom($_Mitems[1],$_Mcoursepart);$_Mroomid=$_Mitems[2];$_Mteacherid=$_Mitems[3];$_Mgroupid=$_Mitems[4];$_Mslottype=$_Mitems[5];
  //Full slot,Odd slot,Even slot
   // $_Mcourseid=418;$_Mteacherid=3;$_Mgroupid=5;$_Mslottype='f';$_Mroomid=1;
  	echo _Fcoursepossibleslots($_Mcourseid,$_Mcoursepart,$_Mteacherid,$_Mgroupid,$_Mslottype,$_Mroomid);exit();
  }

  //****************************************
	//920702
  function _Fgetcourseidfrommoshakhase($_Mcourseid)//will check if groupid is needed for not mistaking course
  {
  	$_Msqlstr="select coursecode from termcoursestatus where courseID=$_Mcourseid and  termID={$GLOBALS['_Mactivetermid']}";
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
  		if(mysql_num_rows($_Mresult)>0)
  		{
  			if($_Mrow=mysql_fetch_assoc($_Mresult))
  			{
  				$_Mcoursecode=$_Mrow['coursecode'];
  			}else return 0;
  		}else return 0;
  	}else return -1;
  	return $_Mcoursecode;			
  }  	

  	  
  //****************************************
  function _Fcoursepossibleteachers($_Mcourseid,$_Mcoursepart,$_Mcurteacherid,$_Mgroupid)
  {
  	$_Moutstr='';
  	$_Mtimeslots=array();$_Mslotstate=array();$_Monlyhas="";
  	//$_Mteachershascoursenousable="";$_Mteachershascoursenotime="";
  	$_Mcoursecode=_Fgetcourseidfrommoshakhase($_Mcourseid);//920702
  	$_Mout=_Fgetcourseslots($_Mcourseid,$_Mtimeslots,$_Mslotstate,$_Mcurteacherid,$_Mgroupid);
	if($_Mout!=1) return $_Mout;
	if($_Mcoursepart[0]=='a') $s=" and coursepart like '%a%' ";else $s=" and coursepart not like '%a%' ";
	$_Msqlstr="select teacherID,teachername,teacherfamily,times,teachercourseids,courseparts,teachergroupid from tchrtimes where teacherID<>$_Mcurteacherid and teachercourseids like '%{$_Mcoursecode}%' and  termID={$GLOBALS['_Mactivetermid']} order by teacherfamily";//add later: curslotsfordays<=(maxslotperday-{$courseunits}) and	
	if($_Mresult=mysql_query($_Msqlstr))
	{
		if(mysql_num_rows($_Mresult)>0)
		{
			while($_Mrow=mysql_fetch_assoc($_Mresult))
			{
				//if($_Mcurteacherid!=$_Mrow['teacherID']) inserted in sql
	 			{
	 				$_Mcourseids=explode(",",$_Mrow['teachercourseids']);
	 				$_Mcourseparts=explode(",",$_Mrow['courseparts']);
	 				$i=array_search($_Mcourseid,$_Mcourseids);
	 				$_Mcpart=$_Mcourseparts[$i];
	 				if(($_Mcoursepart[0]!='a' & $_Mcpart[0]!='a')||($_Mcoursepart[0]=='a' & $_Mcpart[0]=='a'))
	 				{
	  					$_Mttimes=$_Mrow['times'];
	  					$_Mok=_Fteacherhastime4course($_Mttimes,$_Mtimeslots,$_Mslotstate);
	  					if($_Mok)	$_Moutstr.='#'.$_Mrow['teacherID'].'~'.$_Mrow['teachername'].' '.$_Mrow['teacherfamily'].'~'.$_Mrow['teachergroupid'];
	  					else $_Monlyhas.='#'.$_Mrow['teacherID'].'~'.$_Mrow['teachername'].' '.$_Mrow['teacherfamily'].'~'.$_Mrow['teachergroupid'];
	 				}
		 		}
			}
		  	mysql_free_result($_Mresult); 	
		}else return 0;
	}
  	if ($_Moutstr=='' && $_Monlyhas=='') return 0;
	return $_Moutstr.'^*^'.$_Monlyhas;
  }
  
  function _Fteacherhastime4course($_Mttimes,$_Mtimeslots,$_Mslotstate)
  {
  	$_Mcan=1;
	for($i=0 ; $i<count($_Mtimeslots) ; $i++)
	{
		if(($_Mttimes[$_Mtimeslots[$i]]!=$_Mslotstate[$i])&($_Mttimes[$_Mtimeslots[$i]]!='f'))//teacher can pick the course if that time is free they can be not equal
 	       $_Mcan=0;
	} 
  	return $_Mcan;
  }
  
  function _Fgetcourseslots($_Mcourseid,&$_Mtimeslots,&$_Mslotstate,$_Mcurteacherid,$_Mgroupid)
  {
  	$_Msqlstr="select timeslots from assignments where courseID={$_Mcourseid} and teacherID={$_Mcurteacherid} and groupID={$_Mgroupid} and termID={$GLOBALS['_Mactivetermid']}";
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
  		$_Mts='';
  		while($_Mrow=mysql_fetch_array($_Mresult))
  			$_Mts.=$_Mrow['timeslots'].',';
  		$_Mts=substr($_Mts,0,strlen($_Mts)-1);	

		if(mysql_num_rows($_Mresult)>0)
		{
			$_Mrow=mysql_fetch_assoc($_Mresult);
			_Fslottype2slotstypes($_Mts,$_Mtimeslots,$_Mslotstate);
			foreach ($_Mslotstate as &$_Mstate)
			{if($_Mstate=='s') $_Mstate='f';}
			return 1;
		}else return 0;
  	}else return -1;
  }

  
  
  function _Fcoursepossibleslots($_Mcourseid,$_Mcoursepart,$_Mteacherid,$_Mgroupid,$_Mslottype,$_Mroomid)  
  {
  	
  	$_Mgroup1=new _CLgroup();_Fsetgroupinfo($_Mgroup1,$_Mgroupid);
  	$_Mteacher1=new _CLteacher();_Fsetteacherinfo($_Mteacher1,$_Mteacherid);
  	$_Mcourse1=new _CLcourse();_Fsetcourseinfo($_Mcourse1,$_Mcoursepart,$_Mcourseid,$_Mgroupid);  	
  	$_Mroom1=new _CLroom();_Fsetroominfo($_Mroom1,$_Mroomid);
  	$_Moutstr='';$_Mfound=false;$_Mpossibletimes=str_repeat('n',98);
  	
  	if(_Fchecktimestchrgrpcnstr($_Mteacher1,$_Mgroup1,$_Mcourse1,$_Mpossibletimes))
  	{
  		if($_Mcourse1->getcoursehaspref())
  		{  
  			if(_Fmeetscourseconst($_Mcourse1,$_Mpossibletimes,$schedulabaleslots))
  		  	{$_Mfound=true;}
			else 
			{$_Mfound=false;}
		}else {$_Mfound=true;}
  	}else {$_Mfound=false;}

	
  	if($_Mfound==true)
  	{
  		$_Mroomstatus=$_Mroom1->getroomstatus();
  		for($i=0; $i<98 ; $i++)
  		{  			
  			if(($_Mpossibletimes[$i]=='f')||($_Mpossibletimes[$i]==$_Mslottype))
  			  $_Moutstr.='#'.$i.'~'.$_Mpossibletimes[$i].'~'.$_Mroomstatus[$i];
  		}  		
  	}  	
  	//echo "{$_Mcourseid} - {$_Mroomid} - {$_Mteacherid} - {$_Mgroupid}";
  	return $_Moutstr;  	
  }
  
  
  
function _Fsetgroupinfo(&$_Mgroup1,$_Mgroupid)
{
  	if($_Mresult=mysql_query("select groupstatus from groupstatus where groupID={$_Mgroupid} and  termID={$GLOBALS['_Mactivetermid']}"))
  	{
  		if(mysql_num_rows($_Mresult)>0)
  		{
  			$_Mrow=mysql_fetch_array($_Mresult);
  			$_Mgroupstatus=$_Mrow['groupstatus'];
  			mysql_freeresult($_Mresult);
   			$_Mgroup1->setgroupid($_Mgroupid);
  			$_Mgroup1->setgroupstatus($_Mgroupstatus);
  			return 1;
  		}else return 0;
  	}else return -1;
}
  
function _Fsetteacherinfo(&$_Mteacher1,$_Mteacherid)
{  
  	if($_Mresult=mysql_query("select times from tchrtimes where teacherID={$_Mteacherid} and  termID={$GLOBALS['_Mactivetermid']}"))
  	{
  		if(mysql_num_rows($_Mresult)>0)
  		{
  			$_Mrow=mysql_fetch_array($_Mresult);
  			mysql_freeresult($_Mresult);
  			$_Mteacher1->setteacherid($_Mteacherid);
			$_Mteacher1->setteachertimes($_Mrow['times']);
			return 1;
  		}else return 0;
  	}else return -1;
}
function _Fsetcourseinfo(&$_Mcourse1,$_Mcoursepart,$_Mcourseid,$_Mgroupid)
{
	if($_Mcoursepart[0]=='a') $s=" and coursepart like '%a%' ";else $s=" and coursepart not like '%a%' ";
  	$_Msqlstr="select * from termcoursestatus where courseID={$_Mcourseid} $s and groupidconf1={$_Mgroupid} and  termID={$GLOBALS['_Mactivetermid']}";
	if($_Mresult=mysql_query($_Msqlstr))
  	{
  		if(mysql_num_rows($_Mresult)>0)
  		{
  			$_Mrow=mysql_fetch_assoc($_Mresult);
  			mysql_freeresult($_Mresult);
  			$_Mcourse1->setcourseid($_Mcourseid);
  			$_Mcourse1->setcoursehaspref($_Mrow['coursehaspref']);
  			$_Mcourse1->setcoursepreftimes($_Mrow['coursepreferedtimes']);
  			$_Mcourse1->setcourseunits(0,2);// will check later 2 or 1 ?
  			
  			
		    $_Mcourse1->setcourseid($_Mrow["courseID"]);//"courseid"
			$_Mcourse1->setcoursecaption($_Mrow["coursecaption"]);
			$_Mcourse1->setcourseunits($_Mrow["courseAunits"],$_Mrow["courseTunits"]);
			$_Mcourse1->setcoursepreftimes($_Mrow["coursepreferedtimes"]);
			$_Mcourse1->setcoursehaspref($_Mrow["coursehaspref"]);
			$_Mcourse1->setneededroomtypeid($_Mrow["neededroomtypeID"]);
			//$_Mcourse1->setneededroomtypeaid($_Mrow["neededroomtypeAID"]);
			$_Mcourse1->setcoursepart($_Mrow["coursepart"]);
			$_Mcourse1->setcoursepartunits($_Mrow["coursepartunits"]);
			$_Mcourse1->setotherpart1slots($_Mrow["otherpart1slots"]);
			$_Mcourse1->setotherpart2slots($_Mrow["otherpart2slots"]);
			$_Mcourse1->setcoursemaingroupid($_Mrow["groupID"]);
			$_Mcourse1->setcoursegroupidconf($_Mrow["groupidconf1"]);
			$_Mcourse1->setgroupidconf2($_Mrow["groupidconf2"]);
			$_Mcourse1->setgroupidconf3($_Mrow["groupidconf3"]);
			$_Mcourse1->setschwithgroupid($_Mrow["awgroupID"]);
			$_Mcourse1->setcoursecode($_Mrow["coursecode"]);
			if($_Mrow["coursepart"][0]=='a')
				$_Mcourse1->setpartstchrs($_Mrow["courseAunitstchrs"]);
			else $_Mcourse1->setpartstchrs($_Mrow["courseTunitstchrs"]);

			//$_Mcourse1->setcoursecode($_Mrow["coursecode"]); no course code needed 
			_Fsetcoursecnsts($_Mcourse1,$_Mgroupscheduledcourses);
  			
  			
  			
  			return 1;
  		}else return 0;
  	}else return -1;
	
}
function _Fsetroominfo(&$_Mroom1,$_Mroomid)
{
 	if($_Mresult=mysql_query("select roomstatus from roomstatus where roomID={$_Mroomid} and  termID={$GLOBALS['_Mactivetermid']}"))
  	{
  		if(mysql_num_rows($_Mresult)>0)
  		{
  			$_Mrow=mysql_fetch_array($_Mresult);
  			$_Mroomstatus=$_Mrow['roomstatus'];
  			mysql_freeresult($_Mresult);
  			$_Mroom1->setroomid($_Mroomid);
  			$_Mroom1->setroomstatus($_Mrow['roomstatus']);
  			return 1;
  		}else return 0;
  	}else return  -1;
	
}
function _Fgetcourseidfrom($s,&$_Mcoursepart)
{
	$cidcpt=explode("!",$s);
	$_Mcourseid=$cidcpt[0];
	$_Mcoursepart=$cidcpt[1];
	return $_Mcourseid;
}  

?>