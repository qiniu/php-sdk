<?php

use Qiniu\Auth;

$accessKey = getenv('QINIU_ACCESS_KEY');
$secretKey = getenv('QINIU_SECRET_KEY');

//初始化Auth状态：
$auth = new Auth($accessKey, $secretKey);
