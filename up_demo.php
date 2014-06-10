<?php

require_once('qiniu/io.php');
require_once('qiniu/rs.php');

$bucket = 'auditlog';
$key1 = 'rwf';
$file = '../php-sdk/1.mp4';


$client = new Qiniu_MacHttpClient(null);
$putPolicy = new Qiniu_RS_PutPolicy($bucket);
$upToken = $putPolicy->Token(null);

$putExtra = new Qiniu_PutExtra();
$putExtra->Crc32 = 1;
list($ret, $err) = Qiniu_PutFile($upToken, $key1, $file, $putExtra);
echo "\n\n====> Qiniu_PutFile result: \n";
if ($err !== null) {
    var_dump($err);
} else {
    var_dump($ret);
}

