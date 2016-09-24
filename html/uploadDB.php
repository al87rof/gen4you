<?php
require('createDB.php');
define('ROOT', dirname(__FILE__));

$file = ROOT.'/components/goodProxy.txt';

$q=array();
$f = fopen($file, "r");

 while ($line = fgets($f)) {
   $line=trim($line);
   $q[] = "(NULL,"."'".$line."'".")";
 }
fclose($f);
$q=implode(",", $q);

$query = 'truncate table list';
$result = mysql_query($query) or die ('MySQL error: '.mysql_error());

$query = "insert into list VALUES $q";
$result = mysql_query($query) or die ('MySQL error: '.mysql_error());

echo "Database successfully updated !";

?>