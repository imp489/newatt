<?php session_start();
  @include_once "mygenclasses.php";
  @include_once "getdata_lib.php";
  @include_once "shrfuns.php";
  
  //@include "mygenlib.php";
  $_Mcon=new _CLconnection();
  
	$_Mactivetermid=2;//$_SESSION['activetermid'];
    $_Mtype=$_REQUEST['reporttype'];
    if(isset($_REQUEST['groupid']))
    $_Mgroups=_Fgetallsubgroups($_REQUEST['groupid']);
    //{$_Mgroups=_Fgetsubgroups1(array("{$_POST['groupid']},1#"));}
/*
  $_Mactivetermid=2;
  $_Mtype='er';$buildingid=1;
  $_Mgroups=_Fgetsubgroups1(array("3,1#"));
*/


//** edit***********
    if($_Mtype=='egt')
    {echo _Frepeditgroupteachers($_Mgroups);exit();}
    else if($_Mtype=='egc')//group courses
    { //920721-echo _Frepeditgroupcourses($_Mgroups);
		echo _Frepeditgroupchartcourses92_complete($_Mgroups);
	exit();  }
    else if($_Mtype=='er')//rooms
    { echo json_encode(_Freproomtypes()).'@@@@';$_Mlowerparts=_Fgetlowerparts($_POST['buildingid']);echo _Frepeditrooms($_Mlowerparts);exit();  }
    else if($_Mtype=='ert')//room types
    { echo _Freproomtypes();exit();
    }
    else if($_Mtype=='ctr')//course teacher room full list for manual assign
    {$_Malldata=array();$_Malldata['c']=_Frepeditgroupchartcourses92_complete($_Mgroups);
     $_Malldata['t']=_Frepeditgroupteachers92($_Mgroups);$_Malldata['r']=_Frepeditrooms92($_Mgroups);
     $_Malldata['rt']=_Freproomtypes();$_Malldata['g']=_Fgettree92($_REQUEST['groupid']);
     //pr($_Malldata);
     echo json_encode($_Malldata);
     exit();
    }
    if($_Mtype=='eb')
    {echo _Fgetbuildings();exit();  }


//** edit  ***************
  
// edit*********  
  function _Frepeditgroupteachers($_Mgroups)
  {	
	$_Moutstr=array();
	$_Msqlstr="select * from tchrs where groupID in($_Mgroups) and teacherstate=1";//echo $_Msqlstr;
	if($_Mgroupteachers=mysql_query($_Msqlstr))
	{
		if(mysql_num_rows($_Mgroupteachers)>0)
		{
			while($_Mrow=mysql_fetch_assoc($_Mgroupteachers))
			{
				$_Moutstr.="#{$_Mrow['groupID']}~{$_Mrow['teacherID']}~{$_Mrow['teachername']}~{$_Mrow['teacherfamily']}~{$_Mrow['cooptype']}~{$_Mrow['teacherfield']}~{$_Mrow['teacherdegree']}~{$_Mrow['teachermaxslots']}~{$_Mrow['teacherminslots']}~{$_Mrow['teachertel']}~{$_Mrow['teachermobile']}~{$_Mrow['email']}~{$_Mrow['teacheradrs']}~{$_Mrow['teacherdesc']}~{$_Mrow['groupcaption']}";
			}	
			mysql_free_result($_Mgroupteachers);
			return  json_encode( $_Moutstr);			
		}else return 0;
	}else return -1;

  }
  function _Frepeditgroupteachers92($_Mgroups)
  {
  	$_Moutstr=array();
  	$_Msqlstr="select teacherID as c1,teachername as c2,teacherfamily as c3,teachergroupid as c4,teachercourseids as c5,teachercoursepriors as c6,teacherno as c7,teacherPrior as c8,courseparts as c9,initialtimes as c10 from tchrtimes where teachergroupid in($_Mgroups)";//
  	//copy tchrstate to this table
  	// and teacherstate=1";//echo $_Msqlstr;
  	//echo $_Msqlstr;
  	if($_Mgroupteachers=mysql_query($_Msqlstr))
  	{
  		if(mysql_num_rows($_Mgroupteachers)>0)
  		{
  			while($_Mrow=mysql_fetch_assoc($_Mgroupteachers))
  			{
  				$_Moutstr[]=$_Mrow;
  			}
  			mysql_free_result($_Mgroupteachers);
			return $_Moutstr;
  			//return  json_encode( $_Moutstr);
  		}else return 0;
  	}else return -1;
  
  }  
  function _Frepeditgroupcourses($_Mgroups)
  {
  	$_Moutstr= array(); 
  	//$_Msqlstr="select courseID as c1,coursefcaption as c2,coursecaption as c3,coursetype as c4,groupcaption as c5,courseAunits as c6,courseTunits as c7,courseAunitstchrs as c8,courseTunitstchrs as c9,coursesims as c10,coursepres as c11,neededroomtypeID as c12,neededroomtypeAID as c13,coursedesc as c14,coursehrdnes as c15, groupID as c16 from courses where groupID in ($_Mgroups) order by courseID";//echo $_Msqlstr;
	$_Msqlstr="select courseID as c1,coursefcaption as c2,coursecaption as c3,coursetype as c4,groupcaption as c5,courseAunits as c6,courseTunits as c7,courseAunitstchrs as c8,courseTunitstchrs as c9,coursesims as c10,coursepres as c11,neededroomtypeID as c12,neededroomtypeAID as c13,coursedesc as c14,coursehrdnes as c15, groupID as c16 from courses order by courseID";//echo $_Msqlstr;
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
  		if(mysql_num_rows($_Mresult)>0)
  		{
  			while($_Mrow=mysql_fetch_assoc($_Mresult))
  			{
  				$_Moutstr[]=$_Mrow;
   			}
  			mysql_free_result($_Mresult); 	
  			return  json_encode($_Moutstr);;
  		}else return 0;
  	}else return -1;
  }
  function _Frepeditgroupchartcourses92($_Mgroups)
  {
  	$_Moutstr= array(); 
  	//$_Msqlstr="select courseID as c1,coursefcaption as c2,coursecaption as c3,coursetype as c4,groupcaption as c5,courseAunits as c6,courseTunits as c7,courseAunitstchrs as c8,courseTunitstchrs as c9,coursesims as c10,coursepres as c11,neededroomtypeID as c12,neededroomtypeAID as c13,coursedesc as c14,coursehrdnes as c15, groupID as c16 from courses where groupID in ($_Mgroups) order by courseID";//echo $_Msqlstr;
	$_Msqlstr="select courseID as c1,coursefcaption as c2,coursecaption as c3,coursetype as c4,groupcaption as c5,courseAunits as c6,courseTunits as c7,courseAunitstchrs as c8,courseTunitstchrs as c9,coursesims as c10,coursepres as c11,neededroomtypeID as c12,neededroomtypeAID as c13,coursedesc as c14,coursehrdnes as c15, groupID as c16 from courses order by courseID";//echo $_Msqlstr;
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
  		if(mysql_num_rows($_Mresult)>0)
  		{
  			while($_Mrow=mysql_fetch_assoc($_Mresult))
  			{
  				$_Moutstr[]=$_Mrow;
   			}
  			mysql_free_result($_Mresult); 	
  			return  json_encode($_Moutstr);;
  		}else return 0;
  	}else return -1;
  }
  function _Frepeditgroupchartcourses92_complete($_Mgroups)
  {
  	$_Mchartcourses= array(); $_Mcourseids=array();$_Mtermcourses=array();$_Mchart_termcourses=array();
  	//$_Msqlstr="select courseID as c1,coursefcaption as c2,coursecaption as c3,coursetype as c4,groupcaption as c5,courseAunits as c6,courseTunits as c7,courseAunitstchrs as c8,courseTunitstchrs as c9,coursesims as c10,coursepres as c11,neededroomtypeID as c12,neededroomtypeAID as c13,coursedesc as c14,coursehrdnes as c15, groupID as c16 from courses where groupID in ($_Mgroups) order by courseID";//echo $_Msqlstr;
	$_Msqlstr="select courseID as c1,coursefcaption as c2,coursecaption as c3,coursetype as c4,groupcaption as c5,courseAunits as c6,courseTunits as c7,courseAunitstchrs as c8,courseTunitstchrs as c9,coursesims as c10,coursepres as c11,neededroomtypeID as c12,neededroomtypeAID as c13,coursedesc as c14,coursehrdnes as c15, groupID as c16 from courses order by courseID";//echo $_Msqlstr;
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
  		if(mysql_num_rows($_Mresult)>0)
  		{
		
		
  			while($_Mrow=mysql_fetch_assoc($_Mresult))
  			{
  				$_Mchartcourses[]=$_Mrow;
				$_Mcourseids[]=$_Mrow['c1'];
   			}
  			mysql_free_result($_Mresult); 	
  			$_Mcourseidstr=implode(',', $_Mcourseids);
  			//$_Msqlstr="select courseID as c1,coursecode as c2,coursecaption as c3,coursepart as c4,coursepreferedtimes as c5,courseAunits as c6,courseTunits as c7,awgroupID as c8,groupidconf1 as c9,groupidconf2 as c10,groupidconf3 as c11,coursehaspref as c12, groupID as c16 from termcoursestatus where coursecode in($_Mcourseidstr) order by courseID";//echo $_Msqlstr;

			$_Msqlstr="select t1.coursecode as c1, t1.courseID as c2, t1.coursepart as c3, t1.coursepreferedtimes as c4, t1.awgroupID as c5, t1.groupidconf1 as c6, t1.groupidconf2 as c7, t1.groupidconf3 as c8, t1.coursehaspref as c9, a1.teacherID as c10, a1.roomID as c11, a1.timeslots as c12 from termcoursestatus as t1  LEFT JOIN assignments as a1 on (t1.courseID=a1.courseID and t1.coursepart=a1.coursepart) where t1.coursecode in($_Mcourseidstr) order by t1.courseID";
  			if($_Mresult=mysql_query($_Msqlstr))
  			{
  				if(mysql_num_rows($_Mresult)>0)
  				{
  					while($_Mrow=mysql_fetch_assoc($_Mresult))
  					{$_Mtermcourses[]=$_Mrow;}
  				}
  			}
  			for($i=0; $i<count($_Mchartcourses); $i++)
  			{
  				$_Mcourseid=$_Mchartcourses[$i]['c1'];
  				$_Mchartcourses[$i]['c0']=array();
  				
  				for($j=0; $j<count($_Mtermcourses); $j++)
  				{
  					if($_Mtermcourses[$j]['c1']==$_Mcourseid)//c1:coursecode
  					$_Mchartcourses[$i]['c0'][] = $_Mtermcourses[$j];
  				}
  			}
  			//pr($_Mchartcourses);
  			//return  json_encode($_Mchartcourses);
			return $_Mchartcourses;
  		}else return 0;
  	}else return -1;
  }  
  
  function pr($data){
  	echo '<pre>';
  	print_r($data);
  	
  	echo '</pre>';
  	
  }
  
  function _Frepeditgrouptermcourses92($_Mgroups)
  {
  	$_Moutstr= array(); 
  	//$_Msqlstr="select courseID as c1,coursefcaption as c2,coursecaption as c3,coursetype as c4,groupcaption as c5,courseAunits as c6,courseTunits as c7,courseAunitstchrs as c8,courseTunitstchrs as c9,coursesims as c10,coursepres as c11,neededroomtypeID as c12,neededroomtypeAID as c13,coursedesc as c14,coursehrdnes as c15, groupID as c16 from courses where groupID in ($_Mgroups) order by courseID";//echo $_Msqlstr;
	$_Msqlstr="select t1.courseID as c1,t1.coursecode as c2,t1.coursecaption as c3,t1.coursepart as c4,t1.coursepreferedtimes as c5,t1.courseAunits as c6,t1.courseTunits as c7,t1.awgroupID as c8,t1.groupidconf1 as c9,t1.groupidconf2 as c10,t1.groupidconf3 as c11,t1.coursehaspref as c12,t1.neededroomtypeID as c13,t2.teacherID as c14,t2.timeslots as c15,t2.groupID as c16 from termcoursestatus as t1 LEFT JOIN assignments as t2 ON (t1.courseID=t2.courseID AND t1.coursepart=t2.coursepart)order by t1.courseID";//echo $_Msqlstr;  	
	
	if($_Mresult=mysql_query($_Msqlstr))
  	{
  		if(mysql_num_rows($_Mresult)>0)
  		{
  			while($_Mrow=mysql_fetch_assoc($_Mresult))
  			{
  				$_Moutstr[]=$_Mrow;
   			}
  			mysql_free_result($_Mresult); 	
			return $_Moutstr;
  			//return  json_encode($_Moutstr);
  		}else return 0;
  	}else return -1;
  }
  function _Freproomtypes()
  {
  	$_Moutstr=array();
  	$_Msqlstr="select roomtypeID as r1,roomtypecaption as r2 from roomtypes order by roomtypecaption";
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
  		if(mysql_num_rows($_Mresult)>0)
  		{
  			while($_Mrow=mysql_fetch_assoc($_Mresult))
  				$_Moutstr[]=$_Mrow;
  			mysql_free_result($_Mresult); 	
  			return $_Moutstr;
  		}else return 0;
  	}else return -1;
  	
  }
  function _Frepeditrooms($_Mbuildingid)
  {
  	$_Moutstr='';
  	$_Msqlstr="select * from rooms where termID={$GLOBALS['_Mactivetermid']} and subgroupof in ($_Mbuildingid) order by roomcaption";
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
	  	if(mysql_num_rows($_Mresult)>0)
	  	{
  			while($_Mrow=mysql_fetch_assoc($_Mresult))
  			{$_Moutstr.='#'.$_Mrow['roomID'].'~'.$_Mrow['roomcaption'].'~'.$_Mrow['capacity'].'~'.$_Mrow['equipments'].'~'.$_Mrow['roomtype'];}
  			mysql_free_result($_Mresult);
  	  		return $_Moutstr;
	  	}else return 0;
  	}else return -1;
  }
  function _Frepeditrooms92($_Mgroups)
  {
  	$_Moutstr=array();
  	$_Msqlstr="select roomID as c1,roomtype as c2,roomcaption as c3,roomgroupid as c4,roomstatus as c5,initialroomstatus as c6,capacity as c7,equipments as c8,schstate as c9 from roomstatus where termID={$GLOBALS['_Mactivetermid']} and roomgroupid in ($_Mgroups) order by roomcaption";
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
  		if(mysql_num_rows($_Mresult)>0)
  		{
  			while($_Mrow=mysql_fetch_assoc($_Mresult))
  			{
  				$_Moutstr[]=$_Mrow;
  			}
  			mysql_free_result($_Mresult);
  			return $_Moutstr;
  		}else return 0;
  	}else return -1;
  }
  function _Fgettree92($_Mgroupid)
  {
  	$_Moutstr=array();$_Masub=array();
  	$_Msqlstr="select asub from groupstatus where groupID =$_Mgroupid";
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
  		if(mysql_num_rows($_Mresult)>0)
  		{
  			$_Mrow=mysql_fetch_assoc($_Mresult);
  			$_Masub=explode(',',$_Mrow['asub']);
  		}
  	}
  	$_Msqlstr="select groupID as c1,groupcaption as c2,subgroupof as c3 from groupstatus order by subgroupof";
  	//echo $_Msqlstr;
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
  		if(mysql_num_rows($_Mresult)>0)
  		{
  			while($_Mrow=mysql_fetch_assoc($_Mresult))
  			{
  				if(in_array($_Mrow['c1'],$_Masub)) $_Mrow['c0']=1;else$_Mrow['c0']=0;
  				$_Moutstr[]=$_Mrow;
  			}
  			mysql_freeresult($_Mresult);
  			return $_Moutstr;
  		}else return 0;
  	}else return -1;
  
  }
// edit*********

?>