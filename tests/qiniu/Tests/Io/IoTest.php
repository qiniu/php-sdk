<?php
require_once('../src/qiniu/io.php');
require_once('../src/qiniu/auth_token.php');

class IoTest extends PHPUnit_Framework_TestCase
{
	public $upToken;
	public $extra;
	public $bucket = BUCKET_NAME;
	
	public function setUp()
	{
		$putPolicy = new PutPolicy($this->bucket);		
		$this->upToken = $putPolicy->token();
		$this->extra = new PutExtra();
		$this->extra->bucket = $this->bucket;
	}
	
	public function testPut()
	{
		$key = 'file_key_put';
		$body = 'qiniustorage';
		list($ret, $err) = put($this->upToken, $key, $body, $this->extra);
		$this->assertNull(null);
	}
	
	public function testPutFile()
	{
		$key = 'file_key_putfile';
		list($ret, $err) = putFile($this->upToken, $key, __FILE__, $this->extra);
		$this->assertNull($err);
	}
}