<?php
// Hack to override the time returned from the S3SignatureV4
// @codingStandardsIgnoreStart
namespace Qiniu

{
    function time()
    {
        return isset($_SERVER['override_qiniu_auth_time'])
            ? 1234567890
            : \time();
    }
}

namespace Qiniu\Tests

{
    use Qiniu\Auth;

    // @codingStandardsIgnoreEnd

    class AuthTest extends \PHPUnit_Framework_TestCase
    {

        public function testToken()
        {
            global $dummyAuth;
            $token = $dummyAuth->token('test');
            $this->assertEquals('abcdefghklmnopq:mSNBTR7uS2crJsyFr2Amwv1LaYg=', $token);
        }

        public function testTokenWithData()
        {
            global $dummyAuth;
            $token = $dummyAuth->tokenWithData('test');
            $this->assertEquals('abcdefghklmnopq:-jP8eEV9v48MkYiBGs81aDxl60E=:dGVzdA==', $token);
        }

        public function testTokenOfRequest()
        {
            global $dummyAuth;
            $token = $dummyAuth->tokenOfRequest('http://www.qiniu.com?go=1', 'test', '');
            $this->assertEquals('abcdefghklmnopq:cFyRVoWrE3IugPIMP5YJFTO-O-Y=', $token);
            $ctype = 'application/x-www-form-urlencoded';
            $token = $dummyAuth->tokenOfRequest('http://www.qiniu.com?go=1', 'test', $ctype);
            $this->assertEquals($token, 'abcdefghklmnopq:svWRNcacOE-YMsc70nuIYdaa1e4=');
        }

        public function testPrivateDownloadUrl()
        {
            global $dummyAuth;
            $_SERVER['override_qiniu_auth_time'] = true;
            $url =  $dummyAuth->privateDownloadUrl('http://www.qiniu.com?go=1');
            $expect = 'http://www.qiniu.com?go=1&e=1234571490&token=abcdefghklmnopq:8vzBeLZ9W3E4kbBLFLW0Xe0u7v4=';
            $this->assertEquals($expect, $url);
            unset($_SERVER['override_qiniu_auth_time']);
        }

        /**
        * @expectedException        InvalidArgumentException
        * @expectedExceptionMessage asyncOps has deprecated
        */
        public function testDeprecatedPolicy()
        {
            global $dummyAuth;
            $token = $dummyAuth->uploadToken('1', null, 3600, array('asyncOps'=> 1));
        }

        public function testUploadToken()
        {
            global $dummyAuth;
            $_SERVER['override_qiniu_auth_time'] = true;
            $token = $dummyAuth->uploadToken('1', '2', 3600, array('endUser'=> 'y'));
            $expect = 'abcdefghklmnopq:x53hx7845_pygLochtRWlnrL-90=:eyJzY29wZSI6IjE6MiIsImRlYWRsaW5lIjoxMjM0NTcxNDkwfQ==';
            $this->assertEquals($expect, $token);
            unset($_SERVER['override_qiniu_auth_time']);
        }

        public function testVerifyCallback()
        {

        }
    }
}
