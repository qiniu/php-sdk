<?php
require_once __DIR__ . '/../autoload.php';

use Qiniu\Auth;

// 控制台获取密钥：https://portal.qiniu.com/user/key
$accessKey = getenv('QINIU_ACCESS_KEY');
$secretKey = getenv('QINIU_SECRET_KEY');

$auth = new Auth($accessKey, $secretKey);
$config = new \Qiniu\Config();
$bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);

// 更新 bucket 事件通知规则
// 参考文档：https://developer.qiniu.com/kodo/manual/6095/event-notification

$bucket = 'xxxx';
$name = 'demo';
$prefix = 'test';
$suffix = 'mp4';
$event = 'mkfile';
$callbackURL = 'https://www.qiniu.com';

list($Info, $err) = $bucketManager->updateBucketEvent($bucket, $name, $prefix, $suffix, $event, $callbackURL);
if ($err) {
    print_r($err);
} else {
    print_r($Info);
}
