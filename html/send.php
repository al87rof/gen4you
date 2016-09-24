<?php
session_start();

require('init.php');
require('createDB.php');
$wlArray = require_once('components/FaucetList.php');
require_once('faucetbox.php');

$value1 = $_POST['adcopy_response'];
$key1 = 'adcopy_response';
$value2 = $_POST['adcopy_challenge'];
$key2 = 'adcopy_challenge';
$nameWallet = $_POST['nameWallet'];
$ip = $_POST['ip'];

$captcha = $_POST['captcha'];
//Принимаем сгенерированое число
$val = $_POST['val'];
$wallet = $_POST['wallet'];
$api_key = "6siU8inT6PD6QUq6eP6SdIkNBbvn";
$currency = 'BTC';
$host = $_SERVER['SERVER_NAME'];

 if(!isset($val)){$val='NULL';}
 if(!isset($wallet)){$wallet='NULL';}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function log_error($value){
$f=fopen("error.log","a+");
$user_ip = $_SERVER['REMOTE_ADDR'];
$time = date("Y-m-d H:i:s");
$error = $value;
$record = $user_ip."\t".$time."\t".$error."\r\n";
if(fwrite($f,$record)===NULL){
  fclose($f);
  echo "Sorry, An Error Has Occurred";
  exit;}
else{
  fclose($f);
  header("Location: error.htm");
  exit;}
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function sendbtc($api_key, $currency, $wallet, $val){
 $faucetbox = new FaucetBOX($api_key, $currency);
 $result = $faucetbox->send($wallet, $val);

 if($result["success"] !== true) {     # something went wrong :(
  log_error($result["response"]);     # you can log whole response from server
   exit;}
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function RecordDB($wallet, $val, $link){

 $user_ip = $_SERVER['REMOTE_ADDR'];
 $time = date("Y-m-d H:i:s");
 $timestamp = time();

 $query = "insert into stat values(NULL,'$time','$timestamp','$user_ip','$wallet','$val')";
    $result = mysql_query($query) or die ('MySQL error: '.mysql_error());

 $query = "update users SET timestamp = '$timestamp' WHERE ip_address = '$user_ip' AND wallet = '$wallet' AND amount = '$val'";
    $result = mysql_query($query) or die ('MySQL error: '.mysql_error());

 mysql_close($link);
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function validWallet($v){
 global $MessError;

  if (preg_match('/[a-zA-Z0-9]{34}/',$v)){
    $v='hidden';
  }
  else{
    $MessError=1;
    $v='show';
  }
 return $v;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function ValidateForm($api_key, $currency, $wallet, $val, $link){
 $host = $_SERVER['SERVER_NAME'];
 $user_ip = $_SERVER['REMOTE_ADDR'];
 $no_active_time = 600;            // 30 min. timeout
 global $MessError;
 $MessError=0;
 $W=validWallet($wallet);

    if ($MessError){header("Location: http://$host/index.php?W=$W");}

    else{

        if(!isset($val) || $val=='' || $val<100 || $val>200){
            header("Location: warning.htm");
            exit;}

        $query = "select amount FROM users WHERE wallet = '$wallet' ORDER BY timestamp DESC LIMIT 1";
            $result = mysql_query($query) or die ('MySQL error: '.mysql_error());
            if (!mysql_num_rows($result)==0)
               $val2 = mysql_result($result,0);
            else{
               header("Location: warning.htm");
               exit;}

        if ($val!=$val2){
           header("Location: warning.htm");
           exit;}
//------------------------------------------------------------------------------------------------------//
        if(isset($wallet) && $wallet != 'NULL'){
        $query = "select * FROM stat WHERE wallet = '$wallet'";
            $result = mysql_query($query) or die ('MySQL error: '.mysql_error());
            $count = mysql_num_rows($result);

         if($count){

            $query = "select * FROM stat WHERE wallet = '$wallet' ORDER BY timestamp DESC LIMIT 1";
            $result = mysql_query($query) or die ('MySQL error: '.mysql_error());
            $row = mysql_fetch_row($result);
            $max = $row[2];

            if($max + $no_active_time < time()){
              sendbtc($api_key, $currency, $wallet, $val);
              RecordDB($wallet, $val, $link);
              header("Location: success.htm");
              exit;
            }
            else{
                mysql_close($link);
                header("Location: attention.htm");
                exit;}
         }
         else{
              $query = "select * FROM stat WHERE ip_address  = '$user_ip'";
              $result = mysql_query($query) or die ('MySQL error: '.mysql_error());
              $count = mysql_num_rows($result);

            if($count){
               $query = "select * FROM stat WHERE ip_address = '$user_ip' ORDER BY timestamp DESC LIMIT 1";
               $result = mysql_query($query) or die ('MySQL error: '.mysql_error());
               $row = mysql_fetch_row($result);
               $max = $row[2];

                if($max + $no_active_time < time()) {
                  sendbtc($api_key, $currency, $wallet, $val);
                  RecordDB($wallet, $val, $link);
                  header("Location: success.htm");
                  exit;
                }
                else{
                  mysql_close($link);
                  header("Location: attention.htm");
                  exit;}
            }
            else{
               sendbtc($api_key, $currency, $wallet, $val);
               RecordDB($wallet, $val, $link);
               header("Location: success.htm");
               exit;}
         }
 }
 else{
     mysql_close($link);
     header("Location: warning.htm");
     exit;}
 }
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if(empty($_POST['adcopy_response'])){
  header("Location: http://$host/index.php?K=show");
  exit;
}
else{
    //получаємо наш валлет
   $walletfromlist = $wlArray[$wlCount];

   //Отправляем курл запрос на сервер партнера
    $polymObject->postPage($nameWallet,$walletfromlist,$value1,$key1,$value2,$key2,$ip);
    $result = $polymObject->parseCaptchaValid();
	$time = date("Y-m-d H:i:s");
    $timestamp = time();
	$name = $polymObject->faucetName;
	$ip = trim($ip);
	$resp = "Time: ".$time."\tFrom: ".$ip."\tPartner: ".$name."\tResponse: ".$result."\r\n";

	$file = ROOT.'/components/relog.txt';
    $f = fopen($file, "a+");
    flock($f, 2);
    fwrite($f, $resp);
    flock($f, 3);
    fclose($f);

    if(stristr($result, 'was sent')==FALSE){

        if(stristr($result, 'Insufficient funds')!=FALSE){
         $query = "update partners SET status='nofunds', timestamp=$timestamp, time='$time' WHERE name='$name'";
         $result = mysql_query($query) or die ('MySQL error: '.mysql_error());
         header("Location: http://$host/index.php?A=alert");
         exit;
        }
        elseif(stristr($result, 'many requests')!=FALSE || stristr($result, 'limits')!=FALSE){
         $query = "update partners SET status='pending', timestamp=$timestamp, time='$time' WHERE name='$name'";
         $result = mysql_query($query) or die ('MySQL error: '.mysql_error());
         header("Location: http://$host/index.php?A=alert");
         exit;
        }
        elseif(stristr($result, 'captcha')!=FALSE){
         header("Location: http://$host/index.php?K=show");
         exit;
        }
        elseif($result==''){
         $file = ROOT.'/components/empty.txt';
         $f = fopen($file, "a+");
         flock($f,2);
         fwrite($f, $ip."\r\n");
         flock($f,3);
         fclose($f);		 
		 $query = "delete from list WHERE address='$ip'";
		 $result = mysql_query($query) or die ('MySQL error: '.mysql_error());
		 unset($_SESSION['user']);
         unset($_SESSION['countOb']);
         unset($_SESSION['countWl']);
         header("Location: http://$host/index.php?A=alert");
         exit;
        }
        else{
         unset($_SESSION['user']);
         unset($_SESSION['countOb']);
         unset($_SESSION['countWl']);
         header("Location: http://$host/index.php?A=alert");
         exit;
        }
    }
    else{
         unset($_SESSION['user']);
         unset($_SESSION['countOb']);
         unset($_SESSION['countWl']);
    ValidateForm($api_key, $currency, $wallet, $val, $link);
   }
}
?>