<?php
require_once __DIR__ . '/../autoload.php';

use Qiniu\Auth;
use Qiniu\Cdn\CacheManager;

$accessKey = '';
$secretKey = '';

//初始化Auth状态：
$auth = new Auth($accessKey, $secretKey);

//初始化CacheManager
$cacheMgr = new CacheManager($auth);

$urls = array('http://rwxf.qiniudn.com/1.png');

$ret = $cacheMgr->refresh($urls);
var_dump($ret);