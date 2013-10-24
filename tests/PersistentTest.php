<?php

require_once("bootstrap.php");

class PersistentTest extends PHPUnit_Framework_TestCase
{
	public $bucket;
	public $client;

	public function setUp()
	{
		initKeys();
		$this->client = new Qiniu_MacHttpClient(null);
		$this->bucket = getenv("QINIU_BUCKET_NAME");
	}

	public function testPutFileWithPersistentOps()
	{
		$key = 'testPutFileWithPersistentOps' . getTid();
		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);

		$putPolicy = new Qiniu_RS_PutPolicy($this->bucket);
		$putPolicy->PersistentOps = 'avthumb/mp3';
		$putPolicy->PersistentNotifyUrl = 'http://someurl/abc';
		$upToken = $putPolicy->Token(null);
		$putExtra = new Qiniu_PutExtra();
		$putExtra->CheckCrc = 1;
		list($ret, $err) = Qiniu_PutFile($upToken, $key, __file__, $putExtra);
		$this->assertNull($err);
		$this->assertArrayHasKey('hash', $ret);
		$this->assertArrayHasKey('persistentId', $ret);
		var_dump($ret);

		list($ret, $err) = Qiniu_RS_Stat($this->client, $this->bucket, $key);
		$this->assertNull($err);
		var_dump($ret);

		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);
		$this->assertNull($err);
	}

	public function testPutWithPersistentOps()
	{
		$key = 'testPutWithPersistentOps' . getTid();
		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);

		$putPolicy = new Qiniu_RS_PutPolicy($this->bucket);
		$putPolicy->PersistentOps = 'avthumb/mp3';
		$putPolicy->PersistentNotifyUrl = 'http://someurl/abc';
		$upToken = $putPolicy->Token(null);
		list($ret, $err) = Qiniu_Put($upToken, $key, "hello world!", null);
		$this->assertNull($err);
		$this->assertArrayHasKey('hash', $ret);
		$this->assertArrayHasKey('persistentId', $ret);
		var_dump($ret);

		list($ret, $err) = Qiniu_RS_Stat($this->client, $this->bucket, $key);
		$this->assertNull($err);
		var_dump($ret);

		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);
		$this->assertNull($err);
	}
}

