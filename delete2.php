<?php   //delete2.php主要负责删除主楼
header("Content-type: text/html; charset=utf-8"); 
include_once("conn.php");
        $topic_id=$_GET["topic_id"];
	$post_id=$_GET["post_id"]; // echo $post_id;

        //获取post_id的发帖者username
        $get_name = "select post_owner from forum_posts where post_id=$post_id";
        $get_name_res = mysql_query($get_name,$conn)or die("fail to get the post owner's name.".mysql_error());
        $post_owner = mysql_result($get_name_res,0,'post_owner');

        //获取回帖情况
        //Count the number of reply posts
        $get_topics_numbers = "select count(*) from forum_reply_posts where topic_id=$topic_id";
        $get_topics_numbers_res = mysql_query($get_topics_numbers,$conn) or die("fail to count the number of posts.".mysql_error());
        $r = mysql_fetch_row($get_topics_numbers_res); 
        $numreply = $r[0]; 
        //echo $numreply;

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
                     if($numreply<1) {   //全部回帖已删除                    
 
                            if($deleted_by == $post_owner){ // 是发帖本人
	                             $deletesql="delete from forum_posts where post_id=$post_id";
	                             $deletesql_res=mysql_query($deletesql,$conn)or die("fail to delete the post record!".mysql_error());
                                     $deletesql2="delete from forum_topics where topic_id=$topic_id";
	                             $deletesql_res2=mysql_query($deletesql2,$conn)or die("fail to delete the topic record!".mysql_error());
	                             echo "<script>alert('删除本人发帖成功！');window.location='showtopic.php?topic_id=$topic_id';</script>";
                            }elseif($current_privilege>2){// 符合管理员（版主）权限
                                     $deletesql="delete from forum_posts where post_id=$post_id";
	                             $deletesql_res=mysql_query($deletesql,$conn)or die("fail to delete the record!".mysql_error());
                                     $deletesql2="delete from forum_topics where topic_id=$topic_id";
	                             $deletesql_res2=mysql_query($deletesql2,$conn)or die("fail to delete the topic record!".mysql_error());
	                             echo "<script>alert('您是版主，删除帖子成功！');window.location='showtopic.php?topic_id=$topic_id';</script>";
                            }else{
                                     echo "<script>alert('对不起，您无权删除此帖！');history.back();</script>";
                            }//End if
                     
		     }else{
			    echo "<script>alert('对不起，还有回帖，您不能删除本楼。请联系管理员。');window.location='showtopic.php?topic_id=$topic_id';</script>";
                            //echo "对不起，还有回帖，您不能删除本楼。请联系版主。"；
                     } //END IF
                }else{ 
	             echo "<script>alert('对不起，您尚未激活帐号！');history.back();</script>";
                }
	}else{
		$_SESSION["admin"]=false;
		die("Sorry, you have not login. <a href=login.html>Login</a>");
	}

?>


<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
