<?php

require_once("http.php");
require_once("auth_digest.php");

// ----------------------------------------------------------
// class Qiniu_RSF_Client

class Qiniu_RSF_Client
{
	public $Conn;
	public function __construct($mac)
	{
		$this->Conn = new Qiniu_Client($mac);
	}
	
	public function ListPrefix($bucket, $prefix, $marker, $limit)
	{
		global $QINIU_RSF_HOST;
		$query = array("bucket" => $bucket);
		if (isset($prefix)) {
			$query["prefix"] = $prefix;
		}
		if (isset($marker)) {
			$query["marker"] = $marker;
		}
		if (isset($limit)) {
			$query["limit"] = $limit;
		}
		
		return Qiniu_Client_Call($this->Conn, $QINIU_RSF_HOST . "/list?" . http_build_query($query));
	}
	
}