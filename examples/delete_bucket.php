<?php
require_once __DIR__ . '/../autoload.php';

use Qiniu\Auth;

// 控制台获取密钥：https://portal.qiniu.com/user/key
$accessKey = getenv('QINIU_ACCESS_KEY');
$secretKey = getenv('QINIU_SECRET_KEY');

$auth = new Auth($accessKey, $secretKey);
$config = new \Qiniu\Config();
$bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);

// 删除指定的 Bucket
// 1、空间绑定了自定义域名，禁止删除，需要先解绑域名
// 2、空间不为空，禁止删除，需要先把空间内的文件删除完毕

$bucket = 'xxxx'; // 存储空间名称

list($Info, $err) = $bucketManager->deleteBucket($bucket);
if ($err) {
    print_r($err);
} else {
    print_r($Info);
}
