<?php

require_once __DIR__ . '/../autoload.php';

use Qiniu\Auth;
use Qiniu\Storage\BucketManager;

$accessKey = getenv('QINIU_ACCESS_KEY');
$secretKey = getenv('QINIU_SECRET_KEY');
$bucket = getenv('QINIU_TEST_BUCKET');

// 初始化签权对象
$auth = new Auth($accessKey, $secretKey);
// 初始化BucketManager
$bucketMgr = new BucketManager($auth);

$url = 'http://devtools.qiniu.com/qiniu.png';
$key = time() . '.png';

list($ret, $err) = $bucketMgr->asyncFetch($url, $bucket, "", $key);
echo "=====> async fetch $url to bucket: $bucket  key: $key\n";
if ($err !== null) {
    var_dump($err);
} else {
    print_r($ret);
}