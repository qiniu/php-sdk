<?php
require_once __DIR__ . '/../autoload.php';

use Qiniu\Auth;

// 控制台获取密钥：https://portal.qiniu.com/user/key
$accessKey = getenv('QINIU_ACCESS_KEY');
$secretKey = getenv('QINIU_SECRET_KEY');

$auth = new Auth($accessKey, $secretKey);
$config = new \Qiniu\Config();
$bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);

// 获取指定 zone（存储区域）的空间信息列表
// 存储区域，参考文档：https://developer.qiniu.com/kodo/manual/1671/region-endpoint

$region = 'z1';  // 华东：z0，华北：z1，华南：z2，北美：na0，东南亚：as0

list($Info, $err) = $bucketManager->bucketInfos($region);
if ($err) {
    print_r($err);
} else {
    print_r($Info);
}
