<?php
header("Content-type: text/html; charset=utf-8"); 
//connect to server and select database
//$conn = mysql_connect("localhost", "sam", "123") or die(mysql_error());
//mysql_select_db("myishare",$conn)  or die(mysql_error());
include_once("conn.php");

//获取个人信息
session_start();//start session
if(isset($_SESSION["admin"]) && $_SESSION["admin"]==true){ //login successfully
	$currentuser=$_SESSION["adminname"];
}else{
	$_SESSION["admin"]=false;
	die("Sorry, you have not login. <a href=index.html>Login</a>");
}
$get_id = "select id from is_user where username='$currentuser'";
$get_id_res = mysql_query($get_id,$conn)or die("Could not get the personal information.".mysql_error());
$currentuid = mysql_result($get_id_res,0,'id');
//echo $currentuid;

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

//gather the topics
//$get_topics = "select topic_id, topic_title, date_format(topic_create_time,  '%b %e %Y at %r') as fmt_topic_create_time, topic_owner from forum_topics order by topic_create_time desc";
//$get_topics_res = mysql_query($get_topics,$conn) or die("Connect error.".mysql_error());

//Count the number of topics
$get_topics_numbers = "select count(*) from forum_topics";
$get_topics_numbers_res = mysql_query($get_topics_numbers,$conn) or die("Connect error.".mysql_error());
$r = mysql_fetch_row($get_topics_numbers_res);
$numrows = $r[0];

//number of rows displayed per page
$rowsperpage = 10;
//total number of pages
$totalpages = ceil($numrows / $rowsperpage);

//取得当前的页数，或者显示预设的页数
if(isset($_GET['currentpage']) && is_numeric($_GET['currentpage'])){
	//把变量的类型转换成int
	$currentpage = (int)$_GET['currentpage'];
}else{
	//预设的页数
	$currentpage = 1;
}//END IF

//若果当前的页数大于页数总数
if($currentpage>$totalpages){
	//把当前页数设定为最后一页
	$currentpage = $totalpages;
}
//若果当前的页数小于1
if($currentpage<1){
	//把当前的页数设定为1
	$currentpage = 1;
}

//根据当前页数计算名单的起始位置
$offset = ($currentpage-1)*$rowsperpage;

//gather the topics status =1 
$get_topics_status1 = "select topic_id, topic_title, date_format(topic_create_time,  '%b %e %Y at %r') as fmt_topic_create_time, date_format(topic_modify_time,  '%b %e %Y at %r') as fmt_topic_modify_time, topic_owner,status from forum_topics where status=1 order by topic_modify_time desc limit $offset,$rowsperpage";
$get_topics_status1_res = mysql_query($get_topics_status1,$conn) or die("Connect error.".mysql_error());

//gather the topics status =0 
$get_topics_status0 = "select topic_id, topic_title, date_format(topic_create_time,  '%b %e %Y at %r') as fmt_topic_create_time, date_format(topic_modify_time,  '%b %e %Y at %r') as fmt_topic_modify_time, topic_owner,status from forum_topics where status=0 order by topic_modify_time desc limit $offset,$rowsperpage";
$get_topics_status0_res = mysql_query($get_topics_status0,$conn) or die("Connect error.".mysql_error());

if (mysql_num_rows($get_topics_status1_res) < 1 && mysql_num_rows($get_topics_status0_res) < 1) {
	//there are no topics, so say so
	$display_block = "<P><em>暂无话题.</em></p>

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
<li class=\"current\"><a href='topiclist.php'><span>论坛</span></a></li>
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
        
                          <p>                
                          <em>管理员:</em> <a href='personalpage.php?uid=1' style=\"text-decoration:none\">请叫我红领巾</a>
                          </p>
            
                          <table cellpadding=5 cellspacing=4 border=1>
	                  <tr>
	                  <th>话题</th>
	                  <th>热度</th>
                          <th>最新回帖</th>
                       	  </tr>";
} else {
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

<!---------------------------->
<div id=\"nav\">   <!--放入nav容器 -->
<!--nav,start-->
<div class=\"menu_navcc\">
<div class=\"menu_nav clearfix\">
<ul class=\"nav_content\">
<li><a href='index.php'><span>首页</span></a></li>
<li class=\"current\"><a href='topiclist.php'><span>论坛</span></a></li>
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
                          
                       <div id=\"forum_content\">
                          <p>                
                          <em>管理员:</em> <a href='personalpage.php?uid=1' style=\"text-decoration:none\">请叫我红领巾</a>
                          </p>
            
                          <table cellpadding=5 cellspacing=4 border=1>
	                  <tr>
	                  <th>话题</th>
	                  <th>热度</th>
                          <th>最新回帖</th>
                       	  </tr>
                       </div>
</div> <!--end container -->
</div> <!--end outside_container --> ";

	while ($topic_info = mysql_fetch_array($get_topics_status1_res)) { //显示置顶帖
		$topic_id = $topic_info['topic_id'];
		$topic_title = stripslashes($topic_info['topic_title']);
		$topic_create_time = $topic_info['fmt_topic_create_time'];
		$topic_owner = stripslashes($topic_info['topic_owner']);
                $topic_modify_time = $topic_info['fmt_topic_modify_time'];

		//get number of posts
		$get_num_posts = "select count(reply_post_id) from forum_reply_posts where topic_id = $topic_id";
		$get_num_posts_res = mysql_query($get_num_posts,$conn) or die(mysql_error());
		$num_posts = mysql_result($get_num_posts_res,0,'count(reply_post_id)');

                //获得发帖者的uid
                $get_id = "select id from is_user where username = '$topic_owner'";
		$get_id_res = mysql_query($get_id,$conn) or die(mysql_error());
		$uid = mysql_result($get_id_res,0,'id');

		//add to display
		$display_block .= "
		<tr>
		<td> <em> <p class=\"ex\"> <a href=\"showtopic.php?topic_id=$topic_id\"><strong>$topic_title</strong></a> </p> </em>         <br>
		Created on $topic_create_time by <a class=\"ameth\"href='personalpage.php?uid=$uid' style=\"text-decoration:none\">$topic_owner</a></td>     
		<td align=center>$num_posts</td>
                <td align=center>$topic_modify_time</td>
		</tr>";
	}

        while ($topic_info = mysql_fetch_array($get_topics_status0_res)) { //显示非置顶帖
		$topic_id = $topic_info['topic_id'];
		$topic_title = stripslashes($topic_info['topic_title']);
		$topic_create_time = $topic_info['fmt_topic_create_time'];
		$topic_owner = stripslashes($topic_info['topic_owner']);
                $topic_modify_time = $topic_info['fmt_topic_modify_time'];

		//get number of posts
		$get_num_posts = "select count(reply_post_id) from forum_reply_posts where topic_id = $topic_id";
		$get_num_posts_res = mysql_query($get_num_posts,$conn) or die(mysql_error());
		$num_posts = mysql_result($get_num_posts_res,0,'count(reply_post_id)');

                //获得发帖者的uid
                $get_id = "select id from is_user where username = '$topic_owner'";
		$get_id_res = mysql_query($get_id,$conn) or die(mysql_error());
		$uid = mysql_result($get_id_res,0,'id');

		//add to display
		$display_block .= "
		<tr>
		<td><a href=\"showtopic.php?topic_id=$topic_id\"><strong>$topic_title</strong></a><br>
		Created on $topic_create_time by <a class=\"ameth\"href='personalpage.php?uid=$uid' style=\"text-decoration:none\">$topic_owner</a></td>
		<td align=center>$num_posts</td>
                <td align=center>$topic_modify_time</td>
		</tr>";
	}
	//close up the table
	$display_block .= "</table>";

	/*********建立分页连结***************/
	//显示的页数范围
	$range = 3;

	//若果正在显示第一页，无需显示“前一页”连结
	if($currentpage>1){
		//使用<<连结回到第一页
		$display_block .= "<a href='{$_SERVER['PHP_SELF']}?currentpage=1'> << </a>";
		//前一页的页数
		$prevpage = $currentpage - 1;
		//使用<连结回前一页
		$display_block .= "<a href='{$_SERVER['PHP_SELF']}?currentpage=$prevpage'> < </a>";
	}//END IF

	//显示当前分页临近的分页页数
	for($x=(($currentpage-$range)-1); $x<(($currentpage+$range)+1); $x++){
		//如果这是一个正确的页数...
		if(($x>0) && ($x<=$totalpages)){
	        	if($x == $currentpage){
	        		//不使用连结，但用高亮度显示
	        		$display_block .= "[<b>$x</b>]";
	        		//如果这一页不是当前页数...
	        	}else{
	        		//显示连结
	        		$display_block .= "<a href='{$_SERVER['PHP_SELF']}?currentpage=$x'>$x</a>";
			}//END ELSE
		}//END IF
	}//END FOR

	//如果不是最后一页，显示跳往下一页及最后一页的连结
	if($currentpage != $totalpages){
		//下一页的页数
		$nextpage = $currentpage + 1;
		//显示跳往下一页的连结
		$display_block .= "<a href='{$_SERVER['PHP_SELF']}?currentpage=$nextpage'> > </a>";
		//显示跳往最后一页的连结
		$display_block .= "<a href='{$_SERVER['PHP_SELF']}?currentpage=$totalpages'> >> </a>";
	}//END IF
	/**********完成建立分页连结*********/
}

?>
<html>
<head>

<link rel="stylesheet" href="css/topiclist.css" type="text/css" />

<style type="text/css">
</style>

<title>Topics in My Forum</title>
</head>
<body>

<!--<BODY background="image/background4.jpg">
<h1>傻逼欢乐多，节操无下限</h1>
-->
<?php echo $display_block; ?>
<P>发表 <a href="addtopic.html">新话题</a>?</p>
</body>
</html>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
