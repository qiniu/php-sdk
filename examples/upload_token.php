<?php
require_once '../autoload.php';

use Qiniu\Auth;

$accessKey = 'Access Key';
$secretKey = 'Secret Key';
$auth = new Auth($accessKey, $secretKey);

$bucket = 'devtest';
$upToken = $auth->uploadToken($bucket);

echo $upToken;
