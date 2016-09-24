<?php
define('ROOT', dirname(__FILE__));
require('createDB.php');
require('classes/AFaucet.php');
require('classes/Joker.php');
require('classes/Coins4games.php');
require('classes/Coins4america.php');
require('classes/Bitcoinerz.php');
require('classes/Freesatoshiki.php');
//require('classes/Pinktussy.php');
require('classes/Starbitco.php');  //резервний 1
//require('classes/Wytewolfbtc.php');  //резервний 2



//Создаем обьекты партнеров Faucet
$checkObject = new Joker("http://freebtcoins.com");
$jokerFaucet = new Joker("http://faucet.jokertimes.co");
//$coins4games = new Coins4games("http://coins4games.com");
//$coins4america = new Coins4america("http://coins4america.com");
$bitcoinerz = new Bitcoinerz("http://bitcoinmad.xyz");
$freesatoshiki = new Freesatoshiki("http://freesatoshiki.ru");
//$pinktussy = new Pinktussy("http://faucet.pinktussy.co");


//Создаем Массив обьектов
$arrayObjectFaucet = array();
//$arrayObjectFaucet[] = $jokerFaucet;
//$arrayObjectFaucet[] = $coins4games;
//$arrayObjectFaucet[] = $coins4america;
$arrayObjectFaucet[] = $bitcoinerz;
$arrayObjectFaucet[] = $freesatoshiki;
//$arrayObjectFaucet[] = $pinktussy;



$i=0;
$cnt=count($arrayObjectFaucet);

while($i!=$cnt){
$j=$i+1;
$query = "select status FROM partners WHERE PID = '$j'";
$result = mysql_query($query) or die ('MySQL error: '.mysql_error());
$status = mysql_result($result,0);
  if($status=='pending' || $status=='nofunds'){
  unset($arrayObjectFaucet[$i]);}
$i++;
}

$cnt=count($arrayObjectFaucet);
if ($cnt==0){
$btcoins = new Starbitco("http://starbitco.in");
$polymObject = $btcoins;
$wlCount = $polymObject->getWalletCount();
$wlCount++;
if($wlCount>=40){$wlCount=0;}
$polymObject->setWalletCount($wlCount);
}
else{

$arrayObjectFaucet = array_values($arrayObjectFaucet);


$polymObjectTmp = $arrayObjectFaucet[0];
$obCount = $polymObjectTmp->getObjectCount();
$wlCount = $polymObjectTmp->getWalletCount();

if(!isset($_SESSION['user']) && $_SESSION['user'] != 'true'){
   $obCount++;
   if($obCount > count($arrayObjectFaucet)-1){
      $obCount = 0;
      $wlCount++;
      if($wlCount>=40){$wlCount=0;}
      $polymObjectTmp->setWalletCount($wlCount);
   }
   $polymObjectTmp->setObjectCount($obCount);
   $_SESSION['user'] = 'true';
   $_SESSION['countOb'] = $obCount;
   $_SESSION['countWl'] = $wlCount;
}
else{
  $obCount=$_SESSION['countOb'];
  $wlCount=$_SESSION['countWl'];
  
  if($obCount > count($arrayObjectFaucet)-1){
      $obCount = 0;
      $wlCount++;
      if($wlCount>=40){$wlCount=0;}
    $_SESSION['countOb'] = $obCount;
    $_SESSION['countWl'] = $wlCount;  
   }
}

//Получяем обьект (Эффект полиморфизма)
$polymObject = $arrayObjectFaucet[$obCount];
}
?>