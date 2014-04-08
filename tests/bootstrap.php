<?php

require_once("../qiniu/fop.php");
require_once("../qiniu/rs_utils.php");
require_once("../qiniu/rsf.php");

$accessKey = getenv("QINIU_ACCESS_KEY");
$secretKey = getenv("QINIU_SECRET_KEY");

$tid = getenv("TRAVIS_JOB_NUMBER");
if (!empty($tid)) {
    $pid = getmypid();
	$tid = strstr($tid, ".");
    $tid .= "." . $pid;
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

class MockReader
{
	private $off = 0;

	public function __construct($off = 0)
	{
		$this->off = $off;
	}

	public function Read($bytes) // => ($data, $err)
	{
		$off = $this->off;
		$data = '';
		for ($i = 0; $i < $bytes; $i++) {
			$data .= chr(65 + ($off % 26)); // ord('A') = 65
			$off++;
		}
		$this->off = $off;
		return array($data, null);
	}
}

