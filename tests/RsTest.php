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

	public function testBatchStat()
	{
		$key2 = 'testOp2' . getTid();
		Qiniu_RS_Delete($this->client, $this->bucket, $key2);
		$entries = array(new Qiniu_RS_EntryPath($this->bucket, $this->key), new Qiniu_RS_EntryPath($this->bucket, $key2));
		list($ret, $err) = Qiniu_RS_BatchStat($this->client, $entries);
		$this->assertNotNull($err);
		$this->assertEquals($ret[0]['code'], 200);
		$this->assertEquals($ret[1]['code'], 612);
	}

	public function testBatchDeleteMoveCopy()
	{
		$key2 = 'testOp2' . getTid();
		$key3 = 'testOp3' . getTid();
		$key4 = 'testOp4' . getTid();
		$e1 = new Qiniu_RS_EntryPath($this->bucket, $this->key);
		$e2 = new Qiniu_RS_EntryPath($this->bucket, $key2);
		$e3 = new Qiniu_RS_EntryPath($this->bucket, $key3);
		$e4 = new Qiniu_RS_EntryPath($this->bucket, $key4);
		Qiniu_RS_BatchDelete($this->client, array($e2, $e3,$e4));

		$entryPairs = array(new Qiniu_RS_EntryPathPair($e1, $e2), new Qiniu_RS_EntryPathPair($e1, $e3));
		list($ret, $err) = Qiniu_RS_BatchCopy($this->client, $entryPairs);
		$this->assertNull($err);
		$this->assertEquals($ret[0]['code'], 200);
		$this->assertEquals($ret[1]['code'], 200);

		list($ret, $err) = Qiniu_RS_BatchMove($this->client, array(new Qiniu_RS_EntryPathPair($e2, $e4)));
		$this->assertNull($err);
		$this->assertEquals($ret[0]['code'], 200);

		list($ret, $err) = Qiniu_RS_BatchDelete($this->client, array($e3, $e4));
		$this->assertNull($err);
		$this->assertEquals($ret[0]['code'], 200);
		$this->assertEquals($ret[1]['code'], 200);

		Qiniu_RS_BatchDelete($this->client, array($e2, $e3, $e4));
	}
}

