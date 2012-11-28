<?php
header("Content-type: text/html; charset=utf-8"); 
//connect to server and select database; we'll need it soon
include_once("conn.php");

session_start();//start session
if(isset($_SESSION["admin"]) && $_SESSION["admin"]==true){ //login successfully
        $post_owner=$_SESSION["adminname"];

        //get currentuid
        $get_id = "select id from is_user where username='$post_owner'";
        $get_id_res = mysql_query($get_id,$conn)or die("fail to get uid.".mysql_error());
        $currentuid = mysql_result($get_id_res,0,'id'); 

//check to see if we're showing the form or adding the post
if ($_POST[op] != "addpost") {
	// showing the form; check for required item in query string
	/*if (!$_GET[post_id]) {
		header("Location: topiclist.php");
		exit;
	}*/

        //获取reply_post_id 和 topic_id
        $topic_id=$_GET["topic_id"]; //echo $topic_id;
	$reply_post_id=$_GET["reply_post_id"];  //echo $reply_post_id;

        //获取reply_post_id的发帖者username
        $get_reply = "select post_owner,post_text from forum_reply_posts where reply_post_id=$reply_post_id";
        $get_reply_res = mysql_query($get_reply,$conn)or die("fail to get the replypost owner's name.".mysql_error());
        $reply_post_owner = mysql_result($get_reply_res,0,'post_owner');
        $reply_post_text = mysql_result($get_reply_res,0,'post_text');    

        //生成回复帖的前缀
        $reply_prefix = "回复".$reply_post_owner."(".$reply_post_text."):\n";

	//still have to verify topic and post
	$verify = "select ft.topic_id, ft.topic_title from forum_reply_posts as fp left join forum_topics as ft on fp.topic_id = ft.topic_id where fp.reply_post_id = $_GET[reply_post_id]";

	$verify_res = mysql_query($verify, $conn) or die(mysql_error());
	if (mysql_num_rows($verify_res) < 1) {
		//this post or topic does not exist
		header("Location: topiclist.php");
		exit;
	} else {
		//get the topic id and title
		$topic_id = mysql_result($verify_res,0,'topic_id');
		$topic_title = stripslashes(mysql_result($verify_res, 0,'topic_title'));
                
		$display_block = "

<div id=\"header\">  <!--放入header容器-->
<div id=\"headerbg\">
<a href='setting.php?currentuid=$currentuid'><img src=\"images/settings.png\" id=\"settings\"></a>
<a href='msg_receivelist.php'><img src=\"images/mail.png\" id=\"mail\"></a> 
<a href='remind.php'><img src=\"images/remind.png\" id=\"remind\"></a>
<a href='logout.php'><img src=\"images/logout.png\" id=\"logout\"></a>
</div>
</div>

<div id=\"outside_container\"> <!--放入outside_container容器，作为大背景-->
<div id=\"container\">   <!--放入container容器，注意container是作为相对位置定位的，其里面的其他元素放置位置可以相对container进行定位 -->

<a href=\"#\"><img src=\"images/logo.jpg\" id=\"logo\" /></a>  <!--将logo摆进来，其位置在#logo中已进行定义 -->

<a href=\"#\"><img src=\"images/logotext.gif\" id=\"logotext\" /></a>  <!--将logotext摆进来，其位置在#logotext中已进行定义 -->

   <div id=\"panel\">

		
		<h1>回复主题 \"$topic_title\"</h1>
		<form method=post action=\"$_SERVER[PHP_SELF]\">
		<!--
		<p><strong>Your E-Mail Address:</strong><br>
		<input type=\"text\" name=\"post_owner\" size=40 maxlength=150>
		-->

		<P><strong>留言:</strong><br>
		<textarea name=\"post_text\" rows=8 cols=40 wrap=virtual>$reply_prefix</textarea>

		<input type=\"hidden\" name=\"op\" value=\"addpost\">
		<input type=\"hidden\" name=\"topic_id\" value=\"$topic_id\">               <!--收集当前topic_id -->
                <input type=\"hidden\" name=\"topic_title\" value=\"$topic_title\">         <!--收集当前topic_title -->
                <input type=\"hidden\" name=\"reply_to\" value=\"$reply_post_owner\">       <!--收集回复对象 -->
                
		<P><input type=\"submit\" name=\"submit\" value=\"回复\"></p>
                <P><a href=showtopic.php?topic_id=$topic_id>返回主题</a></p>

		</form>
                ";
	}
} else if ($_POST[op] == "addpost") {
        

	
		//check for required items from form
		if ((!$_POST[topic_id]) || (!$_POST[post_text])) {
			header("Location: topiclist.php");
			exit;
		}

                //check privilege
                $check_privilege="select privilege from is_user where username='$post_owner'";
                $check_privilege_res=mysql_query($check_privilege,$conn)or die("checking privilege fails.".mysql_error());
                $current_privilege=mysql_result($check_privilege_res,0,'privilege');
                //  echo $current_privilege;
                //  echo "Sorry, you do not have the privilege to delete this!";

		
                if($current_privilege>-1){ // 已激活帐号 

		       //add the post//执行回复
		       $add_post = "insert into forum_reply_posts values ('', '$_POST[topic_id]', '$_POST[post_text]', now(), '$post_owner','')";
		       mysql_query($add_post,$conn) or die(mysql_error());

                       //更新forum_topics的最新回帖时间
                       $reflesh_topic = "update `forum_topics` set `topic_modify_time`=now() where topic_id=$_POST[topic_id]";
		       mysql_query($reflesh_topic) or die("Fail to reflesh the topic.".mysql_error());

                       $reply_to = $_POST[reply_to];        //收集回复对象
                       if($post_owner!=$reply_to){//自动发送站内信给对方，提醒
                               $topic_id = $_POST[topic_id];        //收集topic_id
                               $topic_title = $_POST[topic_title];  //收集回复主题 (莫忘分号！！！！)
                               //$msg = "http://147.8.166.94/ishare/showtopic.php?topic_id=$topic_id";
                               $msg = $topic_id; 
                               $title = $topic_title; 
                               $send_msg = "insert into message values ('', '$msg', '$title','$post_owner','$reply_to')";
		               mysql_query($send_msg,$conn) or die("could not send message.".mysql_error());
                       }

                       //回帖成功，奖励金钱，不奖励积分
                       //get currentuid
                       $get_id = "select id from is_user where username='$post_owner'";
                       $get_id_res = mysql_query($get_id,$conn)or die("fail to get uid.".mysql_error());
                       $currentuid = mysql_result($get_id_res,0,'id'); 
                       //get credit information
                       $get_credit = "select * from credit where id=$currentuid";
                       $get_credit_res = mysql_query($get_credit,$conn)or die("fail to get credit.".mysql_error());
                       $usertitle = mysql_result($get_credit_res,0,'usertitle');
                       $usercredit = mysql_result($get_credit_res,0,'usercredit');
                       $usermoney = mysql_result($get_credit_res,0,'usermoney');
                       $usergrade = mysql_result($get_credit_res,0,'usergrade');
                       $last_login_time = mysql_result($get_credit_res,0,'last_login_time');
                       $current_login_time = mysql_result($get_credit_res,0,'current_login_time'); 
                       $award_credit = mysql_result($get_credit_res,0,'award_credit');
                       $award_money = mysql_result($get_credit_res,0,'award_money');
                       /* if($award_credit<4){
                              $usercredit = $usercredit + 1; 
                              $award_credit = $award_credit + 1;//增加积分！
                              $update_credit = "update `credit` set `usercredit`=$usercredit, `award_credit`=$award_credit where id=$currentuid";
                              $update_credit_res = mysql_query($update_credit,$conn)or die("fail to update credit record!".mysql_error());
                       } */
                       if($award_money<30){
                              $usermoney = $usermoney + 1;
                              $award_money = $award_money + 1;  //增加财富！
                              $update_credit = "update `credit` set `usermoney`=$usermoney, `award_money`=$award_money where id=$currentuid";
                              $update_credit_res = mysql_query($update_credit,$conn)or die("fail to update credit record!".mysql_error());
                       }

		       //redirect user to topic
		       echo "<script>alert('回复成功！');window.location='showtopic.php?topic_id=$_POST[topic_id]';</script>";
             
                }else{ 
	             //echo "对不起，您尚未激活帐号！";
                     echo "<script>alert('对不起，您尚未激活帐号！');window.location='showtopic.php?topic_id=$_POST[topic_id]';</script>";
                }

     }

}else{
		$_SESSION["admin"]=false;
		die("Sorry, you have not login. <a href=login.html>Login</a>");
}
?>

<html>
<head>
<title>Reply to post</title>

<style>
body { margin:0px; padding:0px;
background-color:rgb(245%,222%,179%);
font-family:Arial, Helvetica, sans-serif;
}

#outside_container {  /*定义id = #outside_container的格式*/
/* background:url(images/background_slice.jpg) repeat-x #000000; */
background-color:rgb(210%,216%,222%);
}

#container {   /*定义id = #container的格式*/
/* background:url(images/background_main.jpg) no-repeat; */
min-height:670px;
width:1300px;
position:relative;  /*注意这里是相对位置 */
}

/* 定义forum_content /Logo / Menu / Panel Positioning */
#forum_page { position:absolute; top:220px; left:550px; }
#forum_content { position:absolute; top:120px; left:350px; }

#logo { position:absolute; top:60px; left:60px; }

#logotext { position:absolute; top:30px; left:120px; }

#panel { position:absolute; top:115px; left:251px; }

#welcometext { position:absolute; top:185px; left:700px}
#welcome {  /* 登录框背景图 */
position:absolute; top:125px; left:690px; 
background-image: url(images/welcome3.gif);
background-repeat: no-repeat;
height: 450px;
width: 270px;
}

#header {  /*定义header */
height:0px;
border-top:0px solid rgb(0%,100%,0%);
padding:30px 50px 80px 50px;
color:rgb(0%,0%,100%);
font-size:15px;
line-height:4px;
max-width:3px;
position:relative;
}
#headerbg {position:absolute;top:0px;left:0px;width:1300px;height:53px;background:url(images/head_bg.jpg) repeat-x top;float:left;}

#settings { position:absolute; top:3px; right:235px; }
#mail { position:absolute; top:3px; right:180px; }
#remind { position:absolute; top:7px; right:130px; }
#logout { position:absolute; top:8px; right:80px; }

</style>

</head>
<body>
<!--<BODY background="image/background4.jpg"> --->
<?php echo $display_block; ?>
</body>
</html>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
