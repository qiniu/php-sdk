<?php

require_once __DIR__ . '/../autoload.php';

use \Qiniu\Cdn\CdnManager;

$accessKey = "";
$secretKey = "";

$auth = new Qiniu\Auth($accessKey, $secretKey);

//待刷新的文件列表和目录，文件列表最多一次100个，目录最多一次10个
//参考文档：http://developer.qiniu.com/article/fusion/api/refresh.html
$urls = array(
    "http://if-pbl.qiniudn.com/qiniu.jpg",
    "http://if-pbl.qiniudn.com/qiniu2.jpg",
);

//刷新目录需要联系七牛技术支持开通账户权限
$dirs = array(
    "http://if-pbl.qiniudn.com/test/"
);

$cdnManager = new CdnManager($auth);
list($refreshResult, $refreshErr) = $cdnManager->refreshUrlsAndDirs($urls, $dirs);
if ($refreshErr != null) {
    var_dump($refreshErr);
} else {
    echo "refresh request sent\n";
    print_r($refreshResult);
}

//预取文件列表，每次最多100个
//参考文档：http://developer.qiniu.com/article/fusion/api/prefetch.html
list($prefetchResult, $prefetchErr) = $cdnManager->prefetchUrls($urls);
if ($prefetchErr != null) {
    var_dump($prefetchErr);
} else {
    echo "prefetch request sent\n";
    print_r($prefetchResult);
}

//获取流量和带宽数据
//参考文档：http://developer.qiniu.com/article/fusion/api/traffic-bandwidth.html

$domains = array(
    "if-pbl.qiniudn.com",
    "qdisk.qiniudn.com"
);

$startDate = "2017-01-01";
$endDate = "2017-01-02";

//5min or hour or day
$granularity = "day";

//获取带宽数据

list($bandwidthData, $getBandwidthErr) = $cdnManager->getBandwidthData($domains, $startDate, $endDate, $granularity);
if ($getBandwidthErr != null) {
    var_dump($getBandwidthErr);
} else {
    echo "get bandwidth data success\n";
    print_r($bandwidthData);
}

//获取流量数据

list($fluxData, $getFluxErr) = $cdnManager->getFluxData($domains, $startDate, $endDate, $granularity);
if ($getFluxErr != null) {
    var_dump($getFluxErr);
} else {
    echo "get flux data success\n";
    print_r($fluxData);
}

//获取日志下载链接
//参考文档：http://developer.qiniu.com/article/fusion/api/log.html
$logDate = "2017-01-01";
list($logListData, $getLogErr) = $cdnManager->getCdnLogList($domains, $logDate);
if ($getLogErr != null) {
    var_dump($getLogErr);
} else {
    echo "get cdn log list success\n";
    print_r($logListData);
}

//创建时间戳防盗链
//时间戳防盗链密钥，后台获取
$encryptKey = 'xxx';

//原始文件名
$testFileName1 = '基本概括.mp4';
$testFileName2 = '2017/01/07/test.png';

//查询参数列表
$queryStringArray = array(
    'name'=>'七牛',
    'year'=>2017,
    '年龄'=>28,
);

//带访问协议的域名
$host = 'http://video.example.com';

//unix时间戳
$deadline = time() + 3600;

$signedUrl1 = CdnManager::createTimestampAntiLeechUrl($host, $testFileName1, $queryStringArray, $encryptKey, $deadline);
print($signedUrl1);
print("\n");

$signedUrl2 = CdnManager::createTimestampAntiLeechUrl($host, $testFileName2, $queryStringArray, $encryptKey, $deadline);
print($signedUrl2);
print("\n");

$signedUrl3 = CdnManager::createTimestampAntiLeechUrl($host, $testFileName1, null, $encryptKey, $deadline);
print($signedUrl3);
print("\n");

$signedUrl4 = CdnManager::createTimestampAntiLeechUrl($host, $testFileName2, null, $encryptKey, $deadline);
print($signedUrl4);
print("\n");
