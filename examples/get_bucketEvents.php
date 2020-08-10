<?php
require_once __DIR__ . '/../autoload.php';

use Qiniu\Auth;

// 控制台获取密钥：https://portal.qiniu.com/user/key
$accessKey = getenv('QINIU_ACCESS_KEY');
$secretKey = getenv('QINIU_SECRET_KEY');

$auth = new Auth($accessKey, $secretKey);
$config = new \Qiniu\Config();
$bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);

// 获取存储空间 - 事件通知规则
// 参考文档：https://developer.qiniu.com/kodo/manual/6095/event-notification

$bucket = 'xxxx'; // 存储空间名称

list($Info, $err) = $bucketManager->getBucketEvents($bucket);
if ($err) {
    print_r($err);
} else {
    print_r($Info);
}
