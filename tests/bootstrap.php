<?php

require_once("../qiniu/rs.php");
require_once("../qiniu/rsf.php");

$accessKey = getenv("QINIU_ACCESS_KEY");
$secretKey = getenv("QINIU_SECRET_KEY");

define("BUCKET_NAME", "php_sdk_test_bucket");
define("KEY_NAME", "file_name");

if (!empty($accessKey) && !empty($secretKey)) {
	Qiniu_SetKeys($accessKey, $secretKey);
}

