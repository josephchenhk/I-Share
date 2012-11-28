<?php //read_msg_send.php实现显示个人站内信收件箱内容
header("Content-type: text/html; charset=utf-8");
include_once("conn.php");

//获取$prv_msgid
$prv_msgid = $_GET[prv_msgid];

//gather the information of messages
$get_msg = "select msg,title,sendtime,sender,receiver from prv_message where prv_msgid=$prv_msgid";
$get_msg_res = mysql_query($get_msg,$conn)or die("Could not get the information of sent message.".mysql_error());
$msg = mysql_result($get_msg_res,0,'msg');
$title = mysql_result($get_msg_res,0,'title');
$sendtime = mysql_result($get_msg_res,0,'sendtime');
$sender = mysql_result($get_msg_res,0,'sender');   
$receiver = mysql_result($get_msg_res,0,'receiver'); 

//获取个人信息
session_start();//start session
if(isset($_SESSION["admin"]) && $_SESSION["admin"]==true){ //login successfully
	$currentuser=$_SESSION["adminname"];      

        //get currentuid
        $get_id = "select id from is_user where username='$currentuser'";
        $get_id_res = mysql_query($get_id,$conn)or die("fail to get uid.".mysql_error());
        $currentuid = mysql_result($get_id_res,0,'id');  
   
        if($currentuser==$receiver){  //无权查看别人的站内信，只能查看属于自己的站内信
                
                //获取来信者的uid
                $get_id = "select id from is_user where username='$sender'";
                $get_id_res = mysql_query($get_id,$conn)or die("Could not get the information of sender.".mysql_error());
                $uid = mysql_result($get_id_res,0,'id'); //echo $uid;

                //create the display string
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

                <h1>站内信</h1>
                <p>
                  <a href=msg_receivelist.php>收件箱</a>
                  <a href=msg_sendlist.php>发件箱</a>
                  <a href=topiclist.php>返回论坛</a>
                </p>
        	<table cellpadding=5 cellspacing=4 border=1>
        	<tr>
        	<td>$sendtime 来自 $sender 的站内信 <br>
                    《 $title 》： <br>
                    $msg
                    <a href=message_reply.php?uid=$uid&prv_msgid=$prv_msgid> 回信 </a> 
                    <a href=del_msg_receive.php?prv_msgid=$prv_msgid> 删除 </a>
                </td>
        	</tr>
                </table>";

        }else{
              $display_block = "
                
        	<table cellpadding=5 cellspacing=4 border=1>
        	<tr>
        	<td> 您无权查看别人的站内信！</td>
        	</tr>
                </table>";
        }       
        
}else{
	$_SESSION["admin"]=false;
	die("Sorry, you have not login. <a href=login.html>Login</a>");
}

?>
<html>
<head>
<title>Private Message</title>

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
