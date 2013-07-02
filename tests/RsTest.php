<?php

require_once("bootstrap.php");

class RsTest extends PHPUnit_Framework_TestCase
{
	public $client;
	public $bucket;
	public $key;
	public $notExistKey = 'not_exist';

	public function setUp()
	{
		initKeys();
		$this->client = new Qiniu_MacHttpClient(null);
		$this->bucket = getenv("QINIU_BUCKET_NAME");
		$this->key = getenv("QINIU_KEY_NAME");
	}

	public function testStat()
	{
		list($ret, $err) = Qiniu_RS_Stat($this->client, $this->bucket, $this->key);
		$this->assertArrayHasKey('hash', $ret);
		$this->assertNull($err);
		list($ret, $err) = Qiniu_RS_Stat($this->client, $this->bucket, $this->notExistKey);
		$this->assertNull($ret);
		$this->assertFalse($err === null);
	}

	public function testDeleteMoveCopy()
	{
		$key2 = 'testOp2' . getTid();
		$key3 = 'testOp3' . getTid();
		Qiniu_RS_Delete($this->client, $this->bucket, $key2);
		Qiniu_RS_Delete($this->client, $this->bucket, $key3);

		$err = Qiniu_RS_Copy($this->client, $this->bucket, $this->key, $this->bucket, $key2);
		$this->assertNull($err);
		$err = Qiniu_RS_Move($this->client, $this->bucket, $key2, $this->bucket, $key3);
		$this->assertNull($err);
		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key3);
		$this->assertNull($err);
		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key2);
		$this->assertNotNull($err, "delete key2 false");
	}
}

