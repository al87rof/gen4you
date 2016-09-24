<?php
require('classes/AFaucet.php');
require('classes/Hoard.php');
require('createDB.php');
define('ROOT', dirname(__FILE__));

//Просмотр full log
if(isset($_POST['viewlog'])){
   $file = ROOT.'/components/relog.txt';
   $f = fopen($file, "r");
   flock($f, 2);
   while ($line = fgets($f)) {
      echo $line.'<br>';}
   flock($f, 3);
   fclose($f);

 echo'<form method="post" action="">
      <input type="submit" name="exit" value="exit">
      </form>';
exit();
}

//Просмотр last day log
if(isset($_POST['viewlastdaylog'])){
   $file = ROOT.'/components/relog.txt';
   $f = fopen($file, "r");
   flock($f, 2);
   while ($line = fgets($f)) {
       if(stristr($line, date("Y-m-d"))){
           echo $line.'<br>';
       }       
    }
   flock($f, 3);
   fclose($f);

 echo'<form method="post" action="">
      <input type="submit" name="exit" value="exit">
      </form>';
exit();
}

//Синхронизация
if (isset($_POST['ApiFaucet']) && !empty($_POST['ApiFaucet'])) {

    $tmp = $_POST['ApiFaucet'];
    $goodProxyArray = unserialize(base64_decode($tmp));
    echo '<pre>';
    print_r($goodProxyArray);

    if (is_array($goodProxyArray)) {

    $result = implode("",$goodProxyArray);
        $file = ROOT.'/components/goodProxy.txt';
        $f = fopen($file, "a+");
        flock($f, 2);
        ftruncate($f, 0);
        fwrite($f, $result);
        flock($f, 3);
        fclose($f);

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

$time = date("Y-m-d H:i:s");
$timestamp = time();

$query = "update partners SET status='active', timestamp=$timestamp, time='$time' WHERE status='pending'";
$result = mysql_query($query) or die ('MySQL error: '.mysql_error());

$query = "select PID, timestamp FROM partners WHERE status = 'nofunds'";
$result = mysql_query($query) or die ('MySQL error: '.mysql_error());

while($row = mysql_fetch_row($result)){
 if($timestamp - $row[1] >= 7200){
    $query = "update partners SET status='active', timestamp=$timestamp, time='$time' WHERE PID='$row[0]'";
    $result2 = mysql_query($query) or die ('MySQL error: '.mysql_error());
 }
}

$file = ROOT.'/classes/my_cookies.txt';
   $f = fopen($file, "a+");
   flock($f, 2);
   ftruncate($f, 0);
   flock($f, 3);
   fclose($f);

 echo "List saved";
 }else{
    echo "ERROR API POST REQUEST";
     return false;
 }
}

//Обратная связь
if( isset($_POST['ApiBack']) && !empty($_POST['ApiBack'])){
    
  $EmptyFile = ROOT.'/components/empty.txt'; 
  $ZeroFile = ROOT.'/components/zero.txt';
  $BadFile = ROOT.'/components/badlist.txt';
  $listIP = array();
  $listEmpty = array();
  $listZero = array();
  $listBad = array();
  $listBack = array();
  //База
  $query = "select address from list";
  $result = mysql_query($query) or die ('MySQL error: '.mysql_error());
   while($row = mysql_fetch_row($result)){
   $listIP[] = $row[0];}
   mysql_free_result($result);
   
  //Файл Пустоты
  $E = fopen($EmptyFile, "r");
   flock($E, 2);
   while ($line = fgets($E)) {
      $listEmpty[] = $line;
   }
   flock($E, 3);
   fclose($E);
   
  //Файл с кодом 0
  $Z = fopen($ZeroFile, "r");
   flock($Z, 2);
   while ($line = fgets($Z)) {
      $listZero[] = $line;
   }
   flock($Z, 3);
   fclose($Z); 
  //Файл BadList
  $B = fopen($BadFile, "r");
   flock($B, 2);
   while ($line = fgets($B)) {
      $listBad[] = $line;
   }
   flock($B, 3);
   fclose($B);
   
  //Масивы в масив
  $listBack[] = $listIP;
  $listBack[] = $listEmpty;
  $listBack[] = $listZero;
  $listBack[] = $listBad;
   
  $listBack = base64_encode(serialize($listBack));
  echo $listBack;
}

?>
<html>
 <meta charset="utf-8">
 <title>API FAUCET</title>
 <body style="background: bisque">
   <h2>FAUCET</h2>
   <h3>Stat</h3>
<?php
   $statObject = new Hoard("http://ok.ru");
   $walletCount = $statObject->getWalletCount();
   $objectCount = $statObject->getObjectCount();

    $listIP=array();
    $query = "select address from list";
    $result = mysql_query($query) or die ('MySQL error: '.mysql_error());
    while($row = mysql_fetch_row($result)){
    $listIP[] = $row[0];}
    mysql_free_result($result);
    $ipCount = count($listIP);

	if ($ipCount<20){
        $to='380684708701@sms.kyivstar.net';
        $subject='attention';
        $msg='SOS.Srochno trebyetsja vashe vmeshatelstvo v raboty saita';
        mail($to,$subject,$msg);
        }

        echo "Wallet Count: $walletCount <br>";
        echo "Object Count: $objectCount <br>";
        echo "Proxy Count: $ipCount <br>";
?>
 <form method="post" action="">
 <input type="submit" name="viewlastdaylog" value="viewLastDayLog">    
 <input type="submit" name="viewlog" value="viewlog">
 </form>
</body>
</html>