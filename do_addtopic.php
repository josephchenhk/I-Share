<?php
header("Content-type: text/html; charset=utf-8");
include_once("conn.php");

//check for required fields from the form
if ((!$_POST[topic_title]) || (!$_POST[post_text])) {
	header("Location: addtopic.html");
	exit;
}else{

	$admin=false; //avoid security crisis from global variable
	session_start(); // start session
	//$aa=$_SESSION["admin"];
	//judge authetication
	if(isset($_SESSION["admin"]) && $_SESSION["admin"]==true) {
		$topic_owner=$_SESSION["adminname"];
		
		//check privilege
                $check_privilege="select privilege from is_user where username='$topic_owner'";
                $check_privilege_res=mysql_query($check_privilege,$conn)or die("checking privilege fails.".mysql_error());
                $current_privilege=mysql_result($check_privilege_res,0,'privilege');
              //  echo $current_privilege;
              //  echo "Sorry, you do not have the privilege to delete this!";

		//执行发帖
                if($current_privilege>-1){ // 已激活帐号



		//create and issue the first query
		$add_topic = "insert into forum_topics values('', '$_POST[topic_title]',now(), '$topic_owner',now() , 0 )";
		mysql_query($add_topic) or die("Could not add a topic.".mysql_error());

		//get the id of the last query
		$topic_id = mysql_insert_id();

		//create and issue the second query
		$add_post = "insert into forum_posts values('', '$topic_id', '$_POST[post_text]', now(), '$topic_owner',now())";
		mysql_query($add_post) or die("Could not post on the current topic.".mysql_error());

		//create nice message for user
		$display_block = "<P>话题 《  <strong>$_POST[topic_title]</strong>  》已添加。</p><a href='topiclist.php'>返回论坛 </a>";   

                //发帖成功，奖励积分和金钱
                //get currentuid
                $get_id = "select id from is_user where username='$topic_owner'";
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
                if($award_credit<4){
                       $usercredit = $usercredit + 1; 
                       $award_credit = $award_credit + 1;//增加积分！
                       $update_credit = "update `credit` set `usercredit`=$usercredit, `award_credit`=$award_credit where id=$currentuid";
                       $update_credit_res = mysql_query($update_credit,$conn)or die("fail to update credit record!".mysql_error());
                }
                if($award_money<30){
                       $usermoney = $usermoney + 2;
                       $award_money = $award_money + 1;  //增加财富！
                       $update_credit = "update `credit` set `usermoney`=$usermoney, `award_money`=$award_money where id=$currentuid";
                       $update_credit_res = mysql_query($update_credit,$conn)or die("fail to update credit record!".mysql_error());
                }
         
               }else{ 
	             //echo "对不起，您尚未激活帐号！";
                     echo "<script>alert('对不起，您尚未激活帐号！');window.location='addtopic.html';</script>";
               }
	}else {
		//fail to login
		$_SESSION["admin"]=false;
		//die("对不起，你不够傻逼，无权发帖。");
		die("Sorry, you have not login. <a href=login.html>Login</a>");
        }
}
?>


<html>
<head>
<title>New Topic Added</title>
</head>
<body>
<h1>话题已添加！</h1>
<?php echo $display_block;?> 
</body>
</html>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
