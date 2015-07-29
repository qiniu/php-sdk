<?php
require_once '../vendor/autoload.php';

use Qiniu\Auth;

$accessKey = 'eSnBeEIyUqGGtidOTmsgQCwE23gjUDNJlsI6_mz9';
$secretKey = 'd4eyXtO4JF_XaLkpNAWHnzygOcBbkx_Ywlhi8sKr';
$auth = new Auth($accessKey, $secretKey);

$bucket = 'devtest';
$upToken = $auth->uploadToken($bucket);

echo $upToken;

