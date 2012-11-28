<?php //read_msg_sendlist.php显示个人站内信收件箱列表
header("Content-type: text/html; charset=utf-8");
include_once("conn.php");

//获取个人信息
session_start();//start session
if(isset($_SESSION["admin"]) && $_SESSION["admin"]==true){ //login successfully
	$currentuser=$_SESSION["adminname"];

        //Count the number of records
        $get_msg_numbers = "select count(*) from prv_message where receiver='$currentuser'";
        $get_msg_numbers_res = mysql_query($get_msg_numbers,$conn) or die("Connect error.".mysql_error());
        $r = mysql_fetch_row($get_msg_numbers_res);
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

        //gather the topics of messages
        $get_msg = "select prv_msgid, msg,title,sendtime,sender,receiver from prv_message where receiver='$currentuser' order by sendtime desc limit $offset,$rowsperpage";
        $get_msg_res = mysql_query($get_msg,$conn)or die("Could not get the information of sent message.".mysql_error());
        //$msg = mysql_result($get_id_res,0,'msg');
        //$title = mysql_result($get_id_res,0,'title');
        //$sendtime = mysql_result($get_id_res,0,'sendtime');
        //$sender = mysql_result($get_id_res,0,'sender');   
        //$receiver = mysql_result($get_id_res,0,'receiver'); 
        //gather the topics
        //$get_topics = "select topic_id, topic_title, date_format(topic_create_time,  '%b %e %Y at %r') as fmt_topic_create_time, topic_owner from forum_topics order by topic_create_time desc limit $offset,$rowsperpage";
        //$get_topics_res = mysql_query($get_topics,$conn) or die("Connect error.".mysql_error());

        if (mysql_num_rows($get_msg_res) < 1) {
        	//there are no topics, so say so
        	$display_block = "<P><em>暂无站内信.</em></p>";
        } else {
                //create the display string
        	$display_block = "
                
        	<table cellpadding=5 cellspacing=4 border=1>
        	<tr>
        	<th>话题</th>
        	<th>来信人</th>
                <th>发送时间</th>
        	</tr>";

                while ($msg_info = mysql_fetch_array($get_msg_res)) {
                        $prv_msgid = $msg_info['prv_msgid'];
        		$msg = $msg_info['msg'];
                        $title = stripslashes($msg_info['title']);
                        $sendtime = $msg_info['sendtime'];
                        $sender = $msg_info['sender'];
                        $receiver = $msg_info['receiver'];
        		
                     	//get number of messages
        		//$get_num_msg = "select count(reply_post_id) from forum_reply_posts where topic_id = $topic_id";
        		//$get_num_posts_res = mysql_query($get_num_posts,$conn) or die(mysql_error());
        		//$num_posts = mysql_result($get_num_posts_res,0,'count(reply_post_id)');
        
        		//add to display
        		$display_block .= "
        		<tr>
        		<td align=center>$title</td>
        		<td align=center>$sender</td>
                        <td align=center>$sendtime</td>
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
        		echo "<a href='{$_SERVER['PHP_SELF']}?currentpage=1'> << </a>";
        		//前一页的页数
        		$prevpage = $currentpage - 1;
        		//使用<连结回前一页
        		echo "<a href='{$_SERVER['PHP_SELF']}?currentpage=$prevpage'> < </a>";
        	}//END IF

                //显示当前分页临近的分页页数
        	for($x=(($currentpage-$range)-1); $x<(($currentpage+$range)+1); $x++){
        		//如果这是一个正确的页数...
        		if(($x>0) && ($x<=$totalpages)){
        	        	if($x == $currentpage){
        	        		//不使用连结，但用高亮度显示
        	        		echo "[<b>$x</b>]";
        	        		//如果这一页不是当前页数...
        	        	}else{
        	        		//显示连结
        	        		echo "<a href='{$_SERVER['PHP_SELF']}?currentpage=$x'>$x</a>";
        			}//END ELSE
        		}//END IF
        	}//END FOR

                //如果不是最后一页，显示跳往下一页及最后一页的连结
        	if($currentpage != $totalpages){
        		//下一页的页数
        		$nextpage = $currentpage + 1;
        		//显示跳往下一页的连结
        		echo "<a href='{$_SERVER['PHP_SELF']}?currentpage=$nextpage'> > </a>";
        		//显示跳往最后一页的连结
        		echo "<a href='{$_SERVER['PHP_SELF']}?currentpage=$totalpages'> >> </a>";
        	}//END IF
        	/**********完成建立分页连结*********/
        }
        
}else{
	$_SESSION["admin"]=false;
	die("Sorry, you have not login. <a href=login.html>Login</a>");
}

?>
<html>
<head>
<title>Private Message</title>
</head>
<body>
<BODY background="image/background4.jpg">
<h1>站内信</h1>
<p>
</p>
<?php echo $display_block; ?>
</body>
</html>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
