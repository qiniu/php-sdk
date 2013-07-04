<?php

require_once("bootstrap.php");

class RsUtilsTest extends PHPUnit_Framework_TestCase
{
	public $bucket;
	public $client;

	public function setUp()
	{
		initKeys();
		$this->client = new Qiniu_MacHttpClient(null);
		$this->bucket = getenv("QINIU_BUCKET_NAME");
	}

	public function testRput()
	{
		$key = 'tmp/testRput' . getTid();
		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);

		$reader = new MockReader;
		list($ret, $err) = Qiniu_RS_Rput($this->client, $this->bucket, $key, $reader, 5, null);
		$this->assertNull($err);
		var_dump($ret);
		$this->assertEquals($ret['hash'], "Fnvgeq9GDVk6Mj0Nsz2gW2S_3LOl");

		list($ret, $err) = Qiniu_RS_Stat($this->client, $this->bucket, $key);
		$this->assertNull($err);
		var_dump($ret);

		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);
		$this->assertNull($err);
	}

	public function testRputFile()
	{
		$key = 'tmp/testRputFile' . getTid();
		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);

		list($ret, $err) = Qiniu_RS_RputFile($this->client, $this->bucket, $key, __file__, null);
		$this->assertNull($err);
		var_dump($ret);
		$this->assertArrayHasKey('hash', $ret);

		list($ret, $err) = Qiniu_RS_Stat($this->client, $this->bucket, $key);
		$this->assertNull($err);
		var_dump($ret);

		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);
		$this->assertNull($err);
	}

	public function testPutFile()
	{
		$key = 'tmp/testPutFile' . getTid();
		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);

		$putExtra = new Qiniu_PutExtra();
		$putExtra->CheckCrc = 1;
		list($ret, $err) = Qiniu_RS_PutFile($this->client, $this->bucket, $key, __file__, $putExtra);
		$this->assertNull($err);
		$this->assertArrayHasKey('hash', $ret);
		var_dump($ret);

		list($ret, $err) = Qiniu_RS_Stat($this->client, $this->bucket, $key);
		$this->assertNull($err);
		var_dump($ret);

		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);
		$this->assertNull($err);
	}

	public function testPut()
	{
		$key = 'tmp/testPut' . getTid();
		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);

		list($ret, $err) = Qiniu_RS_Put($this->client, $this->bucket, $key, 'hello world!', null);
		$this->assertNull($err);
		$this->assertArrayHasKey('hash', $ret);
		var_dump($ret);

		list($ret, $err) = Qiniu_RS_Stat($this->client, $this->bucket, $key);
		$this->assertNull($err);
		var_dump($ret);

		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);
		$this->assertNull($err);
	}
}

