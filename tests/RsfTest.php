<?php

require_once("bootstrap.php");

class RsfTest extends PHPUnit_Framework_TestCase
{
	public $client;
	public $bucket;

	public function setUp()
	{
		initKeys();
		$this->client = new Qiniu_MacHttpClient(null);
		$this->bucket = getenv('QINIU_BUCKET_NAME');
		$this->key = getenv('QINIU_KEY_NAME');
	}

	public function testListPrefix()
	{
		echo $this->bucket;
		list($items, $markerOut, $err) = Qiniu_RSF_ListPrefix($this->client, $this->bucket);
		$this->assertEquals($err, Qiniu_RSF_EOF);
		$this->assertEquals($markerOut, '');

		list($items, $markerOut, $err) = Qiniu_RSF_ListPrefix($this->client, $this->bucket, '', '', 1);
		$this->assertFalse($markerOut === '');

		list($items, $markerOut, $err) = Qiniu_RSF_ListPrefix($this->client, $this->bucket, $this->key);
		$this->assertLessThanOrEqual(count($items), 1);
	}
}
