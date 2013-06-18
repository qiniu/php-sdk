<?php

require_once("../qiniu/rs.php");

$accessKey = getenv("QINIU_ACCESS_KEY");
$secretKey = getenv("QINIU_SECRET_KEY");

if (!empty($accessKey) && !empty($secretKey)) {
	Qiniu_SetKeys($accessKey, $secretKey);
}

