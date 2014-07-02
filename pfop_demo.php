<?php
require_once('qiniu/pfop.php');
require_once('qiniu/http.php');

$client = new Qiniu_MacHttpClient(null);

$pfop = new Qiniu_Pfop();

$pfop->Bucket = "rwxf";
$pfop->Key = "1.mp4";

$savedKey = "6.jpg";
// $pfop->Fops = "avthumb/flv/r/24/vcodec/libx264";
$savedEntry = Qiniu_Encode("$pfop->Bucket:$savedKey");

$pfop->Fops = "vframe/jpg/offset/180/w/1000/h/1000/rotate/90|saveas/$savedEntry";
//$pfop->NotifyURL = "http://api.rwfeng.com/index.php";

list($ret, $err) = $pfop->MakeRequest($client);

echo "===========>>Ret:";
var_dump($ret);


echo "===========>>Err:";
var_dump($err);
