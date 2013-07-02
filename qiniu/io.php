<?php

require_once("http.php");
require_once("auth_digest.php");


define('UNDEFINED_KEY', "?");
define('NO_CRC32', 0);
define('AUTO_CRC32', 1);
define('WITH_CRC32', 2);

// ----------------------------------------------------------
// class Qiniu_PutExtra

class Qiniu_PutExtra
{
	public $Params;
	public $MimeType;
	public $Crc32;
	public $CheckCrc;
}

function Qiniu_Put($upToken, $key, $body, $putExtra) // => ($data, $err)
{
	global $QINIU_UP_HOST;
	if ($key == null) {
		$key = UNDEFINED_KEY;
	}
	$fields = array('key' => $key, 'token' => $upToken);

	if ( $putExtra->CheckCrc == AUTO_CRC32 || $putExtra->CheckCrc == WITH_CRC32 ) {
		$fields['crc32'] = $putExtra->Crc32;
	}
	$files = array(array('file', $key, $body));

	$client = new Qiniu_Client(null);
	return Qiniu_Client_CallWithMultiPart($client, $QINIU_UP_HOST, $fields, $files);
}

function Qiniu_PutFile($upToken, $key, $localFile, $putExtra) // => ($data, $err)
{
	global $QINIU_UP_HOST;
	if ($key == null) {
		$key = UNDEFINED_KEY;
	}
	$fields = array('key' => $key, 'token' => $upToken, 'file' => '@' . $localFile);

	if ( $putExtra->CheckCrc == AUTO_CRC32 ) {
		$hash = hash_file('crc32b', $localFile);
		$crc32 = unpack('N', pack('H*', $hash));
		$fields['crc32'] = $crc32[1];
	} elseif ($putExtra->CheckCrc == WITH_CRC32 ) {
		$fields['crc32'] = $putExtra->Crc32;
	}

	$client = new Qiniu_Client(null);
	return Qiniu_Client_CallWithForm($client, $QINIU_UP_HOST, $fields, 'multipart/form-data');
}
