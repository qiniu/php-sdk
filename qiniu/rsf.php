<?php

require_once("http.php");

// ----------------------------------------------------------
// class Qiniu_RSF_Client

class Qiniu_RSF_Client
{
	public $Conn;

	public function __construct($mac)
	{
		$this->Conn = new Qiniu_MacHttpClient($mac);
	}

	/**
	 * 1. 首次请求 marker = ""
	 * 2. 无论 err 值如何，均应该先看 entries 是否有内容
	 * 3. 如果后续没有更多数据，err 返回 EOF，markerOut 返回 ""（但不通过该特征来判断是否结束）
	 */
	public function ListPrefix($bucket, $prefix, $marker, $limit) // => ($ret => array('items' => items, 'marker': markerOut), $err)
	{
		global $QINIU_RSF_HOST;
		$query = array("bucket" => $bucket);
		if (!empty($prefix)) {
			$query["prefix"] = $prefix;
		}
		if (!empty($marker)) {
			$query["marker"] = $marker;
		}
		if (!empty($limit)) {
			$query["limit"] = $limit;
		}

		$url =  $QINIU_RSF_HOST . "/list?" . http_build_query($query);
		list($ret, $err) = Qiniu_Client_Call($this->Conn, $url);
		if (!isset($ret['marker'])) {
			$err = new Qiniu_Error(400, 'EOF');
		}
		return array($ret, $err);
	}

}
