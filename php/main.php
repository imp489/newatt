<?php 
 session_start();
  if($_POST['rs']!=$_SESSION['rs'])
  {
  	echo "<script>window.location.href='login.php';</script>";
  	exit();
  }  
  @include_once 'php/mygenclasses.php';
  $_Mcon=new _CLconnection();
  //$uu="hamid";$p="apas";
  //$_Muname=trim(substr($uu,0,12));
  //$_Mupas=trim(substr($p,0,12));

  $_Muname=trim(substr($_POST['username'],0,12));
  $_Mupas=trim(substr($_POST['userpass'],0,12));
  $_Mmdpas=md5($_Mupas);
  $_Msqlstr="select * from atusers where usrnm like '{$_Muname}' and usrps like '{$_Mmdpas}'";
  $_Mresult=mysql_query($_Msqlstr);
  if(mysql_num_rows($_Mresult)!=1)
  {echo "<script>window.location.href='login.php';</script>";exit();}  
  
  if(mysql_num_rows($_Mresult)==1)
  {
  	$_Muids=array('tch'=>1,'rsch'=>2,'gm'=>3,'adm'=>4);
  	$_Mrow=mysql_fetch_array($_Mresult);
  	$_SESSION['uname']=$_Muname;
  	$_SESSION['uid']=$_Mrow['userID'];
  	$_SESSION['utype=']=$_Mrow['usertype'];
  	$_SESSION['uprivileges']=$_Mrow['privileges'];
  	if($_Mrow['usertype']=='gm')
  	{
  		$_Mout=setgmsession($_Mrow['usermainID'],$_Mrow['usertype'],$info);
  		if($_Mout!=1) {echo "<script>window.location.href='login.php';</script>";exit();}
  		else echo "<script>$info</script>";
  	}
  	else 
  	{
  		echo "<script>var browserspecifications='{$_Mrow['groupID']}%{$_Muids[$_Mrow['usertype']]}%{$_Mrow['usermainID']}' </script>";
  	}
  	mysql_free_result($_Mresult);
	$_Mcon=new _CLconnection();
    $_Mactiveterm1=new _CLactiveterm();    
  }
  
  
  function setgmsession($id,$utype,&$info)
  {
  	$_Msqlstr="select * from gms where teacherID=$id";
  	if($_Mresult=mysql_query($_Msqlstr))
  	{
  		if(mysql_num_rows($_Mresult)==1)
  		{//ut: teacher:1 , roomscheduler:2 , gmaster:3 , admin: 4
  			$_Mrow=mysql_fetch_array($_Mresult);
  			//$_Mstr1=" var groupid={$_Mrow['groupID']},usertype={$GLOBALS['uids'][$utype]},teacherid=$id,accessstr='{$_Mrow['subgroupids']}';";
  			$_Mstr1=" var browserspecifications='{$_Mrow['groupID']}%{$GLOBALS['uids'][$utype]}%$id%{$_Mrow['subgroupids']}';";
  			$info=$_Mstr1;
  			return 1;
  		}else return 0;
  	}else return -1;
  }
// other main page content
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Homet</title>
<link rel="stylesheet" type="text/css" href="css/tablesort.css" >
<link rel="stylesheet" type="text/css" href="css/button.css" >
<link rel="stylesheet" type="text/css" href="css/buttonmenu.css" >
<link rel="stylesheet" type="text/css" href="css/dtree.css" >
<link rel="stylesheet" type="text/css" href="css/scrollbar.css" >
<OBJECT id=wolfi classid=CLSID:D45FD31B-5C6E-11D1-9EC1-00C04FD7081F></OBJECT>
<SCRIPT language=JavaScript type=text/javascript> 
/*function LoadLocalAgent(CharID, CharACS) {
LoadReq = wolfi.Characters.Load(CharID, CharACS);

return(true);

} 

var MerlinID; 

var MerlinACS; 

wolfi.Connected = true; 
MerlinLoaded = LoadLocalAgent(MerlinID, MerlinACS); 

Merlin = wolfi.Characters.Character(MerlinID); 
Merlin.Show(); 
Merlin.Play("Wave"); 
Merlin.speak ("salam");
Merlin.Play('Greet');
Merlin.speak ("be systeme zamanbandi khosh amadidi");
/*Merlin.Play("Blink"); 
Merlin.Play('GestureLeft'); 
Merlin.Think ("Welcome , This is Best Site");

Merlin.Play("RestPose");
Merlin.Play("readContinued");
Merlin.Play("search");
Merlin.Play("writeContinued");

 Merlin.MoveTo (800,290);
 Merlin.Play('GestureLeft');
 Merlin.speak ("in ghesmat marboot be asatid mohtaram mibashad");
 Merlin.MoveTo (800,340);
 Merlin.Play('GestureLeft');
 Merlin.speak ("ostade mohtaram shoma mitavanid dar in ghesmat vaghthaye azade khod ra vared konid");

/*Merlin.Play('Announce');
Merlin.Play('GetAttention');
Merlin.Play('Greet');
Merlin.Play('Pleased');
Merlin.Play('DoMAgic1');Merlin.Play('DoMAgic2');
Merlin.speak("hi dear");
Merlin.MoveTo (350,250);
Merlin.Play("Surprised");
Merlin.Play("GetAttention"); 
Merlin.Play("Blink"); Merlin.Play("Blink");

Merlin.speak(" Welcome To My Site ");
Merlin.Play("GetAttention"); 
Merlin.Play("GetAttention"); 
Merlin.Play("GetAttention"); 

Merlin.Play("Blink"); Merlin.Play("Confused"); 

Merlin.speak("I Hope Enjoy To This WebSite");

Merlin.speak("Created By EsfahanHost .Com");

Merlin.Play('GestureLeft');
Merlin.Play("Blink")
Merlin.Play("Wave"); 
Merlin.Play('Processing');
Merlin.Hide(); */
</SCRIPT>

<script type="text/javascript" src="js/wait.js"></script>
<script type="text/javascript" src="js/jsScroller.js"></script>
<script type="text/javascript" src="js/jsScrollbar.js"></script>
<script type="text/javascript" src="js/report.js"></script>
<script type="text/javascript" src="js/ajaxeng.js"></script>
<script type="text/javascript" src="js/fader.js"></script>
<script type="text/javascript" src="js/tablesort.js"></script>
<script type="text/javascript" src="js/constraints.js"></script>
<script type="text/javascript" src="js/change.js"></script>
<script type="text/javascript" src="js/dom-drag.js"></script>
<script type="text/javascript" src="js/assign.js"></script>
<script type="text/javascript" src="js/manual.js"></script>
<script type="text/javascript" src="js/automatic.js"></script>
<script type="text/javascript" src="js/dtree.js"></script>
<script type="text/javascript" src="js/edit.js"></script>
<script type="text/javascript" src="js/users.js"></script>
<script type="text/javascript">
document.write('<script src="js/insert.js"><\/script>')
var url = '';
var what = '';
var username = 'admin';
var slotnums = 0;
var slots ='';
var posx = 0;
var posy = 0;
var units = 0;
var tmpunits = 0;
var slotcolors = new Array('#b1d1e9','#f1b8d7','#f1f1a9','#afdaa0','#e8eef2','');//evey week , even week , odd week, preferd , disabled , not Available
var bid = '';//building
var gid = '';//group,
var currentid1 = '';//group,building
var currentid2 = '';//room,course,tchr
var currentid3 = '';//use in mnul
var alteration= new Array();
var allid1 = new Array();
var allid2 = new Array();
var allid3 = new Array();
var url = 'php/returnslots.php';
var what = 'timeslots(req.responseText)';
var opacity1=0;
var opacity2=0;
var interval1 = null;
var interval2 = null;
var interval3 = null;
var interval4 = null;
var tmpfunc = '';
var nodepic = 'pic/group.gif';
var rootpic = 'pic/supergroup.gif';
var folderopen = 'pic/groupfolder.gif';
var nodeid='';
var bnodeid='';
var gnodeid='';
var dragid='';
var dropid='';
var ltop=0;lleft=0;
var hidemenu = '';
var submenuitems = new Array('','','');//[0]=course , [1]=tchr , [2]=room
var scroller  = null;
var scrollbar = null;
var grouptree = '';
var printwinhtml='';
var tchrid='5';
var groupid='5';
var usertype='';
document.oncontextmenu = showsub;
</script>

</head>
<body dir="rtl" onClick="hidecontextmenu();" style="cursor:default">
<span id="inspector0" onMouseOut="if (document.elementFromPoint(window.event.clientX,window.event.clientY).tagName != 'SPAN') this.style.visibility = 'hidden';"><span id="inspector1"></span></span>
<div  id="wait" style="visibility:hidden">
<div id="wait1" style="background:#CCCCCC;position:absolute;width:100%;height:100%;filter:alpha(opacity=40);vertical-align:middle" ></div>
<table style="position:absolute;width:100%;height:100%;filter:alpha(opacity=100)"><tr><td>&nbsp;</td></tr><tr><td style="text-align:center;vertical-align:top;padding-top:200"> <img src="pic/loading.gif" >&nbsp;&nbsp;<span id="waittext"></span></td></tr></table>
</div>
<!-- 2popup window -->
<div  id="popup" style="width:320px;position:absolute;visibility:hidden;overflow:hidden"><table cellspacing="0" bgcolor="#FFFFFF" bordercolor="#0033FF" border="0" height="300px" ><tr height="25"><td background="pic/title2.gif" style="border:solid;border-width:1px;border-color:#0099FF;cursor:default;background-repeat:repeat-x;text-align:right" valign="middle"><img onMouseOver="this.src = 'pic/close1.gif'" onMouseOut="this.src = 'pic/close.gif'" style="cursor:pointer" src="pic/close.gif" onClick="popupmanage('','hide');waitoff();">&nbsp;&nbsp;<span id="topic" ></span></td></tr><tr><td style="border:solid;border-width:1px;border-color:#0099FF;background:url(pic/content-blue.jpg);background-repeat:repeat-x;text-align:right" valign="top" align="justify"><span id="content"></span></td></tr></table></div>


<div style="width:320;position:absolute;visibility:hidden;overflow:hidden"  id="popup2" ><table cellspacing="0" bgcolor="#FFFFFF"  bordercolor="#0033FF" border="0"><tr height="25"><td background="pic/title2.gif" style="border:solid;border-width:1px;border-color:#0099FF;background-repeat:repeat-x;cursor:default;text-align:right" valign="middle"><img onMouseOver="this.src = 'pic/close1.gif'" onMouseOut="this.src = 'pic/close.gif'" style="cursor:pointer" src="pic/close.gif" onClick="popupmanage('2','hide');waitoff();">&nbsp;&nbsp;<span id="topic2" ></span></td></tr><tr><td style="border:solid;border-width:1px;border-color:#0099FF;background:url(pic/content-blue.jpg);background-repeat:repeat-x;text-align:right" valign="top" align="justify"><span id="content2"></span></td></tr></table></div>

<table width="100%" height="107%" cellspacing="0" ><tr style="background:url(pic/header_bg.gif);height:79;padding-right:100px"><td  width="7px">&nbsp;</td><td style="padding-right:20px" width="100"><img src="pic/arm.gif"></td><td><font face="tahoma" size="+2" color="#FFFF00" >سيستم مديريت زمانبندي دروس دانشگاهي</font></td><td width="4%" style="padding-left:10px"><img title="خروج از سيستم" style="cursor:pointer"  onClick="window.location = 'login.php'" src="pic/logout.gif" onMouseOver="this.src='pic/logout2.gif'" onMouseOut="this.src='pic/logout.gif'"></td><td width="7px">&nbsp;</td></tr>
 <tr>
   <td style="background:url(pic/mainborderr.gif) repeat-y"></td>
   <td align="right" style="padding:5px;border:dotted 3px;vertical-align:top;border-left-color:#CCCCCC;border-bottom:none;border-right:none;border-top:none" ><iframe id="frame1" name="menu" frameborder="0" src="menu.html" width="90" height="600px" scrolling="no"></iframe></td>
   <td colspan="2" style="text-align:right;vertical-align:top"><div style="background:#f9ffbc;color:#3c7bfc;font-weight:bold;height:20px;padding:2px" id="innertitlebar">اساتيد</div><div id="maindiv"></div>
</td>
   <td width="7px" style="background:url(pic/maniborderl.gif) repeat-y">&nbsp;</td>
 </tr>
 <tr height="27px"><td style="background:url(pic/mainfooerr.gif)">&nbsp;</td><td colspan="3" style="background:url(pic/mainfootercenter.gif) repeat-x">&nbsp;</td><td style="background:url(pic/mainfooterl.gif)">&nbsp;</td></tr>
</table>

<script type="text/javascript">
document.title = 'ص?حه ي اصلي';
datasend();
Drag.init(document.getElementById("popup"));
Drag.init(document.getElementById("popup2"));
</script>
<div onMouseOver="hidemenu=1" onMouseOut="hidemenu=0" id="contextmenu" style="top:0;left:0;position:absolute;visibility:hidden;overflow:hidden"><table cellspacing="0" style="border:groove 2px;cursor:default;width:100px;height:100px;background:#ece9d8"><tr id="ccm" onMouseOver="showsubmenu(this,0);" ><td width="20"><img src="pic/course.gif"></td><td width="30">درس</td><td width="50">&nbsp;</td><td width="10"><img src="pic/arrow.gif"></td></tr><tr id="tcm" onMouseOver="showsubmenu(this,32);"><td><img src="pic/tchr.gif"></td><td>استاد</td><td width="50">&nbsp;</td><td><img src="pic/arrow.gif"></td></tr><tr id="rcm" onMouseOver="showsubmenu(this,66);"><td><img src="pic/room.gif"></td><td>اتاق</td><td width="50">&nbsp;</td><td><img src="pic/arrow.gif"></td></tr></table></div>


<div onMouseOver="1" onMouseOut="hidemenu=0" id="submenu" style="top:0;left:0;position:absolute;visibility:hidden;overflow:hidden;width:10px"></div>
</body>
</html>
