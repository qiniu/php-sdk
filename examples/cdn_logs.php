<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Qiniu\Auth;
use Qiniu\Cdn\LogManager;

$accessKey = '';
$secretKey = '';

//初始化Auth状态：
$auth = new Auth($accessKey, $secretKey);

//初始化CacheManager
$logMgr = new LogManager($auth);

$day = '2016-10-22';
$domains = array('q.jspatch.com', '7xkfnf.com1.z0.glb.clouddn.com');

$ret = $logMgr->list_logs($domains, $day);

var_dump($ret);