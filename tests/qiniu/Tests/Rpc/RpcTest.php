<?php
require_once('../src/qiniu/rpc.php');
require_once('../src/qiniu/config.php');

class TestClient extends Client
{
	public $path;
	public $body;
	
	public function roundTripper($method, $path, $body)
	{
		$this->path = $path;
		$this->body = $body;
	}
} 

class RpcTest extends PHPUnit_Framework_TestCase
{
	public $testClient;
	
	public function setUp()
	{
		$this->testClient = new TestClient(RS_HOST);
	}
	
	public function testCall() 
	{
		$this->testClient->call('/qiniu');
		$this->assertEquals('/qiniu', $this->testClient->path);
	}
	
	public function testCallWith()
	{
		$this->testClient->callWith('/qiniu', 'storage');
		$this->assertEquals('storage', $this->testClient->body);
	}
	
	public function testCallWithMultipart()
	{
		$fields = array('auth' => 'upTokenString');
		
		$this->testClient->callWithMultiPart('/qiniu', $fields);
		$this->assertEquals(strlen($this->testClient->body), $this->testClient->header['Content-Length']);
		$ct = $this->testClient->header['Content-Type'];
		$boundary = substr($ct, strpos($ct, 'boundary=') + 9);
		$disposition = "Content-Disposition: form-data; name=auth";
		$body = "--$boundary\r\n$disposition\r\n\r\nupTokenString\r\n--$boundary\r\n";
		$this->assertEquals($body, $this->testClient->body);
	}
}

