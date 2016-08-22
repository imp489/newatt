<?php  
	@session_start();$_SESSION['activetermid']=2;$_SESSION['_Mactivetermid']=2;
	@include_once "mygenlib.php" ;
	@include_once "shrfuns.php" ;
	$_Mcon=new _CLconnection();

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
	$_Mcourseslistcounter=0;$groupcoursecount=0;
	$_Massignmentstate='';$assigntype="---";
	$_Merrorsarray=array();
	$_Mwarningarray=array();
	$_Mprocesslogs=array();
	$_Moptimisearray=array();
	$_Mgroups=array();$roomgroups=array();
	$_Mgroupscheduledcourses=array();
	$_Mgrouptermcourses=array();
	$_Mgroupregedscheduledcourses=array();$_Mgroupregedcounter=0;
	$_Mgroupteachers=array();
	$_Mteachershavenocmpletefreeslots=array();
	$_Mtmpcourseschedulingtrace=array();

	$_Mteachersroomcnsts=array();
	$_Mteachersslotcnsts=array();
	$_Mcheckedteachersids=array();
	$_Mcnstteachercontinousslots=true;
	
	$_Mcheckteachersmaxslotcnst=true;
	$_Mcheckgroupsmaxslotsperdaycnst=true;
	$_Mcheckteachersmanualorder=true;
	$_Motherpartteacherchecked=false;
	
	$_Mslotlen=2;$_Mdayslots=12;$_Mtotslots=84;
	
	$_Mcourseschedulestate=0;//1:scheduled  2:anyteacher  3:not teacher-group time matching  4:no coursepreffered slot matching
	$_Mpartialteacherschedule=false;//if not enough free teacher slots for a course, schedule it partialy
	$_Mnoteacherhasthiscourse=true;//there is no teacher to teach the course.
	$_Mschedulebaleslots=0;
	$_Mgroupslistcounter=0;
	//$_Mgas=$_POST['sch'];
	$_Mgas="2,1#";
	$_Mgroupsactions=explode("#",$_Mgas);array_pop($_Mgroupsactions);
	//_Fgetgroups4sch($_Mgroups,$_Mgroupidsstr,$roomgroups,$_Mgroupscount,$_Mgroupsactions);
	_Fgetallfinalsubgroups($_Mgroups,$_Mgroupidsstr,$roomgroups,$_Mgroupscount,$_Mgroupsactions);
//*****************
	_Fpreprocessdata($_Mgroupidsstr);
	while(_Fpickgroup($_Mgroup1,$_Mgroups,$_Mgroupslistcounter,$_Mgroupscount))
	{
	  if (!(_Finitialisegroupvalues('constrained',$_Mgroup1,$_Mgroupscheduledcourses,$_Mgrouptermcourses,$_Mgroupregedscheduledcourses,
	  							  $_Mgroupteachers,$groupcoursecount,$_Mgroupteachercount,$_Mteachersroomcnsts,
	  							  $_Mteacherslistcounter,$_Mcourseslistcounter,$_Mgroupregedcounter))) continue;
	  $_Mcourseslistcounter=0;
	  while(_Fpickgrouptermcourse($_Mgroupscheduledcourses,$_Mcourse1,$_Mgrouptermcourses,$_Mcourseslistcounter,$groupcoursecount))
	  {
	  	  $_Mcheckedteachersids=array();
	  	  $_Mccnst=$_Mcourse1->getcnsttype();
	  	  if($_Mccnst!=$_Mfullassigned)
	  	  {
			  if(($_Mccnst[0]=='t')&($_Mccnst[1]=='s'))//teacher&slot not room
			  {
			  	$_Mteacher1->setteacheridfull($_Mcourse1->getcourseteachercnst(),$activetermid);
			  	_Fschedulets($_Mcourse1,$_Mteacher1,$_Mgroup1,$_Mccnst);
			  }
			  else if(!($_Mccnst[0]=='t') and ($_Mccnst[1]=='s'))
			  {//should be if not while for teacher
			  		if(_Fpickteacherforslottedcourse($_Mteacher1,$_Mcourse1,$_Mgroupteachers,$_Mcheckedteachersids,$_Mgroupteachercount,$_Mcourse1->getcourseslotcnst(),$_Mcourseid,$_Mcoursepart,$_Mgroupregedscheduledcourses,$_Mgroupregedcounter))
			  		{//no group time checking needed,because group constraint not checked while manual course slot assignment
			  			_Fschedulets($_Mcourse1,$_Mteacher1,$_Mgroup1,$_Mccnst);
			  		}
			  }
			  else if(($_Mccnst[0]=='t') and !($_Mccnst[1]=='s'))
			  {
			  			$_Mteacher1->setteacheridfull($_Mcourse1->getcourseteachercnst(),$_SESSION['_Mactivetermid']);

					  	if(_Fchecktimestchrgrpcnstr($_Mteacher1,$_Mgroup1,$_Mcourse1,$_Mpossibletimes))
					  	{
					  		if($_Mcourse1->getcoursehaspref())
					  		{
					  			$_Mschedulebaleslots=0;
					  			if(_Fmeetscourseconst($_Mcourse1,$_Mpossibletimes,$_Mschedulebaleslots))
					  		  	{
					  		  		_Fscheduleit($_Mteacher1,$_Mcourse1,$_Mpossibletimes,$_Mgroup1,$_Mprocesslogs,$_Mccnst);
						  		  	array_push($_Mprocesslogs,"scheduled course:{$_Mcourse1->getcoursecaption()} with (M)teacher:{$_Mteacher1->teachername}  on (a)time {}");
					  		  	}
					  		  	else {array_push($_Mprocesslogs,"===****> not possible constraint teacher setting - course:{$_Mcourse1->getcoursecaption()} with teacher:{$_Mteacher1->teacherfamily} because of no course initial time matching");}
					  		}
					  		else 
					  		{
					  			_Fscheduleit($_Mteacher1,$_Mcourse1,$_Mpossibletimes,$_Mgroup1,$_Mprocesslogs,$_Mccnst);
					  			array_push($_Mprocesslogs,"scheduled course:{$_Mcourse1->getcoursecaption()} with teacher:{$_Mteacher1->teachername} on time {}");
					  		}
					  	}
					  	else array_push($_Mprocesslogs,"===****> not possible constraint teacher setting - course:{$_Mcourse1->getcoursecaption()} with teacher:{$_Mteacher1->teacherfamily} because of no teacher-group time matching");
			  }
/*	  
			  else if(!($_Mccnst[0]=='t') and !($_Mccnst[1]=='s'))//teacher isn't specified,search for it	
			  {
			  	  while(_Fpickteacher($_Mteacher1,$_Mcourse1,$_Mgroupteachers,$_Mcheckedteachersids,$_Mgroupteachercount,''))
				  {
					  	if(_Fchecktimestchrgrpcnstr($_Mteacher1,$_Mgroup1,$_Mcourse1,$_Mpossibletimes))
					  	{
					  		if($_Mcourse1->getcoursehaspref())
					  		{  
					  			if(_Fmeetscourseconst($_Mcourse1,$_Mpossibletimes))
					  		  	{
					  		  		if(_Fscheduleit($_Mteacher1,$_Mcourse1,$_Mpossibletimes,$_Mgroup1,$_Mprocesslogs,$_Mccnst))
						  		  		break;
					  		  	}
					  		  	else array_push($_Mprocesslogs,"===*>not possible course:{$_Mcourse1->getcoursecaption()} with teacher:{$_Mteacher1->teachername} because of no course initial time matching");
					  		}  
					  		else
					  		{
					  			_Fscheduleit($_Mteacher1,$_Mcourse1,$_Mpossibletimes,$_Mgroup1,$_Mprocesslogs,$_Mccnst);
					  			array_push($_Mprocesslogs,"scheduled course:{$_Mcourse1->getcoursecaption()} with teacher:{$_Mteacher1->teachername} on time {}");
					  			break;
					  		}
					  	}
					  	else array_push($_Mprocesslogs,"===**>not possible course:{$_Mcourse1->getcoursecaption()} with teacher:{$_Mteacher1->teachername} because of no teacher-group time matching");
			  	  }
			  }
*/		  
	  	  }
	  	  else //is fullassigned
		  {
			  	$_Mteacher1->setteacheridfull($_Mcourse1->getcourseteachercnst());
			  	_Fslottype2slotstypes($_Mcourse1->getcourseslotcnst(),$timeslots,$timeslotstate);
			  	_Fupdatecourseteachergroup($_Mcourse1,$_Mteacher1,$_Mgroup1,$timeslots,$timeslotstate,$_Mccnst);
			  	array_push($_Mprocesslogs,"Manual full assignment :: course:{$_Mcourse1->getcoursecaption()} with teacher:{$_Mteacher1->teachername} at time:{$_Mcourse1->getcourseslotcnst()}");
		  }
	  }
	}

//****************
	$_Mgrouptermcourses=array();$_Mgroupteachers=array();$_Mgroups=array();_Fgetallfinalsubgroups($_Mgroups,$_Mgroupidsstr,$roomgroups,$_Mgroupscount,$_Mgroupsactions);//_Fgetgroups4sch($_Mgroups,$_Mgroupidsstr,$roomgroups,$_Mgroupscount,$_Mgroupsactions);//for updating group scheduled slots
	$_Mgroupslistcounter=0;
	while(_Fpickgroup($_Mgroup1,$_Mgroups,$_Mgroupslistcounter,$_Mgroupscount))
	{
	  if (!(_Finitialisegroupvalues('normal',$_Mgroup1,$_Mgroupscheduledcourses,$_Mgrouptermcourses,$_Mgroupregedscheduledcourses,$_Mgroupteachers,$groupcoursecount,$_Mgroupteachercount,$_Mteachersroomcnsts,$_Mteacherslistcounter,$_Mcourseslistcounter,$_Mgroupregedcounter))) continue;
	  $_Mcourseslistcounter=0;
	  while(_Fpickgrouptermcourse($_Mgroupscheduledcourses,$_Mcourse1,$_Mgrouptermcourses,$_Mcourseslistcounter,$groupcoursecount))
	  {
	  	//becauseofteachercurslotschanges
	  	  //if(!(_Fgetgroupteachers($_Mgroupteachers,$_Mgroup1,$_Mgroupteachercount))) {echo 'no teacher defined for group : '.$_Mgroup1->getgroupcaption();return false;}
	  	  $_Mcheckedteachersids=array();$_Motherpartteacherchecked=false;
	  	  $_Mccnst=_Fcheckcoursecnst($_Mgroupscheduledcourses,$_Mcourse1);
		  if(!($_Mccnst[0]=='t') and !($_Mccnst[1]=='s'))//teacher isn't specified,search for it	
			  {
			  	  while(_Fpickteacher($_Mteacher1,$_Mcourse1,$_Mgroupteachers,$_Mcheckedteachersids,$_Mgroupteachercount,'',$_Mgroupregedscheduledcourses,$_Mgroupregedcounter))
			  	  //change will test : if instead of while
			  	  //if(_Fpickteacher($_Mteacher1,$_Mcourse1,$_Mgroupteachers,$_Mcheckedteachersids,$_Mgroupteachercount,''))
				  {
					  	if(_Fchecktimestchrgrpcnstr($_Mteacher1,$_Mgroup1,$_Mcourse1,$_Mpossibletimes))
					  	{
					  		if($_Mcourse1->getcoursehaspref())
					  		{
					  			$_Mschedulebaleslots=0;
					  			if(_Fmeetscourseconst($_Mcourse1,$_Mpossibletimes,$schedulabaleslots))
					  		  	{
					  		  		if(_Fscheduleit($_Mteacher1,$_Mcourse1,$_Mpossibletimes,$_Mgroup1,$_Mprocesslogs,$_Mccnst))
					  		  		{$_Mcourseschedulestate=1;break;}
					  		  	}
					  		  	else 
					  		  	{
					  		  		array_push($_Mprocesslogs,"===*>not possible course:{$_Mcourse1->getcoursecaption()} with teacher:{$_Mteacher1->teacherfamily} because of no course initial time matching");
									array_push($_Mtmpcourseschedulingtrace,array("t-g<>cprefs",$_Mgroup1->getgroupid(),$_Mcourse1->getcourseid(),$_Mcourse1->getcoursecaption(),$_Mteacher1->teacherid,$_Mteacher1->teachername.' '.$_Mteacher1->teacherfamily,$_Mcourse1->getcoursepart()));
					  		  	}
					  		}
					  		else
					  		{
					  			if(_Fscheduleit($_Mteacher1,$_Mcourse1,$_Mpossibletimes,$_Mgroup1,$_Mprocesslogs,$_Mccnst))
					  			{$_Mcourseschedulestate=1;break;}
					  			else{$_Motherpartteacherchecked=true;}
					  		}
					  	}
					  	else 
					  	{
					  		array_push($_Mprocesslogs,"===**>not possible course:{$_Mcourse1->getcoursecaption()} with teacher:{$_Mteacher1->teacherfamily} because of no teacher-group time matching");
					  		array_push($_Mtmpcourseschedulingtrace,array("t<>g",$_Mgroup1->getgroupid(),$_Mcourse1->getcourseid(),$_Mcourse1->getcoursecaption(),$_Mteacher1->getteacherid(),$_Mteacher1->getteachernamefam(),$_Mcourse1->getcoursepart()));
					  		$_Motherpartteacherchecked=true;
					  		continue;
					  	}
			  	  }
			  }
	  	 // }abov section
	  	 if($_Mcourseschedulestate!=1)
	  	 {
	  	 	if($_Mnoteacherhasthiscourse)
	  	 	{
	  	 		if(!($_Motherpartteacherchecked))
	  	 		{
	  	 			array_push($_Mprocesslogs,"!!!!!no teacher presents the course :{$_Mcourse1->getcoursecaption()}");
	  	 			array_push($_Moptimisearray,array("anyteach",$_Mgroup1->getgroupid(),$_Mcourse1->getcourseid(),$_Mcourse1->getcoursecaption(),'','',$_Mcourse1->getcoursepart()));
	  	 		}
	  	 		else 
	  	 		{
	  	 			array_push($_Mprocesslogs,"!!!!teacher hasn't enough time for course :{$_Mcourse1->getcoursecaption()}");
	  	 			array_push($_Moptimisearray,array("tnotentime",$_Mgroup1->getgroupid(),$_Mcourse1->getcourseid(),$_Mcourse1->getcoursecaption(),'','',$_Mcourse1->getcoursepart()));						  	 			
	  	 		}
	  	 	}
	  	 	while(count($_Mtmpcourseschedulingtrace)>0)
	  	 	{array_push($_Moptimisearray,array_pop($_Mtmpcourseschedulingtrace));}
	  	 }
	  	 
	  	 $_Mnoteacherhasthiscourse=true;$_Mtmpcourseschedulingtrace=array();
	  	 $_Mcourseschedulestate=0;
	  }
	}

	//roomscheduling
	_Fschedulerooms($_Mgroups);
	$_Mgroupslistcounter=0;

	while(_Fpickgroup($_Mgroup1,$_Mgroups,$_Mgroupslistcounter,$_Mgroupscount))
	{
	  $teacherroomcnsts=array();$courseroomcnsts=array();$grouprooms=array();$_Mcourseslistcounter=0;$room1=new _CLroom();
	  $groupid=$_Mgroup1->getgroupid();
	  $_Mgroupscheduledcourses=array();//$_Mgroups=array();getgroups($_Mgroups,$roomgroups,$_Mgroupscount,$_Mgroupsactions);
	  _Fgetteachersroomcnst($teacherroomcnsts,$_Mgroup1);
	  _Fgetcourseroomcnsts($courseroomcnsts,$_Mgroup1);
	  _Fgetgrouprooms4sch($grouprooms,$_Mgroup1);
	  _Fgetgroupfinalsedtermcourses($_Mgroupscheduledcourses,$groupid,$groupcoursecount);
	  while(_Fpickgroupscheduledcourse($_Mgroupscheduledcourses,$_Mcourse1,$_Mcourseslistcounter,$groupcoursecount))
	  {
	  	 $slotid=$_Mcourse1->getassignedslotid();
	  	 $slottype=$_Mcourse1->getassignedslottype();
	  	 $_Mroomassigned=0;
	  	 if(!($_Mcourse1->getassignedroomid()))
	  	 {
	  	 	if(array_key_exists("{$_Mcourse1->getcourseid()}-{$_Mcourse1->getcoursepart()}",$courseroomcnsts))
	  	 	{
	  	 		$roomid=$courseroomcnsts[$_Mcourse1->getcourseid()][0];
	  	 		//$roomcaption=$courseroomcnsts[$_Mcourse1->getcourseid()][1];
  	 			$room1->setroom($roomid,$groupid,$activetermid);
	  			if($room1->roomisfree($slotid,$slottype,$activetermid))
  	 			{_Fassignroom2course($_Mcourse1->getcourseid(),$groupid,$_Mcourse1->getcoursepart(),$room1,$slotid,$slottype,$activetermid);break;}//,$_Mcourse1->getassignedslottype()
	  	 	}
	  	    if($_Mcourse1->getassignedteacherid())
	  	    {
	  	 		if(array_key_exists($_Mcourse1->getassignedteacherid(),$teacherroomcnsts))
	  	 		{
	  	 			$roomid=$teacherroomcnsts[$_Mcourse1->getassignedteacherid()][0];
	  	 			//$roomcaption=$teacherroomcnsts[$_Mcourse1->getassignedteacherid()][1];
	  	 			$room1->setroom($roomid,$groupid,$activetermid);
		  			if($room1->roomisfree($slotid,$slottype,$activetermid))
	  	 			{_Fassignroom2course($_Mcourse1->getcourseid(),$groupid,$_Mcourse1->getcoursepart(),$room1,$slotid,$slottype,$activetermid);break;}//,$_Mcourse1->getassignedslottype()
		  	 		
		  	 		$teacherprevroomid=_Fgetteacherprevroomid($_Mcourse1->getassignedteacherid(),$_Mcourse1->getassignedslotid());
	  		 		if($teacherprevroomid)
	  	 			{
	  	 				if(_Froomisfree($teacherroomcnsts[$_Mcourse1->getassignedteacherid()],$slotid,$slottype))
	  	 				{}
	  	 			}
	  	 		}
	  	 	}
	  	 	if(count($grouprooms)>0)
	  	 	{
	  	 		$roomids=explode(",",$grouprooms[0]['roomIDs']);
	  	 		//$roomcaptions=explode(",",$grouprooms[0]['roomcaptions']);
	  	 		for($i=0 ; $i<count($roomids) ; $i++)
	  	 		{
	  	 			$roomid=$roomids[$i];
	  	 			$mgroupid=_Fgetmastergroup($groupid,$activetermid);
	  	 			$room1->setroom($roomid,$mgroupid,$activetermid);
		  			if($room1->roomisfree($slotid,$slottype,$activetermid))
	  	 			{_Fassignroom2course($_Mcourse1->getcourseid(),$groupid,$_Mcourse1->getcoursepart(),$room1,$slotid,$slottype,$activetermid);break;}//,$_Mcourse1->getassignedslottype()
	  	 		}
	  	 	}else _Fcreatereport('no room for group : ',$_Mgroup1->getgroupid(),0,0,0,0,0);
	  	 }
	  }
	}
	//***rooms
	for($i=0 ; $i<count($_Mprocesslogs) ; $i++)
	{
		mysql_query("insert into tracelogs (logstring) values('{$_Mprocesslogs[$i]}')");
	}
	for($i=0 ; $i<count($_Moptimisearray) ; $i++)
	{
		mysql_query("insert into optimise (opttype,groupID,courseID,coursecaption,teacherID,teachernamefam,whynots,coursepart) 
		values('{$_Moptimisearray[$i][0]}','{$_Moptimisearray[$i][1]}','{$_Moptimisearray[$i][2]}','{$_Moptimisearray[$i][3]}','{$_Moptimisearray[$i][4]}','{$_Moptimisearray[$i][5]}','','{$_Moptimisearray[$i][6]}')");
	}

?>
