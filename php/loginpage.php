<html>
<body>
<form action="mainpage.php" method="POST">
id : <input type="text" name="username"><br>
pass : <input type="text" name="userpass"><br>
<input type="hidden" name="rs" value="<?php @session_start();$_Mlen = 16;
$_Mbase='ABCDEFGHKLMNOPQRSTWXYZabcdefghjkmnpqrstwxyz123456789';
$_Mmax=strlen($_Mbase)-1;$_Mactivatecode='';
mt_srand((double)microtime()*1000000);
while (strlen($_Mactivatecode)<$_Mlen+1)
  $_Mactivatecode.=$_Mbase{mt_rand(0,$_Mmax)};
$_SESSION['rs']=$_Mactivatecode;  
echo $_Mactivatecode;
?>">
<input type="submit" value="login">
</form>
</body>
</html>