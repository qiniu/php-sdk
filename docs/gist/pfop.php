<?php

require_once("../../qiniu/http.php");
require_once("../../qiniu/auth_digest.php");
require_once("../../qiniu/utils.php");

$accessKey = "";
$secretKey = "";

$bucket = "";
$key = "";
$fops = "";
$notifyURL = "";
$force = 0;


$encodedBucket = urlencode($bucket);
$encodedKey = urlencode($key);
$encodedFops = urlencode($fops);
$encodedNotifyURL = urlencode($notifyURL);

$apiHost = "http://api.qiniu.com";
$apiPath = "/pfop/";
$requestBody = "bucket=$encodedBucket&key=$encodedKey&fops=$encodedFops&notifyURL=$encodedNotifyURL";
if ($force !== 0) {
    $requestBody .= "&force=1";
}

$mac = new Qiniu_Mac($accessKey, $secretKey);
$client = new Qiniu_MacHttpClient($mac);

list($ret, $err) = Qiniu_Client_CallWithForm($client, $apiHost . $apiPath, $requestBody);
if ($err !== null) {
    echo "failed\n";
    var_dump($err);
} else {
    echo "success\n";
    var_dump($ret);
}
