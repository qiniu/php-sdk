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
		$putExtra->Params = array('x:test'=>'test');
		$putExtra->CheckCrc = 1;
		list($ret, $err) = Qiniu_PutFile($upToken, $key, __file__, $putExtra);
		$this->assertNull($err);
		$this->assertArrayHasKey('hash', $ret);
		$this->assertArrayHasKey('x:test', $ret);
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
		$putExtra = new Qiniu_PutExtra();
		$putExtra->Params = array('x:test'=>'test');
		list($ret, $err) = Qiniu_Put($upToken, $key, "hello world!", $putExtra);
		$this->assertNull($err);
		$this->assertArrayHasKey('hash', $ret);
		$this->assertArrayHasKey('x:test', $ret);
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
		$this->assertEquals($ret['mimeType'], 'application/x-php');
		var_dump($ret);

		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);
		$this->assertNull($err);
	}

	public function testPut_mimetype() {
		$key = 'testPut_mimetype' . getTid();
		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);
		$scope = $this->bucket . ":" . $key;

		$putPolicy = new Qiniu_RS_PutPolicy($scope);
		$putPolicy->ReturnBody = '{"key":$(key),"mimeType":$(mimeType)}';
		$upToken = $putPolicy->Token(null);

		$putExtra = new Qiniu_PutExtra();
		$putExtra->MimeType = 'image/jpg';

		list($ret1, $err1) = Qiniu_PutFile($upToken, $key, __file__, $putExtra);
		var_dump($ret1);
		$this->assertNull($err1);
		$this->assertEquals($ret1['mimeType'], 'image/jpg');

		list($ret2, $err2) = Qiniu_Put($upToken, $key, "hello world", $putExtra);
		var_dump($ret2);
		$this->assertNull($err2);
		$this->assertEquals($ret2['mimeType'], 'image/jpg');

		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);
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
		$this->assertEquals($err->Code, 614);
		var_dump($err);

		list($ret, $err) = Qiniu_RS_Stat($this->client, $this->bucket, $key);
		$this->assertNull($err);
		$this->assertEquals($ret['mimeType'], 'application/octet-stream');
		var_dump($ret);

		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);
		$this->assertNull($err);
	}
	public function testPut_transform() {
		$key = 'testPut_transform' . getTid();
		$scope = $this->bucket . ':' . $key;
		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);

		$putPolicy = new Qiniu_RS_PutPolicy($scope);
		$putPolicy->Transform = "imageMogr2/format/png";
		$putPolicy->ReturnBody = '{"key": $(key), "hash": $(etag), "mimeType":$(mimeType)}';
		$upToken = $putPolicy->Token(null);

		list($ret, $err) = Qiniu_PutFile($upToken, $key, __file__, null);
		$this->assertNull($ret);
		$this->assertEquals($err->Err, "fop fail or timeout");
		var_dump($err);

		$pic_path = "../docs/gist/logo.jpg";
		list($ret, $err) = Qiniu_PutFile($upToken, $key, $pic_path, null);
		$this->assertNull($err);
		$this->assertEquals($ret["mimeType"], "image/png");
		var_dump($ret);
	}
	public function testPut_mimeLimit() {
		$key = 'testPut_mimeLimit' . getTid();
		$scope = $this->bucket . ':' . $key;
		$err = Qiniu_RS_Delete($this->client, $this->bucket, $key);

		$putPolicy = new Qiniu_RS_PutPolicy($scope);
		$putPolicy->MimeLimit = "image/*";
		$upToken = $putPolicy->Token(null);

		list($ret, $err) = Qiniu_PutFile($upToken, $key, __file__, null);
		$this->assertNull($ret);
		$this->assertEquals($err->Err, "limited mimeType: this file type is forbidden to upload");
		var_dump($err);
	}
}

