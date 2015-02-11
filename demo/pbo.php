<?php
error_reporting(E_ALL^E_NOTICE^E_WARNING);
header ( 'Content-Type:application/json; charset=utf-8' );

require_once("qiniu/pb.php");

$op = $_GET['op']; // one of 'list', 'stat', 'delete', 'move', 'copy'
$bucket = $_GET['bucket'];
$prefix = $_GET['prefix'];
if(strlen($prefix) < 5) { // to avoid dangerous operation such as deleting all files
  die(json_encode(array(
    'result' => 0,
    'error' => 'Prefix too short'
  )));
}

$destBucket = $_GET['destBucket'];

Qiniu_SetKeys($QINIU_ACCESS_KEY, $QINIU_SECRET_KEY);

exit(json_encode(prefixBatch($op, $bucket, $prefix, $destBucket)));
