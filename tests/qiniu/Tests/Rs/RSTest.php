<?php
require_once('../src/qiniu/auth_digest.php');

class RSTest extends PHPUnit_Framework_TestCase 
{
		public $rs;
		public $bucket = BUCKET_NAME;
		public $key = KEY_NAME;
		public $notExistKey = "not_exist_key";
		public $key2 = "php_sdk_test_key2";
		public $key3 = "php_sdk_test_key3";
		public $key4 = "php_sdk_test_key4";
		
		
		public function setUp()
		{
			$this->rs = new RSClient();
		}
		
		public function testStat()
		{
			list($ret, $err) = $this->rs->stat($this->bucket, $this->key);
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

			list($ret, $err) = $this->rs->copy($this->bucket, $this->key, $this->bucket, $this->key2);
			$this->assertNull($err);
			list($ret, $err) = $this->rs->move($this->bucket, $this->key2, $this->bucket, $this->key3);
			$this->assertNull($err);
			list($ret, $err) = $this->rs->delete($this->bucket, $this->key3);
			$this->assertNull($err);

			list($_, $err) = $this->rs->delete($this->bucket, $this->key2);
			$this->assertFalse($err === null);
		}
		
		public function testBatchStat()
		{
			$entries = array(new EntryPath($this->bucket, $this->key), new EntryPath($this->bucket, $this->key2));
			list($ret, $err) = $this->rs->batchStat($entries);
			$this->assertNull($err);

			$this->assertEquals($ret[0]['code'], 200);
			$this->assertEquals($ret[1]['code'], 612);
		}
		
		public function testBatchDeleteMoveCopy()
		{
			$e1 = new EntryPath($this->bucket, $this->key);
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
		//public function test

}