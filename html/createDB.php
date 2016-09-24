<?php
require_once('config.php');

$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("MySQL error: ".mysql_error());

if (!mysql_select_db(DB_DATABASE)){
  $query = 'create database IF NOT EXISTS '.DB_DATABASE;
  $result = mysql_query($query) or die ('MySQL error: '.mysql_error());
  $db_selected = mysql_select_db(DB_DATABASE,$link) or die ('MySQL error: '.mysql_error());

$query = 'create table users(
timestamp int(11) not null,
ip_address varchar(15) not null,
wallet varchar(35) not null,
amount smallint(4)unsigned not null)';
$result = mysql_query($query) or die ('MySQL error: '.mysql_error());

$query = 'create table stat(
ID int unsigned not null primary key auto_increment,
time datetime not null,
timestamp int(11) not null,
ip_address varchar(15) not null,
wallet varchar(35) not null,
amount smallint(4)unsigned not null)';
$result = mysql_query($query) or die ('MySQL error: '.mysql_error());

$query = 'create table partners(
PID int unsigned not null primary key auto_increment,
name varchar(50) not null,
status varchar(10) not null,
timestamp int(11) not null,
time datetime not null)';
$result = mysql_query($query) or die ('MySQL error: '.mysql_error());

$query = 'create table countclick(
ID int unsigned not null primary key auto_increment,
time datetime not null,
ip_address varchar(15) not null,
wallet varchar(35) not null,
timestamp int(11) not null)';
$result = mysql_query($query) or die ('MySQL error: '.mysql_error());

$query = 'create table list(
ID int unsigned not null primary key auto_increment,
address varchar(25) not null)';
$result = mysql_query($query) or die ('MySQL error: '.mysql_error());

}

else{
$db_selected = mysql_select_db(DB_DATABASE,$link) or die ('MySQL error: '.mysql_error());
}
?>