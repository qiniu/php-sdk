<?php
require_once('../qiniu/pfop.php');
require_once('../qiniu/http.php');

$client = new Qiniu_MacHttpClient(null);

$pfop = new Qiniu_Pfop();

$pfop->Bucket = 'rwxf';
$pfop->Key = '1.mp4';

$savedKey = 'qiniu.jpg';
$entry = Qiniu_Encode("$pfop->Bucket:$savedKey");
$pfop->Fops = "vframe/jpg/offset/180/w/1000/h/1000/rotate/90|saveas/$entry";

list($ret, $err) = $pfop->MakeRequest($client);
echo "\n\n====> pfop result: \n";
if ($err !== null) {
    var_dump($err);
} else {
    var_dump($ret);
}
