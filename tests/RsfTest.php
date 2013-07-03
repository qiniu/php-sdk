<?php

require_once("bootstrap.php");

class RsfTest extends PHPUnit_Framework_TestCase
{
	public $client;
	public $bucket;

	public function setUp()
	{
		initKeys();
		$this->client = new Qiniu_RSF_Client(null);
		$this->bucket = getenv('QINIU_BUCKET_NAME');
		$this->key = getenv('QINIU_KEY_NAME');
	}


	//$bucket, $prefix, $marker, $limit
	public function testListPrefix()
	{
		echo $this->bucket;
		list($ret, $err) = $this->client->ListPrefix($this->bucket, null, null, null);
		$this->assertEquals($err->Err, 'EOF');

		list($ret, $err) = $this->client->ListPrefix($this->bucket, null, null, 1);
		$this->assertArrayHasKey('marker', $ret);

		list($ret, $err) = $this->client->ListPrefix($this->bucket, $this->key, null, null);
		$this->assertLessThanOrEqual(1, count($ret['items']));
	}
}
