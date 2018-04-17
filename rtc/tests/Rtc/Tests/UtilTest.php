<?php

namespace Qiniu\Rtc
{
    function time()
    {
        return isset($_SERVER['override_qiniu_auth_time'])
            ? 1234567890
            : \time();
    }
}

namespace Rtc\Tests
{
    use \Qiniu\Rtc\Utils;

    class Base64Test extends \PHPUnit_Framework_TestCase
    {
        public function testUrlSafe()
        {
            $a = '你好';
            $b = Utils::base64UrlEncode($a);
            $this->assertEquals($a, Utils::base64UrlDecode($b));
        }
    }
}
