<?php
require_once __DIR__ . '/../autoload.php';

use Qiniu\Auth;

$accessKey = getenv('QINIU_ACCESS_KEY');
$secretKey = getenv('QINIU_SECRET_KEY');
$bucket = 'xxxx';

$auth = new Auth($accessKey, $secretKey);
$config = new \Qiniu\Config();
$bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);

$body = array(
    //回源配置的空间名
    "bucket" => $bucket,
    "sources" => array(
        array(
            "addr" => "http://www.qiniu.com", //回源地址
            // "weight"=> "<Weight>",  //权重
            // "backup"=> "<Backup>"   //是否备用回源
        ),
    ),
);

list($Info, $err) = $bucketManager->putBucktSourceConfig($body);
if ($err) {
    print_r($err);
} else {
    print_r($Info);
}
