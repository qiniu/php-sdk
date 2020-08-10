<?php
require_once __DIR__ . '/../autoload.php';

use \Qiniu\Auth;

// 控制台获取密钥：https://portal.qiniu.com/user/key
$accessKey = getenv('QINIU_ACCESS_KEY');
$secretKey = getenv('QINIU_SECRET_KEY');
$bucket = getenv('QINIU_TEST_BUCKET');

$auth = new Auth($accessKey, $secretKey);

$config = new \Qiniu\Config();
$bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);

// 资源元信息查询
// 参考文档：https://developer.qiniu.com/kodo/api/1308/stat

$key = "qiniu.mp4";

list($fileInfo, $err) = $bucketManager->stat($bucket, $key);
if ($err) {
    print_r($err);
} else {
    print_r($fileInfo);
}
