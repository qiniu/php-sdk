<?php

require_once("bootstrap.php");

class AuthDigestTest extends PHPUnit_Framework_TestCase
{

	public function testEncode()
	{
		$cases = array(
			'abc' => 'YWJj',
			'abc0=?e' => 'YWJjMD0_ZQ=='
		);
		foreach ($cases as $k => $v) {
			$v1 = Qiniu_Encode($k);
			$this->assertEquals($v, $v1);
		}
	}

	public function testVerifyCallback()
	{
		initKeys();
		$mac1 = Qiniu_RequireMac(null);
		$auth = 'QBox Vhiv6a22kVN_zhtetbPNeG9sY3JUL1HG597EmBwQ:JrRyg9So6DNrNDY5qj1sygt0SmQ=';
		$url = 'http://rs.qbox.me/batch';
		$body = 'op=/delete/cGhwc2RrOnRlc3RPcDI=&op=/delete/cGhwc2RrOnRlc3RPcDM=&op=/delete/cGhwc2RrOnRlc3RPcDQ=';
		$pass = $mac1->VerifyCallback($auth, $url, $body);
		$this->assertTrue($pass);
	}
	
/*	public function testSetKeys()
	{
		$mac1 = Qiniu_RequireMac(null);
		$this->assertTrue(!empty($mac1->AccessKey) && !empty($mac1->SecretKey), 'please provide keys');

		Qiniu_SetKeys('abc', 'def');
		$mac = Qiniu_RequireMac(null);
		$this->assertEquals($mac->AccessKey, 'abc');
		$this->assertEquals($mac->SecretKey, 'def');

		Qiniu_SetKeys($mac1->AccessKey, $mac1->SecretKey);
	}
*/
}

