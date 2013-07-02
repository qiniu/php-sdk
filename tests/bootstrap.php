<?php

require_once("../qiniu/fop.php");
require_once("../qiniu/rs_utils.php");

$accessKey = getenv("QINIU_ACCESS_KEY");
$secretKey = getenv("QINIU_SECRET_KEY");

$tid = getenv("TRAVIS_JOB_NUMBER");
if (!empty($tid)) {
	$tid = strstr($tid, ".");
}

function initKeys() {
	global $accessKey, $secretKey;
	if (!empty($accessKey) && !empty($secretKey)) {
		Qiniu_SetKeys($accessKey, $secretKey);
	}
}

function getTid() {
	global $tid;
	return $tid;
}

