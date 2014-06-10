<?php
require_once('qiniu/pfop.php');
require_once('qiniu/http.php');

$client = new Qiniu_MacHttpClient(null);

if ($_SERVER["argc"] < 2) {
    echo 'Usage: php pfop_query.php <id>' . "\n";
    exit();
}

$id = $_SERVER["argv"][1];
list($ret, $err) = Qiniu_PfopStatus($client, $id);

echo "===================>>Ret:\n";
var_dump($ret);

echo "===================>>Err:\n";
var_dump($err);
exit();   # code...
