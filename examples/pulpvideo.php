<?php

require_once __DIR__ . '/../autoload.php';

use Qiniu\Auth;
use Qiniu\Http\Client;

$accessKey = getenv('QINIU_ACCESS_KEY');
$secretKey = getenv('QINIU_SECRET_KEY');
$auth = new Auth($accessKey, $secretKey);
$config = new \Qiniu\Config();
$bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);

$reqBody = array();
$reqBody['uri'] = "xxxxxxxx";
$ops = array();
$ops = array(
    array(
        'op' => 'pulp',
    ),
);

$vid = "xxxx";
list($ret, $err) = $bucketManager->pulpVideo($reqBody, $ops, $vid);

if ($err !== null) {
    var_dump($err);
} else {
    var_dump($ret);
}
