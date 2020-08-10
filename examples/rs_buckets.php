<?php
require_once __DIR__ . '/../autoload.php';

use \Qiniu\Auth;

// 控制台获取密钥：https://portal.qiniu.com/user/key
$accessKey = getenv('QINIU_ACCESS_KEY');
$secretKey = getenv('QINIU_SECRET_KEY');

$auth = new Auth($accessKey, $secretKey);

$config = new \Qiniu\Config();
$bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);

// 获取指定账号下所有的空间名
// 参考文档：https://developer.qiniu.com/kodo/api/3926/get-service

list($buckets, $err) = $bucketManager->buckets(true);
if ($err) {
    print_r($err);
} else {
    print_r($buckets);
}
