<?php

require("bootstrap.php");

class IoTest extends PHPUnit_Framework_TestCase
{
	public $bucket;
	public $client;

	public function setUp()
	{
		$this->client = new Qiniu_Client(null);
		$this->bucket = getenv("QINIU_BUCKET_NAME");
	}

	public function testPutFile()
	{
		$key = rand();
		$putPolicy = new Qiniu_RS_PutPolicy($this->bucket);
		$upToken = $putPolicy->Token(null);
		$putExtra = new Qiniu_PutExtra();
		$putExtra->CheckCrc = 1;
		list($ret, $err) = Qiniu_PutFile($upToken, $key, __File__, $putExtra);
		$this->assertArrayHasKey('hash', $ret);
		$this->assertNull($err);

		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);
		$this->assertNull($err);
	}

	public function testPut()
	{
		$key = rand();
		$putPolicy = new Qiniu_RS_PutPolicy($this->bucket);
		$upToken = $putPolicy->Token(null);
		$putExtra = new Qiniu_PutExtra();
		list($ret, $err) = Qiniu_Put($upToken, $key, "hello world!", $putExtra);
		$this->assertArrayHasKey('hash', $ret);
		$this->assertNull($err);

		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);
		$this->assertNull($err);
	}
}
