<?php

require_once('qiniu/io.php');
require_once('qiniu/rs.php');

$bucket = 'alibaba';
$key1 = 'rwf';
$file = 'up_demo.php';


$client = new Qiniu_MacHttpClient(null);
$putPolicy = new Qiniu_RS_PutPolicy($bucket);
$putPolicy->Scope = "$bucket:$key1";
$putPolicy->EndUser = 'fuck';
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

