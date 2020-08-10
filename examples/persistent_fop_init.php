<?php

require_once __DIR__ . '/../autoload.php';

use Qiniu\Auth;
use Qiniu\Processing\PersistentFop;
// 控制台获取密钥：https://portal.qiniu.com/user/key
$accessKey = getenv('QINIU_ACCESS_KEY');
$secretKey = getenv('QINIU_SECRET_KEY');
$auth = new Auth($accessKey, $secretKey);

// 要转码的文件所在的空间。
$bucket = 'Bucket_Name';

// 用户默认没有私有队列，需要在这里创建然后填写 https://portal.qiniu.com/dora/media-gate/pipeline
$pipeline = 'pipeline_name';

// 初始化
$pfop = new PersistentFop($auth, $bucket, $pipeline);
