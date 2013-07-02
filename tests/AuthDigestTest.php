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

