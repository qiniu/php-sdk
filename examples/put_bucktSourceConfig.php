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
    "bucket" => $bucket,
    "sources" => array(
        array(
            "addr" => "http://www.qiniu.com",
        ),
    ),
);

list($Info, $err) = $bucketManager->putBucktSourceConfig($body);
if ($err) {
    print_r($err);
} else {
    print_r($Info);
}
