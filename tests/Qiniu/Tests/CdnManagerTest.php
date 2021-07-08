<?php
/**
 * Created by IntelliJ IDEA.
 * User: wf
 * Date: 2017/6/21
 * Time: AM8:46
 */

namespace Qiniu\Tests;

use Qiniu\Cdn\CdnManager;
use Qiniu\Http\Client;

class CdnManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $cdnManager;
    protected $encryptKey;
    protected $imgUrl;

    protected function setUp()
    {
        global $timestampAntiLeechEncryptKey;
        global $customDomain;
        global $testAuth;

        $this->cdnManager = new CdnManager($testAuth);
        $this->encryptKey = $timestampAntiLeechEncryptKey;
        $this->imgUrl = $customDomain . '/sdktest.png';
    }

    public function testCreateTimestampAntiLeechUrl()
    {

        $signUrl = $this->cdnManager->createTimestampAntiLeechUrl($this->imgUrl, $this->encryptKey, 3600);

        $response = Client::get($signUrl);
        
        $this->assertEquals($response->statusCode, 200);
        $this->assertNull($response->error);
    }
}
