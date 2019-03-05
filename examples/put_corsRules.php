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
    array(
        "allowed_origin" => array("http://www.qiniu.com"),
        "allowed_method" => array("GET", "POST"),
    ),
    array(
        "allowed_origin" => array("http://qiniu.com"),
        "allowed_method" => array("GET", "HEAD"),
        "allowed_header" => array("testheader", "Content-Type"),
        "exposed_header" => array("test1", "test2"),
        "max_age" => 20
    ),
);

list($Info, $err) = $bucketManager->putCorsRules($bucket, $body);
if ($err) {
    print_r($err);
} else {
    print_r($Info);
}
