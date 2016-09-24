<?php

abstract class AFaucet {

    public $page;    // хранит результат запроса КУРЛ в том числе исходный код страницы
    public $page2;    // хранит результат запроса КУРЛ в том числе исходный код страницы
    public $faucetName; //Хранит имя крана (http://example.com)
    public $ipbase;
    public $ipArray;
    public $ipCurrent;
    public static $ipGood;  // Массив рабочих Proxy

  /*
     Конструктор принимает название крана
     */
    public function __construct($FaucetName) {
        $this->faucetName = $FaucetName;
    }

 /*
     * Метод getConnection($ip) принимает параметр ip (proxy)
     * подключяется к сайту через прокси
     * возвращяет дескриптор подключения
     */

    public function getConnection($ip,$dl) {

        $uagent = "Mozilla/5.0 (Windows NT 6.1; rv:44.0) Gecko/20100101 Firefox/44.0";
        $name = $this->faucetName;
        $ch = curl_init($name);
        $path = dirname(__FILE__);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
        curl_setopt($ch, CURLOPT_HEADER, 0);           // не возвращает заголовки
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
        curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
        curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
        curl_setopt($ch, CURLOPT_PROXY, "$ip");        // использование прокси
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $dl); // таймаут соединения
        curl_setopt($ch, CURLOPT_TIMEOUT, $dl);        // таймаут ответа
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);      // останавливаться после 10-ого редиректа
        curl_setopt($ch, CURLOPT_COOKIEFILE, $path."/my_cookies.txt"); //Создание файла куков
        curl_setopt($ch, CURLOPT_COOKIEJAR,  $path."/my_cookies.txt");

        return $ch;
    }

    /*
     * Метод getPage() принимает проксі, дєлает запрос і
     * сохраняет исходный код страницы в переменную $Page
     */

    public function getPage($proxyIp,$dl) {

        $ch = $this->getConnection($proxyIp,$dl);
        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);
        curl_close($ch);

        $header['errno'] = $err;
        $header['errmsg'] = $errmsg;
        $header['content'] = $content;

        $this->page = $header;
    }

    /*
     * Метод postPage() принимает параметры:
     * name_text_vallet - имя поля ввода кошелька
     * vallet_new -номер кошелька
     * count - счетчик кошелька
     * Подготавливает и отправляет пост запрос на сервер
     */
    abstract public function postPage($walletNameTextField,$wallet, $value1, $key1, $value2, $key2, $proxyIp);

     /*
     * Метод парсит капчу с исходного кода страницы
     * возвращяет капчу в виде хтлм кода
     * или сообщение об ошибке
     */
    abstract public function parseCaptcha();

    /*
     * Метод парсит имя поля кошелька с исходного кода страницы
     * возвращяет имя поля кошелька
     * или сообщение об ошибке
     */
    abstract public function parseNameTextFieldWallet();

    /*
     * Метод проверки капчи
     * возвращяет результат
     */
    abstract public function parseCaptchaValid();

     /*
     * Метод получяет счетчик кошелька с файла wlcount.txt
     */
    public function getWalletCount() {

        $file = ROOT.'/components/wlcount.txt';
        $f = fopen($file, "r");
        flock($f, 2);
        $vlcount = 0;
        while ($line = fread($f, filesize($file))) {
            $vlcount = $line;
        }
        flock($f, 3);
        fclose($f);
        return $vlcount;
    }

    /*
     * Метод записывает счетчик кошелька в файл wlcount.txt
     */

    public function setWalletCount($vlcount) {

        $file = ROOT.'/components/wlcount.txt';
        $f = fopen($file, "w");
        flock($f, 2);
        fwrite($f, $vlcount);
        flock($f, 3);
        fclose($f);
    }


    /*
     * Метод получяет счетчик Обьекта  с файла obcount.txt
     */
    public function getObjectCount() {

        $file = ROOT.'/components/obcount.txt';
        $f = fopen($file, "r");
        flock($f, 2);
        $obcount = 0;
        while ($line = fread($f, filesize($file))) {
            $obcount = $line;
        }
        flock($f, 3);
        fclose($f);
        return $obcount;
    }

    /*
     * Метод записывает счетчик кошелька в файл wlcount.txt
     */

    public function setObjectCount($obcount) {

        $file = ROOT.'/components/obcount.txt';
        $f = fopen($file, "w");
        flock($f, 2);
        fwrite($f, $obcount);
        flock($f, 3);
        fclose($f);
    }

  /* Метод читает файл goodProxy.txt в масив ipGood */

    public function getArrayGoodProxy() {

        $file = ROOT.'/components/goodProxy.txt';

        if (is_file($file)) {
            $f = fopen($file, "r");
            while ($line = fgets($f)) {
                self::$ipGood[] = $line;
            }
        }
    }

    /*
     * Метод ищет первый рабочий Proxy в БД и возвращяет его
     */

public function sortArray($Ob) {

$e=1;

while($e==1){
$ip='';
$query = 'select address from list ORDER BY ID LIMIT 1';
$result = mysql_query($query) or die ('MySQL error: '.mysql_error());
$result2 = mysql_result($result,0);

$ip = $result2;

$query = 'delete from list ORDER BY ID LIMIT 1';
$result = mysql_query($query) or die ('MySQL error: '.mysql_error());

$Ob->page='';
$response='';
$i=0;

 while($response == ''){
   if($i==0){
      $i++;
      $Ob->getPage($ip,2);
      }
   $response = $Ob->page;
  }


 if(stristr($response['content'], 'DNS resolution error')!=FALSE){
  $file = ROOT.'/components/badlist.txt';
  $f = fopen($file, "a+");
  flock($f,2);
  fwrite($f, $ip."\r\n");
  flock($f,3);
  fclose($f);
 }
 elseif($response['http_code'] == 200 && $response['total_time'] < 2){
  $query = "INSERT into list VALUES(NULL,'$ip')";
  $result = mysql_query($query) or die ('MySQL error: '.mysql_error());
  $e=0;
  break;
 }
 else{
  $file = ROOT.'/components/zero.txt';
  $f = fopen($file, "a+");
  flock($f,2);
  fwrite($f, $ip."\r\n");
  flock($f,3);
  fclose($f);
 }
}
return $ip;
}

} // End of class
?>