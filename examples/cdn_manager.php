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
