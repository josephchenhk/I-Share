<?php
header("Content-type: text/html; charset=utf-8"); 
include_once("conn.php");

session_start();//start session
if(isset($_SESSION["admin"]) && $_SESSION["admin"]==true){//login successfully
	$current_user = $_SESSION["adminname"];

	//执行删除
	$msgid=$_GET[msgid]; // echo $msgid;
	$remind_del="delete from message where msgid=$msgid";
	$remind_del_res = mysql_query($remind_del,$conn)or die("fail to delete record.".mysql_error());

	//返回提醒页面
	echo "<script>alert('谢谢您！');window.location='remind.php';</script>";
}else{
	$_SESSION["admin"]=false;
	die("Sorry, you have not login. <a href=login.html>Login</a>");
}

?>


