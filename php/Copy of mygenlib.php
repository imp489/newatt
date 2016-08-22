<?php @session_start();
@include_once "jalali.php";
@include_once "mygenclasses.php";

//after entering data check for data & constraint integrities,
//such as : a teacher has a constraint time to teach a course at a time that isn't 
//in the groups valid times
//teacher course priorities 5:highest, 1: lowest
$coursestatus=array("s"=>"scheduled","n"=>"notscheduled","f"=>"timenotfound","t"=>"teachernotfound");
$teachertimestatus=array("s"=>"scheduled","e"=>"evenscheduled","o"=>"oddscheduled","f"=>"free","2"=>"2ndfree","n"=>"nothere");
//e : even schedulabale
$roomstatus=array("s"=>"fullscheduled","e"=>"evenscheduled","o"=>"oddscheduled","f"=>"free","n"=>"notusabale");
$groupstatus=array("s"=>"fullscheduled","e"=>"evenscheduled","o"=>"oddscheduled","f"=>"free","n"=>"notusabale");
$assigntype=array("a"=>"automatic","m"=>"manual");
//$courseconsttype=array();
$reportsarray=array();
$reportcounter=0;

	$fullassigned="tsrc";
	$roomassigned="cr";
	$teacherassigned="ct";
	$slotassigned="cs";
	$teacherslotassigned="tsc";
	
	$fullfree="ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff";
$timedate1=new mytimedate();
function initialisegroupvalues($inittype,$group1,&$groupscheduledcourses,&$grouptermcourses,&$groupteachers,&$groupcoursecount,&$groupteachercount,$teachersroomcnsts,&$teacherslistcounter,&$courseslistcounter)
{
	$groupscheduledcourses=array();$teachersroomcnsts=array();$grouptermcourses=array();$groupteachers=array();
	getgroupscheduledcourses($groupscheduledcourses,$group1->getgroupid());
	getteachersroomcnst($teachersroomcnsts,$group1->getgroupid());
	if($inittype=="constrained")
	{if(!(getgroupsedtermcourses($grouptermcourses,$group1,$groupcoursecount))) {echo 'no course defined for group :'.$group1->getgroupcaption();return false;}}
	else if($inittype=="normal")
	{if(!(getgrouptermcourses($grouptermcourses,$group1,$groupcoursecount))) {echo 'no course defined for group :'.$group1->getgroupcaption();return false;}}
	if(!(getgroupteachers($groupteachers,$group1,$groupteachercount))) {echo 'no teacher defined for group : '.$group1->getgroupcaption();return false;}
	$teacherslistcounter=0;$courseslistcounter=0;
	return true;
}

function writesession($n,$v)
{
	$_SESSION["{$n}"]=$v;
}

function slottype2slotstypes($timetypes,&$timeslots,&$timeslotstate)
{
			  	$times=explode("[:,]",$timetypes); 
			  	$timeslots=array();$timeslotstate=array();$no=0;
			  	for($it=0; $it<(count($times)/2) ;$it++)
			  	{$timeslots[$it]=$times[$it*2];$timeslotstate[$it]=$times[$it*2+1];}
}

function complement($chr)
{if($chr=='e') return 'o';if($chr=='o') return 'e';if($chr=='s') return 's';}

function smaller($ch1,$ch2)
{
	if($ch1==$ch2) return $ch1;
	if( ($ch1=='f') & (($ch2=='o')||($ch2=='e'))) return ($ch2);
	if( ($ch2=='f') & (($ch1=='o')||($ch1=='e'))) return ($ch1);
	if( ($ch1=='n') || ($ch2=='n') ) return 'n';
}

function createreport($report)
{
	global $processlogs;
	array_push($processlogs,$report);
	$GLOBALS['reportcounter']++;
	if($GLOBALS['reportcounter']==20)
	  savereportstodb();
}

function resetcounters(&$teacherslistcounter,&$courseslistcounter)
{
	$teacherslistcounter=0;$courseslistcounter=0;
}

function savereporttodb()
{}
function getgroups(&$groups,&$groupscount)
{
	$sqlstr="select groupID,groupcaption,groupstatus,initialgroupstatus from groupstatus where termID={$_SESSION['activetermid']} and groupID in(select groupID from groups where finalgroup=1)";
	if($result=mysql_query($sqlstr))
	{
		$groupscount=mysql_num_rows($result);
		if($groupscount>0)
		{
			while($row=mysql_fetch_array($result,MYSQL_ASSOC))
				array_push($groups,$row);
			mysql_free_result($result);
			return true;	
		}else return false;
	}else return false;
}

function pickgroup(&$group1,$groups,&$groupslistcounter,$groupscount)
{	
	if($groupslistcounter<$groupscount)
	{
		if($row=$groups[$groupslistcounter])
		{
			$group1->setgroupid($row["groupID"]);
			$group1->setgroupstatus($row["groupstatus"]);
			$group1->setgroupcaption($row["groupcaption"]);
			//$group1->setinitialgroupstatus($row["initialgroupstatus"]);
			$groupslistcounter++;
			return true;
		}
		else return false;
	}
	else return false;
}

function getgroupscheduledcourses(&$groupscheduledcourses,$groupid)
{
	$sqlstr="select * from asgncnsts where cnsttype like '%c%'and groupID={$groupid} and termID={$_SESSION['activetermid']} order by courseID,cnsttype";
	$result=mysql_query($sqlstr);
	while($row=mysql_fetch_array($result,MYSQL_ASSOC))
	{array_push($groupscheduledcourses,$row);}
}
function getteachersroomcnst(&$teachersroomcnsts,$groupid)
{
	$sqlstr="select teacherID,roomID from asgncnsts where cnsttype like 'tr'and groupID={$groupid} and termID={$_SESSION['activetermid']} order by courseID,cnsttype";
	$result=mysql_query($sqlstr);
	while($row=mysql_fetch_array($result,MYSQL_ASSOC))
	  array_push($teachersroomcnsts,$row);
}
function checkcoursecnst(&$groupscheduledcourses,&$course1)
{
	global $fullassigned,$slotassigned,$teacherassigned,$roomassigned;
	$cnsttype='';
	for($i=0; $i<count($groupscheduledcourses) ; $i++)
	{
		if($groupscheduledcourses[$i]['courseID']==$course1->getcourseid())
		{
			if($groupscheduledcourses[$i]['cnsttype']==$fullassigned)
			{
				$course1->setcourseslotcnst($groupscheduledcourses[$i]['slots']);
				$course1->setcourseteachercnst($groupscheduledcourses[$i]['teacherID']);
				$course1->setcourseroomcnst($groupscheduledcourses[$i]['roomID']);
				return $fullassigned;
			}
			else if($groupscheduledcourses[$i]['cnsttype']==$roomassigned)
			{
				$course1->setcourseroomcnst($groupscheduledcourses[$i]['roomID']);
				$cnsttype.='r';
			}
			else if($groupscheduledcourses[$i]['cnsttype']==$teacherassigned)
			{
				$course1->setcourseteachercnst($groupscheduledcourses[$i]['teacherID']);
				$cnsttype.='t';
			}
			else if($groupscheduledcourses[$i]['cnsttype']==$slotassigned)
			{
				$course1->setcourseslotcnst($groupscheduledcourses[$i]['slots']);
				$cnsttype.='s';
			}

		}		
	}
	
	
	
	return setcnstorder($cnsttype);
}
function setcnstorder($cnsttype)
{
	$tmpcnst='';
	if(strpos($cnsttype,"t",0)===false) $tmpcnst='-';
	else $tmpcnst='t';
	if(strpos($cnsttype,"s",0)===false) $tmpcnst.='-';
	else $tmpcnst.='s';
	if(strpos($cnsttype,"r",0)===false) $tmpcnst.='-';
	else $tmpcnst.='r';
	return $tmpcnst;
}

function getgrouptermcourses(&$grouptermcourses,$group1,&$groupcoursecount)
{
	$sqlstr="select courseID,coursecaption,coursehaspref,courseAunits,courseTunits,coursepreferedtimes,coursetype from termcoursestatus where groupidconf1={$group1->getgroupid()} and termID={$_SESSION['activetermid']} and coursestatus<>'s' and courseID not in (select courseID from asgncnsts group by courseID) order by courseID";
	//select not cheduled courses
	if($result=mysql_query($sqlstr))
	{
		$groupcoursecount=mysql_num_rows($result);
		if($groupcoursecount>0)
		{
			while($row=mysql_fetch_array($result,MYSQL_ASSOC))
	  			array_push($grouptermcourses,$row);
			mysql_free_result($result);
			return true;
		}else return false;
	}else return false;
}
function getgroupsedtermcourses(&$grouptermcourses,$group1,&$groupcoursecount)
{
	$sqlstr="select courseID,coursecaption,coursehaspref,courseAunits,courseTunits,coursepreferedtimes,coursetype from termcoursestatus where groupidconf1={$group1->getgroupid()} and termID={$_SESSION['activetermid']} and coursestatus<>'s' and courseID in (select courseID from asgncnsts group by courseID) order by courseID";
	//select not cheduled courses
	if($result=mysql_query($sqlstr))
	{
		$groupcoursecount=mysql_num_rows($result);
		if($groupcoursecount>0)
		{
			while($row=mysql_fetch_array($result,MYSQL_ASSOC))
			{array_push($grouptermcourses,$row);}
			mysql_free_result($result);
			return true;
		}else return false;
	}else return false;
}

function pickgrouptermcourse($groupscheduledcourses,&$course1,&$grouptermcourses,&$courseslistcounter,$groupcoursecount)
{
	if($courseslistcounter<$groupcoursecount)
	{

	    if($row=$grouptermcourses[$courseslistcounter])
	    {
		    $course1->setcourseid($row["courseID"]);//"courseid"
			$course1->setcoursecaption($row["coursecaption"]);
			$course1->setcourseunits($row["courseAunits"],$row["courseTunits"]);
			$course1->setcoursepreftimes($row["coursepreferedtimes"]);
			//$course1->setcoursecode($row["coursecode"]); no course code needed 
			setcoursecnsts($course1,$groupscheduledcourses);
			$courseslistcounter++;
			return true;
	    }
	    else return false;
	}    
    else return false;
}
function setcoursecnsts(&$course1,$groupscheduledcourses)
{
	global $fullassigned,$teacherassigned,$roomassigned,$slotassigned,$teacherslotassigned;
	$cnsttype='';
	for($i=0 ; $i<count($groupscheduledcourses) ; $i++)
	{
		$row=$groupscheduledcourses[$i];
		if($course1->getcourseid()==$row['courseID'])
		{
			if($row['cnsttype']==$fullassigned)
			{
				$course1->setcourseteachercnst($row['teacherID']);
				$course1->setcourseslotcnst($row['slots']);
				$course1->setcourseroomcnst($row['roomID']);
				$course1->setcnsttype($fullassigned);
				return;
			}
			else if($row['cnsttype']==$teacherslotassigned)
			{
				$course1->setcourseteachercnst($row['teacherID']);
		    	$course1->setcourseslotcnst($row['slots']);
		    	$course1->setcnsttype($teacherslotassigned);
		    	return;
			}
			if($row['cnsttype']==$teacherassigned)
			{$course1->setcourseteachercnst($row['teacherID']);$cnsttype.='t';}
			else if($row['cnsttype']==$slotassigned)
			{$course1->setcourseslotcnst($row['slots']);$cnsttype.='s';}
			else if($row['cnsttype']==$roomassigned)
			{$course1->setcourseroomcnst($row['roomID']);$cnsttype.='r';}
		}
	}
	$course1->setcnsttype(setcnstorder($cnsttype));	
}
function getgroupteachers(&$groupteachers,$group1,&$groupteachercount)
{
	$groupid=$group1->getgroupid();
	$sqlstr="select teacherID,teachername,teacherfamily,teachercourseids,teachercoursepriors,times,hastimeconst,teacherPrior,teachermaxslots,teachercurslots from tchrtimes where ((teachergroupid={$groupid}) and 
										(termID={$_SESSION['activetermid']})and(teachercurslots<teachermaxslots)) order by teacherID";
	if($result=mysql_query($sqlstr))
	{
		$groupteachercount=mysql_num_rows($result);
		if($groupteachercount>0)
		{
			while($row=mysql_fetch_array($result,MYSQL_ASSOC))
	  			array_push($groupteachers,$row);
	
			$groupteachercount=mysql_num_rows($result);
			mysql_free_result($result);

			return true;
		}else return false;
	}else return false;
}

function pickteacher(&$teacher1,$course1,&$groupteachers,&$checkedteachersids,$groupteachercount)
{
		if(selectbestteacher4course($groupteachers,$row,$course1->getcourseid(),$course1->getcourseunits(),$checkedteachersids,$groupteachercount,''))
		{
						$teacher1->setteacherid($row["teacherID"]);
						$teacher1->setteachertimes($row["times"]);
						//$teacher1->setteachergroupid($row["teachergroupid"]);
						$teacher1->teachermaxslots=$row["teachermaxslots"];
						$teacher1->teachername=$row["teachername"];
						$teacher1->teacherfamily=$row["teacherfamily"];
						return true;
						break;
					/*{
						createreport("teacher :{$row['teacherID']} hasn't enough free slots!\n");
						$teacherslistcounter++;
					}*/
		}


	
	createreport("no teacher found for course : {$course1->getcourseid()} ,{$course1->getcoursecaption()} . !\n");
	return false;	
}

//will complete
function pickteacherforslottedcourse($teacher1,$course1,$groupteachers,$checkedteachersids,$groupteachercount,$slotcnst)
{
		if(selectbestteacher4course($groupteachers,$row,$course1->getcourseid(),$course1->getcourseunits(),$checkedteachersids,$groupteachercount,$slotcnst))
		{
						$teacher1->setteacherid($row["teacherID"]);
						$teacher1->setteachertimes($row["times"]);
						//$teacher1->setteachergroupid($row["teachergroupid"]);
						$teacher1->teachermaxslots=$row["teachermaxslots"];
						$teacher1->teachername=$row["teachername"];
						$teacher1->teacherfamily=$row["teacherfamily"];
						return true;
						break;
					/*{
						createreport("teacher :{$row['teacherID']} hasn't enough free slots!\n");
						$teacherslistcounter++;
					}*/
		}


	
	createreport("no teacher found for course : {$course1->getcourseid()} ,{$course1->getcoursecaption()} . !\n");
	return false;	

}

//this func can be completed by teacher course priority
function selectbestteacher4course($groupteachers,&$row,$courseid,$courseunits,&$checkedteachersids,$groupteachercount,$slotcnst)
{
	if(count($checkedteachersids)==$groupteachercount)
		return false;
	$selectedteacherid=0;$selectedteacherprior=0;$row1='';
	for($i=0 ; $i<$groupteachercount ;$i++)
	{
		$trow=$groupteachers[$i];
		if(!(in_array($trow['teacherID'],$checkedteachersids)))
		{
			$teachercourses=explode("[,]",$trow["teachercourseids"]);
			$index=array_search($courseid,$teachercourses);
			if($index!==false)
			{
				$teachercoursepriors=explode("[,]",$trow["teachercoursepriors"]);
				
				if( ($teachercoursepriors[$index]>=$selectedteacherprior)&
				    ($trow['teachercurslots']<($trow['teachermaxslots']-$courseunits))&
				    (($slotcnst=='')||(statushastime4slots($trow['times'],$slotcnst))) )//if slotcnst is '' no has checking needed
				{
					$selectedteacherid=$trow['teacherID'];
					$selectedteacherprior=$teachercoursepriors[$index];
					$row1=$trow;
				}
			 }
			 else createreport(":::> teacher: {$trow["teachername"]} hasnot  course : $courseid . !\n"); 
		  }
	}
	$row=$row1;
	if($selectedteacherid)
	{array_push($checkedteachersids,$selectedteacherid);return true;}
	else 
		return false;	
}
function statushastime4slots($timestatus,$slotcnst) 
{
	$has=true;
	slottype2slotstypes($slotcnst,$timeslots,$timeslotstates);
	for($i=0;$i<count($timeslots) ; $i++)
	{
		if($timestatus[$timeslots[$i]]=='f') continue;
		if(($timeslotstates[$i]=='s'))//&($timestatus[$timeslots[$i]]!='f')) not needed, passing prev line means  not 'f'
		{$has=false;break;}
		if($timestatus[$timeslots[$i]]!=$timeslotstates[$i])
		{$has=false;break;}
	}
	return $has;
}
//check times according to course units
function checktimestchrgrpcnstr($teacher1,$group1,$course1,&$possibletimes)
{
	$courseunits=$course1->getcourseunits();
	//if($courseunits==3) $courseunits++;//3 units needs 4 free slot
	$teachertime=$teacher1->getteachertimes();
	$groupstatus=$group1->getgroupstatus();
	$freetimecount=0;
	for($i=0 ; $i<98 ; $i++)
	{
		//checkconstraint4course();
		
		if(($teachertime[$i]=='f')&($groupstatus[$i]=='f'))
		  $freetimecount++;
		if((($teachertime[$i]=='e')&($groupstatus[$i]=='e'))||
		   (($teachertime[$i]=='o')&($groupstatus[$i]=='o'))||
		   (($teachertime[$i]=='f')&($groupstatus[$i]=='o'))||
		   (($teachertime[$i]=='o')&($groupstatus[$i]=='f'))||
		   (($teachertime[$i]=='e')&($groupstatus[$i]=='f')))
			  $freetimecount+=.5;
	   	$possibletimes[$i]=smaller($teachertime[$i],$groupstatus[$i]);
		
	}
	if ($freetimecount>=$courseunits)
	     return true;
	else return false;
}

function getgroupcourseconstraints($group1,&$groupcourseconstraint)
{
	$sqlstr="select * from coursetimeconst where groupID={$group1->getgroupid()} 
				and termID={$_SESSION['activetermid']}";
	$result=mysql_query($sqlstr);
	while($row=mysql_fetch_assoc($result))
	  array_push($groupcourseconstraint,$row);
	return;
}

function meetscourseconst($course1,&$possibletimes)
{
	global $fullfree;
	$courseunits=$course1->getcourseunits();
	if(($courseunits==3)||($courseunits==1)) $courseunits++;//3 units needs 4 free slot
	if($course1->getcoursehaspref()) $coursetime=$course1->getcoursepreftimes();
	else $coursetime=$fullfree;
	$freetimecount=0;
	for($i=0 ; $i<98 ; $i++)
	{
		if((($coursetime[$i]==$possibletimes[$i])&($coursetime[$i]!='n'))||
		   (($coursetime[$i]=='f')&(($possibletimes[$i]=='o')||($possibletimes[$i]=='e')))||
		   (($possibletimes[$i]=='f')&(($coursetime[$i]=='o')||($coursetime[$i]=='e'))))
		{
		   	$freetimecount++;
		}
		else $possibletimes[$i]='n';
	}
	if ($freetimecount>=$courseunits)
	     return true;
	else return false;
}

function scheduleit($teacher1,$course1,$possibletimes,$group1,&$warningarray,$assigntype)
{
  $timeslots=array();
  $timeslotstate=array();//full scheduled or periodic
	//*****will update to be optimized selecting from final possible slots 
	if($course1->getcourseunits()==1)
	{
		schedul4oneu($teacher1,$course1,$group1,$possibletimes,$timeslots,$timeslotstate,$assigntype);
	}
	else if($course1->getcourseunits()==2)
	{
		for($i=0 ; $i<97 ; $i++)
		{
			if(($possibletimes[$i]==$possibletimes[$i+1])&($possibletimes[$i]=='f'))
			{
				
				array_push($timeslots,$i);
				array_push($timeslots,$i+1);
				array_push($timeslotstate,'s');
				array_push($timeslotstate,'s');
				

				break;
			}	
		}	
	}
	else if($course1->getcourseunits()==3)
	{
		$scheduledslots=0;$fullpartselected=0;$halfpartselected=0;
		for($i=0 ; $i<97 ; $i++)
		{
			
			
			if(!($fullpartselected))
			{
				if(($possibletimes[$i]==$possibletimes[$i+1])&($possibletimes[$i]=='f'))
				{
					array_push($timeslots,$i);array_push($timeslots,$i+1);
					array_push($timeslotstate,'s');array_push($timeslotstate,'s');
					$fullpartselected=1;$scheduledslots+=2;
					$possibletimes[$i]='s';$possibletimes[$i+1]='s';
					if($scheduledslots==4) break;
					$i=((int)($i / 14)+1)*14;continue;
				}	
			}							
			if(!($halfpartselected))
			{
				if(($possibletimes[$i]==$possibletimes[$i+1])&($possibletimes[$i]=='f'))
				{
					array_push($timeslots,$i);				
					array_push($timeslots,$i+1);
					array_push($timeslotstate,'o');//if a time is fully free, odd slots will be assigned this can be change
					array_push($timeslotstate,'o');
					$possibletimes[$i]='e';$possibletimes[$i+1]='e';
					$fullpartselected=1;
					$scheduledslots+=2;
					if($scheduledslots==4) break;
					$i=((int)($i / 14)+1)*14;
				}	

				else if(($possibletimes[$i]==$possibletimes[$i+1])&
					   (($possibletimes[$i]=='o')||($possibletimes[$i]=='e')))
				{
					array_push($timeslots,$i);array_push($timeslots,$i+1);
					array_push($timeslotstate,$possibletimes[$i]);array_push($timeslotstate,$possibletimes[$i]);	
					$possibletimes[$i]='s';$possibletimes[$i+1]='s';
					$halfpartselected=1;			
					$scheduledslots+=2;
					if($scheduledslots==4) break;
					$i=((int)($i / 14)+1)*14;
				}

			}							

		}
		if($scheduledslots!=4)//full and half slots are located in 1 day
		{
			createreport("course : {$course1->getcourseid()} with {$course1->getcourseunits()} units scheduled in 1 day !\n");
			for($i=( ((int)($timeslots[0]/14)) *14) ; $i<97 ; $i++)
			{
				if($scheduledslots==4) break;
				
				if(!($fullpartselected))
				{
					if(($possibletimes[$i]==$possibletimes[$i+1])&($possibletimes[$i]=='f'))
					{
						array_push($timeslots,$i);				
						array_push($timeslots,$i+1);
						array_push($timeslotstate,'s');
						array_push($timeslotstate,'s');
						break;
					}	
				}							
				else if((!($halfpartselected)))
				{				
					if($scheduledslots==4) break;
					if(($possibletimes[$i]==$possibletimes[$i+1])&
					  (($possibletimes[$i]=='o')||($possibletimes[$i]=='e')||($possibletimes[$i]=='f')))
					{
						$tmpslottype=$possibletimes[$i];
						if($tmpslottype=='f') $tmpslottype='o';//if a slot is free, o is selected for half ones
						array_push($timeslots,$i);array_push($timeslots,$i+1);
						array_push($timeslotstate,$tmpslottype);array_push($timeslotstate,$tmpslottype);	
						break;
					}
	
				}							
			}
		}
		
	}	
		
	updatecourseteachergroup($course1,$teacher1,$group1,$timeslots,$timeslotstate,$assigntype);

	
}
function schedul4oneu($teacher1,$course1,$group1,$possibletimes,$timeslots,$timeslotstate,$assigntype)
{
		for($i=0 ; $i<97 ; $i++)
		{
			if(($possibletimes[$i]==$possibletimes[$i+1])&($possibletimes[$i]=='f'))
			{
				
				array_push($timeslots,$i);
				array_push($timeslots,$i+1);
				array_push($timeslotstate,'o');// from full free times,odd slots will be assigned for 1unit courses
				array_push($timeslotstate,'o');
				
				updatecourseteachergroup($course1,$teacher1,$_SESSION['activetermid'],$group1,$timeslots,$timeslotstate,$assigntype);
				break;
			}
			if(($possibletimes[$i]==$possibletimes[$i+1])&(($possibletimes[$i]=='o')||($possibletimes[$i]=='e')))
			{
				
				array_push($timeslots,$i);
				array_push($timeslots,$i+1);
				array_push($timeslotstate,$possibletimes[$i]);// from full free times,odd slots will be assigned for 1unit courses
				array_push($timeslotstate,$possibletimes[$i]);
				

				break;
			}
				
		}	
	
}

function schedul4oneu($teacher1,$course1,$group1,$possibletimes,$timeslots,$timeslotstate,$assigntype)
{
	
}
function updatecourseteachergroup($course1,$teacher1,$group1,$timeslots,$timeslotstate,$assigntype)
{
	updatecoursestatus($course1,$group1->getgroupid(),$timeslots);
	updateteachertimes($teacher1,$group1->getgroupid(),$timeslots,$timeslotstate,$course1->getcourseunits());
	updategrouptimes($group1,$timeslots,$timeslotstate);
	updateassignments($course1,$teacher1,$group1,$timeslots,$timeslotstate,$assigntype);
}

function updatecoursestatus($course1,$groupid,$timeslots)
{
	global $processlogs;
	$sqlstr="update termcoursestatus set coursestatus='s' where courseID={$course1->getcourseid()} 
					and termID={$_SESSION['activetermid']} and groupidconf1={$groupid}";
	if(!(mysql_query($sqlstr)))
	{
		global $errorsarray;
		array_push($errorsarray,"course : {$course1->getcourseid()} : {$course1->getcoursecaption()} at time: $timeslots ,status couldnt be updated!");
		createreport($processlogs,"course : {$course1->getcourseid()} : {$course1->getcoursecaption()} at time: $timeslots ,status couldnt be updated!");
	}
}

function updateteachertimes($teacher1,$groupid,$timeslots,$timeslotstate,$courseunits)
{
	$timesstr=$teacher1->getteachertimes();
	$j=0;
	foreach ($timeslots as &$id)
	{  
		if($timesstr[$id]=='f')
			$timesstr[$id]=complement($timeslotstate[$j++]);
	   else 
	   		$timesstr[$id]='s';
		
	}
	
	$teacher1->saveteachertimestodb($groupid,$timesstr,$courseunits);
}

function updategrouptimes($group1,$timeslots,$timeslotstate)
{
	$timesstr=$group1->getgroupstatus();
	$j=0;
	foreach ($timeslots as &$id)
	{
		if($timesstr[$id]=='f')
			$timesstr[$id]=complement($timeslotstate[$j++]);
		else	
			$timesstr[$id]='s';
	}
	$group1->savegrouptimestodb($timesstr);
	
}

function updateassignments($course1,$teacher1,$group1,$timeslots,$timeslotstate,$assigntype)
{
	$timestr="";$j=0;
	foreach ($timeslots as &$id)
	{if($id<10) $timestr.='0'.$id.':'.$timeslotstate[$j++].',';
	 else $timestr.=$id.':'.$timeslotstate[$j++].',';}
	 $timestr=substr($timestr,0,strlen($timestr)-1);//will test
	$timedate1=new mytimedate();
	@$timedate1->setjdate();
	$sqlstr="insert into assignments(termID,groupID,groupcaption,teacherID,teachername,teacherfamily,courseID,coursecaption,courseunits,timeslots,asgndate,asgntype,asgnuserid) values
				('{$_SESSION['activetermid']}','{$group1->getgroupid()}','{$group1->groupcaption}','{$teacher1->teacherid}','{$teacher1->teachername}','{$teacher1->teacherfamily}',
				'{$course1->getcourseid()}','{$course1->getcoursecaption()}','{$course1->getcourseunits()}','$timestr','{$timedate1->jdate}','$assigntype','testuser')";//will change with real user
	mysql_query($sqlstr);
}

function getbesteacher4course($courseID)
{
}



function getgrouplots($groupid)
{
}

function checkassignpossibility($groupID,$courseid,$teacherid)
{
}


?>