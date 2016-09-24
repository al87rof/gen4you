<?php
define('ROOT', dirname(__FILE__));

if (isset($_POST['ApiFaucet']) && !empty($_POST['ApiFaucet'])) {

    $tmp = $_POST['ApiFaucet'];
    $goodProxyArray = unserialize(base64_decode($tmp));
    echo '<pre>';
    print_r($goodProxyArray);

    if (is_array($goodProxyArray)) {
        
        $result = implode("",$goodProxyArray);
            $file = ROOT.'/components/goodProxy.txt';
            $f = fopen($file, "w+");
            flock($f, 2);
            fwrite($f, $result);
            flock($f, 3);
            fclose($f);
            
        
        echo "List saved";
    } else {
        echo "ERROR API POST REQUEST";
        return false;
    }
}
?>
<html>
    <meta>
    <title>API FAUCET</title>
    <body style="background: bisque">
        API FAUCET
        <h3>Stat</h3>
        <?php
		require ROOT.'/classes/AFaucet.php';
        require ROOT.'/classes/Hoard.php';
        $statObject = new Hoard("http://ok.ru");
        $walletCount = $statObject->getWalletCount();
		$statObject->getArrayGoodProxy();
        $objectCount = $statObject->getObjectCount();
        $ipCount = Hoard::$ipGood;
        $ipCount = count($ipCount);
        echo "Wallet Count: $walletCount <br>";
        echo "Object Count: $objectCount <br>";
        echo "Proxy Count: $ipCount <br>";
        ?>
    </body>
</html>