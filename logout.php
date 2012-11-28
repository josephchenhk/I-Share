<?php
header("Content-type: text/html; charset=utf-8"); 
session_start();
unset($_SESSION['admin']);
session_destroy();
echo "<script>alert('您已成功退出！');window.location='index.html';</script>";
?>
