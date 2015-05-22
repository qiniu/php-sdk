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

$key1 = 'saved.mp4';
$entry1 = \Qiniu\base64_urlSafeEncode("$bucket:$key1");
$fops = "avthumb/mp4/ss/60/t/60|saveas/$entry";


list($id, $err) = $pfop->execute($key, $fops);
echo "\n====> pfop avthumb result: \n";
if ($err != null) {
	        var_dump($err);
} else {
	        echo "PersistentFop Id: $id\n";
}
