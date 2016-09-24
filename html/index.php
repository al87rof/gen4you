<?php
if (session_id() == ""){
 session_start();
}
require('init.php');

if(isset($_GET['W'])) $W=$_GET['W'];
else $W='hidden';

if(isset($_GET['K'])) $K=$_GET['K'];
else $K='hidden';

if(isset($_GET['A'])){
  $A=$_GET['A'];
  $Y='hidden';
  }
else{
  $Y='alert2';
  $A='hidden';
}

//--------------------------------------------------------//
$captcha = '';
while($captcha==''){
$ip = $polymObject->sortArray($checkObject);
$polymObject->getPage($ip,7);
$pageHtml = $polymObject->page;

if(!empty($pageHtml['content']) && $pageHtml['content']!=''){

if(stristr($pageHtml['content'], 'DNS resolution error')!=FALSE){
  $query = "delete from list WHERE address='$ip'";
  $result = mysql_query($query) or die ('MySQL error: '.mysql_error());
  $file = ROOT.'/components/badlist.txt';
  $f = fopen($file, "a+");
  flock($f,2);
  fwrite($f, $ip."\r\n");
  flock($f,3);
  fclose($f);
}
else{
$captcha = $polymObject->parseCaptcha();
$nameWallet = $polymObject->parseNameTextFieldWallet();
}}}
//------------------------------------------------------//
?>
<!DOCTYPE HTML><html lang="en"><head><title>Freegen | Get free Bitcoin</title><meta charset="utf-8"><meta name="description" content="Get free Bitcoin"><meta name="keywords" content="Bitcoin, satoshi, bitcoin faucet, earn Bitcoin, get Bitcoin, free Bitcoin"><meta name="copyright" content="NZT team"><meta name="document-state" content="Dynamic"><meta name="referrer" content="never"><meta name="robots" content="index,nofollow"><meta name="viewport" content="width=device-width"><link type="image/x-icon" href="favicon.ico" rel="icon"><link type="text/css" href="css/style.css" rel="stylesheet" /><link type="text/css" href="css/adapt.css" rel="stylesheet" /><script type="text/javascript" src="js/jquery-1.8.0.min.js"></script><script type="text/javascript" src="js/random.js"></script><script type="text/javascript" src="js/validation.js"></script><script type="text/javascript" src="js/ga.js"></script><script type="text/javascript">document.createElement('section');document.createElement('header');document.createElement('article');document.createElement('footer');$(document).ready(function(){$.post("check.php", {ch: 1}, function(res){if(res==0){$("#wallet").removeAttr('disabled');window.inid = window.setInterval('func1()', 200);}else{$(".digit").text(res);$(".gdigit").attr("value",res);var c = $("p").hasClass("show");var c2 = $("p").hasClass("alert");if(c||c2){$("#wallet").removeAttr('disabled');}else{$("#wallet").attr('disabled','disabled');document.location.href="attention.htm";}}});$(":input[type='text']").focus(function(){$(this).addClass('focus');}).blur(function(){$(this).removeClass();var elemID = $(this).attr('id');var value = $(this).attr('value');$.post("check.php", {fieldValue: value}, function(result){var elemid = elemID + "Failed";result==0 ? $("p[id='"+elemid+"']").removeClass().addClass('show') : $("p[id='"+elemid+"']").removeClass().addClass('hidden');});});});</script></head><body><section class="main"><header class="header"><table><tr><td class="area">&nbsp;</td><td class="headerbg"><article class="money"><p class="digit"></p></article></td> <td class="area">&nbsp;</td></tr></table></header><section class="bodybg"><br><noscript><article class="warning"> Javascript is disabled in your web browser.<br> To use this site, please enable Javascript and reload the page.</article></noscript><form class="form1" name="form1" method="post" action="send.php"><button class="btn1" type="button" onclick="return valid()"> Stop </button><p class="<? echo $Y;?>">( 100-200 )&nbsp;&nbsp;satoshi every 10 minutes.</p><p class="<? echo $A;?>">Too many requests! Try again after 10 sec.</p><section class="instr"></section><input class="gdigit" type="hidden" name="val" value=""> <label for="wallet">Input BTC-wallet:&nbsp;</label><input type="text" id="wallet" name="wallet" maxlength="34" placeholder="34 symbols" required="required"><br><p id="walletFailed" class="<? echo $W;?>">Please input correct BTC wallet!</p><br><br><br> <input type="hidden" name="ip" value="<?php echo $ip;?>"><input type="hidden" name="nameWallet" value="<?php echo $nameWallet;?>"><p id="captchaFailed" class="<? echo $K;?>">Wrong Captcha, please try again!</p><article class="img1"><?php echo $captcha; ?></article><br><br> <input type="submit" value="Get Reward !" onclick="return valid2()"></form><article class="info"><p>Bitcoin is a form of digital currency, created and held electronically. No one controls it. Bitcoins aren’t printed, like dollars or euros – they’re produced by people, and increasingly businesses, running computers all around the world, using software that solves mathematical problems.</p><p>A Satoshi is the smallest fraction of a Bitcoin that can currently be sent: 0.00000001 BTC, that is, a hundredth of a millionth BTC. In the future, however, the protocol may be updated to allow further subdivisions, should they be needed.</p><img class="ads" alt="ads" src="images/ads.png"></article></section><footer class="footerback"><article class="links"><a target="_blank" href="https://en.wikipedia.org/wiki/Bitcoin">about bitcoin</a> <a target="_blank" href="https://en.bitcoin.it/wiki/Satoshi_(unit)">about satoshi</a> <a target="_blank" href="https://bitcoin.org/en/choose-your-wallet">get btc-wallet</a> <a target="_blank" href="https://faucetbox.com/en/check">check wallet</a> <a target="_blank" href="policy.htm">privacy policy</a></article><article class="copyright">Copyright &copy; Freegen.tk, All rights reserved | Design by NZT team</article></footer></section><script type="text/javascript" src="js/ver.js"></script></body></html>