<?php
header("Content-type: text/html; charset=utf-8"); 
//check for required info from the query string
if (!$_GET[topic_id]) {
	header("Location: topiclist.php");
	exit;
}

//connect to server and select database
//$conn = mysql_connect("localhost", "sam", "123") or die(mysql_error());
//mysql_select_db("mydb",$conn)  or die(mysql_error());
include_once("conn.php");

session_start();//start session
if(isset($_SESSION["admin"]) && $_SESSION["admin"]==true){ //login successfully
	$currentuser=$_SESSION["adminname"];

        //get currentuid
        $get_id = "select id from is_user where username='$currentuser'";
        $get_id_res = mysql_query($get_id,$conn)or die("fail to get uid.".mysql_error());
        $currentuid = mysql_result($get_id_res,0,'id'); //echo $currentuid;

//verify the topic exists
$verify_topic = "select topic_title from forum_topics where topic_id = $_GET[topic_id]";
$verify_topic_res = mysql_query($verify_topic, $conn) or die(mysql_error());

if (mysql_num_rows($verify_topic_res) < 1) {
//this topic does not exist
$display_block = "<P><em>You have selected an invalid topic.
Please <a href=\"topiclist.php\">try again</a>.</em></p>";
} else {

$topic_id=$_GET[topic_id];

/***********为分页作准备************************/
//Count the number of topics
$get_topics_numbers = "select count(*) from forum_reply_posts where topic_id=$topic_id";
$get_topics_numbers_res = mysql_query($get_topics_numbers,$conn) or die("Connect error.".mysql_error());
$r = mysql_fetch_row($get_topics_numbers_res); 
$numrows = $r[0]; 

//number of rows displayed per page
$rowsperpage = 10;
//total number of pages
$totalpages = ceil(($numrows+1) / $rowsperpage); //+1表示楼主

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
$offset = ($currentpage-1)*$rowsperpage - 1;  //注意这里要减一
/***********分页准备完成************************/

/************ 获取post_id **********************/
//gather the posts
$get_posts = "select post_id, post_text, date_format(post_create_time, '%b %e %Y at %r') as fmt_post_create_time, post_owner from forum_posts where topic_id = $_GET[topic_id] order by post_create_time asc";
$get_posts_res = mysql_query($get_posts,$conn) or die("Could not get posts information.".mysql_error());
if($posts_info = mysql_fetch_array($get_posts_res)){
	$post_id = $posts_info['post_id'];
}
/**********************************************/

if($currentpage==1){
//get the topic title
$topic_title = stripslashes(mysql_result($verify_topic_res,0,'topic_title'));

//display logo 
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

<a href=\"#\"><img src=\"images/logotext.gif\" id=\"logotext\" /></a>  <!--将logotext摆进来，其位置在#logotext中已进行定义 -->";

$display_block .="<div id=\"panel\">
<h1>无限欢乐的话题</h1>
<a href='topiclist.php'>返回论坛 </a>";

//create the display string
$display_block .= "
<p>  <a href=top.php?topic_id=$topic_id style=\"text-decoration:none\" > 置顶 </a>  <a href=untop.php?topic_id=$topic_id style=\"text-decoration:none\"> 取消置顶 </a>  </p>
<P> 《 <strong>$topic_title</strong> 》：</p>

<table width=100% cellpadding=3 cellspacing=1 border=1>
<tr>
<th>AUTHOR</th>
<th>POST</th>
</tr>";

//gather the posts
$get_posts = "select post_id, post_text, date_format(post_create_time, '%b %e %Y at %r') as fmt_post_create_time, post_owner from forum_posts where topic_id = $_GET[topic_id] order by post_create_time asc";
$get_posts_res = mysql_query($get_posts,$conn) or die("Could not get posts information.".mysql_error());
if($posts_info = mysql_fetch_array($get_posts_res)){
	$post_id = $posts_info['post_id'];
	$post_text = nl2br(stripslashes($posts_info['post_text']));
	$post_create_time = $posts_info['fmt_post_create_time'];
	$post_owner = stripslashes($posts_info['post_owner']);
	
	$get_posts_profile = "select id, profile, signature from is_user where username='$post_owner'";
	$get_posts_profile_res = mysql_query($get_posts_profile,$conn) or die("Could not get posts profile.".mysql_error());
	$post_profile = mysql_fetch_array($get_posts_profile_res);  
        $post_uid = $post_profile['id'];  //显示uid
	$post_head = $post_profile['profile'];  //显示头像
	$post_signature = $post_profile['signature'];   //显示签名

      	//add to display posts
	$display_block .= "
		<tr>
		<td width=35% valign=top><img src='$post_head'width=100 height=100> <br> 
		                         <a href=personalpage.php?uid=$post_uid>$post_owner</a>($post_signature)<br>
		                         [$post_create_time]</td>
		<td width=65% valign=top>$post_text<br><br>
                <a href=\"modify2.php?topic_id=$topic_id&post_id=$post_id\"><strong>修改</strong></a>
		<a href=\"replytopost2.php?topic_id=$topic_id&post_id=$post_id\"><strong>回复</strong></a>
                <a href=\"delete2.php?topic_id=$topic_id&post_id=$post_id\"><strong>删除</strong></a></td>
		</tr>";
}else{
	$display_block .="Can not find any records!";
}	


//gather the reply posts
$offset_page1=$offset+1;
$rowsperpage1=$rowsperpage-1;   //对第一页要特殊处理，因为是楼主本楼
$get_reply_posts = "select reply_post_id, post_text, date_format(post_create_time, '%b %e %Y at %r') as fmt_post_create_time, post_owner from forum_reply_posts where topic_id = $_GET[topic_id] order by post_create_time asc limit $offset_page1,$rowsperpage1";
$get_reply_posts_res = mysql_query($get_reply_posts,$conn) or die("Could not get reply posts information.".mysql_error());


while ($posts_info = mysql_fetch_array($get_reply_posts_res)) {
	$reply_post_id = $posts_info['reply_post_id'];
	$post_text = nl2br(stripslashes($posts_info['post_text']));
	$post_create_time = $posts_info['fmt_post_create_time'];
	$post_owner = stripslashes($posts_info['post_owner']);

	$get_posts_profile = "select id, profile, signature from is_user where username='$post_owner'";
	$get_posts_profile_res = mysql_query($get_posts_profile,$conn) or die("Could not get posts profile.".mysql_error());
	$post_profile = mysql_fetch_array($get_posts_profile_res);
        $post_uid = $post_profile['id'];  //显示签名  
	$post_head = $post_profile['profile'];    //显示头像
	$post_signature = $post_profile['signature'];  //显示签名

	//add to display reply posts
	$display_block .= "
	<tr>
	<td width=35% valign=top><img src='$post_head'width=100 height=100> <br> 
	                         <a href=personalpage.php?uid=$post_uid>$post_owner</a>($post_signature)<br>
	                         [$post_create_time]</td>
	<td width=65% valign=top>$post_text<br><br>
        <a href=\"modify.php?topic_id=$topic_id&reply_post_id=$reply_post_id\"><strong>修改</strong></a>
	<a href=\"replytopost.php?topic_id=$topic_id&reply_post_id=$reply_post_id\"><strong>回复</strong></a>
        <a href=\"delete.php?topic_id=$topic_id&reply_post_id=$reply_post_id\"><strong>删除</strong></a></td>
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
		$display_block .= "<a href='{$_SERVER['PHP_SELF']}?topic_id=$topic_id&currentpage=1'> << </a>";
		//前一页的页数
		$prevpage = $currentpage - 1;
		//使用<连结回前一页
		$display_block .= "<a href='{$_SERVER['PHP_SELF']}?topic_id=$topic_id&currentpage=$prevpage'> < </a>";
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
	        		$display_block .= "<a href='{$_SERVER['PHP_SELF']}?topic_id=$topic_id&currentpage=$x'>$x</a>";
			}//END ELSE
		}//END IF
	}//END FOR

	//如果不是最后一页，显示跳往下一页及最后一页的连结
	if($currentpage != $totalpages){
		//下一页的页数
		$nextpage = $currentpage + 1; 
		//显示跳往下一页的连结
		$display_block .= "<a href='{$_SERVER['PHP_SELF']}?topic_id=$topic_id&currentpage=$nextpage'> > </a>";
		//显示跳往最后一页的连结
		$display_block .= "<a href='{$_SERVER['PHP_SELF']}?topic_id=$topic_id&currentpage=$totalpages'> >> </a>";
	}//END IF
	/**********完成建立分页连结*********/

}else{   //处理非第一页

//get the topic title
$topic_title = stripslashes(mysql_result($verify_topic_res,0,'topic_title'));

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

<a href=\"#\"><img src=\"images/logotext.gif\" id=\"logotext\" /></a>  <!--将logotext摆进来，其位置在#logotext中已进行定义 -->";

$display_block .="<div id=\"panel\">
<h1>无限欢乐的话题</h1>
<a href='topiclist.php'>返回论坛 </a>";

//create the display string
$display_block .= "
<p>  <a href=top.php?topic_id=$topic_id style=\"text-decoration:none\" > 置顶 </a>  <a href=untop.php?topic_id=$topic_id style=\"text-decoration:none\"> 取消置顶 </a>  </p>
<P> 《 <strong>$topic_title</strong> 》：</p>

<table width=100% cellpadding=3 cellspacing=1 border=1>
<tr>
<th>AUTHOR</th>
<th>POST</th>
</tr>";

//gather the reply posts
$get_reply_posts = "select reply_post_id, post_text, date_format(post_create_time, '%b %e %Y at %r') as fmt_post_create_time, post_owner from forum_reply_posts where topic_id = $_GET[topic_id] order by post_create_time asc limit $offset,$rowsperpage";
$get_reply_posts_res = mysql_query($get_reply_posts,$conn) or die("Could not get reply posts information.".mysql_error());


while ($posts_info = mysql_fetch_array($get_reply_posts_res)) {
	$reply_post_id = $posts_info['reply_post_id'];
	$post_text = nl2br(stripslashes($posts_info['post_text']));
	$post_create_time = $posts_info['fmt_post_create_time'];
	$post_owner = stripslashes($posts_info['post_owner']);

	$get_posts_profile = "select id, profile, signature from is_user where username='$post_owner'";
	$get_posts_profile_res = mysql_query($get_posts_profile,$conn) or die("Could not get posts profile.".mysql_error());
	$post_profile = mysql_fetch_array($get_posts_profile_res);  
        $post_uid = $post_profile['id'];  //显示uid
	$post_head = $post_profile['profile'];    //显示头像
	$post_signature = $post_profile['signature'];  //显示签名

	//add to display reply posts
	$display_block .= "
	<tr>
	<td width=35% valign=top><img src='$post_head'width=100 height=100> <br> 
	                         <a href=personalpage.php?uid=$post_uid> $post_owner </a> ($post_signature)<br>
	                         [$post_create_time]</td>
	<td width=65% valign=top>$post_text<br><br>
        <a href=\"modify.php?topic_id=$topic_id&reply_post_id=$reply_post_id\"><strong>修改</strong></a>
	<a href=\"replytopost.php?topic_id=$topic_id&reply_post_id=$reply_post_id\"><strong>回复</strong></a>
        <a href=\"delete.php?topic_id=$topic_id&reply_post_id=$reply_post_id\"><strong>删除</strong></a></td>
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
		$display_block .= "<a href='{$_SERVER['PHP_SELF']}?topic_id=$topic_id&currentpage=1'> << </a>";
		//前一页的页数
		$prevpage = $currentpage - 1;
		//使用<连结回前一页
		$display_block .= "<a href='{$_SERVER['PHP_SELF']}?topic_id=$topic_id&currentpage=$prevpage'> < </a>";
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
	        		$display_block .= "<a href='{$_SERVER['PHP_SELF']}?topic_id=$topic_id&currentpage=$x'>$x</a>";
			}//END ELSE
		}//END IF
	}//END FOR

	//如果不是最后一页，显示跳往下一页及最后一页的连结
	if($currentpage != $totalpages){
		//下一页的页数
		$nextpage = $currentpage + 1; 
		//显示跳往下一页的连结
		$display_block .= "<a href='{$_SERVER['PHP_SELF']}?topic_id=$topic_id&currentpage=$nextpage'> > </a>";
		//显示跳往最后一页的连结
		$display_block .= "<a href='{$_SERVER['PHP_SELF']}?topic_id=$topic_id&currentpage=$totalpages'> >> </a>";
	}//END IF
	/**********完成建立分页连结*********/
}//END ELSE

}

}else{
	$_SESSION["admin"]=false;
	echo "<script>alert('请先登录！');window.location='index.html';</script>";
}
?>
<html>
<head>
<title>Posts in Topic</title>

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

#logo { position:absolute; top:0px; left:10px; }

#logotext { position:absolute; top:0px; left:120px; }

#panel { position:absolute; top:10px; left:251px; right:0px}

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
<h1>无限欢乐的话题</h1>
<?php echo "<a href='topiclist.php'>返回论坛 </a>"; ?> -->
<?php echo $display_block; ?>
</body>
</html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
