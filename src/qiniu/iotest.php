<?php
require_once 'io.php';
require_once 'auth_token.php';

$bucket = "phpsdk";
$key = "demo10";
$localFile = "t.php";


$putPolicy = new PutPolicy("phpsdk");
$upToken = $putPolicy->token();

$extra = new PutExtra();
$extra->bucket = $bucket;

$fp = fopen("rpc.php", "rb");
$cxt = stream_get_contents($fp);


//put($upToken, $key, $cxt, $extra);

$ret = putFile($upToken, $key, $localFile, $extra);
