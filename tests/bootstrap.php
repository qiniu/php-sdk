<?php
// @codingStandardsIgnoreFile
require_once __DIR__ . '/../autoload.php';

use Qiniu\Auth;

$accessKey = getenv('QINIU_ACCESS_KEY');
$secretKey = getenv('QINIU_SECRET_KEY');
$testAuth = new Auth($accessKey, $secretKey);

$bucketName = 'phpsdk';
$key = 'php-logo.png';
$key2 = 'niu.jpg';

$testStartDate = '2020-08-18';
$testEndDate = '2020-08-19';
$testGranularity = 'day';
$testLogDate = '2020-08-18';

$bucketNameBC = 'phpsdk-bc';
$bucketNameNA = 'phpsdk-na';
$bucketNameFS = 'phpsdk-fs';
$bucketNameAS = 'phpsdk-as';

$dummyAccessKey = 'abcdefghklmnopq';
$dummySecretKey = '1234567890';
$dummyAuth = new Auth($dummyAccessKey, $dummySecretKey);

//cdn
$timestampAntiLeechEncryptKey = getenv('QINIU_TIMESTAMP_ENCRPTKEY');
$customDomain = "http://sdk.peterpy.cn";
$customDomain2 = "sdk.peterpy.cn";
$customCallbackURL = "https://qiniu.timhbw.com/notify/callback";

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
