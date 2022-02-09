<?php

namespace Qiniu\Tests;

use Qiniu\Config;
use Qiniu\Storage\BucketManager;

class BucketTest extends \PHPUnit_Framework_TestCase
{
    protected $bucketManager;
    protected $dummyBucketManager;
    protected $bucketName;
    protected $key;
    protected $key2;
    protected $customCallbackURL;

    protected function setUp()
    {
        global $bucketName;
        global $key;
        global $key2;
        $this->bucketName = $bucketName;
        $this->key = $key;
        $this->key2 = $key2;

        global $customCallbackURL;
        $this->customCallbackURL = $customCallbackURL;

        global $testAuth;
        $config = new Config();
        $this->bucketManager = new BucketManager($testAuth, $config);

        global $dummyAuth;
        $this->dummyBucketManager = new BucketManager($dummyAuth);
    }

    public function testBuckets()
    {

        list($list, $error) = $this->bucketManager->buckets();
        $this->assertNull($error);
        $this->assertTrue(in_array($this->bucketName, $list));

        list($list2, $error) = $this->dummyBucketManager->buckets();
        $this->assertEquals(401, $error->code());
        $this->assertNotNull($error->message());
        $this->assertNotNull($error->getResponse());
        $this->assertNull($list2);
    }

    public function testListbuckets()
    {
        list($ret, $error) = $this->bucketManager->listbuckets('z0');
        $this->assertNull($error);
        $this->assertNotNull($ret);
    }

    public function testCreateBucket()
    {
        list($ret, $error) = $this->bucketManager->createBucket('phpsdk-ci-test');
        $this->assertNull($error);
        $this->assertNotNull($ret);
    }

    public function testDeleteBucket()
    {
        list($ret, $error) = $this->bucketManager->deleteBucket('phpsdk-ci-test');
        $this->assertNull($error);
        $this->assertNotNull($ret);
    }

    public function testDomains()
    {
        list($ret, $error) = $this->bucketManager->domains($this->bucketName);
        $this->assertNull($error);
        $this->assertNotNull($ret);
    }

    public function testBucketInfo()
    {
        list($ret, $error) = $this->bucketManager->bucketInfo($this->bucketName);
        $this->assertNull($error);
        $this->assertNotNull($ret);
    }

    public function testBucketInfos()
    {
        list($ret, $error) = $this->bucketManager->bucketInfos('z0');
        $this->assertNull($error);
        $this->assertNotNull($ret);
    }

    public function testList()
    {
        list($ret, $error) = $this->bucketManager->listFiles($this->bucketName, null, null, 10);
        $this->assertNull($error);
        $this->assertNotNull($ret['items'][0]);
        $this->assertNotNull($ret['marker']);
    }

    public function testListFilesv2()
    {
        list($ret, $error) = $this->bucketManager->listFilesv2($this->bucketName, null, null, 10);
        $this->assertNull($error);
        $this->assertNotNull($ret);
    }

    public function testBucketLifecycleRule()
    {
        list($ret, $error) = $this->bucketManager->bucketLifecycleRule($this->bucketName, 'demo', 'test', 80, 70);
        $this->assertNull($error);
        $this->assertNotNull($ret);
    }

    public function testGetbucketLifecycleRule()
    {
        list($ret, $error) = $this->bucketManager->getBucketLifecycleRules($this->bucketName);
        $this->assertNull($error);
        $this->assertNotNull($ret);
    }

    public function testUpdatebucketLifecycleRule()
    {
        list($ret, $error) = $this->bucketManager->updateBucketLifecycleRule(
            $this->bucketName,
            'demo',
            'testupdate',
            80,
            70
        );
        $this->assertNull($error);
        $this->assertNotNull($ret);
    }

    public function testDeleteBucketLifecycleRule()
    {
        list($ret, $error) = $this->bucketManager->deleteBucketLifecycleRule($this->bucketName, 'demo');
        $this->assertNull($error);
        $this->assertNotNull($ret);
    }

    public function testPutBucketEvent()
    {
        list($ret, $error) = $this->bucketManager->putBucketEvent(
            $this->bucketName,
            'bucketevent',
            'test',
            'img',
            array('copy'),
            $this->customCallbackURL
        );
        $this->assertNull($error);
        $this->assertNotNull($ret);
    }

    public function testUpdateBucketEvent()
    {
        list($ret, $error) = $this->bucketManager->updateBucketEvent(
            $this->bucketName,
            'bucketevent',
            'test',
            'video',
            array('copy'),
            $this->customCallbackURL
        );
        $this->assertNull($error);
        $this->assertNotNull($ret);
    }

    public function testGetBucketEvent()
    {
        list($ret, $error) = $this->bucketManager->getBucketEvents($this->bucketName);
        $this->assertNull($error);
        $this->assertNotNull($ret);
    }

    public function testDeleteBucketEvent()
    {
        list($ret, $error) = $this->bucketManager->deleteBucketEvent($this->bucketName, 'bucketevent');
        $this->assertNull($error);
        $this->assertNotNull($ret);
    }

    public function testStat()
    {
        list($stat, $error) = $this->bucketManager->stat($this->bucketName, $this->key);
        $this->assertNull($error);
        $this->assertNotNull($stat);
        $this->assertNotNull($stat['hash']);

        list($stat, $error) = $this->bucketManager->stat($this->bucketName, 'nofile');
        $this->assertEquals(612, $error->code());
        $this->assertNotNull($error->message());
        $this->assertNull($stat);

        list($stat, $error) = $this->bucketManager->stat('nobucket', 'nofile');
        $this->assertEquals(631, $error->code());
        $this->assertNotNull($error->message());
        $this->assertNull($stat);
    }

    public function testDelete()
    {
        list($ret, $error) = $this->bucketManager->delete($this->bucketName, 'del');
        $this->assertNotNull($error);
        $this->assertNull($ret);
    }


    public function testRename()
    {
        $key = 'renamefrom' . rand();
        $this->bucketManager->copy($this->bucketName, $this->key, $this->bucketName, $key);
        $key2 = 'renameto' . $key;
        list($ret, $error) = $this->bucketManager->rename($this->bucketName, $key, $key2);
        $this->assertNull($error);
        list($ret, $error) = $this->bucketManager->delete($this->bucketName, $key2);
        $this->assertNull($error);
    }


    public function testCopy()
    {
        $key = 'copyto' . rand();
        $this->bucketManager->delete($this->bucketName, $key);

        list($ret, $error) = $this->bucketManager->copy(
            $this->bucketName,
            $this->key,
            $this->bucketName,
            $key
        );
        $this->assertNull($error);

        //test force copy
        list($ret, $error) = $this->bucketManager->copy(
            $this->bucketName,
            $this->key2,
            $this->bucketName,
            $key,
            true
        );
        $this->assertNull($error);

        list($key2Stat,) = $this->bucketManager->stat($this->bucketName, $this->key2);
        list($key2CopiedStat,) = $this->bucketManager->stat($this->bucketName, $key);

        $this->assertEquals($key2Stat['hash'], $key2CopiedStat['hash']);

        list($ret, $error) = $this->bucketManager->delete($this->bucketName, $key);
        $this->assertNull($error);
    }


    public function testChangeMime()
    {
        list($ret, $error) = $this->bucketManager->changeMime(
            $this->bucketName,
            'php-sdk.html',
            'text/html'
        );
        $this->assertNull($error);
    }

    public function testPrefetch()
    {
        list($ret, $error) = $this->bucketManager->prefetch(
            $this->bucketName,
            'php-sdk.html'
        );
        $this->assertNull($error);
    }

    public function testPrefetchFailed()
    {
        list($ret, $error) = $this->bucketManager->prefetch(
            'fakebucket',
            'php-sdk.html'
        );
        $this->assertNotNull($error);
        $this->assertNull($ret);
    }

    public function testFetch()
    {
        list($ret, $error) = $this->bucketManager->fetch(
            'http://developer.qiniu.com/docs/v6/sdk/php-sdk.html',
            $this->bucketName,
            'fetch.html'
        );
        $this->assertNull($error);
        $this->assertArrayHasKey('hash', $ret);

        list($ret, $error) = $this->bucketManager->fetch(
            'http://developer.qiniu.com/docs/v6/sdk/php-sdk.html',
            $this->bucketName,
            ''
        );
        $this->assertNull($error);
        $this->assertArrayHasKey('key', $ret);

        list($ret, $error) = $this->bucketManager->fetch(
            'http://developer.qiniu.com/docs/v6/sdk/php-sdk.html',
            $this->bucketName
        );
        $this->assertNull($error);
        $this->assertArrayHasKey('key', $ret);
    }

    public function testFetchFailed()
    {
        list($ret, $error) = $this->bucketManager->fetch(
            'http://developer.qiniu.com/docs/v6/sdk/php-sdk.html',
            'fakebucket'
        );
        $this->assertNotNull($error);
        $this->assertNull($ret);
    }

    public function testAsynchFetch()
    {
        list($ret, $error) = $this->bucketManager->asynchFetch(
            'http://devtools.qiniu.com/qiniu.png',
            $this->bucketName,
            null,
            'qiniu.png'
        );
        $this->assertNull($error);
        $this->assertArrayHasKey('id', $ret);

        list($ret, $error) = $this->bucketManager->asynchFetch(
            'http://devtools.qiniu.com/qiniu.png',
            $this->bucketName,
            null,
            ''
        );
        $this->assertNull($error);
        $this->assertArrayHasKey('id', $ret);

        list($ret, $error) = $this->bucketManager->asynchFetch(
            'http://devtools.qiniu.com/qiniu.png',
            $this->bucketName
        );
        $this->assertNull($error);
        $this->assertArrayHasKey('id', $ret);
    }

    public function testAsynchFetchFailed()
    {
        list($ret, $error) = $this->bucketManager->asynchFetch(
            'http://devtools.qiniu.com/qiniu.png',
            'fakebucket'
        );
        $this->assertNotNull($error);
        $this->assertNull($ret);
    }


    public function testBatchCopy()
    {
        $key = 'copyto' . rand();
        $ops = BucketManager::buildBatchCopy(
            $this->bucketName,
            array($this->key => $key),
            $this->bucketName,
            true
        );
        list($ret, $error) = $this->bucketManager->batch($ops);
        $this->assertEquals(200, $ret[0]['code']);
        $ops = BucketManager::buildBatchDelete($this->bucketName, array($key));
        list($ret, $error) = $this->bucketManager->batch($ops);
        $this->assertEquals(200, $ret[0]['code']);
    }

    public function testBatchMove()
    {
        $key = 'movefrom' . rand();
        $this->bucketManager->copy($this->bucketName, $this->key, $this->bucketName, $key);
        $key2 = $key . 'to';
        $ops = BucketManager::buildBatchMove(
            $this->bucketName,
            array($key => $key2),
            $this->bucketName,
            true
        );
        list($ret, $error) = $this->bucketManager->batch($ops);
        $this->assertEquals(200, $ret[0]['code']);
        list($ret, $error) = $this->bucketManager->delete($this->bucketName, $key2);
        $this->assertNull($error);
    }

    public function testBatchRename()
    {
        $key = 'rename' . rand();
        $this->bucketManager->copy($this->bucketName, $this->key, $this->bucketName, $key);
        $key2 = $key . 'to';
        $ops = BucketManager::buildBatchRename($this->bucketName, array($key => $key2), true);
        list($ret, $error) = $this->bucketManager->batch($ops);
        $this->assertEquals(200, $ret[0]['code']);
        list($ret, $error) = $this->bucketManager->delete($this->bucketName, $key2);
        $this->assertNull($error);
    }

    public function testBatchStat()
    {
        $ops = BucketManager::buildBatchStat($this->bucketName, array('php-sdk.html'));
        list($ret, $error) = $this->bucketManager->batch($ops);
        $this->assertEquals(200, $ret[0]['code']);
    }

    public function testDeleteAfterDays()
    {
        $key = rand();
        list($ret, $error) = $this->bucketManager->deleteAfterDays($this->bucketName, $key, 1);
        $this->assertNotNull($error);

        $this->bucketManager->copy($this->bucketName, $this->key, $this->bucketName, $key);
        list($ret, $error) = $this->bucketManager->deleteAfterDays($this->bucketName, $key, 1);
        $this->assertEquals(null, $ret);
    }

    public function testGetCorsRules()
    {
        list($ret, $err) = $this->bucketManager->getCorsRules($this->bucketName);
        $this->assertNull($err);
    }

    public function testPutBucketAccessStyleMode()
    {
        list($ret, $err) = $this->bucketManager->putBucketAccessStyleMode($this->bucketName, 0);
        $this->assertNull($err);
    }

    public function testPutBucketAccessMode()
    {
        list($ret, $err) = $this->bucketManager->putBucketAccessMode($this->bucketName, 0);
        $this->assertNull($err);
    }

    public function testPutReferAntiLeech()
    {
        list($ret, $err) = $this->bucketManager->putReferAntiLeech($this->bucketName, 0, "1", "*");
        $this->assertNull($err);
    }

    public function testPutBucketMaxAge()
    {
        list($ret, $err) = $this->bucketManager->putBucketMaxAge($this->bucketName, 31536000);
        $this->assertNull($err);
    }

    public function testPutBucketQuota()
    {
        list($ret, $err) = $this->bucketManager->putBucketQuota($this->bucketName, -1, -1);
        $this->assertNull($err);
    }

    public function testGetBucketQuota()
    {
        list($ret, $err) = $this->bucketManager->getBucketQuota($this->bucketName);
        $this->assertNull($err);
    }

    public function testChangeType()
    {
        list($ret, $err) = $this->bucketManager->changeType($this->bucketName, $this->key, 0);
        $this->assertNull($err);

        list($ret, $err) = $this->bucketManager->changeType($this->bucketName, $this->key, 1);
        $this->assertNull($err);
    }

    public function testChangeStatus()
    {
        list($ret, $err) = $this->bucketManager->changeStatus($this->bucketName, $this->key, 1);
        $this->assertNull($err);

        list($ret, $err) = $this->bucketManager->changeStatus($this->bucketName, $this->key, 0);
        $this->assertNull($err);
    }
}
