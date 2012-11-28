<?php 
header("Content-type: text/html; charset=utf-8");

include_once("conn.php");

if($_POST["submit"]){

$username=$_POST['username'];
	$pw1=$_POST['pw1'];
	$pw2=$_POST['pw2'];
	$email=$_POST['email'];
	$regtime=date("Y-m-j");
	$gender=$_POST['sex'];

	// Registration verification
	$checkuser="select id from is_user where username='$username'";
	$checkuser_res=mysql_query($checkuser);
        
        $checkemail="select id from is_user where email='$email'";
	$checkemail_res=mysql_query($checkemail);


        if(mysql_num_rows($checkemail_res)>0){  //check existing email
                echo"<script>alert('该邮箱已被注册！');history.back();</script>";
	}else{
                if(mysql_num_rows($checkuser_res)>0){  //check existing username
       		echo"<script>alert('该用户名已经存在，请重新输入！');history.back();</script>";
       	}else{
       		if($pw1!=$pw2){  //check whether password1 equal to password2
       			echo "<script>alert('两次输入的密码不一致，请重新输入！');history.back();</script>";
       		}else{
                              //获取密码的长度
                              $len=strlen($pw1);
                              if($len<6 || $len>16){ // check the length of the password
                                      echo "<script>alert('密码须为长度6位到16位的字母数字组合！');history.back();</script>";
                              }else{
                                    //创建验证码 用户注册信息存入数据表
                                    $activationKey =  mt_rand() . mt_rand() . mt_rand() . mt_rand() . mt_rand();
                                    
                                    //初始化is_user表
                                    $regsql="INSERT INTO `is_user`(`id`, `username`, `password`, `email`, `regtime`,`gender`,`privilege`,`activationKey`) VALUES('','$username','$pw1','$email','$regtime','$gender',-1,'$activationKey')";
                     		    $register=mysql_query($regsql);
       
                                    //初始化credit表
                                    $regsql_credit="INSERT INTO `credit`(`id`, `username`, `usertitle`, `usercredit`, `usermoney`, `usergrade`, `last_login_time`, `current_login_time`, `award_credit`, `award_money`) VALUES ('','$username','老百姓',0,0,'轻度傻逼',now(),now(),0,0)";
                     		    $register_credit=mysql_query($regsql_credit,$conn)or die("fail to initialize the table credit.".mysql_error());
  
                                    echo "An email has been sent to $_POST[email] with an activation key. Please check your mail to complete registration.";
       
                                    //$to = "clever009@126.com";//echo $to;
                                    $to = $email; 
                                    $destAddress=$to; //echo $destAddress;
                                    $fromName="ishare";
                                    $subject=" ishare Registration";
                                    $content="Welcome to our website!\r\rYou, or someone using your email address, has completed registration at ishare.com. You can complete registration by clicking the following link:\r <a href=http://147.8.166.94/ishare/verify.php?$activationKey> http://147.8.166.94/isharebeta/verify.php?$activationKey </a> \r\rIf this is an error, ignore this email and you will be removed from our mailing list.\r\rRegards,\r ishare Team";
                                    require_once 'class.phpmailer.php';      //视情况改动  
                                    $mail = new PHPMailer (); //得到一个PHPMailer实例  
                                      
                                  
                                    $mail->CharSet = "UTF-8";  
                             $mail->IsSMTP (); //设置采用SMTP方式发送邮件  
                             $mail->Host = "smtp.126.com"; //设置邮件服务器的地址  
                             $mail->Port = 25; //设置邮件服务器的端口，默认为25  
                               
                           
                             $mail->From = "ishare_team@126.com"; //设置发件人的邮箱地址  
                             $mail->FromName = $fromName; //设置发件人的姓名  
                             $mail->SMTPAuth = true; //设置SMTP是否需要密码验证，true表示需要  
                               
                           
                             $mail->Username = "ishare_team";    //你登录 163 的用户名  
                             $mail->Password = '19820226';  
                             $mail->Subject = $subject; //设置邮件的标题  
                               
                           
                             $mail->AltBody = "text/html"; // optional, comment out and test  
                             $mail->Body = $content;  
                               
                             $mail->IsHTML ( true ); //设置内容是否为html类型  
                             //$mail->WordWrap = 50;                                 //设置每行的字符数  
                             $mail->AddReplyTo ( "ishare_team@126.com", $fromName); //设置回复的收件人的地址  
                               
                           
                             if (is_array ( $destAddress )) {  
                                 foreach ( $destAddress as $address ) {  
                                     $mail->AddAddress ( $address ); //设置收件的地址  
                                 }  
                             } else {  
                                 $mail->AddAddress ( $destAddress ); //设置收件的地址  
                             }  
                               
                             if (! $mail->Send ()) { //发送邮件  
                                 return FALSE;  
                             } else{  
                             //return true;  
                              
                         	//$to = $_POST['email'];
                              //$subject = " ishare.com Registration";
                              //$message = "Welcome to our website!\r\rYou, or someone using your email address, has completed registration at ishare.com. You can complete registration by clicking the following link:\rhttp://147.8.166.94/ishare/verify.php?$activationKey\r\rIf this is an error, ignore this email and you will be removed from our mailing list.\r\rRegards,\r ishare.com Team";
                              //$headers = 'From: googolphys@gmail.com' . "\r\n" .'Reply-To: googolphys@gmail.com' . "\r\n" .'X-Mailer: PHP/' . phpversion();
                              //mail($to, $subject, $message, $headers);

                              //验证激活代码 verify.php

              	              echo "<script>alert('恭喜，注册成功！请登录');window.location='login.html';</script>";
                              }
                        }
		}
	}
       }

}
?>
