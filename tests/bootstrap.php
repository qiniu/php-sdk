<?php

require_once("../qiniu/fop.php");
require_once("../qiniu/rs_utils.php");

$accessKey = getenv("QINIU_ACCESS_KEY");
$secretKey = getenv("QINIU_SECRET_KEY");

if (!empty($accessKey) && !empty($secretKey)) {
	Qiniu_SetKeys($accessKey, $secretKey);
}

$tid = getenv("TRAVIS_JOB_NUMBER");
if !empty($tid) {
	$tid = strstr($tid, ".");
}

