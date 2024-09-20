<?php
namespace Qiniu\Tests;

use PHPUnit\Framework\TestCase;

use Qiniu\Processing\PersistentFop;
use Qiniu\Storage\UploadManager;
use Qiniu\Region;
use Qiniu\Config;

class PfopTest extends TestCase
{
    private static function getConfig()
    {
        // use this func to test in test env
        // `null` means to use production env
        return null;
    }

    public function testPfopExecuteAndStatusWithSingleFop()
    {
        global $testAuth;
        $bucket = 'testres';
        $key = 'sintel_trailer.mp4';
        $pfop = new PersistentFop($testAuth, self::getConfig());

        $fops = 'avthumb/m3u8/segtime/10/vcodec/libx264/s/320x240';
        list($id, $error) = $pfop->execute($bucket, $key, $fops);
        $this->assertNull($error);
        list($status, $error) = $pfop->status($id);
        $this->assertNotNull($status);
        $this->assertNull($error);
    }


    public function testPfopExecuteAndStatusWithMultipleFops()
    {
        global $testAuth;
        $bucket = 'testres';
        $key = 'sintel_trailer.mp4';
        $fops = array(
            'avthumb/m3u8/segtime/10/vcodec/libx264/s/320x240',
            'vframe/jpg/offset/7/w/480/h/360',
        );
        $pfop = new PersistentFop($testAuth, self::getConfig());

        list($id, $error) = $pfop->execute($bucket, $key, $fops);
        $this->assertNull($error);

        list($status, $error) = $pfop->status($id);
        $this->assertNotNull($status);
        $this->assertNull($error);
    }

    private function pfopTypeTestData()
    {
        return array(
            array(
                'type' => null
            ),
            array(
                'type' => -1
            ),
            array(
                'type' => 0
            ),
            array(
                'type' => 1
            ),
            array(
                'type' => 2
            )
        );
    }

    public function testPfopWithIdleTimeType()
    {
        global $testAuth;

        $bucket = 'testres';
        $key = 'sintel_trailer.mp4';
        $persistentEntry =  \Qiniu\entry($bucket, 'test-pfop-type_1');
        $fops = 'avthumb/m3u8/segtime/10/vcodec/libx264/s/320x240|saveas/' . $persistentEntry;
        $pfop = new PersistentFop($testAuth, self::getConfig());

        $testCases = $this->pfopTypeTestData();

        foreach ($testCases as $testCase) {
            list($id, $error) = $pfop->execute(
                $bucket,
                $key,
                $fops,
                null,
                null,
                false,
                $testCase['type']
            );

            if (in_array($testCase['type'], array(null, 0, 1))) {
                $this->assertNull($error);
                list($status, $error) = $pfop->status($id);
                $this->assertNotNull($status);
                $this->assertNull($error);
                if ($testCase['type'] == 1) {
                    $this->assertEquals(1, $status['type']);
                }
                $this->assertNotEmpty($status['creationDate']);
            } else {
                $this->assertNotNull($error);
            }
        }
    }


    public function testPfopByUploadPolicy()
    {
        global $testAuth;
        $bucket = 'testres';
        $key = 'sintel_trailer.mp4';
        $persistentEntry =  \Qiniu\entry($bucket, 'test-pfop-type_1');
        $fops = 'avthumb/m3u8/segtime/10/vcodec/libx264/s/320x240|saveas/' . $persistentEntry;

        $testCases = $this->pfopTypeTestData();

        foreach ($testCases as $testCase) {
            $putPolicy = array(
                'persistentOps' => $fops,
                'persistentType' => $testCase['type']
            );

            if ($testCase['type'] == null) {
                unset($putPolicy['persistentType']);
            }

            $token = $testAuth->uploadToken(
                $bucket,
                $key,
                3600,
                $putPolicy
            );
            $upManager = new UploadManager(self::getConfig());
            list($ret, $error) = $upManager->putFile(
                $token,
                $key,
                __file__,
                null,
                'text/plain',
                true
            );

            if (in_array($testCase['type'], array(null, 0, 1))) {
                $this->assertNull($error);
                $this->assertNotEmpty($ret['persistentId']);
                $id = $ret['persistentId'];
            } else {
                $this->assertNotNull($error);
                return;
            }

            $pfop = new PersistentFop($testAuth, self::getConfig());
            list($status, $error) = $pfop->status($id);

            $this->assertNotNull($status);
            $this->assertNull($error);
            if ($testCase['type'] == 1) {
                $this->assertEquals(1, $status['type']);
            }
            $this->assertNotEmpty($status['creationDate']);
        }
    }

    public function testMkzip()
    {
        global $testAuth;
        $bucket = 'phpsdk';
        $key = 'php-logo.png';
        $pfop = new PersistentFop($testAuth, null);

        $url1 = 'http://phpsdk.qiniudn.com/php-logo.png';
        $url2 = 'http://phpsdk.qiniudn.com/php-sdk.html';
        $zipKey = 'test.zip';

        $fops = 'mkzip/2/url/' . \Qiniu\base64_urlSafeEncode($url1);
        $fops .= '/url/' . \Qiniu\base64_urlSafeEncode($url2);
        $fops .= '|saveas/' . \Qiniu\base64_urlSafeEncode("$bucket:$zipKey");

        list($id, $error) = $pfop->execute($bucket, $key, $fops);
        $this->assertNull($error);

        list($status, $error) = $pfop->status($id);
        $this->assertNotNull($status);
        $this->assertNull($error);
    }
}
