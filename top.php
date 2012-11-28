<?php  //top.php实现置顶功能
header("Content-type: text/html; charset=utf-8"); 
include_once("conn.php");

$topic_id = $_GET[topic_id];

session_start(); //start session
	if(isset($_SESSION["admin"]) && $_SESSION["admin"]==true){ //login successfully
		$top_by=$_SESSION["adminname"]; // echo $deleted_by;
               
                //check privilege
                $check_privilege="select privilege from is_user where username='$top_by'";
                $check_privilege_res=mysql_query($check_privilege,$conn)or die("checking privilege fails.".mysql_error());
                $current_privilege=mysql_result($check_privilege_res,0,'privilege');
       
		//执行置顶
                if($current_privilege>-1){ // 已激活帐号
                     if($current_privilege>2){// 符合管理员（版主）权限
                            $topsql="update `forum_topics` set `status`=1 where topic_id=$topic_id";
		            mysql_query($topsql) or die("Fail to top the topic.".mysql_error());
	                    echo "<script>alert('您是版主，置顶帖子成功！');window.location='showtopic.php?topic_id=$topic_id';</script>";
                     }else{
                            echo "<script>alert('对不起，您无权进行此操作！');history.back();</script>";
                     }//End if
                }else{ 
	             echo "<script>alert('对不起，您尚未激活帐号！');history.back();</script>";
                }
	}else{
		$_SESSION["admin"]=false;
		die("Sorry, you have not login. <a href=login.html>Login</a>");
	}
?>
