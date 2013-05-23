<?php
require_once('../src/qiniu/auth_token.php');

class AuthTokenTest extends PHPUnit_Framework_TestCase
{
	public $bucket = BUCKET_NAME;
	
	public function setUp()
	{
		
	}
	
	public function testPutPolicy()
	{		
		$putpolicy = new PutPolicy($this->bucket);
		$putpolicy->customer = 'qiniu';
		$token = $putpolicy->token();
		$tokens = explode(":", $token);
		$this->assertEquals($tokens[0], ACCESS_KEY);
		
		$data = URLSafeBase64Decode($tokens[2]);
		$data = json_decode($data);
		$this->assertEquals($data->scope, $this->bucket);
		$this->assertEquals($data->customer, $putpolicy->customer);
		
		$checkSum = hash_hmac('sha1', $tokens[2], SECRET_KEY, true);
		$encodeCheckSum = URLSafeBase64Encode($checkSum);
		$this->assertEquals($encodeCheckSum, $tokens[1]);
	}
	
	public function testGetPolic()
	{
		$getPolicy = new GetPolicy($this->bucket);
		$tokens = explode(':', $getPolicy->token());
		$data = URLSafeBase64Decode($tokens[2]);
		$data = json_decode($data);
		$this->assertEquals($data->S, $this->bucket);
	}

}

function URLSafeBase64Decode($str) // URLSafeBase64Encode
{
	$find = array("-","_");
	$replace = array("+", "/");
	$unUrlSafe = str_replace($find, $replace, $str);
	return base64_decode($unUrlSafe);
}