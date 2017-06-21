<?php

require_once __DIR__ . '/../autoload.php';

use \Qiniu\Cdn\CdnManager;

//创建时间戳防盗链
//时间戳防盗链密钥，后台获取
$encryptKey = '9c897c897d997f99fda9069a988bd6ab2fb9e8b9';

//带访问协议的域名
$url1 = 'http://sf.szcf.duochang.cc/TS/00027288.ts?avinfo';
$url2 = 'http://sf.szcf.duochang.cc/TS/00027288.ts';

//有效期时间（单位秒）
$durationInSeconds = 3600;

$signedUrl = CdnManager::createTimestampAntiLeechUrl($url1, $encryptKey, $durationInSeconds);
print($signedUrl);
print("\n");
