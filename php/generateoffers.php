<?php  @session_start();$_SESSION['activetermid']=2;
	@include_once "mygenlib.php" ;

	$con=new _CLconnection();
	
	$activetermid=$_SESSION['activetermid'];

	$fullassigned="tsrc";
	$roomassigned="cr";
	$teacherassigned="ct";
	$slotassigned="cs";

	$teacher1=new _CLteacher();
	$course1=new _CLcourse();
	$group1=new _CLgroup();
	$user1=new _CLatuser();
	
	$possibletimes=str_repeat('n',98);
	$groupslistcounter=0;$groupscount=0;
	$teacherslistcounter=0;$_Mgroupteachercount=0;
	$courseslistcounter=0;$groupcoursecount=0;
	$assignmentstate='';$assigntype="---";
	$errorsarray=array();
	$warningarray=array();
	$processlogs=array();
	$optimisearray=array();
	$groups=array();$roomgroups=array();
	$groupscheduledcourses=array();
	$grouptermcourses=array();
	$groupteachers=array();
	$teachershavenocmpletefreeslots=array();
	$tmpcourseschedulingtrace=array();
	
	$teachersroomcnsts=array();
	$teachersslotcnsts=array();
	$checkedteachersids=array();
	$cnstteachercontinousslots=true;
	
	$checkteachersmaxslotcnst=true;
	$checkgroupsmaxslotsperdaycnst=true;
	
	$slotlen=2;
	
	$courseschedulestate=0;//1:scheduled  2:anyteacher  3:not teacher-group time matching  4:no coursepreffered slot matching
	$partialteacherschedule=true;//if not enough free teacher slots for a course schedule it partialy
	$anyteacherhasthiscourse=1;//there is no teacher to teach the course.
	$schedulebaleslots=0;
	$groupslistcounter=0;
	//$gas=$_POST['sch'];
	//*********************
	$schwarnings=array();
	$schnotscheduled=array();
	$scherrors=array(); 
	$teachertimeconflicts=array();
	$groupscount=0;
	//********************
	$gas="1,1#";
	$groupsactions=explode("#",$gas);array_pop($groupsactions);
	_Fgetgroups4sch($groups,$_Mgroupidsstr,$roomgroups,$groupscount,$groupsactions);
/*	
	getteachertimeconflicts($groups,$teachertimeconflicts);
	getwarnings($groups,$schwarnings);
	getnotscheduled($groups,$schnotscheduled);
	geterrors($groups,$scherrors);
*/	
	getteachertimeconflicts($groups,$_Mgroupidsstr,$teachertimeconflicts,$groupscount);
//*****************opt
	function getteachertimeconflicts($groups,$_Mgroupidsstr,$teachertimeconflicts,$groupscount)
	{
		$groupslistcounter=0;$group1=new _CLgroup();
		while(_Fpickgroup($group1,$groups,$groupslistcounter,$groupscount))
		{
			$groupteachers=array();$_Mgroupteachercount=0;
			_Fgetgroupteachers($groupteachers,$group1,$_Mgroupteachercount);
			for($i=0 ; $i<count($groupteachers) ; $i++)
			{
				
				$teacherid=$groupteachers[$i]['teacherID'];$teachernamefam=$groupteachers[$i]['teachername'].' '.$groupteachers[$i]['teacherfamily'];
				$teachersschedule=array();$teacherconflictreport=array();
				$sqlstr="select * from assignments where termID={$GLOBALS['activetermid']} and teacherID=$teacherid";
				if($result=mysql_query($sqlstr))
					if(mysql_num_rows($result)>0)
					{
						while($row=mysql_fetch_array($result))
						   array_push($teachersschedule,$row);
						checkconflicts($teacherid,$teachernamefam,$teachersschedule,$teacherconflictreport);
					}
			}
			if(count($GLOBALS['teachertimeconflicts'])>0)
				saveteacherconflictstodb();	
			$GLOBALS['teachertimeconflicts']=array();
		}
	}
	function saveteacherconflictstodb()
	{
		$sqlstr="insert into teacherconflicts(teacherID,courseID,groupID,timeslots,teachernamefam,coursecaption,groupcaption) values";
		for($i=0 ; $i<count($GLOBALS['teachertimeconflicts']) ; $i++)
		{
			$sqlstr.="('{$GLOBALS['teachertimeconflicts'][$i][0]}','{$GLOBALS['teachertimeconflicts'][$i][1]}','{$GLOBALS['teachertimeconflicts'][$i][2]}','{$GLOBALS['teachertimeconflicts'][$i][3]}',
					   '{$GLOBALS['teachertimeconflicts'][$i][4]}','{$GLOBALS['teachertimeconflicts'][$i][5]}','{$GLOBALS['teachertimeconflicts'][$i][6]}'),";
		}
		$sqlstr=substr($sqlstr,0,strlen($sqlstr)-1);
		mysql_query($sqlstr);
	}
    function checkconflicts($teacherid,$teachernamefam,$teachersschedule,&$teacherconflictreport)
    {
    	$ttimes=str_repeat('n',98);$teachercoursesheet=array();$conflictsno=0;
    	$conflicts=array();
    	for($i=0 ; $i<count($teachersschedule) ; $i++)
    	{
    		$timetypes=$teachersschedule[$i]['timeslots'];
    		$timeslots=array();$timeslotstate=array();
    		_Fslottype2slotstypes($timetypes,$timeslots,$timeslotstate);
    		$conflictsno+=addcoursetotimes($teacherid,$teachernamefam,$ttimes,$conflicts,$teachercoursesheet,$timeslots,$timeslotstate,$timetypes,$teachersschedule[$i]['courseID'],$teachersschedule[$i]['coursecaption'],$teachersschedule[$i]['groupID'],$teachersschedule[$i]['groupcaption']);
    	}
    	return $conflictsno;
    }
	function addcoursetotimes($teacherid,$teachernamefam,&$ttimes,&$conflicts,&$teachercoursesheet,$timeslots,$timeslotstate,$timetypes,$courseid,$coursecaption,$groupid,$groupcaption)
	{
		$conflictsno=0;
		for($i=0 ; $i<count($timeslots) ; $i+=$GLOBALS['slotlen'])//instead of $i++
		{
			$no=$timeslots[$i];
			if(  ($ttimes[$no]=='s') ||
				 ((($ttimes[$no]=='e')||($ttimes[$no]=='o'))&(($timeslotstate[$i]==$ttimes[$no])||($timeslotstate[$i]=='s')))   )  
				
			{
				$conflictsno++;
				addteachertimeconflict($teacherid,$teachernamefam,$timetypes,$courseid,$coursecaption,$groupid,$groupcaption);
				//addtotimesheet($teachercoursesheet,$no,$timeslotstate[$i],$courseid,$coursecaption,$groupid,$groupcaption);
				array_push($conflicts,array($no,$timeslotstate[$i],$courseid,$coursecaption,$groupid,$groupcaption));
				array_push($conflicts,array($no,$timeslotstate[$i],$teachercoursesheet[$no][1],$teachercoursesheet[$no][2],$teachercoursesheet[$no][3],$teachercoursesheet[$no][4]));
			}//																			'courseID'	   		     'coursecaption'					'groupID'				 'groupcaption'

			else if($ttimes[$no]=='n') 							   {$ttimes[$no]=$timeslotstate[$i];$teachercoursesheet[$no]=array($no,array($timeslotstate[$i]),array($courseid),array($coursecaption),array($groupid),array($groupcaption));}
			else if(($ttimes[$no]=='e')&($timeslotstate[$i]=='o')) {$ttimes[$no]='s';addtotimesheet($teachercoursesheet,$no,$timeslotstate[$i],$courseid,$coursecaption,$groupid,$groupcaption);}
			else if(($ttimes[$no]=='o')&($timeslotstate[$i]=='e')) {$ttimes[$no]='s';addtotimesheet($teachercoursesheet,$no,$timeslotstate[$i],$courseid,$coursecaption,$groupid,$groupcaption);}

		}
		return $conflictsno;
	}
	function addteachertimeconflict($teacherid,$teachernamefam,$timetypes,$courseid,$coursecaption,$groupid,$groupcaption)
	{
		array_push($GLOBALS['teachertimeconflicts'],array($teacherid,$courseid,$groupid,$timetypes,$teachernamefam,$coursecaption,$groupcaption));		
	}
	function addtotimesheet(&$teachercoursesheet,$no,$timeslotstate,$courseid,$coursecaption,$groupid,$groupcaption)
	{
		if(!(is_array($teachercoursesheet[$no][0])))
		{
			$teachercoursesheet[$no][0]=array();
			$teachercoursesheet[$no][1]=array();
			$teachercoursesheet[$no][2]=array();
			$teachercoursesheet[$no][3]=array();
			$teachercoursesheet[$no][4]=array();
		}
		array_push($teachercoursesheet[$no][0],$timeslotstate);
		array_push($teachercoursesheet[$no][1],$courseid);
		array_push($teachercoursesheet[$no][2],$coursecaption);
		array_push($teachercoursesheet[$no][3],$groupid);
		array_push($teachercoursesheet[$no][4],$groupcaption);		
	}
//*****************opt
	while(_Fpickgroup($group1,$groups,$groupslistcounter,$groupscount))
	{
	  if (!(_Finitialisegroupvalues('constrained',$group1,$groupscheduledcourses,$grouptermcourses,
	  							  $groupteachers,$groupcoursecount,$_Mgroupteachercount,$teachersroomcnsts,
	  							  $teacherslistcounter,$courseslistcounter))) break;

	  while(_Fpickgrouptermcourse($groupscheduledcourses,$course1,$grouptermcourses,$courseslistcounter,$groupcoursecount))
	  {
	  	  $checkedteachersids=array();
	  	  $ccnst=$course1->getcnsttype();
	  	  if($ccnst!=$fullassigned)
	  	  {
			  if(($ccnst[0]=='t')&($ccnst[1]=='s'))//teacher&slot not room
			  {
			  	$teacher1->setteacheridfull($course1->getcourseteachercnst());
			  	_Fschedulets($course1,$teacher1,$group1,$ccnst);
			  }
			  else if(!($ccnst[0]=='t') and ($ccnst[1]=='s'))
			  {//should be if not while for teacher
			  		if(_Fpickteacherforslottedcourse($teacher1,$course1,$groupteachers,$checkedteachersids,$_Mgroupteachercount,$course1->getcourseslotcnst()))
			  		{//no group time checking needed,because group constraint not checked while manual course slot assignment
			  			_Fschedulets($course1,$teacher1,$group1,$ccnst);
			  		}
			  }
			  else if(($ccnst[0]=='t') and !($ccnst[1]=='s'))
			  {
			  			$teacher1->setteacheridfull($course1->getcourseteachercnst());

					  	if(_Fchecktimestchrgrpcnstr($teacher1,$group1,$course1,$possibletimes))
					  	{
					  		if($course1->getcoursehaspref())
					  		{  
					  			if(_Fmeetscourseconst($course1,$possibletimes))
					  		  	{
					  		  		_Fscheduleit($teacher1,$course1,$possibletimes,$group1,$processlogs,$ccnst);
						  		  	array_push($processlogs,"scheduled course:{$course1->getcoursecaption()} with (M)teacher:{$teacher1->teachername}  on (a)time {}");
					  		  	}
					  		  	else {array_push($processlogs,"===****> not possible constraint teacher setting - course:{$course1->getcoursecaption()} with teacher:{$teacher1->teachername} because of no course initial time matching");}
					  		}  
					  		else 
					  		{
					  			_Fscheduleit($teacher1,$course1,$possibletimes,$group1,$processlogs,$ccnst);
					  			array_push($processlogs,"scheduled course:{$course1->getcoursecaption()} with teacher:{$teacher1->teachername} on time {}");
					  		}	
					  	}
					  	else array_push($processlogs,"===****> not possible constraint teacher setting - course:{$course1->getcoursecaption()} with teacher:{$teacher1->teachername} because of no teacher-group time matching");
			  }
		  
			  else if(!($ccnst[0]=='t') and !($ccnst[1]=='s'))//teacher isn't specified,search for it	
			  {
			  	  while(_Fpickteacher($teacher1,$course1,$groupteachers,$checkedteachersids,$_Mgroupteachercount,''))
				  {
					  	if(_Fchecktimestchrgrpcnstr($teacher1,$group1,$course1,$possibletimes))
					  	{
					  		if($course1->getcoursehaspref())
					  		{  
					  			if(_Fmeetscourseconst($course1,$possibletimes))
					  		  	{
					  		  		if(_Fscheduleit($teacher1,$course1,$possibletimes,$group1,$processlogs,$ccnst))
						  		  		break;
					  		  	}
					  		  	else array_push($processlogs,"===*>not possible course:{$course1->getcoursecaption()} with teacher:{$teacher1->teachername} because of no course initial time matching");
					  		}  
					  		else
					  		{
					  			_Fscheduleit($teacher1,$course1,$possibletimes,$group1,$processlogs,$ccnst);
					  			array_push($processlogs,"scheduled course:{$course1->getcoursecaption()} with teacher:{$teacher1->teachername} on time {}");
					  			break;
					  		}
					  	}
					  	else array_push($processlogs,"===**>not possible course:{$course1->getcoursecaption()} with teacher:{$teacher1->teachername} because of no teacher-group time matching");
			  	  }
			  }
	  	  }
	  	  else //is fullassigned
		  {
			  	$teacher1->setteacheridfull($course1->getcourseteachercnst());
			  	_Fslottype2slotstypes($course1->getcourseslotcnst(),$timeslots,$timeslotstate);
			  	_Fupdatecourseteachergroup($course1,$teacher1,$group1,$timeslots,$timeslotstate,$ccnst);
			  	array_push($processlogs,"Manual full assignment :: course:{$course1->getcoursecaption()} with teacher:{$teacher1->teachername} at time:{$course1->getcourseslotcnst()}");
		  }
	  }
	}



//****************?>
