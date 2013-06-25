<?php

require("bootstrap.php");

class RsApiTest extends PHPUnit_Framework_TestCase
{
	public $rs;
	public $bucket = BUCKET_NAME;
	public $notExistKey = "not_exist";
	public $key1 = KEY_NAME;
	public $key2 = "file_name_2";
	public $key3 = "file_name_3";
	public $key4 = "file_name_4";
	public function setUp()
	{
		$this->rs = new Qiniu_RS_Client(null);
	}

	public function testStat()
	{
		list($ret, $err) = $this->rs->stat($this->bucket, $this->key1);
		$this->assertArrayHasKey('hash', $ret);
		$this->assertNull($err);
		list($ret, $err) = $this->rs->stat($this->bucket, $this->notExistKey);
		$this->assertNull($ret);
		$this->assertFalse($err === null);
	}

	public function testDeleteMoveCopy()
	{
		$this->rs->delete($this->bucket, $this->key2);
		$this->rs->delete($this->bucket, $this->key3);

		list($ret, $err) = $this->rs->copy($this->bucket, $this->key1, $this->bucket, $this->key2);
		$this->assertNull($err);
		list($ret, $err) = $this->rs->move($this->bucket, $this->key2, $this->bucket, $this->key3);
		$this->assertNull($err);
		$err = $this->rs->delete($this->bucket, $this->key3);
		$this->assertNull($err);
		$err = $this->rs->delete($this->bucket, $this->key2);
		$this->assertNotNull($err, "delete key2 false");
	}

	public function testBatchStat()
	{
		$entries = array(new EntryPath($this->bucket, $this->key1), new EntryPath($this->bucket, $this->key2));
		list($ret, $err) = $this->rs->batchStat($entries);
		$this->assertNotNull($err);
		error_log(print_r($ret, true));
		$this->assertEquals($ret[0]['code'], 200);
		$this->assertEquals($ret[1]['code'], 612);
	}

	public function testBatchDeleteMoveCopy()
	{
		$e1 = new EntryPath($this->bucket, $this->key1);
		$e2 = new EntryPath($this->bucket, $this->key2);
		$e3 = new EntryPath($this->bucket, $this->key3);
		$e4 = new EntryPath($this->bucket, $this->key4);
		$this->rs->batchDelete(array($e2, $e3,$e4));

		$entryPairs = array(new EntryPathPair($e1, $e2), new EntryPathPair($e1, $e3));
		list($ret, $err) = $this->rs->batchCopy($entryPairs);
		$this->assertNull($err);
		$this->assertEquals($ret[0]['code'], 200);
		$this->assertEquals($ret[0]['code'], 200);

		list($ret, $err) = $this->rs->batchMove(array(new EntryPathPair($e2, $e4)));
		$this->assertNull($err);
		$this->assertEquals($ret[0]['code'], 200);

		list($ret, $err) = $this->rs->batchDelete(array($e3, $e4));
		$this->assertNull($err);
		$this->assertEquals($ret[0]['code'], 200);
		$this->assertEquals($ret[0]['code'], 200);

		$this->rs->batchDelete(array($e2, $e3, $e4));
	}

}
