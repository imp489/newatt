<?php session_start();
  @include_once "mygenclasses.php";
  @include_once "getdata_lib.php";
  @include_once "shrfuns.php";
  //@include "mygenlib.php";
  $_Mcon=new _CLconnection();
  //$_Mactivetermid=2;$_Mgroupid=3;
  //$_Mgroups=_Fgetsubgroups1(array("1,1#"));
  //if((isset($_POST['reporttype']))&(isset($_POST['dest'])))
  {
  	 //if($_POST['dest']!='getdata') break;
	
	  $_Mactivetermid=$_SESSION['activetermid'];
	  $_Mtype=$_POST['reporttype'];
	  if(isset($_POST['groupid']))
	  {
	  	$_Mgroupid=$_POST['groupid'];
	  	//$_Mgroups=_Fgetsubgroups1(array("$_Mgroupid,1#"));
	  	$_Mgroups=_Fgetallsubgroups($_Mgroupid);
	  	$_Mmgid1=_Fgetmastergroup($_POST["groupid"],$_Mactivetermid);$_Mgroups1=_Fgetallsubgroups($_Mmgid1);//_Fgetsubgroups1(array("$_Mmgid1,1#"));
	  }

   /*
	  $_Mactivetermid=2;
	  $_Mtype='gtpc';
	  //$_Mgroups=_Fgetsubgroups1(array("3,1#"));
	  $_Mgroupid=2;
   */
	  if($_Mtype=='b')
	  {  echo _Fgetbuildings();exit();  }
	  
	  else if($_Mtype=='br')
	  { if(isset($_POST['buildingid']) & $_POST['buildingid']!=0){$_Mbuildingid=$_POST['buildingid'];$_Mlowerparts=_Fgetlowerparts($_Mbuildingid); echo _Fgetbuildingrooms($_Mlowerparts);}else echo -1;exit();  }  

	  else if($_Mtype=='gbr')
	  { if(isset($_POST['buildingid']) & $_POST['buildingid']!=0){$_Mbuildingid=$_POST['buildingid'];$_Mlowerparts=_Fgetlowerparts($_Mbuildingid); echo _Fgetbuildingrooms($_Mlowerparts);}else echo -1;exit();  }

	  else if($_Mtype=='u')//universities
	  {echo _Fgetunvs();exit();}
	  
	  else if($_Mtype=='ug')// a university groups
	  {$unvid=$_POST['unvid'];echo _Fgetunvmaingroups();exit();}
	  
	  else if($_Mtype=='ugs')// all universities and groups
	  {echo _Frepunvmaingroups();}
																															
	  else if($_Mtype=='g')
	  {  echo _Fgetgroupslist();exit();}
	  
	  else if($_Mtype=='gc')//group courses
	  { echo _Fgetgroupcourses4asgn($_Mgroups,'');exit();  }

	  else if($_Mtype=='gcuni')//group courses
	  { echo _Fgetgroupcourses4teach($_Mgroups,'');exit();  }

	  else if($_Mtype=='agc')//all group courses
	  { echo _Fgetallgroupcourses4asgn($_Mgroups,'awgcaption');exit();  }

	  else if($_Mtype=='gt')
	  {echo _Frepgroupteachers($_Mgroups1,'');exit();}

	  else if($_Mtype=='gtpr')//groupteachers prior
	  {echo _Frepgroupteachers($_Mgroups1,'prior');exit();}

	  else if($_Mtype=='gtm')
	  {echo _Fgetgroupmasters($_Mgroupid)._Frepgroupteachers($_Mgroups1,'');exit();}
	  
	  // manual assignment data from here
	  
	  else if($_Mtype=='gtc')
	  {echo _Frepgroupteacherscourses($_Mgroups,$_Mgroups1);exit();}

	  else if($_Mtype=='gtcuni')
	  {echo _Frepgroupteacherscoursesuni($_Mgroups,$_Mgroups1);exit();}
	  
	  else if($_Mtype=='grt')
	  {echo _Frepgroupteachersrooms($_Mgroups,$_Mgroups1,$_Mmgid1);exit();}
	  
	  else if($_Mtype=='gcr')//assign room to course : course-room
	  {echo _Frepgroupcoursesrooms($_Mgroups);exit();}
	
	  else if($_Mtype=='gsc')//assign time to course : course-slot
	  {echo _Frepgroupcoursesslots($_Mgroups,$_Mgroupid);exit();}
	  
	  else if($_Mtype=='ts')//get teachers timeslots 
	  {$_Mteacherid=$_POST['teacherid'];echo _Frepteachersslots($_Mteacherid);exit();}
	  else if($_Mtype=='ats')//get teachers timeslots 
	  {echo _Frepgroupallteachersslots($_Mgroups);exit();}	  
	//******************rep cnsts
	  else if($_Mtype=='cscnst')//get courses timeslots constraits 
	  {$_Mcourseid=_Fgetcourseidfrom($_POST['courseid'],$_Mcoursepart);echo _Frepcourseslotcnst($_Mcourseid,$_Mcoursepart,$_Mgroups);exit();}
	
	  else if($_Mtype=='trcnst')//get teachers timeslots 
	  {$_Mcourseid=$_POST['teacherid'];echo _Frepteacherslotcnst($_Mteacherid);exit();}

	  else if($_Mtype=='tpc')//teachersprefferedcourses
	  {$_Mteacherid=$_POST['teacherid'];
	  	echo _Frepteachersprefcourses($_Mteacherid);exit();}
	//************** teachers prefered courses for a group
	  else if($_Mtype=='gtpc')//teachersprefferedcourses
	  {
	  	echo _Frepgroupteachersprefcourses($_Mgroupid);exit();}
	  	
	  else if($_Mtype=='tree')// university master groups tree 
	  {echo _Fgettree(0);exit();}

	  else if($_Mtype=='ftree')// university full groups tree
	  {echo _Fgettree(1);exit();}

	  else if($_Mtype=='stree')// university full groups tree
	  {$_Moutstr=_Fgetstree($_Mgroups,'')."$$$$"._Fgetgroupcourses4asgn($_Mgroups,'groupcaption').$_Mgroups;echo $_Moutstr;exit();}
	  //******** will change to return courses final related groups
	  
	  else if($_Mtype=='sgtree')// university sub groups tree (only finals)
	  {echo _Fgetstree($_Mgroups,'');exit();}
	  
	  else if($_Mtype=='sgftree')// university sub groups tree fullllllll subgroups, not only finals
	  {$_Moutstr=_Fgetstree($_Mgroups,'fullsubs')."$$$$"._Fgetgroupcourses4asgn($_Mgroups,'groupcaption').$_Mgroups;echo $_Moutstr;exit();}
	   //$_Moutstr defined for tracing output-920702
	  else if($_Mtype=='btree')// buildings tree
	  {echo _Fgetbtree();exit();}

	  else if($_Mtype=='bgtree')// buildings and groups tree
	  {echo _Fgetbgtree();exit();}

	  else if($_Mtype=='gtcs')//groupteachercourseschedule
	  {echo _Frepgtcandschedules($_Mgroups,$_Mgroups1);exit();}
	  
	  else if($_Mtype=='gct')//group course available teachers
	  {$_Mcoursecode=$_POST['ccode'];echo _Frepgroupcoursesteachers($_Mgroups1,$_Mgroupid,$_Mcoursecode);exit();}


	  else echo "not of predefined request types!";
  }

?>