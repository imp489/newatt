<?php  @session_start();$_SESSION['activetermid']=2;
	@include_once "mygen_lib.php" ;
	@include_once "mygenclasses.php";
	@include_once "shrfuns.php";
	$_Mcon=new _CLconnection();
	
	$_Mactivetermid=$_SESSION['activetermid'];

	$_Mfullassigned="tsrc";
	$_Mroomassigned="cr";
	$_Mteacherassigned="ct";
	$_Mslotassigned="cs";

	$_Mteacher1=new _CLteacher();
	$_Mcourse1=new _CLcourse();
	$_Mgroup1=new _CLgroup();
	$_Muser1=new _CLatuser();
	
	$_Mpossibletimes=str_repeat('n',98);
	$_Mgroupslistcounter=0;$_Mgroupscount=0;
	$_Mteacherslistcounter=0;$_Mgroupteachercount=0;
	$_Mcourseslistcounter=0;$_Mgroupcoursecount=0;
	$_Massignmentstate='';$_Massigntype="---";
	$_Merrorsarray=array();
	$_Mwarningarray=array();
	$_Mprocesslogs=array();
	$_Moptimisearray=array();
	$_Mgroups=array();$_Mroomgroups=array();
	$_Mgroupscheduledcourses=array();
	$_Mgrouptermcourses=array();
	$_Mgroupteachers=array();
	$_Mteachershavenocmpletefreeslots=array();
	$_Mtmpcourseschedulingtrace=array();
	
	$_Mteachersroomcnsts=array();
	$_Mteachersslotcnsts=array();
	$_Mcheckedteachersids=array();
	$_Mcnstteachercontinousslots=true;
	
	$_Mcheckteachersmaxslotcnst=true;
	$_Mcheckgroupsmaxslotsperdaycnst=true;
	
	$_Mslotlen=2;
	
	$_Mcourseschedulestate=0;//1:scheduled  2:anyteacher  3:not teacher-group time matching  4:no coursepreffered slot matching
	$_Mpartialteacherschedule=true;//if not enough free teacher slots for a course schedule it partialy
	$_Manyteacherhasthiscourse=1;//there is no teacher to teach the course.
	$_Mschedulebaleslots=0;
	$_Mgroupslistcounter=0;
	//$_Mgas=$_POST['sch'];
	//*********************
	$_Mschwarnings=array();
	$_Mschnotscheduled=array();
	$_Mscherrors=array(); 
	$_Mteachertimeconflicts=array();
	$_Mgroupscount=0;
	//********************
	$_Mgas="1,1#";
	$_Mgroupsactions=explode("#",$_Mgas);array_pop($_Mgroupsactions);
	//_Fgetgroups4sch($_Mgroups,$_Mgroupidsstr,$_Mroomgroups,$_Mgroupscount,$_Mgroupsactions);
	_Fgetallfinalsubgroups($_Mgroups,$_Mgroupidsstr,$roomgroups,$_Mgroupscount,$_Mgroupsactions);
/*
	getteachertimeconflicts($_Mgroups,$_Mteachertimeconflicts);
	getwarnings($_Mgroups,$_Mschwarnings);
	getnotscheduled($_Mgroups,$_Mschnotscheduled);
	geterrors($_Mgroups,$_Mscherrors);
*/
	//getteachertimeconflicts($_Mgroups,$_Mgroupidsstr,$_Mteachertimeconflicts,$_Mgroupscount);
	if(isset($_POST['type'])){if($_POST['type']=='schers') echo repscherrs();}
//*****************opt
	function getteachertimeconflicts($_Mgroups,$_Mgroupidsstr,$_Mteachertimeconflicts,$_Mgroupscount)
	{
		$_Mgroupslistcounter=0;$_Mgroup1=new _CLgroup();
		while(_Fpickgroup($_Mgroup1,$_Mgroups,$_Mgroupslistcounter,$_Mgroupscount))
		{
			$_Mgroupteachers=array();$_Mgroupteachercount=0;
			_Fgetgroupteachers($_Mgroupteachers,$_Mgroup1,$_Mgroupteachercount);
			for($i=0 ; $i<count($_Mgroupteachers) ; $i++)
			{
				
				$_Mteacherid=$_Mgroupteachers[$i]['teacherID'];$_Mteachernamefam=$_Mgroupteachers[$i]['teachername'].' '.$_Mgroupteachers[$i]['teacherfamily'];
				$_Mteachersschedule=array();$_Mteacherconflictreport=array();
				$_Msqlstr="select * from assignments where termID={$GLOBALS['_Mactivetermid']} and teacherID=$_Mteacherid";
				if($_Mresult=mysql_query($_Msqlstr))
					if(mysql_num_rows($_Mresult)>0)
					{
						while($_Mrow=mysql_fetch_array($_Mresult))
						   array_push($_Mteachersschedule,$_Mrow);
						checkconflicts($_Mteacherid,$_Mteachernamefam,$_Mteachersschedule,$_Mteacherconflictreport);
					}
			}
			if(count($GLOBALS['_Mteachertimeconflicts'])>0)
				saveteacherconflictstodb();	
			$GLOBALS['_Mteachertimeconflicts']=array();
		}
	}
	function saveteacherconflictstodb()
	{
		$_Msqlstr="insert into teacherconflicts(teacherID,courseID,groupID,timeslots,teachernamefam,coursecaption,groupcaption) values";
		for($i=0 ; $i<count($GLOBALS['_Mteachertimeconflicts']) ; $i++)
		{
			$_Msqlstr.="('{$GLOBALS['_Mteachertimeconflicts'][$i][0]}','{$GLOBALS['_Mteachertimeconflicts'][$i][1]}','{$GLOBALS['_Mteachertimeconflicts'][$i][2]}','{$GLOBALS['_Mteachertimeconflicts'][$i][3]}',
					   '{$GLOBALS['_Mteachertimeconflicts'][$i][4]}','{$GLOBALS['_Mteachertimeconflicts'][$i][5]}','{$GLOBALS['_Mteachertimeconflicts'][$i][6]}'),";
		}
		$_Msqlstr=substr($_Msqlstr,0,strlen($_Msqlstr)-1);
		mysql_query($_Msqlstr);
	}
    function checkconflicts($_Mteacherid,$_Mteachernamefam,$_Mteachersschedule,&$_Mteacherconflictreport)
    {
    	$_Mttimes=str_repeat('n',98);$_Mteachercoursesheet=array();$_Mconflictsno=0;
    	$_Mconflicts=array();
    	for($i=0 ; $i<count($_Mteachersschedule) ; $i++)
    	{
    		$_Mtimetypes=$_Mteachersschedule[$i]['timeslots'];
    		$_Mtimeslots=array();$_Mtimeslotstate=array();
    		_Fslottype2slotstypes($_Mtimetypes,$_Mtimeslots,$_Mtimeslotstate);
    		$_Mconflictsno+=addcoursetotimes($_Mteacherid,$_Mteachernamefam,$_Mttimes,$_Mconflicts,$_Mteachercoursesheet,$_Mtimeslots,$_Mtimeslotstate,$_Mtimetypes,$_Mteachersschedule[$i]['courseID'],$_Mteachersschedule[$i]['coursecaption'],$_Mteachersschedule[$i]['groupID'],$_Mteachersschedule[$i]['groupcaption']);
    	}
    	return $_Mconflictsno;
    }
	function addcoursetotimes($_Mteacherid,$_Mteachernamefam,&$_Mttimes,&$_Mconflicts,&$_Mteachercoursesheet,$_Mtimeslots,$_Mtimeslotstate,$_Mtimetypes,$_Mcourseid,$_Mcoursecaption,$_Mgroupid,$_Mgroupcaption)
	{
		$_Mconflictsno=0;
		for($i=0 ; $i<count($_Mtimeslots) ; $i+=$GLOBALS['_Mslotlen'])//instead of $i++
		{
			$_Mno=$_Mtimeslots[$i];
			if(  ($_Mttimes[$_Mno]=='s') ||
				 ((($_Mttimes[$_Mno]=='e')||($_Mttimes[$_Mno]=='o'))&(($_Mtimeslotstate[$i]==$_Mttimes[$_Mno])||($_Mtimeslotstate[$i]=='s')))   )  
				
			{
				$_Mconflictsno++;
				addteachertimeconflict($_Mteacherid,$_Mteachernamefam,$_Mtimetypes,$_Mcourseid,$_Mcoursecaption,$_Mgroupid,$_Mgroupcaption);
				//addtotimesheet($_Mteachercoursesheet,$_Mno,$_Mtimeslotstate[$i],$_Mcourseid,$_Mcoursecaption,$_Mgroupid,$_Mgroupcaption);
				array_push($_Mconflicts,array($_Mno,$_Mtimeslotstate[$i],$_Mcourseid,$_Mcoursecaption,$_Mgroupid,$_Mgroupcaption));
				array_push($_Mconflicts,array($_Mno,$_Mtimeslotstate[$i],$_Mteachercoursesheet[$_Mno][1],$_Mteachercoursesheet[$_Mno][2],$_Mteachercoursesheet[$_Mno][3],$_Mteachercoursesheet[$_Mno][4]));
			}//																			'courseID'	   		     'coursecaption'					'groupID'				 'groupcaption'

			else if($_Mttimes[$_Mno]=='n') 							   {$_Mttimes[$_Mno]=$_Mtimeslotstate[$i];$_Mteachercoursesheet[$_Mno]=array($_Mno,array($_Mtimeslotstate[$i]),array($_Mcourseid),array($_Mcoursecaption),array($_Mgroupid),array($_Mgroupcaption));}
			else if(($_Mttimes[$_Mno]=='e')&($_Mtimeslotstate[$i]=='o')) {$_Mttimes[$_Mno]='s';addtotimesheet($_Mteachercoursesheet,$_Mno,$_Mtimeslotstate[$i],$_Mcourseid,$_Mcoursecaption,$_Mgroupid,$_Mgroupcaption);}
			else if(($_Mttimes[$_Mno]=='o')&($_Mtimeslotstate[$i]=='e')) {$_Mttimes[$_Mno]='s';addtotimesheet($_Mteachercoursesheet,$_Mno,$_Mtimeslotstate[$i],$_Mcourseid,$_Mcoursecaption,$_Mgroupid,$_Mgroupcaption);}

		}
		return $_Mconflictsno;
	}
	function addteachertimeconflict($_Mteacherid,$_Mteachernamefam,$_Mtimetypes,$_Mcourseid,$_Mcoursecaption,$_Mgroupid,$_Mgroupcaption)
	{
		array_push($GLOBALS['_Mteachertimeconflicts'],array($_Mteacherid,$_Mcourseid,$_Mgroupid,$_Mtimetypes,$_Mteachernamefam,$_Mcoursecaption,$_Mgroupcaption));
	}
	function addtotimesheet(&$_Mteachercoursesheet,$_Mno,$_Mtimeslotstate,$_Mcourseid,$_Mcoursecaption,$_Mgroupid,$_Mgroupcaption)
	{
		if(!(is_array($_Mteachercoursesheet[$_Mno][0])))
		{
			$_Mteachercoursesheet[$_Mno][0]=array();
			$_Mteachercoursesheet[$_Mno][1]=array();
			$_Mteachercoursesheet[$_Mno][2]=array();
			$_Mteachercoursesheet[$_Mno][3]=array();
			$_Mteachercoursesheet[$_Mno][4]=array();
		}
		array_push($_Mteachercoursesheet[$_Mno][0],$_Mtimeslotstate);
		array_push($_Mteachercoursesheet[$_Mno][1],$_Mcourseid);
		array_push($_Mteachercoursesheet[$_Mno][2],$_Mcoursecaption);
		array_push($_Mteachercoursesheet[$_Mno][3],$_Mgroupid);
		array_push($_Mteachercoursesheet[$_Mno][4],$_Mgroupcaption);		
	}
	
	function repscherrs()
	{
		$_Moutstr="";
		$_Msqlstr="select opttype,courseID,coursecaption,coursepart,teacherID,teachernamefam,groupID,groupcaption,slottype,slotid,opttime,optdate from optimise where courseID<>0 group by courseID order by opttype,groupID,courseID";
		if($_Mresult=mysql_query($_Msqlstr))
		{
			if(mysql_num_rows($_Mresult)>0)
			{
				while($_Mrow=mysql_fetch_assoc($_Mresult))
				//{$_Moutstr.="#{$_Mrow['opttype']}~{$_Mrow['courseID']}~{$_Mrow['coursecaption']}~{$_Mrow['teacherID']}~{$_Mrow['teachernamefam']}~{$_Mrow['groupID']}~{$_Mrow['groupcaption']}~{$_Mrow['slottype']}~{$_Mrow['slotid']}~{$_Mrow['opttime']}~{$_Mrow['optdate']}";}
				//920703{$_Moutstr.="#{$_Mrow['groupID']}~{$_Mrow['groupcaption']}~{$_Mrow['courseID']}~{$_Mrow['coursepart']}~ {$_Mrow['coursecaption']} ~{$_Mrow['courseunits']}~{$_Mrow['opttype']}";}
				{$_Moutstr.="#{$_Mrow['groupID']}~{$_Mrow['groupcaption']}~{$_Mrow['courseID']}~{$_Mrow['coursepart']}~ {$_Mrow['coursecaption']} ~2~{$_Mrow['opttype']}";}
			}else return 0;
		}else return -1;
		return $_Moutstr;
	}
//*****************opt

//****************
?>
