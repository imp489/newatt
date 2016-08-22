<?php session_start();

  @include_once "mygenclasses.php";
  @include_once "cnstvalues.php";
  @include_once "getdata_lib.php";
  $_Mcon=new _CLconnection();
  $_Mfullfree=str_repeat('f',98);
  $_Mactivetermid=$_SESSION['activetermid'];
  $_Mtr=$_POST['termres'];//echo $_Mtr;
  //$_Mactivetermid=2;
  //$_Mtr="tpc,5,1-1!c1::2-2!w1::3,2-2!a1::3-3!w1::4";
  //$_Mtr="c,2,641!@@6411~I~6412~I~6413~I,638!@@6381~I";
  //$_Mtr="c,2,638@@6389~D";
  $_Mitems=explode(",",$_Mtr);
  $_Mtype=$_Mitems[0];
  if($_Mtype=='t')//set term teachers
  { 
  	echo _Fsavethistermsteachers($_Mitems);exit();  
  }
  else if($_Mtype=='c')//set term courses
  { //c,gid,cid,ccode,actiontype:Insert-Delete
  	echo _Fsavethistermscourses($_Mitems);exit();
  }
  else if($_Mtype=='r')//set term rooms
  { 
  	echo _Fsavethistermsrooms($_Mitems);exit();
  }
  else if($_Mtype=='tpc')//set teachers term courses
  {
  	echo _Fsaveteachersprefcourses($_Mitems);exit();
  }
  else if($_Mtype=='ttp')//set teachers total priority
  {
  	echo _Fsaveteacherstprior($_Mitems);exit();
  }//ctp,gtp,stp
  else if($_Mtype=='ctp')//set courses total priority
  {
  	echo _Fsavecoursestprior($_Mitems);exit();
  }//ctp,gtp,stp

  else if($_Mtype=='gtp')//set groups total priority
  {
  	echo _Fsavegroupstprior($_Mitems);exit();
  }
  
  else if($_Mtype=='c2og')//set teachers term courses
  {
  	echo _Fsavecourseschwithothergroup($_Mitems);exit();
  }
  else if($_Mtype=='c2sg')//assign courses to subgroups
  {
  	echo _Fsavecoursessubgroup($_Mitems);exit();
  }
  else if($_Mtype=='gm')//set teachers term courses
  {
  	echo _Fsetgroupmaster($_Mitems);exit();
  }
  
  
  
  //****************************************
  function _Fsetgroupmaster($_Mitems)
  {
  	$_Mgroupid=$_Mitems[1];$_Mteacherid=$_Mitems[2];$_Mteachername='';$_Mteacherfamily='';
  	$_Msqlstr="select * from tchrtimes where teacherID=$_Mteacherid and termID={$GLOBALS['_Mactivetermid']}";
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
  		if(mysql_num_rows($_Mresult)>0)
  		{
  			$_Mrow=mysql_fetch_array($_Mresult);
  			$_Mteachername=$_Mrow['teachername'];
  			$_Mteacherfamily=$_Mrow['teacherfamily'];
  		}else return 0;
  	}else return -1;
  	$_Msqlstr="select * from gms where groupID=$_Mgroupid and termID={$GLOBALS['_Mactivetermid']}";
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
  		if(mysql_num_rows($_Mresult)>0)
  		{
  			$_Msqlstr="update gms set teacherID=$_Mteacherid,teachername='$_Mteachername',teacherfamily='$_Mteacherfamily' where groupID=$_Mgroupid and termID={$GLOBALS['_Mactivetermid']}";
  			if($_Mresult=mysql_query($_Msqlstr)) return 1;
  			else return 0;
  		}
  		else
  		{
  			$_Msubgroups=_Fgetsubgroups1(array("$_Mgroupid,1#"));
  			$_Msqlstr="insert into gms(teacherID,termID,teachername,teacherfamily,groupID,groupcaption,subgroupids)values
  			('$_Mteacherid','{$GLOBALS['_Mactivetermid']}','$_Mteachername','$_Mteacherfamily','$_Mgroupid','$_Mgroupcaption','$_Msubgroups')";
  			//echo $_Msqlstr;
  			if($_Mresult=mysql_query($_Msqlstr)) return 1;
  			else return 0;
  		}
  	}else return -1;
  }	
  
  function _Fsavecourseschwithothergroup($_Mitems)
  {
  	$_Mgroupid=$_Mitems[1];$_Mawgroupid=$_Mitems[2];
  	$_Mcourseids="";
  	for($i=3 ; $i< count($_Mitems)-1 ; $i++)
  	{
  		$_Mcourseid=_Fgetcourseidfrom($_Mitems[$i],$_Mcoursepart);
  		$_Mcourseids.="$_Mcourseid,";
  	}
  	$_Mcourseids=substr($_Mcourseids,0,strlen($_Mcourseids)-1);
  	if($_Mresult=mysql_query("select groupcaption from groupstatus where groupID=$_Mgroupid and termID={$GLOBALS['_Mactivetermid']}"))
  	{	
  		$_Mrow=mysql_fetch_array($_Mresult);
  		$_Mgroupcaption=$_Mrow[0];
  	}
  	
  	$_Msqlstr="update termcoursestatus set awgroupid=$_Mawgroupid,group1caption='$_Mgroupcaption' where courseID in($_Mcourseids) and termID={$GLOBALS['_Mactivetermid']}";
  	if($_Mresult=mysql_query($_Msqlstr)) return 1;
	else return 0;
  }
  function _Fsavecoursessubgroup($_Mitems)
  {
  	$groupno=$_Mitems[1];$_Mgroupidconf1=$_Mitems[2];
  	$_Mcourseids="";
  	for($i=3 ; $i< count($_Mitems)-1 ; $i++)
  	{
  		$_Mcourseid=_Fgetcourseidfrom($_Mitems[$i],$_Mcoursepart);
  		$_Mcourseids.="$_Mcourseid,";
  	}
  	$_Mcourseids=substr($_Mcourseids,0,strlen($_Mcourseids)-1);
  	if($groupno<=3)
  		$_Msqlstr="update termcoursestatus set groupidconf$groupno=$_Mgroupidconf1 where courseID in($_Mcourseids) and termID={$GLOBALS['_Mactivetermid']}";
  	else if($groupno<=5)
  	{$i=$groupno-2;$_Msqlstr="update termcoursestatus set groupidconf$i=0 where courseID in($_Mcourseids) and termID={$GLOBALS['_Mactivetermid']}";}
  	//echo $_Msqlstr;
  	if($_Mresult=mysql_query($_Msqlstr)) return 1;
	else return 0;
  }
  function _Fsaveteachersprefcourses($_Mitems)//12-1-8026!c1::1-7870!c1::1@@@
  {
  	$_Mout=0;$_Mitems1=explode("@@@",$_Mitems[1]);
  	for($i=0 ; $i<(count($_Mitems1)-1) ; $i++)
  	{
  		$_Mcourses='';$_Mcoursepriors='';$_Mcourseparts="";$_Mteachernos="";
  		//$_Mitems1[$i]=substr($_Mitems1[$i],0,strlen($_Mitems1[$i])-1);
  		$_Mcoursespres=explode("-",$_Mitems1[$i]);
  		$_Mteacherid=$_Mcoursespres[0];$_Mgroupid=$_Mcoursespres[1];
  		for($j=2 ; $j<count($_Mcoursespres) ; $j++)
  		{
  			$cp=explode("::",$_Mcoursespres[$j]);//interface sequence : courseid!coursepart::coursepres::teacherprior
  			$_Mcourses.=_Fgetcourseidfrom($cp[0],$_Mcoursepart).',';
  			$_Mcoursepriors.=$cp[1].',';
  			$_Mcourseparts.=$_Mcoursepart.',';
  			//$_Mteachernos.=$cp[2].',';
  			$_Mteachernos.='1,';//920621
  		}
  		$len1=strlen($_Mcourses);if($_Mcourses[$len1-2]==',') $len1-=2;else $len1--;
  		$len2=strlen($_Mcoursepriors);if($_Mcoursepriors[$len2-2]==',') $len2-=2;else $len2--;
  		$len3=strlen($_Mcourseparts);if($_Mcourseparts[$len3-2]==',') $len3-=2;else $len3--;
  		$len4=strlen($_Mteachernos);if($_Mteachernos[$len4-2]==',') $len4-=2;else $len4--;

  		$_Mcourses=substr($_Mcourses,0,$len1);
  		$_Mcoursepriors=substr($_Mcoursepriors,0,$len2);
  		$_Mcourseparts=substr($_Mcourseparts,0,$len3);
  		$_Mteachernos=substr($_Mteachernos,0,$len4);
	  	$_Msqlstr="update tchrtimes set teachercourseids='$_Mcourses',teachercoursepriors='$_Mcoursepriors',courseparts='$_Mcourseparts',teacherno='$_Mteachernos' where teacherID=$_Mteacherid and teachergroupid=$_Mgroupid and termID={$GLOBALS['_Mactivetermid']}";
	  	//echo $_Msqlstr;
	  	if(mysql_query($_Msqlstr)) $_Mout=1;
  	}
  	return $_Mout;
  }
  function _Fsaveteacherstprior($_Mitems)
  {
  	$_Mout=1;
  	for($i=1 ; $i<(count($_Mitems)-1) ; $i++)
  	{
  		//$_Mitems1[$i]=substr($_Mitems1[$i],0,strlen($_Mitems1[$i])-1);
  		$_Mtpgid=explode("-",$_Mitems[$i]);
  		$_Mteacherid=$_Mtpgid[0];$_Mtprior=$_Mtpgid[1];$_Mgroupid=$_Mtpgid[2];
	  	$_Msqlstr="update tchrtimes set teacherPrior='$_Mtprior' where teacherID=$_Mteacherid and teachergroupid=$_Mgroupid and termID={$GLOBALS['_Mactivetermid']}";
	  	//echo $_Msqlstr;
	  	if(!(mysql_query($_Msqlstr))) $_Mout=0;
  	}
  	return $_Mout;
  }
  function _Fsavecoursestprior($_Mitems)
    {
  	$_Mout=1;
  	for($i=1 ; $i<(count($_Mitems)-1) ; $i++)
  	{
  		//$_Mitems1[$i]=substr($_Mitems1[$i],0,strlen($_Mitems1[$i])-1);
  		$_Mtpgid=explode("-",$_Mitems[$i]);
  		$_Mcourseid=$_Mtpgid[0];$_Mcprior=$_Mtpgid[1];$_Mgroupid=$_Mtpgid[2];
	  	$_Msqlstr="update termcoursestatus set coursep='$_Mcprior' where courseID=$_Mcourseid and termID={$GLOBALS['_Mactivetermid']}";
	  	//$_Msqlstr="update termcoursestatus set coursep='$_Mtprior' where courseID=$_Mteacherid and teachergroupid=$_Mgroupid and termID={$GLOBALS['_Mactivetermid']}";
	  	//echo $_Msqlstr;
	  	if(!(mysql_query($_Msqlstr))) $_Mout=0;
  	}
  	return $_Mout;
  }

  function _Fsavegroupstprior($_Mitems)
    {
  	$_Mout=1;
  	for($i=1 ; $i<(count($_Mitems)-1) ; $i++)
  	{
  		$_Mtpgid=explode("-",$_Mitems[$i]);
  		$_Mgroupid=$_Mtpgid[0];$_Mgprior=$_Mtpgid[1];//$_Mgroupid=$_Mtpgid[2];
	  	$_Msqlstr="update groupstatus set groupprior='$_Mgprior' where groupID=$_Mgroupid and termID={$GLOBALS['_Mactivetermid']}";
	  	//echo $_Msqlstr;
	  	if(!(mysql_query($_Msqlstr))) $_Mout=0;
  	}
  	return $_Mout;
  }
  
function _Fsavethistermsteachers($_Mitems)
{
	global $_Mfullfree;$_Mout=1;
	$_Minstr='';$_Manyinsert=0;$_Mdstr='';$_Manydelete=0;
	for($i=2; $i<count($_Mitems) ; $i+=2)
	{
		if($_Mitems[$i+1]=='I') {$_Minstr.="'$_Mitems[$i]',";$_Manyinsert=1;}
		else if($_Mitems[$i+1]=='D') {$_Mdstr.="'$_Mitems[$i]',";$_Manydelete=1;}
	}
	if($_Manyinsert)
	{
		$_Minstr=substr($_Minstr,0,strlen($_Minstr)-1);
		$_Msqlstr="select teacherID,teachername,teacherfamily,cooptype,groupID from tchrs where teacherID in ($_Minstr)";
		$_Mresult=mysql_query($_Msqlstr);
		
		$_Msqlstr="insert into tchrtimes(teacherID,teachername,teacherfamily,cooptype,times,initialtimes,teachergroupid,groupcaption,termID) values ";
		while($_Mrow=mysql_fetch_array($_Mresult))
		{	$_Msqlstr.="('{$_Mrow['teacherID']}','{$_Mrow['teachername']}','{$_Mrow['teacherfamily']}','{$_Mrow['cooptype']}','$_Mfullfree','$_Mfullfree','{$_Mrow['groupID']}','gcaption','{$GLOBALS['_Mactivetermid']}'),";}
		$_Msqlstr=substr($_Msqlstr,0,strlen($_Msqlstr)-1);		
		if(!(mysql_query($_Msqlstr))) $_Mout=0;
	}	
	if($_Manydelete) 
	{
		$_Mdstr=substr($_Mdstr,0,strlen($_Mdstr)-1);
		$_Msqlstr="delete from tchrtimes where teacherID in($_Mdstr) and termID={$GLOBALS['_Mactivetermid']}";
		if(!(mysql_query($_Msqlstr))) $_Mout=0;
	}
	return $_Mout;
}

function _Fsavethistermscourses($_Mitems)  
{//var_dump($_Mitems);
	global $_Mfullfree;$_Mout=1;
	$_Minstr='';$_Manyinsert=0;$_Manydelete=0;$_Mdstr='';$_Minsposes=array();$delposes=array();
	for($i=2; $i<count($_Mitems) ; $i+=1)
	{
		//$cidcpt=explode("!",$_Mitems[$i]);
		$_Mcids=explode("@@",$_Mitems[$i]);
		$_Maction=explode("~",$_Mcids[1]);
		//if($_Mitems[$i+1]=='I')
		$_Minserted=0;$_Mdeleted=0;
		for($k=0 ; $k<count($_Maction) ; $k+=2)
		{
			if($_Maction[$k+1]=='I')
			{
				if($_Minserted==0){$_Minstr.="{$_Mcids[0]},";$_Minserted=1;}
				array_push($_Minsposes,$_Mcids[0],$_Maction[$k]);$_Manyinsert=1;
				_Fsetcoursespecs($_Mcids[0],$_Maction[$k]);
			}
			else if($_Maction[$k+1]=='D')
			{
				$_Mdstr.="$_Maction[$k],";
				array_push($delposes,$_Mcids[0],$_Maction[$k]);$_Manydelete=1;
				_Fdelcoursespecs($_Mcids[0],$_Maction[$k]);
			}
		}
	}
	if($_Manyinsert)
	{
		$_Minstr=substr($_Minstr,0,strlen($_Minstr)-1);
		$_Msqlstr="select courseID,groupID,coursecaption,coursecode,coursetype,courseAunits,courseTunits,neededroomtypeID,neededroomtypeAID,courseatimes,coursettimes from courses where courseID in ($_Minstr)";
		$_Mresult=mysql_query($_Msqlstr);
		$_Msqlstr="insert into termcoursestatus(courseID,coursepart,coursecaption,coursecode,coursetype,coursehaspref,coursepreferedtimes,courseAunits,courseTunits,coursepartunits,groupID,groupidconf1,termID,neededroomtypeID,courserealAunits,courserealTunits) values ";
		while($_Mrow=mysql_fetch_assoc($_Mresult))
		{
			/*
			for($i=2;$i<count($_Mitems) ; $i+=2)
			   if($_Mitems[$i]==$_Mrow['courseID'])
			   {$termcourseid=$_Mitems[$i];break;}//assign course code user entered to terms courseid in termcoursestatus table
			*/
			$_Mpos=array_search($_Mrow['courseID'],$_Minsposes);
			while(($_Mpos!==false)&&($_Mpos%2==0))//($_Mpos%2==0):to be sure that found position is for coursecode,not courseid that is equal by source code
			{
				$_Mcoursespecs=$_Minsposes[$_Mpos+1];
				for($k=0 ; $k<count($_Mcoursespecs) ; $k++)
				{
					if($_Mcoursespecs==0) continue;
					if($_Mrow['coursettimes']==1)
					{	$_Msqlstr.="('$_Mcoursespecs','w1','{$_Mrow['coursecaption']}','{$_Mrow['courseID']}','{$_Mrow['coursetype']}','0','$_Mfullfree','{$_Mrow['courseatimes']}','{$_Mrow['coursettimes']}','1','{$_Mrow['groupID']}','{$_Mrow['groupID']}','{$GLOBALS['_Mactivetermid']}','{$_Mrow['neededroomtypeID']}','{$_Mrow['courseAunits']}','{$_Mrow['courseTunits']}'),";}
					else if($_Mrow['coursettimes']==2)
					{	$_Msqlstr.="('$_Mcoursespecs','c1','{$_Mrow['coursecaption']}','{$_Mrow['courseID']}','{$_Mrow['coursetype']}','0','$_Mfullfree','{$_Mrow['courseatimes']}','{$_Mrow['coursettimes']}','2','{$_Mrow['groupID']}','{$_Mrow['groupID']}','{$GLOBALS['_Mactivetermid']}','{$_Mrow['neededroomtypeID']}','{$_Mrow['courseAunits']}','{$_Mrow['courseTunits']}'),";}
					else if($_Mrow['coursettimes']==3)
					{
						$_Msqlstr.="('$_Mcoursespecs','c1','{$_Mrow['coursecaption']}','{$_Mrow['courseID']}','{$_Mrow['coursetype']}','0','$_Mfullfree','{$_Mrow['courseatimes']}','{$_Mrow['coursettimes']}','2','{$_Mrow['groupID']}','{$_Mrow['groupID']}','{$GLOBALS['_Mactivetermid']}','{$_Mrow['neededroomtypeID']}','{$_Mrow['courseAunits']}','{$_Mrow['courseTunits']}'),";
						$_Msqlstr.="('$_Mcoursespecs','w1','{$_Mrow['coursecaption']}','{$_Mrow['courseID']}','{$_Mrow['coursetype']}','0','$_Mfullfree','{$_Mrow['courseatimes']}','{$_Mrow['coursettimes']}','1','{$_Mrow['groupID']}','{$_Mrow['groupID']}','{$GLOBALS['_Mactivetermid']}','{$_Mrow['neededroomtypeID']}','{$_Mrow['courseAunits']}','{$_Mrow['courseTunits']}'),";
					}
					else if($_Mrow['coursettimes']==4)
					{
						$_Msqlstr.="('$_Mcoursespecs','c1','{$_Mrow['coursecaption']}','{$_Mrow['courseID']}','{$_Mrow['coursetype']}','0','$_Mfullfree','{$_Mrow['courseatimes']}','{$_Mrow['coursettimes']}','2','{$_Mrow['groupID']}','{$_Mrow['groupID']}','{$GLOBALS['_Mactivetermid']}','{$_Mrow['neededroomtypeID']}','{$_Mrow['courseAunits']}','{$_Mrow['courseTunits']}'),";
						$_Msqlstr.="('$_Mcoursespecs','c2','{$_Mrow['coursecaption']}','{$_Mrow['courseID']}','{$_Mrow['coursetype']}','0','$_Mfullfree','{$_Mrow['courseatimes']}','{$_Mrow['coursettimes']}','2','{$_Mrow['groupID']}','{$_Mrow['groupID']}','{$GLOBALS['_Mactivetermid']}','{$_Mrow['neededroomtypeID']}','{$_Mrow['courseAunits']}','{$_Mrow['courseTunits']}'),";
					}	
					if($_Mrow['courseatimes']==1)//should be changed to  "aw1" slot type.
					{
						$_Msqlstr.="('$_Mcoursespecs','w1','{$_Mrow['coursecaption']}','{$_Mrow['courseID']}','{$_Mrow['coursetype']}','0','$_Mfullfree','{$_Mrow['courseatimes']}','{$_Mrow['coursettimes']}','1','{$_Mrow['groupID']}','{$_Mrow['groupID']}','{$GLOBALS['_Mactivetermid']}','{$_Mrow['neededroomtypeAID']}','{$_Mrow['courseAunits']}','{$_Mrow['courseTunits']}'),";
					}
					if($_Mrow['courseatimes']==2)
					{
						$_Msqlstr.="('$_Mcoursespecs','a1','{$_Mrow['coursecaption']}','{$_Mrow['courseID']}','{$_Mrow['coursetype']}','0','$_Mfullfree','{$_Mrow['courseatimes']}','{$_Mrow['coursettimes']}','2','{$_Mrow['groupID']}','{$_Mrow['groupID']}','{$GLOBALS['_Mactivetermid']}','{$_Mrow['neededroomtypeAID']}','{$_Mrow['courseAunits']}','{$_Mrow['courseTunits']}'),";
					}
					if($_Mrow['courseatimes']==3)//should be separated for each for example aw1.
					{
						$_Msqlstr.="('$_Mcoursespecs','w1','{$_Mrow['coursecaption']}','{$_Mrow['courseID']}','{$_Mrow['coursetype']}','0','$_Mfullfree','{$_Mrow['courseatimes']}','{$_Mrow['coursettimes']}','1','{$_Mrow['groupID']}','{$_Mrow['groupID']}','{$GLOBALS['_Mactivetermid']}','{$_Mrow['neededroomtypeAID']}','{$_Mrow['courseAunits']}','{$_Mrow['courseTunits']}'),";
						$_Msqlstr.="('$_Mcoursespecs','a1','{$_Mrow['coursecaption']}','{$_Mrow['courseID']}','{$_Mrow['coursetype']}','0','$_Mfullfree','{$_Mrow['courseatimes']}','{$_Mrow['coursettimes']}','2','{$_Mrow['groupID']}','{$_Mrow['groupID']}','{$GLOBALS['_Mactivetermid']}','{$_Mrow['neededroomtypeAID']}','{$_Mrow['courseAunits']}','{$_Mrow['courseTunits']}'),";
					}
					
					else if($_Mrow['courseatimes']==4)
					{
						$_Msqlstr.="('$_Mcoursespecs','a1','{$_Mrow['coursecaption']}','{$_Mrow['courseID']}','{$_Mrow['coursetype']}','0','$_Mfullfree','{$_Mrow['courseatimes']}','{$_Mrow['coursettimes']}','2','{$_Mrow['groupID']}','{$_Mrow['groupID']}','{$GLOBALS['_Mactivetermid']}','{$_Mrow['neededroomtypeAID']}','{$_Mrow['courseAunits']}','{$_Mrow['courseTunits']}'),";
						$_Msqlstr.="('$_Mcoursespecs','a2','{$_Mrow['coursecaption']}','{$_Mrow['courseID']}','{$_Mrow['coursetype']}','0','$_Mfullfree','{$_Mrow['courseatimes']}','{$_Mrow['coursettimes']}','2','{$_Mrow['groupID']}','{$_Mrow['groupID']}','{$GLOBALS['_Mactivetermid']}','{$_Mrow['neededroomtypeAID']}','{$_Mrow['courseAunits']}','{$_Mrow['courseTunits']}'),";
					}
					else if($_Mrow['courseatimes']==5)//should be separated for each for example aw1.
					{
						$_Msqlstr.="('$_Mcoursespecs','w1','{$_Mrow['coursecaption']}','{$_Mrow['courseID']}','{$_Mrow['coursetype']}','0','$_Mfullfree','{$_Mrow['courseatimes']}','{$_Mrow['coursettimes']}','1','{$_Mrow['groupID']}','{$_Mrow['groupID']}','{$GLOBALS['_Mactivetermid']}','{$_Mrow['neededroomtypeAID']}','{$_Mrow['courseAunits']}','{$_Mrow['courseTunits']}'),";
						$_Msqlstr.="('$_Mcoursespecs','a1','{$_Mrow['coursecaption']}','{$_Mrow['courseID']}','{$_Mrow['coursetype']}','0','$_Mfullfree','{$_Mrow['courseatimes']}','{$_Mrow['coursettimes']}','2','{$_Mrow['groupID']}','{$_Mrow['groupID']}','{$GLOBALS['_Mactivetermid']}','{$_Mrow['neededroomtypeAID']}','{$_Mrow['courseAunits']}','{$_Mrow['courseTunits']}'),";
						$_Msqlstr.="('$_Mcoursespecs','a2','{$_Mrow['coursecaption']}','{$_Mrow['courseID']}','{$_Mrow['coursetype']}','0','$_Mfullfree','{$_Mrow['courseatimes']}','{$_Mrow['coursettimes']}','2','{$_Mrow['groupID']}','{$_Mrow['groupID']}','{$GLOBALS['_Mactivetermid']}','{$_Mrow['neededroomtypeAID']}','{$_Mrow['courseAunits']}','{$_Mrow['courseTunits']}'),";
					}
					
					else if($_Mrow['courseatimes']==6)
					{
						$_Msqlstr.="('$_Mcoursespecs','a1','{$_Mrow['coursecaption']}','{$_Mrow['courseID']}','{$_Mrow['coursetype']}','0','$_Mfullfree','{$_Mrow['courseatimes']}','{$_Mrow['coursettimes']}','2','{$_Mrow['groupID']}','{$_Mrow['groupID']}','{$GLOBALS['_Mactivetermid']}','{$_Mrow['neededroomtypeAID']}','{$_Mrow['courseAunits']}','{$_Mrow['courseTunits']}'),";
						$_Msqlstr.="('$_Mcoursespecs','a2','{$_Mrow['coursecaption']}','{$_Mrow['courseID']}','{$_Mrow['coursetype']}','0','$_Mfullfree','{$_Mrow['courseatimes']}','{$_Mrow['coursettimes']}','2','{$_Mrow['groupID']}','{$_Mrow['groupID']}','{$GLOBALS['_Mactivetermid']}','{$_Mrow['neededroomtypeAID']}','{$_Mrow['courseAunits']}','{$_Mrow['courseTunits']}'),";
						$_Msqlstr.="('$_Mcoursespecs','a3','{$_Mrow['coursecaption']}','{$_Mrow['courseID']}','{$_Mrow['coursetype']}','0','$_Mfullfree','{$_Mrow['courseatimes']}','{$_Mrow['coursettimes']}','2','{$_Mrow['groupID']}','{$_Mrow['groupID']}','{$GLOBALS['_Mactivetermid']}','{$_Mrow['neededroomtypeAID']}','{$_Mrow['courseAunits']}','{$_Mrow['courseTunits']}'),";
					}
				}
				$_Minsposes[$_Mpos]=-1;
				$_Mpos=array_search($_Mrow['courseID'],$_Minsposes);
			}
		}
		$_Msqlstr=substr($_Msqlstr,0,strlen($_Msqlstr)-1);//echo $_Msqlstr;
		if(!(mysql_query($_Msqlstr))) $_Mout=0;//echo "out:".$queryexe;
	}
	if($_Manydelete)
	{
		$_Mdstr=substr($_Mdstr,0,strlen($_Mdstr)-1);
		$_Msqlstr="delete from termcoursestatus where courseID in ($_Mdstr) and termID={$GLOBALS['_Mactivetermid']}";// LIMIT 1";
		if(!(mysql_query($_Msqlstr))) $_Mout=0;
	}//echo $_Msqlstr;
	return $_Mout;
}
function _Fsetcoursespecs($_Mcoursecode,$_Mcoursespec)
{
	$_Msqlstr="select * from courses where courseID=$_Mcoursecode and termID={$GLOBALS['_Mactivetermid']}";
	if(!($_Mresult=mysql_query($_Msqlstr))) return -1;
	if(mysql_num_rows($_Mresult)<=0) return 0;
	$_Mrow=mysql_fetch_assoc($_Mresult);
	$_Mcurcoursespecs=explode(",",$_Mrow['coursespecs']);
	$_Mpos=array_search($_Mcoursespec,$_Mcurcoursespecs);
	if($_Mpos===false)
	{
		if($_Mrow['coursespecs']=='')
			$_Mnewcoursespec=$_Mcoursespec;
		else $_Mnewcoursespec=$_Mrow['coursespecs'].','.$_Mcoursespec;
		$_Msqlstr="update courses set coursespecs='$_Mnewcoursespec' where courseID=$_Mcoursecode and termID={$GLOBALS['_Mactivetermid']}";
		if(mysql_query($_Msqlstr)) return 1;
		return 0;
	}
}
function _Fdelcoursespecs($_Mcoursecode,$_Mcoursespec)
{
	$_Msqlstr="select * from courses where courseID=$_Mcoursecode and termID={$GLOBALS['_Mactivetermid']}";
	if(!($_Mresult=mysql_query($_Msqlstr))) return -1;
	if(mysql_num_rows($_Mresult)<=0) return 0;
	$_Mrow=mysql_fetch_assoc($_Mresult);
	$_Mcurcoursespecs=explode(",",$_Mrow['coursespecs']);$_Mnewcoursespec='';
	$_Mpos=array_search($_Mcoursespec,$_Mcurcoursespecs);
	if($_Mpos!==false)
	{
		for($j=0 ; $j<count($_Mcurcoursespecs) ; $j++)//$_Mrow['coursespecs']=='')
		{
			if($j!=$_Mpos)	
				$_Mnewcoursespec.=$_Mcurcoursespecs[$j].',';
		}
		$_Mnewcoursespec=substr($_Mnewcoursespec,0,strlen($_Mnewcoursespec)-1);		
		$_Msqlstr="update courses set coursespecs='$_Mnewcoursespec' where courseID=$_Mcoursecode and termID={$GLOBALS['_Mactivetermid']}";
		if(mysql_query($_Msqlstr)) return 1;
		return 0;
	}
}
function _Fsavethistermsrooms($_Mitems)//willlllllll check change
{
	$_Minstr='';$_Manyinsert=0;$_Mout=1;
	for($i=2; $i<count($_Mitems) ; $i+=2)
		if($_Mitems[$i+1]=='I') {$_Minstr.="'$_Mitems[$i]',";$_Manyinsert=1;}
	if($_Manyinsert)
	{
		$_Minstr=substr($_Minstr,0,strlen($_Minstr)-1);
		$_Msqlstr="select roomID,roomcaption,capacity,roomtype,equipments from rooms where roomID in ($_Minstr)";
		$_Mresult=mysql_query($_Msqlstr);
		
		$_Msqlstr="insert into roomstatus(roomID,roomcaption,capacity,roomtype,equipments,termID) values ";
		while($_Mrow=mysql_fetch_array($_Mresult))
		{$_Msqlstr.="('{$_Mrow['roomID']}','{$_Mrow['roomcaption']}','{$_Mrow['capacity']}','{$_Mrow['roomtype']}','{$_Mrow['equipments']}','{$GLOBALS['_Mactivetermid']}'),";}
		$_Msqlstr=substr($_Msqlstr,0,strlen($_Msqlstr)-1);
	
		if(!(mysql_query($_Msqlstr))) $_Mout=0;
	}
	
	for($i=2; $i<count($_Mitems) ; $i+=2)
		if($_Mitems[$i+1]=='D') 
		{
			$_Msqlstr="delete from roomstatus where roomID=$_Mitems[$i] and termID={$GLOBALS['_Mactivetermid']} LIMIT 1";
			if(!(mysql_query($_Msqlstr))) $_Mout=0;
		}	
		return $_Mout;
}
//**********************************  will delete future
  function _Fcourseslotchange($_Mteacherid,$_Mcourseid,$_Mgroupid,$_Mslotid,$_Mnewslotid)
	{
	   _Fchangecourseassignment($_Mcourseid,$_Mgroupid,$_Mslotid,$_Mnewslotid,$_Mprevcourseslotsar,$_Mnewcourseslotar);//update assignment table
	   
	   _Fchangeteachertimestatus($_Mteacherid,$_Mgroupid,$_Mprevcourseslotsar,$_Mnewcourseslotar);//updating teacher times.

	   _Fchangegrouptimes($_Mgroupid,$_Mprevcourseslotsar,$_Mnewcourseslotar);//updating group times.

	}
	
function _Fchangegrouptimes($_Mgroupid,$_Mprevcourseslotsar,$_Mnewcourseslotar)
{
	  	$_Msqlstr="select groupstatus,initialgroupstatus from groupstatus where groupID={$_Mgroupid} and termID={$GLOBALS['_Mactivetermid']}";
	  	$_Mresult=mysql_query($_Msqlstr);$_Mrow=mysql_fetch_assoc($_Mresult);
		$_Mtimes=$_Mrow['groupstatus'];$initialtimes=$_Mrow['initialgroupstatus'];
		mysql_free_result($_Mresult);
		//from change teachertimes
		_Fchangestatusslots($_Mtimes,$initialtimes,$_Mprevcourseslotsar,$_Mnewcourseslotar);	  	
		$_Msqlstr="update groupstatus set groupstatus='{$_Mtimes}' where groupID={$_Mgroupid} and termID={$GLOBALS['_Mactivetermid']}";
		mysql_query($_Msqlstr);
}	


// taqir konad,
//be jaye neveshtane code joda baraye eslahe eslathaye dars o gorooh o ostad o otaq,
//codi be soorat functional neveshte shavad ke 6timestatus ra gerefte va taqirat ra rooye an emal namayad.  
  function _Fchangeteachertimestatus($_Mteacherid,$_Mgroupid,$_Mprevcourseslotsar,$_Mnewcourseslotar)
{
	  	$_Msqlstr="select times,initialtimes from tchrtimes where teacherID={$_Mteacherid} and termID={$GLOBALS['_Mactivetermid']} and groupID={$_Mgroupid}";

	  	$_Mresult=mysql_query($_Msqlstr);$_Mrow=mysql_fetch_assoc($_Mresult);
		$_Mtimes=$_Mrow['times'];$initialtimes=$_Mrow['initialtimes'];
		mysql_free_result($_Mresult);
		//remove lasttime
		_Fchangestatusslots($_Mtimes,$initialtimes,$_Mprevcourseslotsar,$_Mnewcourseslotar);
	  	$_Msqlstr="update tchrtimes set times='{$_Mtimes}' where teacherID={$_Mteacherid} and termID={$GLOBALS['_Mactivetermid']} and groupID={$_Mgroupid}";
		mysql_query($_Msqlstr);
}
	
function _Fchangestatusslots(&$_Mtimes,$initialtimes,$_Mprevcourseslotsar,$_Mnewcourseslotar)
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
						$_Mtimes[$_Mprevcourseslotsar[$i]]=$initialtimes[$_Mprevcourseslotsar[$i]];
						$_Mtimes[$_Mprevcourseslotsar[$i]+1]=$initialtimes[$_Mprevcourseslotsar[$i]+1];
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
		$_Msqlstr="select courseunits,timeslots from assignments where courseID={$_Mcourseid} and termID={$GLOBALS['_Mactivetermid']} and groupID={$_Mgroupid}";
	  	$_Mresult=mysql_query($_Msqlstr);$_Mrow=mysql_fetch_assoc($_Mresult);$_Mcourseunits=$_Mrow['courseunits'];
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
				$_Mnewslotstr.="{$_Mprevcourseslotsar[$i]}:{$_Mprevcourseslotsar[$i+1]},{$_Mprevcourseslotsar[$i+2]}:{$_Mprevcourseslotsar[$i+3]},";
		}
		$_Mnewslotstr=substr($_Mnewslotstr,0,strlen($_Mnewslotstr)-1);
	  	$_Msqlstr="update assignments set timeslots='{$_Mnewslotstr}' where courseID={$_Mcourseid} and termID={$GLOBALS['_Mactivetermid']} and groupID={$_Mgroupid}";
	  	mysql_query($_Msqlstr);

}  
  
  function _Fcoursechangeteachers($_Mcourseid,$_Mprevteacherid,$_Mcurteacherid,$_Mgroupid,$_Mslotid,$_Mslottype)
  {
  	_Fteacherscourseremove($_Mprevteacherid,$_Mcourseid,$_Mgroupid,$_Mcourseunits,$_Mcoursetimeslots);
  	_Fteacherscourseadd($_Mcurteacherid,$_Mprevteacherid,$_Mcourseid,$_Mgroupid,$_Mcourseunits,$_Mcoursetimeslots);
  }
     
  function _Fteacherscourseremove($_Mteacherid,$_Mcourseid,$_Mgroupid,&$_Mcourseunits,&$_Mcoursetimeslots)
	{
		$_Msqlstr="select times from tchrtimes where teacherID={$_Mteacherid} and termID={$GLOBALS['_Mactivetermid']} and groupID={$_Mgroupid}";
	  	$_Mresult=mysql_query($_Msqlstr);
	  	$_Mrow=mysql_fetch_assoc($_Mresult);
	  	$_Mttimes=$_Mrow['times'];
	
	  	$_Msqlstr="select courseunits,timeslots from assignments where courseID={$_Mcourseid} and termID={$GLOBALS['_Mactivetermid']} and groupID={$_Mgroupid} and teacherID={$_Mteacherid}";
		$_Mresult=mysql_query($_Msqlstr);$_Mrow=mysql_fetch_assoc($_Mresult);$_Mcourseunits=$_Mrow['courseunits'];
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
	  	$_Msqlstr="update tchrtimes set times='{$_Mttimes}',teachercurslots=teachercurslots-{$_Mcourseunits} where teacherID={$_Mteacherid} and termID={$GLOBALS['_Mactivetermid']} and groupID={$_Mgroupid}";
	  	mysql_query($_Msqlstr);

	  	$_Msqlstr="update assignments set teacherID=0 where courseID={$_Mcourseid} and termID={$GLOBALS['_Mactivetermid']} and groupID={$_Mgroupid} and teacherID={$_Mteacherid}";
		mysql_query($_Msqlstr);
	}
  
function _Fteacherscourseadd($_Mteacherid,$_Mprevteacherid,$_Mcourseid,$_Mgroupid,$_Mcourseunits,$_Mcoursetimeslots)//write another function for insert course
{
  	$_Msqlstr="select times,teachername,teacherfamily from tchrtimes where teacherID={$_Mteacherid} and termID={$GLOBALS['_Mactivetermid']} and groupID={$_Mgroupid}";
  	$_Mresult=mysql_query($_Msqlstr);
  	$_Mrow=mysql_fetch_assoc($_Mresult);
  	$_Mttimes=$_Mrow['times'];$_Mteachername=$_Mrow['teachername'];$_Mteacherfamily=$_Mrow['teacherfamily'];
	
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
  	$_Msqlstr="update tchrtimes set times='{$_Mttimes}',teachercurslots=teachercurslots+{$_Mcourseunits} where teacherID={$_Mteacherid} and termID={$GLOBALS['_Mactivetermid']} and groupID={$_Mgroupid}";
  	mysql_query($_Msqlstr);

  	$_Msqlstr="update assignments set teacherID={$_Mteacherid},teachername='{$_Mteachername}',teacherfamily='{$_Mteacherfamily}' where courseID={$_Mcourseid} and 
  			 termID={$GLOBALS['_Mactivetermid']} and groupID={$_Mgroupid}";//prevteacher lahaz shavad
	//echo $_Msqlstr;
  	mysql_query($_Msqlstr);
}	

  
  function _Fcoursepossibleteachers($_Mcourseid,$_Mcurteacherid,$_Mgroupid)
  {
  	$_Moutstr='';
  	if($_Mresult=mysql_query("select groupstatus from groupstatus where groupID={$_Mgroupid} and  termID={$GLOBALS['_Mactivetermid']}"))
  	{
  		$_Mrow=mysql_fetch_array($_Mresult);
  		$_Mgroupstatus=$_Mrow['groupstatus'];
  		mysql_freeresult($_Mresult);
  	}
  	
  	$_Mnotincourseslots='*';$_Moutstr.='*';
  	$_Mpossibletimes=array_fill(0,98,'n');
  	$_Mteacher1=new _CLteacher();
  	$_Mgroup1=new _CLgroup();
  	$_Mgroup1->setgroupID($_Mgroupid);
  	$_Mgroup1->setgroupstatus($_Mgroupstatus);
  	$_Mcourse1=new _CLcourse();
  	$_Mcourse1->setcourseunits(0,$_Mcourseunits);//**will change to correct units
  	$_Msqlstr="select teacherID,teachername,teacherfamily,times from tchrtimes where teachercourseids like '%{$_Mcourseid}%' and curslotsfordays<=(maxslotperday-{$_Mcourseunits}) and termID={$GLOBALS['_Mactivetermid']} order by teacherfamily";
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
  	
  	if($_Mresult=mysql_query("select groupstatus from groupstatus where groupID={$_Mgroupid} and  termID={$GLOBALS['_Mactivetermid']}"))
  	{
  		$_Mrow=mysql_fetch_array($_Mresult);
  		$_Mgroupstatus=$_Mrow['groupstatus'];
  		mysql_freeresult($_Mresult);
  	}
  	
  	$_Mpossibletimes=array_fill(0,98,'n');
  	$_Mgroup1=new _CLgroup();
  	$_Mgroup1->setgroupID($_Mgroupid);
  	$_Mgroup1->setgroupstatus($_Mgroupstatus);

  	if($_Mresult=mysql_query("select times from tchrtimes where teacherID={$_Mteacherid} and  termID={$GLOBALS['_Mactivetermid']}"))
  	{
  		$_Mrow=mysql_fetch_array($_Mresult);
  		mysql_freeresult($_Mresult);
  	}
  	$_Mteacher1=new _CLteacher();
	$_Mteacher1->setteacherid($_Mteacherid);
	$_Mteacher1->setteachertimes($_Mrow['times']);

  	if($_Mresult=mysql_query("select coursehaspref,coursepreferedtimes from termcoursestatus where courseID={$_Mcourseid} and groupIDconf1={$_Mgroupid} and  termID={$GLOBALS['_Mactivetermid']}"))
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

 	if($_Mresult=mysql_query("select roomstatus1 from roomstatus where roomID={$roomid} and  termID={$GLOBALS['_Mactivetermid']}"))
  	{
  		$_Mrow=mysql_fetch_array($_Mresult);
  		$roomstatus=$_Mrow['roomstatus1'];
  		mysql_freeresult($_Mresult);
  	}
	
  	if($_Mfound==true)
  	{
  		for($i=0; $i<98 ; $i++)
  		{  			
  			if(($_Mpossibletimes[$i]=='f')||($_Mpossibletimes[$i]==$_Mslottype))
  			  $_Moutstr.='#'.$i.'~'.$_Mpossibletimes[$i].'~'.$roomstatus[$i];
  		}  		
  	}  	
  	//echo "{$_Mcourseid} - {$roomid} - {$_Mteacherid} - {$_Mgroupid}";
  	return $_Moutstr;  	
  }

  
   
?>