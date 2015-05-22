<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Qiniu\Auth;
use Qiniu\Processing\PersistentFop;

$accessKey = '<your_ak>';
$secretKey = '<your_sk>';
$auth = new Auth($accessKey, $secretKey);

$bucket = 'rwxf';
$key = '1.mp4';
$pipeline = 'abc';
$pfop = New PersistentFop($auth, $bucket, $pipeline);

$savedkey = 'saved.mp4';
$entry = \Qiniu\base64_urlSafeEncode("$bucket:$savedkey");
$fops = "avthumb/mp4/ss/60/t/60|saveas/$entry";


list($id, $err) = $pfop->execute($key, $fops);
echo "\n====> pfop avthumb result: \n";
if ($err != null) {
	        var_dump($err);
} else {
	        echo "PersistentFop Id: $id\n";
}
