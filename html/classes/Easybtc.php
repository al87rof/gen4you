<?php

class Easybtc extends AFaucet {
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
            $res = explode("<div class=\"form-group\">", $page);
            $res2 = explode("<div class=\"text-center\">", $res[3]);
            return $res2[0];
        }
    }

    /*
     * Метод парсит имя поля кошелька с исходного кода страницы
     * возвращяет имя поля кошелька
     * или сообщение об ошибке
     */
    public function parseNameTextFieldWallet() {

        $result = $this->page;

        if (($result['errno'] != 0 ) || ($result['http_code'] != 200)) {
            echo $result['errmsg'];
            return false;
        } else {
            $page = $result['content'];

            $res = explode("<input type=\"text\" name=\"", $page);
            $res2 = explode("\" class", $res[2]);

            return $res2[0];
        }
    }
    
  

    /*
     * Метод проверки капчи
     * возвращяет результат
     */
    public function parseCaptchaValid() {
        $page = $this->page2;
        $invalid_address = 'Invalid address';
        $invalid_captcha = 'Invalid captcha code!';
        $limit = "This faucet exceeded it's safety limits!";
        $limit2 = "This faucet made too many requests, try again later!";
        $send = "was sent";
        $insufficient="Insufficient funds.";
        
        if(!isset($page) && empty($page)){
             return 'Empty request!';
        }        
        elseif(stristr($page,$invalid_address  ) != false){
            return $invalid_address;
        }
        elseif(stristr($page,$invalid_captcha) != false){
            return $invalid_captcha;
        }
        elseif(stristr($page,$limit) != false){
            return $limit;
        }
        elseif(stristr($page,$limit2) != false){
            return $limit2;
        }
        elseif(stristr($page,$send) != false){
             
                $res_1 = explode(" <div class=\"alert alert-success\">", $page);
                $res_2 = explode("<a target=\"_blank\"", $res_1[1]);
                return $res_2[0];
            
        }
        elseif(stristr($page,$insufficient) != false){
            return $insufficient;
        }else{
            return "Invalid captcha code!*".$this->errmsg;
        }
        
    }
}
