<?php
require_once('createDB.php');
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function ValidateAJAX($fieldValue){
$fieldValue = trim($fieldValue);
  if (isset($fieldValue) && $fieldValue!='')
      return 1;
  else
      return 0;
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function RecordToBD($value,$wallet,$link){
$user_ip = $_SERVER['REMOTE_ADDR'];
$time = time();

$query = "insert into users values('$time','$user_ip','$wallet','$value')";
       $result = mysql_query($query) or die ('MySQL error: '.mysql_error());
       mysql_close($link);
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function ValidateRecord($wallet, $link){
$user_ip = $_SERVER['REMOTE_ADDR'];
$no_active_time = 600;

    if(isset($wallet) && $wallet!=''){
        $query = "select * FROM users WHERE wallet = '$wallet'";
            $result = mysql_query($query) or die ('MySQL error: '.mysql_error());
            $count = mysql_num_rows($result);

         if($count){
                $query = "select * FROM users WHERE wallet = '$wallet' ORDER BY timestamp DESC LIMIT 1";
                $result = mysql_query($query) or die ('MySQL error: '.mysql_error());
                $row = mysql_fetch_row($result);
                $max = $row[0];
                mysql_close($link);

           if($max+$no_active_time > time()){
                return $row[3];}
           else {return 0;}
         }
         else{
           mysql_close($link);
           return 0;}
    }
    else{
        $query = "select * FROM users WHERE ip_address = '$user_ip'";
        $result = mysql_query($query) or die ('MySQL error: '.mysql_error());
        $count = mysql_num_rows($result);

        if($count){
           $query = "select * FROM users WHERE ip_address = '$user_ip' ORDER BY timestamp DESC LIMIT 1";
           $result = mysql_query($query) or die ('MySQL error: '.mysql_error());
           $row = mysql_fetch_row($result);
           $max = $row[0];
           mysql_close($link);

           if($max+$no_active_time > time()){
                return $row[3];}
           else {return 0;}
        }
        else{
           mysql_close($link);
           return 0;}
    }
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function checkTime($link,$Low){
 $user_ip = $_SERVER['REMOTE_ADDR'];

 $query = "select * FROM users WHERE ip_address  = '$user_ip'";
            $result = mysql_query($query) or die ('MySQL error: '.mysql_error());
            $count = mysql_num_rows($result);

        if($count){
           $query = "select * FROM users WHERE ip_address = '$user_ip' ORDER BY timestamp DESC LIMIT 1";
           $result = mysql_query($query) or die ('MySQL error: '.mysql_error());
           $row = mysql_fetch_row($result);
           $timestamp = $row[0];
           }
        else{
           $timestamp = 0;
        }

$time = time();
$datetime = date("Y-m-d H:i:s");

 if ($timestamp){
  $z = $time-$timestamp;
  if($z>600) $interval=5;
  else $interval = 600-$z;
 }
 else $interval = 600;

 if(isset($Low) && $Low==1){

   $query = "select wallet FROM stat WHERE ip_address  = '$user_ip' ORDER BY timestamp DESC LIMIT 1";
   $result = mysql_query($query) or die ('MySQL error: '.mysql_error());
   $row = mysql_fetch_row($result);
   $wallet = $row[0];

   $query = "select * FROM countclick WHERE wallet = '$wallet'";
   $result = mysql_query($query) or die ('MySQL error: '.mysql_error());
   $count = mysql_num_rows($result);

   if ($count){
       $query = "select timestamp FROM countclick WHERE wallet = '$wallet' ORDER BY timestamp DESC LIMIT 1";
       $result = mysql_query($query) or die ('MySQL error: '.mysql_error());
       $row = mysql_fetch_row($result);
       $result = $row[0];

       if($time-$result>14400){
           $query = "insert into countclick values(NULL,'$datetime','$user_ip','$wallet','$time')";
           $result = mysql_query($query) or die ('MySQL error: '.mysql_error());

           $interval = 60;
           $timestamp = $time - 1740;
           $query = "update users SET timestamp = '$timestamp' WHERE ip_address = '$user_ip' ORDER BY timestamp DESC LIMIT 1";
           $result = mysql_query($query) or die ('MySQL error: '.mysql_error());
           $query = "update stat SET timestamp = '$timestamp' WHERE ip_address = '$user_ip' ORDER BY timestamp DESC LIMIT 1";
           $result = mysql_query($query) or die ('MySQL error: '.mysql_error());
       }else{
           $query = "insert into countclick values(NULL,'$datetime','$user_ip','$wallet','$time')";
           $result = mysql_query($query) or die ('MySQL error: '.mysql_error());}
  }else{
           $query = "insert into countclick values(NULL,'$datetime','$user_ip','$wallet','$time')";
           $result = mysql_query($query) or die ('MySQL error: '.mysql_error());

           $interval = 60;
           $timestamp = $time - 1740;
           $query = "update users SET timestamp = '$timestamp' WHERE ip_address = '$user_ip' ORDER BY timestamp DESC LIMIT 1";
           $result = mysql_query($query) or die ('MySQL error: '.mysql_error());
           $query = "update stat SET timestamp = '$timestamp' WHERE ip_address = '$user_ip' ORDER BY timestamp DESC LIMIT 1";
           $result = mysql_query($query) or die ('MySQL error: '.mysql_error());
  }
 }

 mysql_close($link);
 return $interval;
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if(isset($_POST['Value']) && isset($_POST['Wallet'])){
$value = $_POST['Value'];
$wallet = $_POST['Wallet'];
RecordToBD($value,$wallet,$link);
}

if(isset($_POST['ch'])){
 if(isset($_POST['w'])) $wallet=$_POST['w'];
 else $wallet='';
$res=ValidateRecord($wallet, $link);
echo $res;
}

if(isset($_POST['fieldValue'])){
$fieldValue=$_POST['fieldValue'];
$res = ValidateAJAX($fieldValue);
echo $res;
}

if(isset($_POST['ch_time'])){
  if(isset($_POST['low'])) $Low=$_POST['low'];
  else $Low='';
$res = checkTime($link,$Low);
echo $res;
}
?>