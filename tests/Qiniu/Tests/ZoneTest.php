<?php
namespace Qiniu\Tests;

use Qiniu;
use Qiniu\Zone;

class ZoneTest extends \PHPUnit_Framework_TestCase
{
    protected $zone;
    protected $zoneHttps;
    protected $ak;

    protected $bucketName;
    protected $bucketNameBC;


    protected function setUp()
    {
        global $bucketName;
        $this->bucketName = $bucketName;

        global $bucketNameBC;
        $this->bucketNameBC = $bucketNameBC;

        global $accessKey;
        $this->ak = $accessKey;

        $this->zone = new Zone();
        $this->zoneHttps = new Zone('https');
    }

    public function testUpHosts()
    {

        list($upHosts, $err) = $this->zone->getUpHosts($this->ak, $this->bucketName);
        $this->assertNull($err);
        $this->assertEquals('http://up.qiniu.com', $upHosts[0]);
        $this->assertEquals('http://upload.qiniu.com', $upHosts[1]);

        list($upHosts, $err) = $this->zone->getUpHosts($this->ak, $this->bucketNameBC);
        $this->assertNull($err);
        $this->assertEquals('http://up-z1.qiniu.com', $upHosts[0]);
        $this->assertEquals('http://upload-z1.qiniu.com', $upHosts[1]);

        list($upHosts, $err) = $this->zoneHttps->getUpHosts($this->ak, $this->bucketName);
        $this->assertNull($err);
        $this->assertEquals('https://up.qbox.me', $upHosts[0]);

        list($upHosts, $err) = $this->zoneHttps->getUpHosts($this->ak, $this->bucketNameBC);
        $this->assertNull($err);
        $this->assertEquals('https://up-z1.qbox.me', $upHosts[0]);
    }

    public function testIoHosts()
    {

        $ioHost = $this->zone->getIoHost($this->ak, $this->bucketName);
        $this->assertEquals('http://iovip.qbox.me', $ioHost);

        $ioHost = $this->zone->getIoHost($this->ak, $this->bucketNameBC);
        $this->assertEquals('http://iovip-z1.qbox.me', $ioHost);

        $ioHost = $this->zoneHttps->getIoHost($this->ak, $this->bucketName);
        $this->assertEquals('https://iovip.qbox.me', $ioHost);

        $ioHost = $this->zoneHttps->getIoHost($this->ak, $this->bucketNameBC);
        $this->assertEquals('https://iovip-z1.qbox.me', $ioHost);
    }
}
