<?php //del_msg.php实现删除个人站内信
header("Content-type: text/html; charset=utf-8"); 
include_once("conn.php");

//获取$prv_msgid
$prv_msgid = $_GET[prv_msgid];

//gather the information of messages
$get_msg = "select * from prv_message where prv_msgid=$prv_msgid";
$get_msg_res = mysql_query($get_msg,$conn)or die("Could not get the information of sent message.".mysql_error());
$msg = mysql_result($get_msg_res,0,'msg');
$title = mysql_result($get_msg_res,0,'title');
$sendtime = mysql_result($get_msg_res,0,'sendtime');
$sender = mysql_result($get_msg_res,0,'sender');  
$receiver = mysql_result($get_msg_res,0,'receiver'); 

//获取个人信息
session_start();//start session
if(isset($_SESSION["admin"]) && $_SESSION["admin"]==true){ //login successfully
	$deleted_by=$_SESSION["adminname"];      

	//check privilege
        $check_privilege="select privilege from is_user where username='$deleted_by'";
        $check_privilege_res=mysql_query($check_privilege,$conn)or die("checking privilege fails.".mysql_error());
        $current_privilege=mysql_result($check_privilege_res,0,'privilege');
        //echo $current_privilege;
        //echo "Sorry, you do not have the privilege to delete this!";

         
        if($current_privilege>-1){ // 已激活帐号
             if($deleted_by==$sender){  //无权查看别人的站内信，只能查看属于自己的站内信
                    $deletesql="delete from prv_message where prv_msgid=$prv_msgid";
                    $deletesql_res=mysql_query($deletesql,$conn)or die("fail to delete the record!".mysql_error());
                    echo "<script>alert('删除本人发出的站内信成功！');window.location='msg_sendlist.php';</script>";
             }elseif($current_privilege>5){// 符合站长权限
                    $deletesql="delete from prv_message where prv_msgid=$prv_msgid";
                    $deletesql_res=mysql_query($deletesql,$conn)or die("fail to delete the record!".mysql_error());
                    echo "<script>alert('您是站长，删除站内信成功！');window.location='msg_sendlist.php';</script>";
             }else{
                    echo "<table cellpadding=5 cellspacing=4 border=1>
                   <tr>
                   <td> 对不起，您无权删除此站内信！</td>
                   </tr>
                          </table>";
             }//End if
        }else{ 
             echo "对不起，您尚未激活帐号！";
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
<a href=msg_receivelist.php>收件箱</a>
<a href=msg_sendlist.php>发件箱</a>
<a href=topiclist.php>返回论坛</a>
</p>
<?php echo $display_block; ?>
</body>
</html>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
