<?php
require('createDB.php');

$query = 'drop table partners';
$result = mysql_query($query) or die ('MySQL error: '.mysql_error());

$query = 'create table partners(
PID int unsigned not null primary key auto_increment,
name varchar(50) not null,
status varchar(10) not null,
timestamp int(11) not null,
time datetime not null)';
$result = mysql_query($query) or die ('MySQL error: '.mysql_error());

$time = date("Y-m-d H:i:s");
$timestamp = time();

$query = "insert into partners values
(NULL,'http://bitcoinmad.xyz','active','$timestamp','$time'),
(NULL,'http://freesatoshiki.ru','active','$timestamp','$time')";

$result = mysql_query($query) or die ('MySQL error: '.mysql_error());

echo 'All correct';
?>