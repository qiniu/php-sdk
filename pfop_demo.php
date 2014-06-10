<?php
require_once('qiniu/pfop.php');
require_once('qiniu/http.php');

$client = new Qiniu_MacHttpClient(null);

$pfop = new Qiniu_Pfop();

$pfop->Bucket = "auditlog";
$pfop->Key = "1.mp4";
// $pfop->Fops = "avthumb/flv/r/24/vcodec/libx264";
$pfop->Fops = "avthumb/m3u8/segtime/1";
$pfop->NotifyURL = "http://api.rwfeng.com/index.php";

list($ret, $err) = $pfop->MakeRequest($client);

echo "===========>>Ret:";
var_dump($ret);


echo "===========>>Err:";
var_dump($err);
