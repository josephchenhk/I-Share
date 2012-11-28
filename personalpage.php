<?php
header("Content-type: text/html; charset=utf-8");
$uid = $_GET["uid"]; // echo $uid;
//获取该id当前的对应头像
include_once("conn.php");
$user_info = "select username,signature,profile from is_user where id=$uid";
$user_info_res = mysql_query($user_info,$conn)or die("fail to get personal information.".mysql_error());
$current_head = mysql_result($user_info_res,0,'profile');
$current_name = mysql_result($user_info_res,0,'username');
$current_sign = mysql_result($user_info_res,0,'signature');
       
        //get currentuid
        $currentuid = $uid; 

        //get credit information
        $get_credit = "select * from credit where id=$currentuid";
        $get_credit_res = mysql_query($get_credit,$conn)or die("fail to get credit.".mysql_error());
        $usertitle = mysql_result($get_credit_res,0,'usertitle');
        $usercredit = mysql_result($get_credit_res,0,'usercredit');
        $usermoney = mysql_result($get_credit_res,0,'usermoney');
        $usergrade = mysql_result($get_credit_res,0,'usergrade');
        $last_login_time = mysql_result($get_credit_res,0,'last_login_time');
        $current_login_time = mysql_result($get_credit_res,0,'current_login_time'); 
       
        //计算两次登录的时间差
        $calc_timediff = "select TIMESTAMPDIFF(hour,'$last_login_time ','$current_login_time')";
        $calc_timediff_res = mysql_query($calc_timediff,$conn) or die("Connect error.".mysql_error());
        $t = mysql_fetch_row($calc_timediff_res);
        $login_timediff = $t[0];  

$display_block = "

<div id=\"outside_container\"> <!--放入outside_container容器，作为大背景-->
<div id=\"container\">   <!--放入container容器，注意container是作为相对位置定位的，其里面的其他元素放置位置可以相对container进行定位 -->

<a href=\"#\"><img src=\"images/logo.jpg\" id=\"logo\" /></a>  <!--将logo摆进来，其位置在#logo中已进行定义 -->

<a href=\"#\"><img src=\"images/logotext.gif\" id=\"logotext\" /></a>  <!--将logotext摆进来，其位置在#logotext中已进行定义 -->

<!-- 欢迎界面 -->
<div id=\"welcome\"> <!--放入welcome容器，显示欢迎框背景 -->
</div> <!--结束welcome容器 -->
<div id=\"welcometext\">  <!--放入welcometext容器，显示欢迎界面-->
<ul>$current_name ( $currentsign )</ul>
<ul><img src=\"images/usertitle.png\">(职务)：$usertitle</ul>
<ul><img src=\"images/usergrade.png\">(等级)：$usergrade</ul>
<ul><img src=\"images/credit.png\">(经验)：$usercredit</ul>
<ul><img src=\"images/money.png\">(金币)：$usermoney</ul>
</div> <!--结束welcometext容器-->

<div id=\"panel\">
<h1>个人主页</h1> 
<table>
<tr>
<td width=35% valign=top><img src='$current_head'width=250 height=250> <br> 
</td>
</tr>
<tr>
<p><a href='message.php?uid=$uid'>发送站内信给ta</a></p>
</tr>
</table>
<p><a href='topiclist.php'>返回论坛</a></p>
</div>

</div>  <!--结束container容器 -->
</div>  <!--结束outside_container容器 -->

";

?>


<html>
<head>
<title>Personal Page</title>

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


