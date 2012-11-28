<?php
header("Content-type: text/html; charset=utf-8");
$currentuid = $_GET["currentuid"]; //echo $currentuid;
//获取该id当前的对应头像
include_once("conn.php");
$user_info = "select username,signature,profile from is_user where id=$currentuid";
$user_info_res = mysql_query($user_info,$conn)or die("fail to get personal information.".mysql_error());
$current_head = mysql_result($user_info_res,0,'profile');
$current_name = mysql_result($user_info_res,0,'username');
$current_sign = mysql_result($user_info_res,0,'signature');

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

<!---------------------------->
<div id=\"nav\">   <!--放入nav容器 -->
<!--nav,start-->
<div class=\"menu_navcc\">
<div class=\"menu_nav clearfix\">
<ul class=\"nav_content\">
<li><a href='index.php'><span>首页</span></a></li>
<li><a href='topiclist.php'><span>论坛</span></a></li>
<!--
<li class=\"current\"><a href='setting.php?currentuid=$currentuid'><span>个人设置</span></a></li>
<li><a href='msg_receivelist.php'><span>站内信</span></a></li>
<li><a href='remind.php'><span>消息提醒</span></a></li>
<li><a href=\"http://www.mianfeimoban.com/\"><span>登录</span></a></li>
<a href='logout.php'><img src=\"images/logout.png\" id=\"logout\"></a>
-->
</ul>
<div class=\"menu_nav_right\"></div>
</div>
</div>
<!--nav,end-->
</div> <!-- 结束nav容器-->
<!---------------------------->  

<!-- 欢迎界面 -->
<div id=\"welcome\"> <!--放入welcome容器，显示欢迎框背景 -->
</div> <!--结束welcome容器 -->
<div id=\"welcometext\">  <!--放入welcometext容器，显示欢迎界面-->
<ul>$current_name,欢迎您回来！</ul>
<ul><img src=\"images/usertitle.png\">(职务)：$usertitle</ul>
<ul><img src=\"images/usergrade.png\">(等级)：$usergrade</ul>
<ul><img src=\"images/credit.png\">(经验)：$usercredit</ul>
<ul><img src=\"images/money.png\">(金币)：$usermoney</ul>
</div> <!--结束welcometext容器-->

<!-- 个人设置 ----->
<div id=\"setting\"> 
$current_name( $current_sign ) <br>
<p><a href='upload2.php'>上传新头像</a>
<form name=\"setsignature\"action=\"setsignature.php\"method=\"post\">
<table>
<tr>
<td width=35% valign=top><img src='$current_head'width=250 height=250> <br> 
</td>
</tr>

<tr>
个性签名档:
<input type=\"text\"name=\"signature\"/>
<input type=\"submit\"name=\"submit\"value=\"Sign\"/>
</tr>
</table>
</form>

<p><a href='topiclist.php'>返回论坛.</a></p>
</div>
                          
</div> <!--end container -->
</div> <!--end outside_container --> ";


$display_block .= "";
?>


<html>
	<head>
		<title>Personal Settings</title>

                <link rel="stylesheet" href="css/setting.css" type="text/css" />

                <style type="text/css">
                </style>  <!-- style结束标签 -->
	</head>
	<body>
		<!-- <BODY background="image/background4.jpg">  -->
           

               <?php echo $display_block;?>

                
	</body>
</html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
