<?php

require_once("http.php");

// ----------------------------------------------------------
// class Qiniu_RS_GetPolicy

class Qiniu_RS_GetPolicy
{
	public $Expires;

	public function MakeRequest($baseUrl, $mac) // => $privateUrl
	{
		$deadline = $this->Expires;
		if ($deadline == 0) {
			$deadline = 3600;
		}
		$deadline += time();

		$pos = strpos($baseUrl, '?');
		if ($pos !== false) {
			$baseUrl .= '&e=';
		} else {
			$baseUrl .= '?e=';
		}
		$baseUrl .= $deadline;

		$token = Qiniu_Sign($mac, $baseUrl);
		return "$baseUrl&token=$token";
	}
}

function Qiniu_RS_MakeBaseUrl($domain, $key) // => $baseUrl
{
	$keyEsc = rawurlencode($key);
	return "http://$domain/$keyEsc";
}

// --------------------------------------------------------------------------------
// class Qiniu_RS_PutPolicy

class Qiniu_RS_PutPolicy
{
	public $Scope;
	public $CallbackUrl;
	public $CallbackBody;
	public $ReturnUrl;
	public $ReturnBody;
	public $AsyncOps;
	public $EndUser;
	public $Expires;

	public function Token($mac) // => $token
	{
		$deadline = $this->Expires;
		if ($deadline == 0) {
			$deadline = 3600;
		}
		$deadline += time();

		$policy = array('scope' => $this->Scope, 'deadline' => $deadline);
		if (!empty($this->CallbackUrl)) {
			$policy['callbackUrl'] = $this->CallbackUrl;
		}
		if (!empty($this->CallbackBody)) {
			$policy['callbackBody'] = $this->CallbackBody;
		}
		if (!empty($this->ReturnUrl)) {
			$policy['returnUrl'] = $this->ReturnUrl;
		}
		if (!empty($this->ReturnBody)) {
			$policy['returnBody'] = $this->ReturnBody;
		}
		if (!empty($this->AsyncOps)) {
			$policy['asyncOps'] = $this->AsyncOps;
		}
		if (!empty($this->EndUser)) {
			$policy['endUser'] = $this->EndUser;
		}

		$b = json_encode($policy);
		return Qiniu_SignWithData($mac, $b);
	}
}

// ----------------------------------------------------------

function Qiniu_RS_Stat($self, $bucket, $key) // => ($statRet, $error)
{
	global $QINIU_RS_HOST;

	$entryURIEncoded = Qiniu_Encode("$bucket:$key");
	return Qiniu_Client_Call($self, "$QINIU_RS_HOST/stat/$entryURIEncoded");
}

function Qiniu_RS_Delete($self, $bucket, $key) // => $error
{
	global $QINIU_RS_HOST;

	$entryURIEncoded = Qiniu_Encode("$bucket:$key");
	return Qiniu_Client_CallNoRet($self, "$QINIU_RS_HOST/delete/$entryURIEncoded");
}

// ----------------------------------------------------------

