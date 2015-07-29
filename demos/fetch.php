<?php
require_once '../vendor/autoload.php';

use Qiniu\Auth;
use Qiniu\Storage\BucketManager;

$accessKey = 'XI0n2kV1LYwzcxqSZQxJ7bpycxDIAXFGJMWUt_zG';
$secretKey = '9WTmIAiwKQ2Nq6o93mfKd6VQqq56HjjLZonMWLJl';

$auth = new Auth($accessKey, $secretKey);
$bmgr = new BucketManager($auth);

$url = 'http://php.net/favicon.ico';
$bucket = 'rwxf';
$key = time() . '.ico';

list($ret, $err) = $bmgr->fetch($url, $bucket, $key);
echo "fetch $url to bucket: $bucket  key: $key\n";
if ($err !== null) 
{
	var_dump($err);
} else {
	var_dump($ret);
}

