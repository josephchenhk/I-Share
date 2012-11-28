<?php //read_msg_send.php实现显示个人站内信发件箱内容
header("Content-type: text/html; charset=utf-8"); 
include_once("conn.php");

//获取$prv_msgid
$prv_msgid = $_GET[prv_msgid];

//获取个人信息
session_start();//start session
if(isset($_SESSION["admin"]) && $_SESSION["admin"]==true){ //login successfully
	$currentuser=$_SESSION["adminname"];      

        //gather the information of messages
        $get_msg = "select msg,title,sendtime,sender,receiver from prv_message where prv_msgid=$prv_msgid";
        $get_msg_res = mysql_query($get_msg,$conn)or die("Could not get the information of sent message.".mysql_error());
        $msg = mysql_result($get_msg_res,0,'msg');
        $title = mysql_result($get_msg_res,0,'title');
        $sendtime = mysql_result($get_msg_res,0,'sendtime');
        $sender = mysql_result($get_msg_res,0,'sender');   
        $receiver = mysql_result($get_msg_res,0,'receiver'); 

                //create the display string
        	$display_block = "
                
        	<table cellpadding=5 cellspacing=4 border=1>
        	<tr>
        	<td>$sendtime发送给$receiver的站内信</td>
                <td>$title</td> <br>
                <td>$msg</td>
        	</tr>
                </table>";

                
        
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
