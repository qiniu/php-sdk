<?php

require_once __DIR__ . '/../autoload.php';

use \Qiniu\Cdn\CdnManager;

$accessKey = getenv('QINIU_ACCESS_KEY');
$secretKey = getenv('QINIU_SECRET_KEY');

$auth = new Qiniu\Auth($accessKey, $secretKey);
$cdnManager = new CdnManager($auth);

$domains = array(
    "javasdk.qiniudn.com",
    "phpsdk.qiniudn.com"
);

list($logListData, $getLogErr) = $cdnManager->getCdnLoganalyzeIsptraffic($domains, array('china', 'oversea'), '2018-08-01', '2018-08-03');
if ($getLogErr != null) {
    var_dump($getLogErr);
} else {
    echo "get cdn log analyze top count url success\n";
    print_r($logListData);
}
