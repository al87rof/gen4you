<?php


class Coins4games extends AFaucet {
    public $errmsg;
   /*
     * Метод postPage() принимает параметры:
     * name_text_vallet - имя поля ввода кошелька 
     * vallet_new -номер кошелька
     * count - счетчик кошелька 
     * Подготавливает и отправляет пост запрос на сервер
     */
    public function postPage($walletNameTextField, $wallet_new, $captcha1, $name_captcha_field, $captcha2, $name_captcha_field2, $proxyIp) {        
        $ch = $this->getConnection($proxyIp,15);
        $name = $this->faucetName;
        $adsress = '';
        curl_setopt($ch, CURLOPT_REFERER, $name);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "address=$adsress&$walletNameTextField=$wallet_new&$name_captcha_field=$captcha1&$name_captcha_field2=$captcha2");
        $content = curl_exec($ch);
        $this->page2 = $content;
        $this->errmsg = curl_error($ch);
        curl_close($ch);
       
    }

    /*
     * Метод парсит капчу с исходного кода страницы
     * возвращяет капчу в виде хтлм кода
     * или сообщение об ошибке
     */
    public function parseCaptcha() {
        $result = $this->page;
        if (($result['errno'] != 0 ) || ($result['http_code'] != 200)) {
            echo $result['errmsg'];
            return false;
        } else {
            $page = $result['content'];
            $res = explode("<div class=\"form-group\" id=\"faucet-captcha\">", $page);
            $res2 = explode("</div>", $res[1]);
            return $res2[0];
        }
    }

    /*
     * Метод парсит имя поля кошелька с исходного кода страницы
     * возвращяет имя поля кошелька
     * или сообщение об ошибке
     */
    public function parseNameTextFieldWallet() {
        return "username";    
    }
    /*
     * Метод проверки капчи
     * возвращяет результат
     */
    public function parseCaptchaValid() {
        $page = $this->page2;
        $page = strip_tags($page);
        $page = trim($page,"\n");
        $page = trim($page,"\t");    
        
        $invalid_address = "Invalid \'to\' address.";
        $invalid_captcha = 'Wrong captcha, try again!';
        $limit = "This faucet made too many requests, try again later!";
        $limit2 = "This faucet made too many requests, try again later!";
        $send = "120 satoshi was sent to";
        $insufficient="Insufficient funds.";
        
        if(!isset($page) && empty($page)){
             return 'Empty request!';
        }        
        elseif(stristr($page,$invalid_address  ) != false){
            return "Invalid address";
        }
        elseif(stristr($page,$invalid_captcha) != false){
            return "Invalid captcha code!";
        }
        elseif(stristr($page,$limit) != false){
            return "This faucet exceeded it's safety limits!";
        }
        elseif(stristr($page,$limit2) != false){
            return $limit2;
        }
        elseif(stristr($page,$send) != false){
            return "120 satoshi was sent to";
        }
        elseif(stristr($page,$insufficient) != false){
            return "Insufficient funds.";
        }else{
            return "Wrong captcha, try again!*".$this->errmsg;
        }
        
    }
}
