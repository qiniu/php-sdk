<?php

require_once("../../qiniu/http.php");
require_once("../../qiniu/auth_digest.php");
require_once("../../qiniu/utils.php");

$accessKey = "";
$secretKey = "";

$bucket = "";
$key = "";

$entry = "$bucket:$key";
$encodedEntry = Qiniu_Encode($entry);

$apiHost = "http://iovip.qbox.me";
$apiPath = "/prefetch/$encodedEntry";
$requestBody = "";

$mac = new Qiniu_Mac($accessKey, $secretKey);
$client = new Qiniu_MacHttpClient($mac);

list($ret, $err) = Qiniu_Client_CallWithForm($client, $apiHost . $apiPath, $requestBody);
if ($err !== null) {
    echo "failed\n";
    var_dump($err);
} else {
    echo "success\n";
}
