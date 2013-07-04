<?php

require_once("bootstrap.php");

class MockReader
{
	private $off = 0;

	public function __construct($off = 0)
	{
		$this->off = $off;
	}

	public function Read($bytes) // => ($data, $err)
	{
		$off = $this->off;
		$data = '';
		for ($i = 0; $i < $bytes; $i++) {
			$data .= chr(65 + ($off % 26)); // ord('A') = 65
			$off++;
		}
		$this->off = $off;
		return array($data, null);
	}
}

class RioTest extends PHPUnit_Framework_TestCase
{
	public $bucket;
	public $client;

	public function setUp()
	{
		initKeys();
		$this->client = new Qiniu_MacHttpClient(null);
		$this->bucket = getenv("QINIU_BUCKET_NAME");
	}

	public function testMockReader()
	{
		$reader = new MockReader;
		list($data) = $reader->Read(5);
		$this->assertEquals($data, "ABCDE");

		list($data) = $reader->Read(27);
		$this->assertEquals($data, "FGHIJKLMNOPQRSTUVWXYZABCDEF");
	}

	public function testPut()
	{
		$key = 'testRioPut' . getTid();
		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);

		$putPolicy = new Qiniu_RS_PutPolicy($this->bucket);
		$upToken = $putPolicy->Token(null);
		$putExtra = new Qiniu_Rio_PutExtra($this->bucket);
		$reader = new MockReader;
		list($ret, $err) = Qiniu_Rio_Put($upToken, $key, $reader, 5, $putExtra);
		$this->assertNull($err);
		$this->assertEquals($ret['hash'], "Fnvgeq9GDVk6Mj0Nsz2gW2S_3LOl");
		var_dump($ret);

		list($ret, $err) = Qiniu_RS_Stat($this->client, $this->bucket, $key);
		$this->assertNull($err);
		var_dump($ret);

		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);
		$this->assertNull($err);
	}

	public function testLargePut()
	{
		$key = 'testRioLargePut' . getTid();
		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);

		$putPolicy = new Qiniu_RS_PutPolicy($this->bucket);
		$upToken = $putPolicy->Token(null);
		$putExtra = new Qiniu_Rio_PutExtra($this->bucket);
		$reader = new MockReader;
		list($ret, $err) = Qiniu_Rio_Put($upToken, $key, $reader, QINIU_RIO_BLOCK_SIZE + 5, $putExtra);
		$this->assertNull($err);
		$this->assertEquals($ret['hash'], "lgQEOCZ8Ievliq8XOfZmWTndgOll");
		var_dump($ret);

		list($ret, $err) = Qiniu_RS_Stat($this->client, $this->bucket, $key);
		$this->assertNull($err);
		var_dump($ret);

		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);
		$this->assertNull($err);
	}
}

