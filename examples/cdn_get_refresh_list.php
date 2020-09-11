<?php

require_once __DIR__ . '/../autoload.php';

use Qiniu\Auth;
use Qiniu\Cdn\CdnManager;

// 控制台获取密钥：https://portal.qiniu.com/user/key
$accessKey = getenv('QINIU_ACCESS_KEY');
$secretKey = getenv('QINIU_SECRET_KEY');

$auth = new Auth($accessKey, $secretKey);
$cdnManager = new CdnManager($auth);

// 获取CDN刷新查询
// 参考文档：https://developer.qiniu.com/fusion/api/1229/cache-refresh

//$params['requestId'] = ''; // 指定要查询记录所在的刷新请求id
//$params['isDir'] = ''; // 指定是否查询目录，取值为 yes/no，默认不填则为两种类型记录都查询
//$params['urls'] = array(); // 要查询的url列表，每个url可以是文件url，也可以是目录url
$params['state'] = 'success'; // 指定要查询记录的状态，取值 processing／success／failure
//$params['pageNo'] = 0; // 要求返回的页号，默认为0
//$params['pageSize'] = 100; // 要求返回的页长度，默认为100
//$params['startTime'] = ''; // 指定查询的开始日期，格式2006-01-01
//$params['endTime'] = ''; // 指定查询的结束日期，格式2006-01-01

list($refreshListResult, $refreshListErr) = $cdnManager->getCdnRefreshList($params);
if ($refreshListErr !== null) {
    var_dump($refreshListErr);
} else {
    echo 'query refresh list request sent';
    print_r($refreshListResult);
}

