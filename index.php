<?php
header("Content-type: text/html; charset=utf-8"); 
include_once("conn.php");

session_start();//start session
if(isset($_SESSION["admin"]) && $_SESSION["admin"]==true){ //login successfully
	$currentuser=$_SESSION["adminname"];

        //get currentuid
        $get_id = "select id from is_user where username='$currentuser'";
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
       
        //计算两次登录的时间差
        $calc_timediff = "select TIMESTAMPDIFF(hour,'$last_login_time ','$current_login_time')";
        $calc_timediff_res = mysql_query($calc_timediff,$conn) or die("Connect error.".mysql_error());
        $t = mysql_fetch_row($calc_timediff_res);
        $login_timediff = $t[0];  
        

$display_block ="
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
<li class=\"current\"><a href=\"#\"><span>首页</span></a></li>
<li><a href='topiclist.php'><span>论坛</span></a></li>
<!--
<li><a href='setting.php?currentuid=$currentuid'><span>个人设置</span></a></li>
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


<!--
<img src=\"images/panel_home.jpg\" id=\"panel\" /> 将panel摆进来，其位置在#panel中已进行定义 
-->

<div id=\"panel\"> <!-- 将panel容器摆进来，其位置在在#panel中已进行定义 -->
<div id=\"id_portal_imgNews\" class=\"pp_portal_imgNews\">  <!----- 显示动态新闻图  -------->
  <div id=\"id_portal_imgNew1\" class=\"pp_portal_imgNew\" style=\"display:block;\"> <a href=\"#\"><img alt=\"图片新闻一\" src=\"images/frontpage/news1.jpg\"></img></a>
    <div class=\"pp_portal_imgNew_title\"><a href=\"#\">喜迎十八大</a></div>
  </div>
  <div id=\"id_portal_imgNew2\" class=\"pp_portal_imgNew\" style=\"display:none;\"> <a href=\"#\"><img alt=\"图片新闻二\" src=\"images/frontpage/news2.jpg\"></img></a>
    <div class=\"pp_portal_imgNew_title\"><a href=\"#\">精神鉴定中心颁发傻逼证书</a></div>
  </div>
  <div id=\"id_portal_imgNew3\" class=\"pp_portal_imgNew\" style=\"display:none;\"> <a href=\"#\"><img alt=\"图片新闻三\" src=\"images/frontpage/news3.jpg\"></img></a>
    <div class=\"pp_portal_imgNew_title\"><a href=\"#\">据说傻逼这样做了，就可以保护眼睛</a></div>
  </div>
  <div id=\"id_portal_imgNew4\" class=\"pp_portal_imgNew\" style=\"display:none;\"> <a href=\"#\"><img alt=\"图片新闻四\" src=\"images/frontpage/news4.jpg\"></img></a>
    <div class=\"pp_portal_imgNew_title\"><a href=\"#\">撸完了，大晨开心地笑了</a></div>
  </div>
  <div id=\"id_portal_navLinks\" class=\"pp_portal_navLink\">
    <ul>
      <li id=\"id_portal_navLink1\" ><a href=\"javascript:void(0)\" onClick=\"pp_portal_selectPicNew(1)\"> 1 </a></li>
      <li id=\"id_portal_navLink2\" ><a href=\"javascript:void(0)\" onClick=\"pp_portal_selectPicNew(2)\"> 2 </a></li>
      <li id=\"id_portal_navLink3\" ><a href=\"javascript:void(0)\" onClick=\"pp_portal_selectPicNew(3)\"> 3 </a></li>
      <li id=\"id_portal_navLink4\" ><a href=\"javascript:void(0)\" onClick=\"pp_portal_selectPicNew(4)\"> 4 </a></li>
    </ul>
  </div>
</div> 
</div> <!--结束panel容器-->

<div id=\"content\"> <!--放入content容器-->

</div>

<!-- 欢迎界面 -->
<div id=\"welcome\"> <!--放入welcome容器，显示欢迎框背景 -->
</div> <!--结束welcome容器 -->
<div id=\"welcometext\">  <!--放入welcometext容器，显示欢迎界面-->
<ul>$currentuser,欢迎您回来！</ul>
<ul><img src=\"images/usertitle.png\">(职务)：$usertitle</ul>
<ul><img src=\"images/usergrade.png\">(等级)：$usergrade</ul>
<ul><img src=\"images/credit.png\">(经验)：$usercredit</ul>
<ul><img src=\"images/money.png\">(金币)：$usermoney</ul>
</div> <!--结束welcometext容器-->

</div>  <!--结束container容器 -->
</div>  <!--结束outside_container容器 -->
 
<div id=\"footer\">  <!--放入footer容器 -->

<img src=\"images/footer_logo.jpg\" />  <!--放入#footer img -->

<span id=\"footer_text\">   <!--放入#footer span -->
欢迎光临傻逼论坛！
<!--
See the <a href=\"Photoshop'>http://psdtuts.com\">Photoshop Tutorial</a>,
see the <a href=\"Web'>http://nettuts.com\">Web Tutorial</a>
-->
</span>

</div>   <!--结束footer容器 -->";


}else{
	$_SESSION["admin"]=false;
	echo "<script>alert('请先登录！');window.location='index.html';</script>";
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>ishare</title>


<link rel="stylesheet" href="css/index.css" type="text/css" />


<style type="text/css">

</style>  <!-- style结束标签 -->
</head>

<!-------------首页动态图显示的javascript脚本，开始 ----->
<script type="text/javascript"> 
var newsNum = 4; 
var nn = 1; 

function pp_portal_selectPicNew(o) {
    for(var i=1; i<=newsNum; i++) { 
        var newsId = "id_portal_imgNew" + i; 
            if(o==i) { // 被选中 
                document.getElementById(newsId).style.display="block"; 
                document.getElementById("id_portal_navLink"+i).style.background="red"; 
            } else { 
                document.getElementById(newsId).style.display="none"; 
                document.getElementById("id_portal_navLink"+i).style.background="#333"; 
            } 
    } 
    nn=o; 
} 

// 自动选择图片新闻 
function pp_portal_changePicNew() { 
    if(nn>newsNum) nn=1 
    //alert(nn); 
    pp_portal_selectPicNew(nn); 
    nn++; 
} 

function pp_portal_picNew_auto() { 
    pp_portal_picNew_autoTask = setInterval('pp_portal_changePicNew()', 2000); 
} 
pp_portal_picNew_auto(); 
</script>
<!-------------首页动态图显示的javascript脚本，结束 ----->

<body>  <!--开始body体 -->

<?php echo $display_block; ?>

</body>
</html>

