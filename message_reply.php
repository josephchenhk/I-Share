<?php //message.php实现发送和接收私信功能
header("Content-type: text/html; charset=utf-8");
include_once("conn.php");

if ($_POST[op] != "sendmsg") {

        //获取收信者id
        $receiver_id=$_GET["uid"]; //echo $receiver_id;
        $prv_msgid = $_GET["prv_msgid"] ; //echo $prv_msgid;

        //获取reply_post_id的发帖者username
        $get_reply = "select title,msg from prv_message where prv_msgid=$prv_msgid";
        $get_reply_res = mysql_query($get_reply,$conn)or die("fail to get the prv_msg information.".mysql_error());
        $reply_title = mysql_result($get_reply_res,0,'title');
        $reply_text = mysql_result($get_reply_res,0,'msg');    

        //生成回复帖的前缀
        $reply_title_prefix = $reply_title;
        $reply_msg_prefix = $reply_text."\n";
	
        //获取收信者的相关信息
        $get_info = "select username from is_user where id=$receiver_id";
        $get_info_res = mysql_query($get_info,$conn)or die("fail to get the receiver's name.".mysql_error());
        $receiver = mysql_result($get_info_res,0,'username');
          
        session_start();//start session
        if(isset($_SESSION["admin"]) && $_SESSION["admin"]==true){ //login successfully
		$sender=$_SESSION["adminname"];
                
                //get currentuid
                $get_id = "select id from is_user where username='$sender'";
                $get_id_res = mysql_query($get_id,$conn)or die("fail to get uid.".mysql_error());
                $currentuid = mysql_result($get_id_res,0,'id'); 
      
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
  
		<html>
		<head>
		<title>message to $receiver</title>
		</head>
		<body>
		<h1>发送站内信给 \"$receiver\"</h1>
		<form method=post action=\"$_SERVER[PHP_SELF]\">

		<p><strong>标题:</strong><br>
            <!--    <input type=\"text\" name=\"topic\"  size=40 maxlength=150> -->
                <input type=\"text\" name=\"topic\"  size=40 maxlength=150 value=\"$reply_title_prefix\"> 
             <!--   <textarea name=\"topic\" rows=1 cols=40 wrap=virtual> $reply_title_prefix </textarea> -->
		<P><strong>内容:</strong><br>
		<textarea name=\"post_text\" rows=28 cols=80 wrap=virtual> $reply_msg_prefix </textarea>

		<input type=\"hidden\" name=\"op\" value=\"sendmsg\">
                <input type=\"hidden\" name=\"receiver\" value=\"$receiver\">               <!--收集接收者username -->
                <input type=\"hidden\" name=\"receiver_id\" value=\"$receiver_id\">         <!--收集接收者uid -->
                
		<P><input type=\"submit\" name=\"submit\" value=\"发送\"></p>
                <P><a href=showtopic.php?topic_id=$topic_id>返回主题</a></p>

		</form>
		</body>
		</html>";
        }else{
		$_SESSION["admin"]=false;
		die("Sorry, you have not login. <a href=login.html>Login</a>");
	}
	
}elseif ($_POST[op] == "sendmsg") {
        
	session_start();//start session
        if(isset($_SESSION["admin"]) && $_SESSION["admin"]==true){ //login successfully
		$sender=$_SESSION["adminname"];
                //check for required items from form
		if ((!$_POST[topic]) || (!$_POST[post_text])) {
			header("Location: topiclist.php");
			exit;
		}

                //check privilege
                $check_privilege="select privilege from is_user where username='$sender'";
                $check_privilege_res=mysql_query($check_privilege,$conn)or die("checking privilege fails.".mysql_error());
                $current_privilege=mysql_result($check_privilege_res,0,'privilege');
                if($current_privilege>-1){ // 已激活帐号 
                     //获取收信者信息
                     $receiver = $_POST[receiver];  
                     $receiver_id = $_POST[receiver_id]; 

                     //检查接收id的用户是否存在
                     $check_receiver="select count(*) from is_user where id=$receiver_id";
                     $check_receiver_res=mysql_query($check_receiver,$conn)or die("checking receiver fails.".mysql_error());
                     $r = mysql_fetch_row($check_receiver_res);
                     $record = $r[0];
  
                     if($record!=0){
                     
                           if($sender==$receiver){
                                  echo "<script>alert('您给自己发站内信做什么？');window.location='personalpage.php?uid=$receiver_id';</script>";        
                           }else{        

                                  //send msg//执行发送 
                                  $send_msg = "INSERT INTO `prv_message`(`prv_msgid`, `msg`, `title`, `sendtime`, `sender`, `receiver`) VALUES ('','$_POST[post_text]','$_POST[topic]',now(),'$sender','$receiver')";
		                  mysql_query($send_msg,$conn) or die("fail to send msg.".mysql_error());   
                     
                                  //redirect user to topic
		                  echo "<script>alert('发送成功！');window.location='personalpage.php?uid=$receiver_id';</script>";  
                           }
                     }else{
                            echo "<script>alert('对不起，暂无此用户！');history.back();</script>";          
                     } 
                }else{ 
	             //echo "对不起，您尚未激活帐号！";
                     echo "<script>alert('对不起，您尚未激活帐号！');history.back();</script>"; 
                }
	}else{
		$_SESSION["admin"]=false;
		die("Sorry, you have not login. <a href=login.html>Login</a>");
	}

} 

?>

<html>
<head>
<title>Send message</title>

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
		<!--<BODY background="image/background4.jpg">
                <h1>个人主页</h1> 
                <?php echo "$current_name( $current_sign )" ?> -->
		<?php echo "$display_block" ?>                
	</body>
</html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

