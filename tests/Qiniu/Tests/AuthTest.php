<?php
// Hack to override the time returned from the S3SignatureV4
// @codingStandardsIgnoreStart
namespace Qiniu {
    function time()
    {
        return isset($_SERVER['override_qiniu_auth_time'])
            ? 1234567890
            : \time();
    }
}

namespace Qiniu\Tests {
    use Qiniu\Auth;

    // @codingStandardsIgnoreEnd

    class AuthTest extends \PHPUnit_Framework_TestCase
    {

        public function testSign()
        {
            global $dummyAuth;
            $token = $dummyAuth->sign('test');
            $this->assertEquals('vHg2e7nOh7Jsucv2Azr5FH6omPgX22zoJRWa0FN5:ctIk00JjhyKWX6DfmX-Rqy6e2U0=', $token);
        }

        public function testSignWithData()
        {
            global $dummyAuth;
            $token = $dummyAuth->signWithData('test');
            $this->assertEquals('vHg2e7nOh7Jsucv2Azr5FH6omPgX22zoJRWa0FN5:D0TKcI_ZrPkfVcQf7jGDMEyIa-c=:dGVzdA==', $token);
        }

        public function testSignRequest()
        {
            global $dummyAuth;
            $token = $dummyAuth->signRequest('http://www.qiniu.com?go=1', 'test', '');
            $this->assertEquals('vHg2e7nOh7Jsucv2Azr5FH6omPgX22zoJRWa0FN5:SUg7X6VuSkvoAtB7n7LmArDomzw=', $token);
            $ctype = 'application/x-www-form-urlencoded';
            $token = $dummyAuth->signRequest('http://www.qiniu.com?go=1', 'test', $ctype);
            $this->assertEquals($token, 'abcdefghklmnopq:svWRNcacOE-YMsc70nuIYdaa1e4=');
        }

        public function testPrivateDownloadUrl()
        {
            global $dummyAuth;
            $_SERVER['override_qiniu_auth_time'] = true;
            $url = $dummyAuth->privateDownloadUrl('http://www.qiniu.com?go=1');
            $expect = 'http://www.qiniu.com?go=1&e=1234571490&token=vHg2e7nOh7Jsucv2Azr5FH6omPgX22zoJRWa0FN5';
            $this->assertEquals($expect, $url);
            unset($_SERVER['override_qiniu_auth_time']);
        }

        public function testUploadToken()
        {
            global $dummyAuth;
            $_SERVER['override_qiniu_auth_time'] = true;
            $token = $dummyAuth->uploadToken('1', '2', 3600, array('endUser' => 'y'));
            // @codingStandardsIgnoreStart
            $exp = 'vHg2e7nOh7Jsucv2Azr5FH6omPgX22zoJRWa0FN5:8KDwZ4Z1nDoww5XFn_9RafNWr2A=:eyJlbmRVc2VyIjoieSIsInNjb3BlIjoiMToyIiwiZGVhZGxpbmUiOjEyMzQ1NzE0OTB9';
            // @codingStandardsIgnoreEnd
            $this->assertEquals($exp, $token);
            unset($_SERVER['override_qiniu_auth_time']);
        }

        public function testVerifyCallback()
        {
        }
    }
}
