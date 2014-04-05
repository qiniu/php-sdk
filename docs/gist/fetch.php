<?php

require_once("../../qiniu/http.php");
require_once("../../qiniu/auth_digest.php");
require_once("../../qiniu/utils.php");

$accessKey = "";
$secretKey = "";
$targetUrl = "";

$destBucket = "";
$destKey = "";

$encodedUrl = Qiniu_Encode($targetUrl);

$destEntry = "$destBucket:$destKey";
$encodedEntry = Qiniu_Encode($destEntry);

$apiHost = "http://iovip.qbox.me";
$apiPath = "/fetch/$encodedUrl/to/$encodedEntry";
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
