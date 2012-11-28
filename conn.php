<?php
$conn=mysql_connect("localhost","sam","123")or die("Connect server error".mysql_error());
mysql_select_db("myishare",$conn)or die("Connect database error".mysql_error());
mysql_query("set names 'utf8'");
?>
