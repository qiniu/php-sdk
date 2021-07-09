<?php
require_once __DIR__ . '/../autoload.php';

use Qiniu\Auth;

$accessKey = getenv('QINIU_ACCESS_KEY');
$secretKey = getenv('QINIU_SECRET_KEY');

$auth = new Auth($accessKey, $secretKey);
$config = new \Qiniu\Config();
$bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);

$bucket = '';

$beginTime = "20200401000000";
$endTime = "20200424000000";

//5min or hour or day
$granularity = "day";

//存储区域   z0:华东 z1:华北 z2:华南 na0:北美 as0:东南亚
$region = "";

//获取流量数据
list($spaceData, $getSpaceErr) = $bucketManager->getSpaceData($bucket, $beginTime, $endTime, $granularity, $region);
if ($getSpaceErr != null) {
    print_r($getSpaceErr);
} else {
    echo "get space data success\n";
    print_r($spaceData);
}
