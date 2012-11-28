<?php
header("Content-type: text/html; charset=utf-8"); 
include_once("conn.php");
//start a new conversation
//session_start();
//declare a variable "admin", and initialize it as null
//$_SESSION["admin"]=null;
?>
<?php
if($_POST["submit"]) { //submit login
	$posts=$_POST;
	// trim space
	foreach($posts as $key => $value){
		$posts[$key]=trim($value);
	}
	$password=$posts["pw"];
	$username=$posts["username"];

	$logsql="select username from is_user where username='$username' and password='$password'";
	$logres=mysql_query($logsql);

	if(mysql_num_rows($logres)==1) { // verified, start session
		session_start();
		//set "admin" as true
		$_SESSION["admin"]=true;
		$_SESSION["adminname"]=$username;

                //get currentuid
                $get_id = "select id from is_user where username='$username'";
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
               
                //更新login_time
                $update_login_time = "UPDATE `credit` SET `last_login_time`='$current_login_time',`current_login_time`=now() WHERE id=$currentuid";
                $update_login_time_res = mysql_query($update_login_time,$conn)or die("fail to update login_time.".mysql_error());

                //登录加分
                //get time information
                $get_time = "select date_format(last_login_time,'%Y-%m-%d') as fmt_last_login_time, date_format(current_login_time,'%Y-%m-%d') as fmt_current_login_time from credit where id=$currentuid";  //只取出日期进行比较
                $get_time_res = mysql_query($get_time,$conn)or die("fail to get time.".mysql_error());
                $last_login_time = mysql_result($get_time_res,0,'fmt_last_login_time');
                $current_login_time = mysql_result($get_time_res,0,'fmt_current_login_time'); 
                //echo $last_login_time;echo $current_login_time;
                // 更新credit的usercredit，usermoney，award_credit,award_money
                if($last_login_time!=$current_login_time){
                       $award_credit = 0;
                       $award_money = 0; //对积分和财富增添记录重置为零
                       $usercredit = $usercredit + 2; 
                       $usermoney = $usermoney + 5;
                       $award_credit = $award_credit + 1;
                       $award_money = $award_money + 1;  //增加积分和财富！
                       $update_credit = "update `credit` set `usercredit`=$usercredit, `usermoney`=$usermoney, `award_credit`=$award_credit,`award_money`=$award_money where id=$currentuid";
                       $update_credit_res = mysql_query($update_credit,$conn)or die("fail to update credit record!".mysql_error());
                }
		echo "<script>alert('登录成功！');window.location='index.php';</script>";
	}else {
		echo "<script>alert('请输入正确的用户名和密码！');history.back();</script>";
	}
}
?>


