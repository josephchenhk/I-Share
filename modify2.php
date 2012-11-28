<?php
header("Content-type: text/html; charset=utf-8"); 
//connect to server and select database; we'll need it soon
include_once("conn.php");

session_start();//start session
if(isset($_SESSION["admin"]) && $_SESSION["admin"]==true){ //login successfully
	$modified_by=$_SESSION["adminname"];

        //get currentuid
        $get_id = "select id from is_user where username='$modified_by'";
        $get_id_res = mysql_query($get_id,$conn)or die("fail to get uid.".mysql_error());
        $currentuid = mysql_result($get_id_res,0,'id'); 

//check to see if we're showing the form or adding the post
if ($_POST[op] != "modifypost") {
	// showing the form; check for required item in query string
	/*if (!$_GET[post_id]) {
		header("Location: topiclist.php");
		exit;
	}*/

        //获取post_id 和 topic_id
        $topic_id=$_GET["topic_id"]; //echo $topic_id;
	$post_id=$_GET["post_id"];  //echo $post_id;

        //获取post_id的发帖者username
        $get_post = "select post_owner,post_text from forum_posts where post_id=$post_id";
        $get_post_res = mysql_query($get_post,$conn)or die("fail to get the post owner's name.".mysql_error());
        $post_owner = mysql_result($get_post_res,0,'post_owner');
        $post_text = mysql_result($get_post_res,0,'post_text');    

        //生成回复帖的前缀
        $post_prefix = $post_text;

	//still have to verify topic and post
	$verify = "select ft.topic_id, ft.topic_title from forum_posts as fp left join forum_topics as ft on fp.topic_id = ft.topic_id where fp.post_id = $_GET[post_id]";

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

		<h1>修改主题 \"$topic_title\" 的回帖</h1>
		<form method=post action=\"$_SERVER[PHP_SELF]\">
		<!--
		<p><strong>Your E-Mail Address:</strong><br>
		<input type=\"text\" name=\"post_owner\" size=40 maxlength=150>
		-->
                
                <p><strong>标题:</strong><br>
                <input type=\"text\" name=\"topic\" value=\"$topic_title\" size=40 maxlength=150>
		<P><strong>内容:</strong><br>
		<textarea name=\"post_text\" rows=28 cols=80 wrap=virtual>$post_prefix</textarea>

		<input type=\"hidden\" name=\"op\" value=\"modifypost\">
		<input type=\"hidden\" name=\"topic_id\" value=\"$topic_id\">               <!--收集当前topic_id -->
                <input type=\"hidden\" name=\"topic_title\" value=\"$topic_title\">         <!--收集当前topic_title -->
                <input type=\"hidden\" name=\"post_owner\" value=\"$post_owner\">           <!--收集主题对象 -->
                <input type=\"hidden\" name=\"post_id\" value=\"$post_id\">                 <!--收集主题对象id -->
                
		<P><input type=\"submit\" name=\"submit\" value=\"修改\"></p>
                <P><a href=showtopic.php?topic_id=$topic_id>返回主题</a></p>

		</form>";
	}
}elseif ($_POST[op] == "modifypost") {
        
	
		//check for required items from form
		if ((!$_POST[topic_id]) || (!$_POST[post_text])) {
			header("Location: topiclist.php");
			exit;
		}

                //check privilege
                $check_privilege="select privilege from is_user where username='$modified_by'";
                $check_privilege_res=mysql_query($check_privilege,$conn)or die("checking privilege fails.".mysql_error());
                $current_privilege=mysql_result($check_privilege_res,0,'privilege');
                //  echo $current_privilege;
                //  echo "Sorry, you do not have the privilege to delete this!"
	
               if($current_privilege>-1){ // 已激活帐号 
                     //获取post_id
                     $post_id = $_POST[post_id];                     
                     $post_owner = $_POST[post_owner]; 
                       
                     if($modified_by == $post_owner){ // 是发帖本人
	                     //modify the post//执行修改
		             $modify_post = "update `forum_posts` set `post_text`='$_POST[post_text]',`post_modify_time`=now() where post_id =$post_id";
		             mysql_query($modify_post,$conn) or die(mysql_error());

		             //redirect user to topic
		             echo "<script>alert('修改成功！');window.location='showtopic.php?topic_id=$_POST[topic_id]';</script>";
                     }elseif($current_privilege>2){// 符合管理员（版主）权限
                             //modify the post//执行修改
		             $modify_post = "update `forum_posts` set `post_text`='$_POST[post_text]',`post_modify_time`=now() where post_id =$post_id ";
		             mysql_query($modify_post,$conn) or die(mysql_error());

		             //redirect user to topic
		             echo "<script>alert('您以超级管理员身份，修改帖子成功！');window.location='showtopic.php?topic_id=$_POST[topic_id]';</script>";
                     }else{
                            echo "<script>alert('对不起，您无权修改此帖！');window.location='showtopic.php?topic_id=$_POST[topic_id]';</script>";
                     }//End if     
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
<title>Modify the post</title>

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
