<?php

require_once("http.php");
require_once("auth_digest.php");

// ----------------------------------------------------------
// class Qiniu_RS_Client

class Qiniu_RS_Client
{
	public $Conn;

	public function __construct($mac){
			$this->Conn = new Qiniu_Client($mac);
	}

	public function Stat($bucket, $key) // => ($statRet, $error)
	{
		return Qiniu_RS_Stat($this->Conn, $bucket, $key);
	}

	public function Delete($bucket, $key) // => $error
	{
		return Qiniu_RS_Delete($this->Conn, $bucket, $key);
	}

	public function Move($bucketSrc, $keySrc, $bucketDest, $keyDest) // => $error
	{
		global $QINIU_RS_HOST;
		return Qiniu_Client_CallNoRet($this->Conn, "$QINIU_RS_HOST".Qiniu_RS_URIMove($bucketSrc, $keySrc, $bucketDest, $keyDest));
	}

	public function Copy($bucketSrc, $keySrc, $bucketDest, $keyDest) // => $error
	{
		global $QINIU_RS_HOST;
		return Qiniu_Client_CallNoRet($this->Conn, "$QINIU_RS_HOST".Qiniu_RS_URICopy($bucketSrc, $keySrc, $bucketDest, $keyDest));
	}

	// batch
	public function Batch($url)
	{
		global $QINIU_RS_HOST;
		return Qiniu_Client_CallWithForm($this->Conn, $QINIU_RS_HOST . "/batch?", $url);
	}

	public function BatchStat($entryPaths)
	{
		$params = '';
		foreach ($entryPaths as $entryPath) {
			if ($params == '') {
				$params = 'op=' . Qiniu_RS_URIStat($entryPath->bucket, $entryPath->key);
				continue;
			}
			$params .= '&op=' . Qiniu_RS_URIStat($entryPath->bucket, $entryPath->key);
		}
		return $this->batch($params);
	}

	public function BatchDelete($entryPaths)
	{
		$params = '';
		foreach ($entryPaths as $entryPath) {
			if ($params == '') {
				$params = 'op=' . Qiniu_RS_URIDelete($entryPath->bucket, $entryPath->key);
				continue;
			}
			$params .= '&op=' . Qiniu_RS_URIDelete($entryPath->bucket, $entryPath->key);
		}
		return $this->batch($params);
	}

	public function BatchMove($entryPairs)
	{
		$params = '';
		foreach ($entryPairs as $entryPair) {
			if ($params == '') {
				$params = 'op=' . Qiniu_RS_URIMove($entryPair->src->bucket, $entryPair->src->key, $entryPair->dest->bucket, $entryPair->dest->key);
				continue;
			}
			$params .= '&op=' . Qiniu_RS_URIMove($entryPair->src->bucket, $entryPair->src->key, $entryPair->dest->bucket, $entryPair->dest->key);
		}
		return $this->batch($params);
	}

	public function BatchCopy($entryPairs)
	{
		$params = '';
		foreach ($entryPairs as $entryPair) {
			if ($params == '') {
				$params = 'op=' . Qiniu_RS_URICopy($entryPair->src->bucket, $entryPair->src->key, $entryPair->dest->bucket, $entryPair->dest->key);
				continue;
			}
			$params .= '&op=' . Qiniu_RS_URICopy($entryPair->src->bucket, $entryPair->src->key, $entryPair->dest->bucket, $entryPair->dest->key);
		}
		return $this->batch($params);
	}

}

// ----------------------------------------------------------
// class Qiniu_RS_EntryPath

class EntryPath
{
	public $bucket;
	public $key;
	public function __construct($bucket, $key)
	{
		$this->bucket = $bucket;
		$this->key = $key;
	}
}

class EntryPathPair
{
	public $src;
	public $dest;
	public function __construct($src, $dest)
	{
		$this->src = $src;
		$this->dest = $dest;
	}
}

// ----------------------------------------------------------

function Qiniu_RS_URIStat($bucket, $key) //	==> $entryURIEncoded
{
	return "/stat/" . Qiniu_Encode("$bucket:$key");
}

function Qiniu_RS_URIDelete($bucket, $key)
{
	return "/delete/" . Qiniu_Encode("$bucket:$key");
}

function Qiniu_RS_URICopy($bucketSrc, $keySrc, $bucketDest, $keyDest)
{
	return "/copy/" . Qiniu_Encode("$bucketSrc:$keySrc") . "/" . Qiniu_Encode("$bucketDest:$keyDest");
}

function Qiniu_RS_URIMove($bucketSrc, $keySrc, $bucketDest, $keyDest)
{
	return "/move/" . Qiniu_Encode("$bucketSrc:$keySrc") . "/" . Qiniu_Encode("$bucketDest:$keyDest");
}
