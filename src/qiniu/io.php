<?php
require_once('rpc.php');

class PutExtra
{
	public $callbackParams;
	public $bucket;
	public $customMeta;
	public $mimeType;
}

function put($upToken, $key, $body, $extra) 
{
	$httpHeaders = array('Content-Type: application/octet-stream');
	$entryURI = $extra->bucket . ':' . $key;
	$action = '/rs-put/' . URLSafeBase64Encode($entryURI);
	
	if (isset($extra->mimeType) && $extra->mimeType !== '') {
		$action .= '/mimeType/' . URLSafeBase64Encode($extra->mimeType);
	}
	if (isset($extra->customMeta) && $extra->customMeta !== '') {
		$action .= '/meta/' . URLSafeBase64Encode($extra->customMeta);
	}

	$fields = array('action' => $action, 'auth' => $upToken);
	if (isset($extra->callbackParams) && $extra->callbackParams !== '') {
		if (is_array($extra->callbackParams)) {
			$extra->callbackParams = http_build_query($extra->callbackParams);
		}
		$fields['params'] = $extra->callbackParams;
	}
	
	$files = array(array("file", $key, $body));
	
	$client = new Client(UP_HOST);
	return $client->callWithMultiPart('/upload', $fields, $files); 
}


function putFile($upToken, $key, $localFile, $extra)
{
	$fp = fopen($localFile, 'rb');
	if (!$fp) {
		return array('error' => 'open file failed!');
	}
	$body = stream_get_contents($fp);
	fclose($fp);
	return put($upToken, $key, $body, $extra);	
}

function getUrl($domain, $key, $dnToken)
{
	return "$domain/$key" . '?token=' . $dnToken;
}
