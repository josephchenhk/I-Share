<?php   //delete2.php主要负责删除回帖
header("Content-type: text/html; charset=utf-8"); 
include_once("conn.php");
        $topic_id=$_GET["topic_id"];
	$reply_post_id=$_GET["reply_post_id"]; // echo $reply_post_id;

       //获取reply_post_id的发帖者username
        $get_reply_name = "select post_owner from forum_reply_posts where reply_post_id=$reply_post_id";
        $get_reply_name_res = mysql_query($get_reply_name,$conn)or die("fail to get the replypost owner's name.".mysql_error());
        $reply_post_owner = mysql_result($get_reply_name_res,0,'post_owner');

	session_start(); //start session
	if(isset($_SESSION["admin"]) && $_SESSION["admin"]==true){ //login successfully
		$deleted_by=$_SESSION["adminname"]; // echo $deleted_by;
               
                //check privilege
                $check_privilege="select privilege from is_user where username='$deleted_by'";
                $check_privilege_res=mysql_query($check_privilege,$conn)or die("checking privilege fails.".mysql_error());
                $current_privilege=mysql_result($check_privilege_res,0,'privilege');
              //  echo $current_privilege;
              //  echo "Sorry, you do not have the privilege to delete this!";

		//执行删除
                if($current_privilege>-1){ // 已激活帐号
                     if($deleted_by == $reply_post_owner){ // 是回帖本人
	                    $deletesql="delete from forum_reply_posts where reply_post_id=$reply_post_id";
	                    $deletesql_res=mysql_query($deletesql,$conn)or die("fail to delete the record!".mysql_error());
	                    echo "<script>alert('删除本人回帖成功！');window.location='showtopic.php?topic_id=$topic_id';</script>";
                     }elseif($current_privilege>2){// 符合管理员（版主）权限
                            $deletesql="delete from forum_reply_posts where reply_post_id=$reply_post_id";
	                    $deletesql_res=mysql_query($deletesql,$conn)or die("fail to delete the record!".mysql_error());
	                    echo "<script>alert('您是版主，删除帖子成功！');window.location='showtopic.php?topic_id=$topic_id';</script>";
                     }else{
                            echo "<script>alert('对不起，您无权删除此帖！');history.back();</script>";
                     }//End if
                }else{ 
	             echo "<script>alert('对不起，您尚未激活帐号！');history.back();</script>";
                }
	}else{
		$_SESSION["admin"]=false;
		die("Sorry, you have not login. <a href=login.html>Login</a>");
	}

?>


<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
