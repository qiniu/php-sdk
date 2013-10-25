<?php

require_once("bootstrap.php");

class IoTest extends PHPUnit_Framework_TestCase
{
	public $bucket;
	public $client;

	public function setUp()
	{
		initKeys();
		$this->client = new Qiniu_MacHttpClient(null);
		$this->bucket = getenv("QINIU_BUCKET_NAME");
	}

	public function testPutFile()
	{
		$key = 'testPutFile' . getTid();
		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);

		$putPolicy = new Qiniu_RS_PutPolicy($this->bucket);
		$upToken = $putPolicy->Token(null);
		$putExtra = new Qiniu_PutExtra();
		$putExtra->CheckCrc = 1;
		list($ret, $err) = Qiniu_PutFile($upToken, $key, __file__, $putExtra);
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
		$key = 'testPut' . getTid();
		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);

		$putPolicy = new Qiniu_RS_PutPolicy($this->bucket);
		$upToken = $putPolicy->Token(null);
		list($ret, $err) = Qiniu_Put($upToken, $key, "hello world!", null);
		$this->assertNull($err);
		$this->assertArrayHasKey('hash', $ret);
		var_dump($ret);

		list($ret, $err) = Qiniu_RS_Stat($this->client, $this->bucket, $key);
		$this->assertNull($err);
		var_dump($ret);

		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);
		$this->assertNull($err);
	}

	public function testPut_sizelimit()
	{
		$key = 'testPut_sizelimit' . getTid();
		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);

		$putPolicy = new Qiniu_RS_PutPolicy($this->bucket);
		$putPolicy->FsizeLimit = 1;
		$upToken = $putPolicy->Token(null);
		list($ret, $err) = Qiniu_Put($upToken, $key, "hello world!", null);
		$this->assertNull($ret);
		$this->assertEquals($err->Err, 'exceed FsizeLimit');
		var_dump($err);
	}

	public function testPut_mime_save()
	{
		$key = 'testPut_mime_save' . getTid();
		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);

		$putPolicy = new Qiniu_RS_PutPolicy($this->bucket);
		$putPolicy->DetectMime = 1;
		$putPolicy->SaveKey = $key;
		$upToken = $putPolicy->Token(null);
		$putExtra = new Qiniu_PutExtra();
		$putExtra->MimeType = 'image/jpg';
		list($ret, $err) = Qiniu_PutFile($upToken, null, __file__, $putExtra);
		$this->assertNull($err);

		list($ret, $err) = Qiniu_RS_Stat($this->client, $this->bucket, $key);
		$this->assertNull($err);
		$this->assertEquals($ret['mimeType'], 'application/x-httpd-php');
		var_dump($ret);

		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);
		$this->assertNull($err);
	}

	public function testPut_exclusive()
	{
		$key = 'testPut_exclusive' . getTid();
		$scope = $this->bucket . ':' . $key;
		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);

		$putPolicy = new Qiniu_RS_PutPolicy($scope);
		$putPolicy->InsertOnly = 1;
		$upToken = $putPolicy->Token(null);

		list($ret, $err) = Qiniu_Put($upToken, $key, "hello world!", null);
		$this->assertNull($err);
		list($ret, $err) = Qiniu_PutFile($upToken, $key, __file__, null);
		$this->assertNull($ret);
		$this->assertEquals($err->Err, 'file exists');
		var_dump($err);

		list($ret, $err) = Qiniu_RS_Stat($this->client, $this->bucket, $key);
		$this->assertNull($err);
		$this->assertEquals($ret['mimeType'], 'application/octet-stream');
		var_dump($ret);

		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);
		$this->assertNull($err);
	}
}

