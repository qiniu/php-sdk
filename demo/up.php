<?php

require_once('../qiniu/io.php');
require_once('../qiniu/rs.php');

$bucket = 'rwxf';
$key = 'up.php';
$file = __FILE__;  //path to local file


$client = new Qiniu_MacHttpClient(null);
$putPolicy = new Qiniu_RS_PutPolicy("$bucket:$key");
$putPolicy->CallbackUrl = 'https://10fd05306325.a.passageway.io';
$putPolicy->CallbackBody = 'key=$(key)&hash=$(etag)';
$upToken = $putPolicy->Token(null);

$putExtra = new Qiniu_PutExtra();
$s = time();
list($ret, $err) = Qiniu_PutFile($upToken, $key, $file, $putExtra);
echo "time elapse:". (time() - $s) . "\n";
echo "====> Qiniu_PutFile result: \n";
if ($err !== null) {
    var_dump($err);
} else {
    var_dump($ret);
}

