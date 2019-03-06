<?php
// @codingStandardsIgnoreFile
require_once __DIR__ . '/../autoload.php';

use Qiniu\Auth;

$accessKey = getenv('accessKey');
$secretKey = getenv('secretKey');
$testAuth = new Auth($accessKey, $secretKey);

$bucketName = 'phpsdk';
$key = 'php-logo.png';
$key2 = 'niu.jpg';

$bucketNameBC = 'phpsdk-bc';
$bucketNameNA = 'phpsdk-na';

$dummyAccessKey = getenv('accessKey');
$dummySecretKey = getenv('secretKey');
$dummyAuth = new Auth($dummyAccessKey, $dummySecretKey);

//cdn
$timestampAntiLeechEncryptKey = getenv('QINIU_TIMESTAMP_ENCRPTKEY');
$customDomain = "http://phpsdk.qiniuts.com";

$tid = getenv('TRAVIS_JOB_NUMBER');
if (!empty($tid)) {
    $pid = getmypid();
    $tid = strstr($tid, '.');
    $tid .= '.' . $pid;
}

function qiniuTempFile($size)
{
    $fileName = tempnam(sys_get_temp_dir(), 'qiniu_');
    $file = fopen($fileName, 'wb');
    if ($size > 0) {
        fseek($file, $size - 1);
        fwrite($file, ' ');
    }
    fclose($file);
    return $fileName;
}
