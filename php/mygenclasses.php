<?php

class _CLteacher
{
	public $_CMteacherid;public $teacherid;
	public $teachername;
	public $teacherfamily;
	public $teachermaxslots;
	private $teachertimes;
	private $initialtimes;
	private $teachergroupid;
	private $teacherroomcnst=0;
	private $teacerslotcnst='';
	
	
	function _CLteacher()
	{
		
	}
	public function setteacherid($teacherid)
	{$this->teacherid=$teacherid;}

	public function setteachertimes($teachertimes)
	{$this->teachertimes=$teachertimes;}
	
	public function setteachergroupid($teachergroupid)
	{$this->teachergroupid=$teachergroupid;}
	
	public function setteacherroomcnst($teacherroomcnst)
	{$this->teacherroomcnst=$teacherroomcnst;}

	public function setteacherslotcnst($teacherslotcnst)
	{$this->teacherslotcnst=$teacherslotcnst;}

	public function saveteachertimestodb($groupid,$timesstr,$courseunits,$activetermid)
	{
		$sqlstr="update tchrtimes set times='{$timesstr}',teachercurslots=teachercurslots+{$courseunits} where teacherID={$this->teacherid} and termID=$activetermid
										and teachergroupid=$groupid";
		$result=mysql_query($sqlstr);
		$this->teachertimes=$timesstr;
	}
	
	function checkteacherquota()
	{}
	public function setteacheridfull($teacherid,$activetermid)
	{
		$this->_CMteacherid=$teacherid;$this->teacherid=$teacherid;
		$sqlstr="select * from tchrtimes where ((tchrtimes.teacherID={$teacherid})and(tchrtimes.termID=$activetermid))";//will correct activeterm
		$result=mysql_query($sqlstr);
		$row=mysql_fetch_array($result);
		$this->teachername=$row["teachername"];
		$this->teacherfamily=$row["teacherfamily"];
		$this->teachertimes=$row["times"];
		$this->initialtimes=$row["initialtimes"];
	}
	public function reset()
	{
		$this->_CMteacherid='';$this->teacherid='';
		$this->teachername='';
		$this->teacherfamily='';
		$this->teachertimes='';
		$this->initialtimes='';
	}

	public function getteachergroupid()
	{return $this->teachergroupid;}

	public function getteachertimes()
	{return $this->teachertimes;}

	public function getteacherroomcnst()
	{
		if($this->teacherroomcnst!=0) 
			return $this->teacherroomcnst;
		else return false;
	}
	public function getteacherslotcnst()
	{
		if($this->teacherslotcnst!='') 
			return $this->teacherslotcnst;
		else return false;
	}
	public function getteacherid()
	{
		return $this->teacherid;//return $this->_CMteacherid;
	}
	public function getteachernamefam()
	{
		return $this->teachername.' '.$this->teacherfamily;
	}
	
	
}

class _CLcourse
{
  private $courseid;
  private $coursecaption;
  private $coursecode;
  private $coursegroupcode;
  private $courseAunits;
  private $courseTunits;
  private $courserealAunits;
  private $courserealTunits;	
  private $coursehardness;
  private $coursehaspref=false;
  private $coursepreferdtimes;
  private $coursestatus;
  private $courseslotcnst="";
  private $courseteachercnst=0;
  private $courseroomcnst=0;
  private $courseroomid=0;
  private $cnsttype="";
  private $neededroomtypeid=0;
  private $neededroomtypeaid=0;
  private $coursepart="";
  private $coursepartunits=0;
  private $otherpart1slots='';
  private $otherpart2slots='';
  private $otherparttid=0;
  private $coursemaingroupid;
  private $coursegroupidconf;
  private $schwithgroupid=0;
  private $groupidconf2=0;
  private $groupidconf3=0;
  private $cncslots=0;
  //**********
  private $assignedroomid=0;
  private $assignedteacherid=0;
  private $assignedslotid=0;//first slot id only
  private $assignedslottypes='';//first slot type only
  private $courseparttchrs=0;
  
  public function setpartstchrs($cparttchrs) {$this->courseparttchrs=$cparttchrs;}
  public function getpartstchrs() {return $this->courseparttchrs;}
  
  public function setcoursemaingroupid($cmgroupid) {$this->coursemaingroupid=$cmgroupid;}

  public function setcoursegroupidconf($groupidconf)  {$this->coursegroupidconf=$groupidconf;}

  public function setschwithgroupid($schwithgroupid)  {$this->schwithgroupid=$schwithgroupid;}
  
  public function setgroupidconf2($groupidconf2)  {$this->groupidconf2=$groupidconf2;}

  public function setgroupidconf3($groupidconf3)  {$this->groupidconf3=$groupidconf3;}
  
  public function getschwithgroupid()  {return $this->schwithgroupid;}

  public function getcoursemaingroupid()  {return $this->coursemaingroupid;}

  public function getcoursegroupidconf()  {return $this->coursegroupidconf;}

  public function getgroupidconf2()  {return $this->groupidconf2;}
  
  public function getgroupidconf3()  {return $this->groupidconf3;}
  
  public function setassignedroomid($assignedroomid)  {$this->assignedroomid=$assignedroomid;}
  
  public function setassignedteacherid($assignedteacherid)  {$this->assignedteacherid=$assignedteacherid;}
  
  public function setcoursepart($coursepart)  {$this->coursepart=$coursepart;}
  
  public function setcoursepartunits($coursepartunits)  {$this->coursepartunits=$coursepartunits;}
  
  public function setotherpart1slots($otherpart1slots)  {$this->otherpart1slots=$otherpart1slots;}

  public function setotherpart2slots($otherpart2slots)  {$this->otherpart2slots=$otherpart2slots;}
  
  public function setassignedslotid($assignedslotid)  {$this->assignedslotid=$assignedslotid;}
  
  public function setassignedslottype($assignedslottype)  {$this->assignedslottype=$assignedslottype;}
  
  public function setcoursecode($coursecode)  {$this->coursecode=$coursecode;}
  
  public function getassignedroomid()  {return $this->assignedroomid;}
  
  public function getassignedteacherid()  {return $this->assignedteacherid;}

  public function getassignedslotid()  {return $this->assignedslotid;}
  
  public function getassignedslottype()  {return $this->assignedslottype;}
  
  public function getcourseremainedunits() 
  {
  	// this should be changed for matchig with course needed actual time, not units montazeri
  	//$_Mremainedtimes=$this->courseAunits*2+$this->courseTunits;
  	$_Mremainedtimes=$this->courseAunits+$this->courseTunits;
  	if($this->otherpart1slots!='')
  	{
  		if(strpos($this->otherpart1slots,'s')>=0)
  		{$_Mremainedtimes=$_Mremainedtimes-2;}
  		else if((strpos($this->otherpart1slots,'e'))||(strpos($this->otherpart1slots,'0')))
  		{$_Mremainedtimes=$_Mremainedtimes-1;}
  	}
  	if($this->otherpart2slots!='')
  	{
  		if(strpos($this->otherpart2slots,'s')>=0)
  		{$_Mremainedtimes=$_Mremainedtimes-2;}
  		else if((strpos($this->otherpart2slots,'e'))||(strpos($this->otherpart2slots,'0')))
  		{$_Mremainedtimes=$_Mremainedtimes-1;}
  	}
	return $_Mremainedtimes;
  	
  }

  //**************
  public function setcourseid($courseid)  {$this->courseid=$courseid;$this->emptyothers();}
  
  public function setcoursecnc($cnc){$this->cncslots=$cnc;}
  public function getcoursecnc(){return $this->cncslots;}
  private function emptyothers()
  {
    $coursecaption='';$coursegroupcode=0;$courseAunits=0;
    $courseTunits=0;$coursehardness=0;$coursehaspref=false;
    $coursepreferdtimes=false;$coursestatus='';$courseslotcnst="";
    $courseteachercnst=0;$courseroomcnst=0;$cnsttype="";
  }
  
  public function setcoursecaption($coursecaption)  {$this->coursecaption=$coursecaption;}

  public function setneededroomtypeid($neededroomtypeid)  {$this->neededroomtype=$neededroomtypeid;}

  public function setneededroomtypeaid($neededroomtypeaid)  {$this->neededroomtypeaid=$neededroomtypeaid;}
  
  public function setcoursegroupcode($coursegroupcode)  {$this->coursegroupcode=$coursegroupcode;}
    
  public function setcourseunits($courseAunits,$courseTunits)  {$this->courseAunits=$courseAunits;$this->courseTunits=$courseTunits;}
  public function setcourserealunits($courserealAunits,$courserealTunits)  {$this->courserealAunits=$courserealAunits;$this->courserealTunits=$courserealTunits;}
  
  
  public function setcoursepreftimes($coursepreferedtimes)
  {$this->coursepreferdtimes=$coursepreferedtimes;
   if($coursepreferedtimes!='') $this->coursehaspref=true;
   else $this->coursehaspref=false;}

  public function setcourseslotcnst($courseslotcnst)  {$this->courseslotcnst=$courseslotcnst;}

  public function setcourseteachercnst($coursesteachercnst)  {$this->courseteachercnst=$coursesteachercnst;}

  public function setcourseroomcnst($courseroomcnst)  {$this->courseroomcnst=$courseroomcnst;}
  
  public function setcnsttype($cnsttype)  {$this->cnsttype=$cnsttype;}
  
  public function setcoursehaspref($chp)  {if($chp==1)$this->coursehaspref= true;else $this->coursehaspref= false;}
  
  
  public function getcourseid()  {return $this->courseid;}
  
  public function getcoursecode()  {return $this->coursecode;}

  public function getcoursegroupcode()  {return $this->coursegroupcode;}
  
  public function getneededroomtypeid()  {return $this->neededroomtypeid;}

  public function getneededroomtypeaid()  {return $this->neededroomtypeaid;}
  
  public function getcourseunits()  {return $this->courseAunits+$this->courseTunits;}
  
  public function getcoursepreftimes()  {return $this->coursepreferdtimes;}
  
  public function getcoursecaption()  {return $this->coursecaption;}
  
  public function getcoursehaspref()  {return $this->coursehaspref;}
    
  public function getcourseroomcnst()  {return $this->courseroomcnst;}
  
  public function getcourseslotcnst()  {return $this->courseslotcnst;}
  
  public function getcourseteachercnst()  {return $this->courseteachercnst;}
 
  public function getcnsttype()  {return $this->cnsttype;}

  public function getcoursepart()  {return $this->coursepart;}
  
  public function getcoursepartunits()  {return $this->coursepartunits;}
  
  public function getotherpart1slots()  {return $this->otherpart1slots;}

  public function getotherpart2slots()  {return $this->otherpart2slots;}
  
  public function getotherparttid()  {return $this->otherparttid;}  
  public function setcourseidfull($courseid)//will change
  {
  	$this->courseid=$courseid;
  	$sqlstr="select * from coursestatus where courseid={$this->$courseid} and termid={$_SESSION['activetermid']}";
  	$result=mysql_query($sqlstr);
  	$row=mysql_fetch_array($result);
  	$this->coursecaption=$row["coursecaption"];
  	$this->coursegroup=$row["groupidconf1"];
  	$this->coursecaption=$row["coursecaption"];
  }
  public function reset()
  {
  	$this->courseid='';
  	$this->coursecaption='';
  	$this->coursegroup=0;
  	$this->coursecaption='';
  	$this->coursepart='';
  }
}

class _CLroom
{
	private $roomid;
	private $roomcaption;
	private $buildingid;
	private $buildingname;
	private $roomcapacity;
	private $roomtype;
	private $capacity;
	private $roomgroupid;
	private $roomstatus;
	private $schstate;//usable or not
	public function setroombyvalues($roomid,$roomcaption,$buildingid,$buildingname,$roomcapacity,
  									 $roomtype,$capacity,$roomstatus,$schstate,$rgid1,$rgid2,$rgid3,$rgid4)
	{
		$this->roomid=$roomid;
		$this->roomtype=$roomtype;
		$this->roomcaption=$roomcaption;
		$this->roomstatus=$roomstatus;
		$this->roomtype=$roomtype;
		$this->buildingid=$buildingid;
		$this->buildingname=$buildingname;
		$this->capacity=$capacity;
		$this->roomgroupid=$rgid;
	}
	public function setroom($roomid,$groupid,$activetermid)
	{
		$sqlstr="select * from roomstatus where termID=$activetermid and roomgroupid=$groupid and roomID=$roomid";//echo $sqlstr;
		if(!($result=mysql_query($sqlstr))) return -1;
		if(mysql_num_rows($result)<=0) return 0;
		$row=mysql_fetch_array($result);
		$this->roomid=$roomid;
		$this->roomtype=$row["roomtype"];
		$this->roomcaption=$row["roomcaption"];
		$this->roomstatus=$row["roomstatus"];
		$this->initialroomstatus=$row["initialroomstatus"];
		//$this->buildingid=$row["buildingID"];
		$this->capacity=$row["capacity"];
		$this->roomgroupid=$row["roomgroupid"];
		//get other fields;
		if($result) mysql_free_result($result);		
	}
	public function reset()
	{
		$this->roomid='';
		$this->roomtype=0;
		$this->roomcaption='';
		$this->roomstatus='';
		$this->roomtype='';
		$this->capacity=0;
		$this->roomgroupid=0;
		
	}
	public function roomisfree($slotid,$slottype,$activetermid)
	{
		if(_Fispossibleins($this->roomstatus,$slotid,$slottype))
		{ if(_Ftotalroomschisfree($this->roomid,$slotid,$slottype,$activetermid))	return true;}
		else return false;	
	}
	public function savestatus($status1,$activetermid)
	{
		$sqlstr="update roomstatus set roomstatus='$status1' where roomID={$this->roomid} and termID=$activetermid and roomgroupid={$this->roomgroupid}";
		if(mysql_query($sqlstr)) return true;
		return false;
	}
	public function setroomid($roomid) {$this->roomid=$roomid;}
	public function getroomid()	{return $this->roomid;}
	public function setroomcaption($roomcaption) {$this->roomcaption=$roomcaption;}
	public function getroomcaption() {return $this->roomcaption;}	
	public function setroomstatus($roomstatus)	{$this->roomstatus=$roomstatus;}
	public function getroomstatus()	{return $this->roomstatus;}
	public function setinitialroomstatus($roomstatus)	{$this->roomstatus=$roomstatus;}
	public function getinitialroomstatus()	{return $this->initialroomstatus;}
	
}

class _CLgroup
{
	private $groupid;
	public $groupcaption;
	private $groupstatus;
	private $initialgroupstatus;
	private $grouptype;//f,m1,m2,s :final,master1,master2,super
	private $mastergroup1id=0;
	private $mastergroup2id=0;
	private $supergroupid=0;
	private $groupsmaxslotsperday=-1;
	
	public function setgroupid($groupid)
	{$this->groupid=$groupid;}

	public function setgroupcaption($groupcaption)
	{$this->groupcaption=$groupcaption;}
	
	public function setgroupstatus($groupstatus)
	{$this->groupstatus=$groupstatus;}
	
	public function setinitialgroupstatus($initialgroupstatus)
	{$this->initialgroupstatus=$initialgroupstatus;}

	function setuppergroups($m1,$m2,$s)
	{
		if($m1!=-1) $this->mastergroup1id=$m1;
		if($m2!=-1) $this->mastergroup2id=$m2;
		if($s!=-1) $this->supergroupid=$s;
	}
	public function setgroupsmaxslotsperday($groupsmaxslotsperday)
	{$this->groupsmaxslotsperday=$groupsmaxslotsperday;}
	
	function getgroupid()
	{return $this->groupid;}

	public function getgroupstatus()
	{return $this->groupstatus;}
	public function updatestatus($timeslots,$timeslotstate)
	{
		for($i=0; $i<count($timeslots) ; $i++)
		{
		    if($this->groupstatus[$timeslots[$i]]=='f')
				$this->groupstatus[$timeslots[$i]]=_Fcomplement($timeslotstate[$i]);
			else if(($this->groupstatus[$timeslots[$i]]=='e')||($this->groupstatus[$timeslots[$i]]=='o'))
				$this->groupstatus[$timeslots[$i]]='s';
			else if($this->groupstatus[$timeslots[$i]]=='s');
				//will check later
			else $this->groupstatus[$timeslots[$i]]='!';
		}
	}

	public function getinitialgroupstatus()
	{return $this->initialgroupstatus;}

	public function getgroupcaption()
	{return $this->groupcaption;}
	
	public function getmastergroup1id()
	{return $this->mastergroup1id;}
	
	public function getmastergroup2id()
	{return $this->mastergroup2id;}

	public function getsupergroup1id()
	{return $this->supergroupid;}
	
	public function getgroupsmaxslotsperday()
	{return $this->groupsmaxslotsperday;}
	
	// \\--// check set fanction call for sending parameter activetermid
	public function savegrouptimescurunitstodb($timesstr,$units,$activetermid)
	{
		$sqlstr="update groupstatus set groupstatus='{$timesstr}',groupscheduledslots=groupscheduledslots+$units where groupID={$this->groupid} and termID=$activetermid";
		$result=mysql_query($sqlstr);
		$this->groupstatus=$timesstr;
	}

	
}
class _CLmytimedate
{ 
	public $gretime;
	public $gredate;
	public $jtime;
	public $jdate;
	
	public $gyear;
	public $gmonth;
	public $gday;
	
	public $jyear;
	public $jmonth;
	public $jday;
	
	public function setjdate()
	{
		list($this->gyear, $this->gmonth, $this->gday ) = @preg_split ('/-/', @date("Y-m-d"));
		list( $this->jyear, $this->jmonth, $this->jday ) = @gregorian_to_jalali($this->gyear, $this->gmonth, $this->gday);
		$this->jdate = $this->jyear."/".$this->jmonth."/".$this->jday;
	}
}

class _CLatuser
{
	private $username;
	private $usertype;
	private $privileges;
	public function setusername($username)
	{$this->username=$username;}
	public function getusername()
	{return $this->username;}
}

class _CLactiveterm
{
	private $termid;
	private $termcaptipn;
	private $termstart;
	private $termend;
	private $termtype;
	
	function _CLactiveterm()
	{
		$result=mysql_query("select * from terms where isactive=1");
		$row=mysql_fetch_array($result);
		$this->termid=$row["termid"];
		$this->termcaption=$row["termcaption"];
		$this->termstart=$row["termstart"];
		$this->termend=$row["termend"];
		$this->termtype=$row["termtype"];
		
    $_SESSION['activetermid']=$this->termid;  
	$_SESSION['activetermcaption']=$this->termcaption;   

	}
	public function gettermid()
	{return $this->termid;}
	public function gettermcaption()
	{return $this->termcaption;}
}

class _CLconnection
{
	private $servername;
	private $username;
	private $pass;
	public $connectionname;
	private $dbname;
	//function connection()
	//{} how create multi input constructure???
	function _CLconnection()
	{
		$this->servername='localhost';
		$this->username='root';
		$this->pass='110';
		$this->dbname='unicss_db';
		$this->connectdb();
		return $this->connectionname;
	}
	public function setusername($username)
	{$this->username=$username;}
	public function setpass($pass)
	{$this->pass=$pass;}
	public function setservername($servername)
	{$this->servername=$servername;}
	public function setdbname($dbname)
	{$this->dbname=$dbname;}

	public function connectdb()
	{
		$this->connectionname=mysql_pconnect($this->servername,$this->username,$this->pass);
		mysql_select_db($this->dbname,$this->connectionname);
		mysql_query('SET NAMES "utf8"');
	}
	
}

class _CLoptimisation
{
	private $opttype;
	private $courseid;
	private $teacherid;
	private $groupid;
	private $slottype;
	private $slotid;
	private $whynots;
	private $optdate;
	
	public function setall($opttype,$courseid,$teacherid,$groupid,$slottype,$slotid,$whynots)
	{
		$this->opttype=$opttype;
		$this->courseid=$courseid;
		$this->teacherid=$teacherid;
		$this->groupid=$groupid;
		$this->slottype=$slottype;
		$this->slotid=$slotid;
		$this->whynots=$whynots;
		$sqlstr="insert into optimise($opttype,$courseID,$teacherID,$groupid,$slottype,$slotid,$whynots)";
		mysql_query($sqlstr);
	}
}

?>