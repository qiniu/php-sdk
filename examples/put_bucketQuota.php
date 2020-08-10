<?php
require_once __DIR__ . '/../autoload.php';

use Qiniu\Auth;

// 控制台获取密钥：https://portal.qiniu.com/user/key
$accessKey = getenv('QINIU_ACCESS_KEY');
$secretKey = getenv('QINIU_SECRET_KEY');
$bucket = getenv('QINIU_TEST_BUCKET');

$auth = new Auth($accessKey, $secretKey);

$config = new \Qiniu\Config();
$bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);

// 设置 bucket 配额
// size 表示空间存储量配额，count 表示空间文件数配额，新创建的空间默认没有限额

$size = 99999;
$count = 99;

list($Info, $err) = $bucketManager->putBucketQuota($bucket, $size, $count);
if ($err) {
    print_r($err);
} else {
    print_r($Info);
}
