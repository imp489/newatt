<?php session_start();
  @include_once "mygenclasses.php";

  $_Mcon=new _CLconnection();


  $_Muname=trim(substr($_POST['username'],0,10));
  $_Mupas=trim(substr($_POST['userpass'],0,10));
  
  $_Mhpas=md5($_Mupas);
  $_Msqlstr="insert into atusers (usrnm,usrps) values('{$_Muname}','{$_Mhpas}')";
  mysql_query($_Msqlstr);
  

?>