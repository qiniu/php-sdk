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
    protected $testStartDate;
    protected $testEndDate;
    protected $testGranularity;
    protected $testLogDate;
    protected $refreshUrl;
    protected $refreshDirs;
    protected $customDomain;
    protected $customDomain2;

    protected function setUp()
    {
        global $testAuth;
        $this->cdnManager = new CdnManager($testAuth);

        global $timestampAntiLeechEncryptKey;
        $this->encryptKey = $timestampAntiLeechEncryptKey;

        global $testStartDate;
        $this->testStartDate = $testStartDate;

        global $testEndDate;
        $this->testEndDate = $testEndDate;

        global $testGranularity;
        $this->testGranularity = $testGranularity;

        global $testLogDate;
        $this->testLogDate = $testLogDate;

        global $customDomain;
        $this->refreshUrl = $customDomain . '/sdktest.png';
        $this->refreshDirs = $customDomain;
        $this->customDomain = $customDomain;

        global $customDomain2;
        $this->customDomain2 = $customDomain2;
    }

    public function testRefreshUrls()
    {
        list($ret, $err) = $this->cdnManager->refreshUrls(array($this->refreshUrl));
        $this->assertNotNull($ret);
        $this->assertNull($err);
    }

    public function testRefreshDirs()
    {
        list($ret, $err) = $this->cdnManager->refreshDirs(array($this->refreshDirs));
        $this->assertNotNull($ret);
        $this->assertNull($err);
    }

    public function testRefreshUrlsAndDirs()
    {
        list($ret, $err) = $this->cdnManager->refreshUrlsAndDirs(array($this->refreshUrl), array($this->refreshDirs));
        $this->assertNotNull($ret);
        $this->assertNull($err);
    }

    public function testGetCdnRefreshList()
    {
        list($ret, $err) = $this->cdnManager->getCdnRefreshList(null, null, null, 'success');
        $this->assertNotNull($ret);
        $this->assertNull($err);
    }

    public function testPrefetchUrls()
    {
        list($ret, $err) = $this->cdnManager->prefetchUrls(array($this->refreshUrl));
        $this->assertNotNull($ret);
        $this->assertNull($err);
    }

    public function testGetCdnPrefetchList()
    {
        list($ret, $err) = $this->cdnManager->getCdnPrefetchList(null, null, 'success');
        $this->assertNotNull($ret);
        $this->assertNull($err);
    }

    public function testGetBandwidthData()
    {
        list($ret, $err) = $this->cdnManager->getBandwidthData(
            array($this->customDomain2),
            $this->testStartDate,
            $this->testEndDate,
            $this->testGranularity
        );
        $this->assertNotNull($ret);
        $this->assertNull($err);
    }

    public function testGetFluxData()
    {
        list($ret, $err) = $this->cdnManager->getFluxData(
            array($this->customDomain2),
            $this->testStartDate,
            $this->testEndDate,
            $this->testGranularity
        );
        $this->assertNotNull($ret);
        $this->assertNull($err);
    }

    public function testGetCdnLogList()
    {
        list($ret, $err) = $this->cdnManager->getCdnLogList(array('fake.qiniu.com'), $this->testLogDate);
        $this->assertNull($ret);
        $this->assertNotNull($err);
    }

    public function testCreateTimestampAntiLeechUrl()
    {
        $signUrl = $this->cdnManager->createTimestampAntiLeechUrl($this->refreshUrl, $this->encryptKey, 3600);
        $response = Client::get($signUrl);
        $this->assertEquals($response->statusCode, 200);
        $this->assertNull($response->error);

        $signUrl = $this->cdnManager->createTimestampAntiLeechUrl(
            $this->refreshUrl . '?qiniu',
            $this->encryptKey,
            3600
        );
        $response = Client::get($signUrl);
        $this->assertEquals($response->statusCode, 200);
        $this->assertNull($response->error);
    }
}
